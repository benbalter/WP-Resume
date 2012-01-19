<li id="contact_info_row[]" class="contact_info_row">
	<select name="wp_resume_options[contact_info_field][]" id="contact_info_field[]">
	<option></option>
	<?php foreach ( self::$parent->contact_fields() as $id => $field) { ?>
		<?php if ( is_array($field) ) {
			foreach ($field as $subid => $subfield) { ?>
				<option value="<?php echo $id . '|' . $subid; ?>" <?php selected( $field_id, $subid);?>>
					<?php echo $subfield; ?>
				</option>				
			<?php } ?>
		<?php } else { ?>
				<option value="<?php echo $id; ?>" <?php selected($field_id, $id);?>><?php echo esc_attr( $field ); ?></option>	
		<?php } ?>
	<?php } ?>
	</select>
	<input type="text" name="wp_resume_options[contact_info_value][]" id="contact_info_value[]" value="<?php echo $value; ?>"/> <br />
</li>