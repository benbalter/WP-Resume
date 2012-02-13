<?php 
/**
 * Template for date box inputs
 * @package WP_Resume
 */
?><label for="from"><?php _e( 'From', 'wp-resume' ); ?>
	<input type="text" name="from" id="from" value="<?php echo $from; ?>" placeholder="e.g., 2011-05"/>
</label>
<label for="to"><?php _e( 'To', 'wp-resume' ); ?>
	<input type="text" name="to" id="to" value="<?php echo $to; ?>" placeholder="e.g., Present" />
</label>
