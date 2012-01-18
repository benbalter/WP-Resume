<tr valign="top" id="donate">
	<th scope="row">
		<?php _e( 'Support' ); ?>
	</th>
	<td>
		<em><?php sprintf( __( 'Enjoy using %1$s? Please consider <a href="%2$s">making a small donation</a> to support the software\'s continued development.' ), self::$parent->name, $this->link ); ?></em>
		<span style="font-size: 10px;">(<a href="#" id="hide-donate"><?php _e( 'hide this message' ); ?></a>)</span>
		<?php wp_nonce_field( self::$parent->slug_ . '_hide_donate' , '_ajax_nonce-' . self::$parent->slug . '-hide-donate' ); ?>
		<?php $data = array( 'action' => self::$parent->slug_ . '_hide_donate', 'nonce' => '_ajax_nonce-' . self::$parent->slug . '-hide-donate' ); ?>
		<script>
		var donate = <?php echo json_encode( $data ); ?>;
		jQuery(document).ready( function() {
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