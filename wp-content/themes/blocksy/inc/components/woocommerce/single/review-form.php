<?php

if (! function_exists('blocksy_product_review_comment_form_args')) {
	function blocksy_product_review_comment_form_args($comment_form) {
		$comment_form['comment_field'] = '';

		if (get_option('woocommerce_enable_review_rating') === 'yes') {
			$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'blocksy' ) . '</label><select name="rating" id="rating" required>
				<option value="">' . esc_html__( 'Rate&hellip;', 'blocksy' ) . '</option>
				<option value="5">' . esc_html__( 'Perfect', 'blocksy' ) . '</option>
				<option value="4">' . esc_html__( 'Good', 'blocksy' ) . '</option>
				<option value="3">' . esc_html__( 'Average', 'blocksy' ) . '</option>
				<option value="2">' . esc_html__( 'Not that bad', 'blocksy' ) . '</option>
				<option value="1">' . esc_html__( 'Very poor', 'blocksy' ) . '</option>
				</select></div>';
		}

		$comment_form['comment_field'] .= '<p class="comment-form-field-textarea"><label for="comment">' . esc_html__( 'Your review', 'blocksy' ) . '<span class="required">&nbsp;*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';
		$comment_form['submit_button'] = '<button name="%1$s" type="submit" id="%2$s" class="%3$s woo-review-submit" value="%4$s">%4$s</button>';

		$comment_form['fields']['author'] = '<p class="comment-form-field-input-author"><label for="author">' . esc_html__( 'Name', 'blocksy' ) . '<span class="required">&nbsp;*</span></label><input id="author" name="author" type="text" value="" size="30" required /></p>';
		$comment_form['fields']['email'] = '<p class="comment-form-field-input-email"><label for="email">' . esc_html__( 'Email', 'blocksy' ) . '<span class="required">&nbsp;*</span></label><input id="email" name="email" type="email" value="" size="30" required /></p>';

		return $comment_form;
	}
}

add_filter(
	'woocommerce_product_review_comment_form_args',
	'blocksy_product_review_comment_form_args',
	10, 1
);

