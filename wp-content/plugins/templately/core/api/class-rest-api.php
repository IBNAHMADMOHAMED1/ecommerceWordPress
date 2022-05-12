<?php
namespace Templately;

class REST {
	/**
	 * Instance of REST
	 * @var REST
	 */
    protected static $_instance = null;
	/**
	 * Get the instance of REST
	 * @return REST
     */
	public static function get_instance() {
		if ( static::$_instance === null ) {
			static::$_instance = new static;
		}

		return static::$_instance;
    }
    /**
     * Contains WP_User
     *
     * @var WP_User
     */
    protected $current_user;
    /**
     * Initially Invoked
     */
    private static $query = null;
    public function __construct(){
        self::$query = Query::instance();
        \add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }
    /**
     * Permission check for each route
     *
     * @return boolean
     */
    public function check_permission( $request ) {
        $api_key = DB::get_user_specific_login_meta( '_templately_api_key' );
        if( ! empty( $api_key ) && \user_can($this->current_user, 'edit_posts') ) {
            return true;
        }
        return false;
    }
    /**
     * Register Rest Api Init
     *
     * @param WP_REST_Server $wp_rest_server
     * @return void
     */
    public function register_routes( $wp_rest_server ){
        /**
         * Get the current user
         * @var WP_User
         */
        $this->current_user = \wp_get_current_user();
        /**
         * Register Routes
         */
        register_rest_route( 'templately/v1', '/clouds', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'download' ],
            'permission_callback' => [ $this, 'check_permission' ],
            'args' => [
                'id' => [
                    'required' => true,
                    'validate_callback' => function( $id ) { return is_numeric( $id ); }
                ]
            ]
        ]);
        /**
         * My Favourites
         */
        register_rest_route( 'templately/v1', '/my-favourites', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'my_favourites' ],
            'permission_callback' => [ $this, 'check_permission' ]
        ]);
        /**
         * My Downloads
         */
        register_rest_route( 'templately/v1', '/my-downloads', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [ $this, 'my_downloads' ],
            'permission_callback' => [ $this, 'check_permission' ]
        ]);
    }

    public function my_favourites( $request ){
        $type = "";
        if( $request->has_param('type') ) {
            $type = $request->get_param('type');
        }
        $plan_type = 1; // 1 = all, 2 = free, 3 = pro
        if( $request->has_param('plan_type') ) {
            $plan_type = intval( $request->get_param('plan_type') );
        }
        $page = 1;
        if( $request->has_param('page') ) {
            $page = intval( $request->get_param('page') );
        }

        $api_key = DB::get_user_specific_login_meta( '_templately_api_key' );
        $response = self::$query->get(
            self::$query->prepare(
                'mutation { myFavouriteItem( api_key: "%s", type: "%s", plan_type: %d, per_page: 8, page: %d ){ total_page, current_page, data { id, name, rating, type, slug, favourite_count, thumbnail, price, author{ display_name, name, joined }, category{ id, name } } } }',
                $api_key, $type, $plan_type, $page
            ),
            [
                'is_rest' => true,
                'only_data' => true,
                'query' => 'myFavouriteItem'
            ]
        );

        return $response;
    }
    public function my_downloads( $request ){
        $page = 1;
        if( $request->has_param('page') ) {
            $page = $request->get_param('page');
        }
        $api_key = DB::get_user_specific_login_meta( '_templately_api_key' );
        $response = self::$query->get(
            self::$query->prepare(
                'mutation { myDownloadHistory( api_key: "%s", per_page: 10, page: %s ){ data{ name, thumbnail, downloaded_at, slug, type }, current_page, total_page } }',
                $api_key,
                $page
            ),
            [
                'is_rest' => true,
                'only_data' => true,
                'query' => 'myDownloadHistory'
            ]
        );

        return $response;
    }

    public function error( $type = '' ) {
        switch( $type ) {
            case 'api':
                return $this->formattedError( 'api_error', __( 'Unathorized Access: You have to logged in first.', 'templately' ), 401 );
                break;
            default:
                return $this->formattedError( 'response_error', __( '400 Bad Request.', 'templately' ), 400 );
        }
    }

    public function download( $request ){
        if( $request->has_param('id') ) {
            $id = $request->get_param('id');
            $api_key = DB::get_user_specific_login_meta( '_templately_api_key' );
            if( empty( $api_key ) ) {
                return $this->error('api');
            }
            $response = self::$query->get(
                self::$query->prepare(
                    'mutation { downloadMyCloudItem( api_key: "%s", id: %d ){ file, status, message, file_name, file_type } }',
                    $api_key, +$id
                ),
                [
                    'is_rest' => true,
                    'only_data' => true,
                    'query' => 'downloadMyCloudItem'
                ]
            );

            if( isset( $response['file_name'] ) ) {
                require_once ABSPATH . '/wp-admin/includes/file.php';
                $upload_dir = wp_upload_dir();
                $file_name = 'templately-tmp.json';
                $templately_temp_file = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $file_name;
                if ( \file_exists( $templately_temp_file ) ) {
                    unlink( $templately_temp_file );
                }
                $templately_temp_file_url = $upload_dir['baseurl'] . DIRECTORY_SEPARATOR . $file_name;
                $handle = fopen( $templately_temp_file, 'x+' );
                fwrite( $handle, $response['file'] );
                fclose( $handle );
                $response['fileURL'] = $templately_temp_file_url;
            }
            return $response;
        }
        return $this->error();
    }

    private function formattedError( $code, $message, $http_code, $args = [] ){
        return new \WP_Error( "templately_$code", $message, [ 'status' => $http_code ] );
    }
}