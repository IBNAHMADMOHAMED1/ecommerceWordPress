<?php

// wp_login_form([]);
$redirect_to_url = apply_filters( 'blocksy:account:modal:login:redirect_to', $current_url );
$forgot_password_inline = apply_filters( 'blocksy:account:modal:login:forgot-password-inline', true );
$forgot_pass_class = 'ct-forgot-password';
if ( !$forgot_password_inline ) {
    $forgot_pass_class .= '-static';
}
?>

<form name="loginform" id="loginform" action="#" method="post">
	<?php 
do_action( 'woocommerce_login_form_start' );
?>
	<?php 
do_action( 'blocksy:account:modal:login:start' );
?>

	<p class="login-username">
		<label for="user_login"><?php 
echo  __( 'Username or Email Address', 'blocksy-companion' ) ;
?></label>
		<input type="text" name="log" id="user_login" class="input" value="" size="20">
	</p>

	<p class="login-password">
		<label for="user_pass"><?php 
echo  __( 'Password', 'blocksy-companion' ) ;
?></label>
		<input type="password" name="pwd" id="user_pass" class="input" value="" size="20">
	</p>

	<p class="login-remember col-2">
		<span>
			<input name="rememberme" type="checkbox" id="rememberme" class="ct-checkbox" value="forever">
			<label for="rememberme"><?php 
echo  __( 'Remember Me', 'blocksy-companion' ) ;
?></label>
		</span>

		<a href="<?php 
echo  wp_lostpassword_url() ;
?>" class="<?php 
echo  $forgot_pass_class ;
?>">
			<?php 
echo  __( 'Forgot Password?', 'blocksy-companion' ) ;
?>
		</a>
	</p>

	<?php 
do_action( 'login_form' );
?>

	<p class="login-submit">
		<button name="wp-submit" class="ct-button">
			<?php 
echo  __( 'Log In', 'blocksy-companion' ) ;
?>
		</button>

		<input type="hidden" name="redirect_to" value="<?php 
echo  $redirect_to_url ;
?>">
	</p>

	<?php 
do_action( 'blocksy:account:modal:login:end' );
?>
	<?php 
do_action( 'woocommerce_login_form_end' );
?>
</form>

