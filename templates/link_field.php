<?php 
/**
 * Template for organization link inputs
 * @package WP_Resume
 */
?><?php if ( $edit ) { ?>
	<tr class="form-row">
		<th scope="row" valign="top">
			<label for="link">Link</label>
		</th>
		<td class="form-field">
<?php } else { ?> 
	<div class="form-field">
		<label for="link">Link</label>			
<?php } ?>
<?php wp_nonce_field( 'wp_resume_org_link', 'wp_resume_nonce' ); ?>
<input type="text" name="org_link" value="<?php echo esc_attr( $value ); ?>" <?php if ( $edit ) { echo 'size="40"'; } ?> />
<p class="description"><?php _e( '(optional) The link to the organization\'s home page', 'wp-resume' ); ?></p>
<?php echo ( $taxonomy == '' ) ? '</div>' : '</td></tr>'; ?>
