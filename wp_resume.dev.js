jQuery(document).ready(function($){

	//order box
	$('#wp_resume_help, #wp_resume_clearfix').hide();
	$('#wp_resume_help_toggle').click(function(){
	    $('#wp_resume_help, #wp_resume_clearfix').toggle('fast');
	    if ($(this).text() == wp_resume.more )
	    	$(this).text( wp_resume.less );
	    else
	    	$(this).text( wp_resume.more );
	    return false;
	});
		
	//taxonomy box 
	var types = ['wp_resume_section', 'wp_resume_organization'];
	for (var i in types) {
		$('#add_' + types[i] + '_toggle').live( 'click', function(){
			var type = $(this).attr('id').replace('_toggle', '').replace('add_', '');
			$('#add_' + type + '_div').toggle();
		});
		$('#add_' + types[i] + '_button').live( 'click', function() {
			var type = $(this).attr('id').replace('_button', '').replace('add_', '');
			$('#' + type + '-ajax-loading').show();
			$.post('admin-ajax.php?action=add_' + type, $('#new_' + type + ', #new_' + type + '_location, #_ajax_nonce-add-' + type + ', #post_ID').serialize(), function(data) { 
				$('#' + type + 'div .inside').html(data); 
			});
			event.preventDefault();
		});
	} 

	if ( pagenow == 'wp_resume_position_page_wp_resume_options' ) {

		//options page -- contact info rows
		$('#contact_info').append( $('.contact_info_blank').html() );
		$('.contact_info_row:last').show();
		$('#add_contact_field').click(function(){
			$('#contact_info').append( $('.contact_info_blank').html() );
			$('.contact_info_row:last').fadeIn();						
			return false;
		});
		
		//options page -- help toggles
		$('#multiple').hide();
		$('#toggleMultiple').click(function() {
			$('#multiple').toggle('fast');
			if ($(this).text() == wp_resume.yes )
				$(this).text( wp_resume.no );
			else
				$(this).text( wp_resume.yes );
			return false;
		});
		$('.underHood').hide();
		$('#toggleHood').click(function() {
			$('.underHood').toggle('fast');
			if ($(this).text() == wp_resume.hideAdv  )
				$(this).text( wp_resume.showAdv );
			else
				$(this).text( wp_resume.hideAdv );
			return false;
		});
		
		//options page -- sorting
		$("#sections, .positions, .organizations").sortable({
			axis:'y', 
			containment: 'parent',
			opacity: .5,
			update: function(){},
			placeholder: 'placeholder',
			forcePlaceholderSize: 'true'
		});
		$("#sections").disableSelection();
		
		//options page --submit button
		$('.button-primary').click(function(){
			var i = 0;
			$('.section').each(function(){
				$('#wp_resume_form').append('<input type="hidden" name="wp_resume_options[order]['+$(this).attr('id')+']" value="' + i + '">');
				i = i +1;
			});
			var i = 1;
			$('.position').each(function(){
				$('#wp_resume_form').append('<input type="hidden" name="wp_resume_options[position_order]['+$(this).attr('id')+']" value="' + i + '">');
				i = i +1;
			});
		});
		
		//options page -- user change
		$('#user').change(function(){
			$('.button-primary').click();		
		}); 
		
	}
			
	//organizations page
	if ( pagenow == 'edit-wp_resume_organization') {
		$('#parent, #tag-slug').parent().hide();
		$('#tag-name').siblings('p').text( wp_resume.orgName );
		$('#tag-description').attr('rows','1').siblings('label').text('Location').siblings('p').text( wp_resume.orgLoc );
	}
	
	//sections page
	if ( pagenow == 'edit-wp_resume_section') {
		$('#parent').parent().hide();
		$('#tag-description, #tag-slug').parent().hide();
	}					
});		