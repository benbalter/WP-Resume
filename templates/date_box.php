<?php 
/**
 * Template for date box inputs
 * @package WP_Resume
 */
?><label for="from"><?php _e( 'From', 'wp-resume' ); ?>
	<input type="text" name="from" id="from" value="<?php echo esc_attr( $from ); ?>" placeholder="<?php esc_attr_e( 'e.g., May 2011', 'wp-resume' ); ?>"/>
</label>
<label for="to"><?php _e( 'To', 'wp-resume' ); ?>
	<input type="text" name="to" id="to" value="<?php echo esc_attr( $to ); ?>" placeholder="<?php esc_attr_e( 'e.g., Present', 'wp-resume' ); ?>t" />
</label>
