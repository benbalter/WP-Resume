<?php 
/**
 * Template for exclusive taxonomies metabox (section and organizations)
 * @package WP_Resume
 */
?><?php foreach ($terms as $term) { ?>
	<input type="radio" name="<?php echo $type; ?>" value="<?php echo $term->term_id; ?>" id="<?php echo $term->slug; ?>"<?php	if ( isset( $current[0]->term_id ) )
			checked( $term->term_id, $current[0]->term_id );
?>>
	<label for="<?php echo $term->slug; ?>"><?php echo $term->name; ?></label><br />
<?php } ?>
<input type="radio" name="<?php echo $type; ?>" value="" id="none" <?php checked( empty($current[0]->term_id) ); ?> />
<label for="none"><?php _e('None', 'wp-resume'); ?></label><br />
<a href="#" id="add_<?php echo $type ?>_toggle">+ <?php echo $taxonomy->labels->add_new_item; ?></a>
<div id="add_<?php echo $type ?>_div" style="display:none">
	<label for="new_<?php echo $type ?>"><?php echo $taxonomy->labels->singular_name; ?>:</label> 
	<input type="text" name="new_<?php echo $type ?>" id="new_<?php echo $type ?>" /><br />
	<?php if ($type == 'wp_resume_organization') { ?>
			<label for="new_<?php echo $type ?>_location" style="padding-right:24px;"><?php _e('Location', 'wp_resume'); ?>:</label> 
			<input type="text" name="new_<?php echo $type ?>_location" id="new_<?php echo $type ?>_location" /><br />
	<?php } ?>
	<input type="button" value="Add New" id="add_<?php echo $type ?>_button" />
	<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="<?php echo $type ?>-ajax-loading" style="display:none;" alt="" />
</div>
<?php wp_nonce_field( 'add_'.$type, '_ajax_nonce-add-'.$type ); ?>
<?php wp_nonce_field( 'wp_resume_taxonomy', 'wp_resume_nonce'); ?>