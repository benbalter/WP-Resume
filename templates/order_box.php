<label class="screen-reader-text" for="menu_order"><?php _e('Order', 'wp-resume'); ?></label>
<input type="text" name="menu_order" size="4" id="menu_order" value="<?php echo $post->menu_order; ?>">
<p>
	<?php _e('Your resume will be sorted based on this number (ascending)', 'wp-resume'); ?>. <a href="#" id="wp_resume_help_toggle"><?php _e('More', 'wp-resume'); ?></a><br />
	<div id="wp_resume_help"><?php _e('When you add a new position, feel free to leave this number at "0" and a best guess will be made based on the position\'s end date (reverse chronological order)', 'wp-resume'); ?>. <br /><br /><?php _e('Of Course, you can always <a href="edit.php?post_type=wp_resume_position&page=wp_resume_options#sections">fine tune your resume order</a> on the options page', 'wp-resume');?>.</div>
</p>
