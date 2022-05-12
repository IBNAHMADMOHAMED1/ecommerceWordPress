<?php

$attr = apply_filters('blocksy:hero:wrapper-attr', $attr);

?>

<div <?php echo blocksy_attr_to_html($attr) ?>>
	<header class="entry-header">
		<?php echo $elements ?>
	</header>
</div>
