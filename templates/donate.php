<?php
/**
 * Simple, unobtrusive prompt to solicit donations
 * Only displays to admins who have not clicked hide
 * @package Plugin_Boilerplate
 */
?>
<?php if ( current_user_can( 'manage_options' ) && !$this->parent->options->get_user_option( 'hide-donate' ) ) : ?>
<tr valign="top" id="donate">
	<th scope="row">
		<?php _e( 'Support' ); ?>
	</th>
	<td>
		<em><?php echo sprintf( __( 'Enjoy using %1$s? Please consider <a href="%2$s">making a small donation</a> to support the software\'s continued development.' ), $this->parent->name, $this->parent->donate->link ); ?></em>
		<span style="font-size: 10px;">(<a href="#" id="hide-donate"><?php _e( 'hide this message' ); ?></a>)</span>
		<?php wp_nonce_field( $this->parent->slug_ . '_hide_donate' , '_ajax_nonce-' . $this->parent->slug . '-hide-donate' ); ?>
		<?php $data = array( 'action' => $this->parent->slug_ . '_hide_donate', 'nonce' => '_ajax_nonce-' . $this->parent->slug . '-hide-donate' ); ?>
		<script>
		var donate = <?php echo json_encode( $data ); ?>;
		jQuery(document).ready( function($) {
			$('#hide-donate').click( function(event){
				event.preventDefault();
				$.ajax({
					url: ajaxurl + '?action=' + donate.action + '&' + donate.nonce + '=' + $('#'+donate.nonce).val(),
					success: function() { $('#donate').fadeOut(); }
				});
				return false;
			});
		});
		</script>
	</td>
</tr>
<?php endif; ?>