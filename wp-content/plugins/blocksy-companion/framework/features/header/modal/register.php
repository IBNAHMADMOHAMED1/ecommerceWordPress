<?php

$has_username = true;
$has_password = false;
if ( \Blocksy\Plugin::instance()->account_auth->has_woo_register_flow() && 'no' !== get_option( 'woocommerce_registration_generate_username' ) ) {
    $has_username = false;
}
if ( \Blocksy\Plugin::instance()->account_auth->has_woo_register_flow() && 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
    $has_password = true;
}
?>

<form name="registerform" id="registerform" action="#" method="post" novalidate="novalidate">
	<?php 
do_action( 'woocommerce_register_form_start' );
?>
	<?php 
do_action( 'blocksy:account:modal:register:start' );
?>

	<?php 

if ( $has_username ) {
    ?>
		<p>
			<label for="user_login_register"><?php 
    echo  __( 'Username', 'blocksy-companion' ) ;
    ?></label>
			<input type="text" name="user_login" id="user_login_register" class="input" value="" size="20" autocapitalize="off">
		</p>
	<?php 
}

?>

	<p>
		<label for="user_email"><?php 
echo  __( 'Email', 'blocksy-companion' ) ;
?></label>
		<input type="email" name="user_email" id="user_email" class="input" value="" size="25">
	</p>

	<?php 

if ( $has_password ) {
    ?>
		<p>
			<label for="user_pass_register"><?php 
    echo  __( 'Password', 'blocksy-companion' ) ;
    ?></label>
			<input type="password" name="user_pass" id="user_pass_register" class="input" value="" size="20" autocapitalize="off" autocomplete="new-password">
		</p>
	<?php 
}

?>

	<?php 
do_action( 'register_form' );
?>

	<p id="reg_passmail">
		<?php 
echo  __( 'Registration confirmation will be emailed to you', 'blocksy-companion' ) ;
?>
	</p>

	<p>
		<button name="wp-submit" class="ct-button">
			<?php 
echo  __( 'Register', 'blocksy-companion' ) ;
?>
		</button>

		<!-- <input type="hidden" name="redirect_to" value="<?php 
echo  blocksy_current_url() ;
?>"> -->
	</p>

	<?php 
do_action( 'blocksy:account:modal:register:end' );
?>
	<?php 
do_action( 'woocommerce_register_form_end' );
?>
	<?php 
wp_nonce_field( 'blocksy-register', 'blocksy-register-nonce' );
?>
</form>

