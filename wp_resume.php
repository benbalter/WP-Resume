<?php
/*
Plugin Name: WP Resume
Plugin URI: http://ben.balter.com/2010/09/12/wordpress-resume-plugin/
Description: Out-of-the-box plugin which utilizes custom post types and taxonomies to add a snazzy resume to your personal blog or Web site. 
Version: 1.41
Author: Benjamin J. Balter
Author URI: http://ben.balter.com/
License: GPL2
*/

/**
 * @author Benjamin J. Balter
 * @shoutout Andrew Nacin (http://andrewnacin.com) for help with CPTs
 * @shoutout Andrew Norcross (http://andrewnorcross.com) for the drag-and-drop CSS
 */

/**
 * Registers the "resume block" custom post type and the the section and organization custom taxonomy
 * @since 1.0a
 */
function wp_resume_register_cpt_and_t() {
	
	//Custom post type labels array
	$labels = array(
    'name' => 'Resume',
    'singular_name' => 'Resume',
    'add_new' => _x('Add New Position', 'wp_resume_position'),
    'add_new_item' => 'Add Position',
    'edit_item' => 'Edit Position',
    'new_item' => 'New Position',
    'view_item' => 'View Position',
    'search_items' => 'Search Positions',
    'not_found' =>  'No Positions Found',
    'not_found_in_trash' => 'No Positions Found in Trash',
    'parent_item_colon' => ''
  );
  
  //Custom post type settings array
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
	'rewrite' => false,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_position' => null,
    'register_meta_box_cb' => 'wp_resume_meta_callback',
    'supports' => array( 'title', 'editor', 'revisions', 'custom-fields', 'page-attributes'),
    'taxonomies' => array('wp_resume_section', 'wp_resume_organization'),
  ); 
  
  //Register the "wp_resume_position" custom post type
  register_post_type( 'wp_resume_position', $args );
  
	//Section labels array
	 $labels = array(
 	   'name' => _x( 'Sections', 'taxonomy general name' ),
 	   'singular_name' => _x( 'Section', 'taxonomy singular name' ),
 	   'search_items' =>  __( 'Search Sections' ),
 	   'all_items' => __( 'All Sections' ),
 	   'parent_item' => __( 'Parent Section' ),
 	   'parent_item_colon' => __( 'Parent Section:' ),
 	   'edit_item' => __( 'Edit Section' ), 
 	   'update_item' => __( 'Update Section' ),
 	   'add_new_item' => __( 'Add New Section' ),
 	   'new_item_name' => __( 'New Section Name' ),
 	 ); 	
 	 
	//Register section taxonomy	
	register_taxonomy( 'wp_resume_section', 'wp_resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
	
	//orgs labels array
	$labels = array(
 	   'name' => _x( 'Organizations', 'taxonomy general name' ),
 	   'singular_name' => _x( 'Organization', 'taxonomy singular name' ),
 	   'search_items' =>  __( 'Search Organizations' ),
 	   'all_items' => __( 'All Organizations' ),
 	   'parent_item' => __( 'Parent Organization' ),
 	   'parent_item_colon' => __( 'Parent Organization:' ),
 	   'edit_item' => __( 'Edit Organization' ), 
 	   'update_item' => __( 'Update Organization' ),
 	   'add_new_item' => __( 'Add New Organization' ),
 	   'new_item_name' => __( 'New Organization Name' ),
 	 ); 
 	 
	//Register organization taxonomy
	register_taxonomy( 'wp_resume_organization', 'wp_resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
	
}
add_action( 'init', 'wp_resume_register_cpt_and_t', 10 );


/**
 * Customizes the edit screen for our custom post type
 * @since 1.0a
 */
function wp_resume_meta_callback() {

	//pull out the standard post meta box , we don't need it
	remove_meta_box( 'postcustom', 'wp_resume_position', 'normal' );
	
	//build our own section taxonomy selector using radios rather than checkboxes
	//We use the same callback for both taxonomies and just pass the taxonomy type as an argument
	add_meta_box( 'wp_resume_sectiondiv', 'Section', 'wp_resume_taxonomy_box', 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_section') );
	
	//same with orgs 
	add_meta_box( 'wp_resume_organizationdiv', 'Organization', 'wp_resume_taxonomy_box', 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_organization') ); 
	
	//build the date meta input box
	add_meta_box( 'dates', 'Date', 'wp_resume_date_box', 'wp_resume_position', 'normal', 'high');
	
	//build custom order box w/ helptext
	add_meta_box( 'pageparentdiv', 'Resume Order', 'wp_resume_order_box', 'wp_resume_position', 'side', 'low');
}

function wp_resume_order_box($post) {
?>
	<p><strong>Order</strong></p>
	<p>	
		<div style="float:right; width: 200px; padding-right:10px; margin-top: -1em; display: inline;">
			<i>Hint:</i> Your resume will be sorted based on this number (ascending). <a href="#" id="wp_resume_help_toggle">More</a><br /> <br />

			<div id="wp_resume_help">When you add a new position, feel free to leave this number at "0" and a best guess will be made based on the position's end date (reverse chronological order). <br /><br />Of Course, you can always <a href="edit.php?post_type=wp_resume_position&page=wp_resume_options#sections">fine tune your resume order</a> on the options page.</div>
		</div>
		<label class="screen-reader-text" for="menu_order">Order</label>
		<input type="text" name="menu_order" size="4" id="menu_order" value="<?php echo $post->menu_order; ?>">
	</p>
	<p style="clear: both; height: 5px;" id="wp_resume_clearfix"> </p>
	<script>
		jQuery(document).ready(function($){
			$('#wp_resume_help, #wp_resume_clearfix').hide();
			$('#wp_resume_help_toggle').click(function(){
				$('#wp_resume_help, #wp_resume_clearfix').toggle('fast');
				if ($(this).text() == "More")
					$(this).text('Less');
				else
					$(this).text('More');
				return false;
			});
		});
	</script>
<?php
}

/**
 * Generates the taxonomy radio inputs 
 * @since 1.0a
 * @params object $post the post object WP passes
 * @params object $box the meta box object WP passes (with our arg stuffed in there)
 */
function wp_resume_taxonomy_box( $post, $type ) {

	//pull the type out from the meta box object so it's easier to reference
	if ( is_array( $type) )
		$type = $type['args']['type'];
	
	//get the taxonomies details
	$taxonomy = get_taxonomy($type);
		
	//grab an array of all terms within our custom taxonomy, including empty terms
	$terms = get_terms( $type, array( 'hide_empty' => false ) );

	//garb the current selected term where applicable so we can select it
	$current = wp_get_object_terms( $post->ID, $type );
	
	//loop through the terms
	foreach ($terms as $term) {
		
		//build the radio box with the term_id as its value
		echo '<input type="radio" name="'.$type.'" value="'.$term->term_id.'" id="'.$term->slug.'"';
		
		//if the post is already in this taxonomy, select it
		checked( $term->term_id, $current[0]->term_id );
		
		//build the label
		echo '> <label for="'.$term->slug.'">' . $term->name . '</label><br />'. "\r\n";
	}
		echo '<input type="radio" name="'.$type.'" value="" id="none" ';
		checked( empty($current[0]->term_id) );
		echo '/> <label for="none">None</label><br />'. "\r\n"; ?>
		
		<a href="#" id="add_<?php echo $type ?>_toggle">+ <?php echo $taxonomy->labels->add_new_item; ?></a>
		<div id="add_<?php echo $type ?>_div" style="display:none">
			<label for="new_<?php echo $type ?>"><?php echo $taxonomy->labels->singular_name; ?>:</label> 
			<input type="text" name="new_<?php echo $type ?>" id="new_<?php echo $type ?>" /><br />
<?php if ($type == 'wp_resume_organization') { ?>
			<label for="new_<?php echo $type ?>_location" style="padding-right:24px;">Location:</label> 
			<input type="text" name="new_<?php echo $type ?>_location" id="new_<?php echo $type ?>_location" /><br />
<?php } ?>
			<input type="button" value="Add New" id="add_<?php echo $type ?>_button" />
			<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" id="<?php echo $type ?>-ajax-loading" style="display:none;" alt="" />
		</div>
		<script>
			jQuery(document).ready(function($){
				$('#add_<?php echo $type ?>_toggle').click(function(){
					$('#add_<?php echo $type ?>_div').toggle();
				});
				$('#add_<?php echo $type ?>_button').click(function() {
					$('#<?php echo $type ?>-ajax-loading').show();
					$.post('admin-ajax.php?action=add_<?php echo $type; ?>', $('#new_<?php echo $type; ?>, #new_<?php echo $type; ?>_location, #_ajax_nonce-add-<?php echo $type; ?>, #post_ID').serialize(), function(data) { 
						$('#<?php echo $type; ?>div .inside').html(data); 
						});
				});
			});
		</script>
<?php
	//nonce is a funny word
	wp_nonce_field( 'add_'.$type, '_ajax_nonce-add-'.$type );
	wp_nonce_field( 'wp_resume_taxonomy', 'wp_resume_nonce'); 
}

/**
 * Processes AJAX request to add new terms
 * @since 1.2
 */
function wp_resume_ajax_add() {
	
	//pull the taxonomy type (section or organization) from the action query var
	$type = substr($_GET['action'],4);
	
	//pull up the taxonomy details
	$taxonomy = get_taxonomy($type);
	
	//check the nonce
	check_ajax_referer( $_GET['action'] , '_ajax_nonce-add-' . $taxonomy->name );
	
	//check user capabilities
	if ( !current_user_can( $taxonomy->cap->edit_terms ) )
		die('-1');

	//insert term
	$term = wp_insert_term( $_POST['new_'. $type], $type, array('description' => $_POST['new_'. $type . '_location']) );
	
	//associate position with new term
  	wp_set_object_terms( $_POST['post_ID'], $term['term_id'], 'wp_resume_section' );
  	
  	//get updated post to send to taxonomy box
	$post = get_post( $_POST['post_ID'] );
	
	//return the HTML of the updated metabox back to the user so they can use the new term
	wp_resume_taxonomy_box( $post, $type );
	exit();
}

add_action('wp_ajax_add_wp_resume_section', 'wp_resume_ajax_add');
add_action('wp_ajax_add_wp_resume_organization', 'wp_resume_ajax_add');

/**
 * Generates our date custom metadata box
 * @since 1.0a
 * @params object $post the post object WP passes
 */
function wp_resume_date_box( $post ) {	

	//pull the current values where applicable
	$from = get_post_meta( $post->ID, 'wp_resume_from', true );
	$to = get_post_meta( $post->ID, 'wp_resume_to', true );
	
	//format and spit out
	echo '<label for="from">From</label> <input type="text" name="from" id="from" value="'.$from.'" /> ';
	echo '<label for="to">to</label> <input type="text" name="to" id="to" value="'.$to.'" />';

}

/**
 * Saves our custom taxonomies and date metadata on post add/update
 * @since 1.0a
 * @params int $post_id the ID of the current post as passed by WP
 */
function wp_resume_save_wp_resume_position( $post_id ) {

	//Verify our nonce, also varifies that we are on the edit page and not updating elsewhere
  	if ( !wp_verify_nonce( $_POST['wp_resume_nonce'], 'wp_resume_taxonomy' , 'wp_resume_nonce' ) )
  	  	return $post_id;
  	  	  	
  	//If we're autosaving we don't really care all that much about taxonomies and metadata
  	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
  	  	return $post_id;
  	  	
  	//If this is a post revision and not the actual post, kick
  	//(the save_post action hook gets called twice on every page save)
  	if ( wp_is_post_revision($post_id) )
  		return $post_id;
  	
  	//Verify user permissions
  	if ( !current_user_can( 'edit_post', $post_id ) )
  	    return $post_id;
     	   	
  	//Associate the wp_resume_position with our taxonomies
  	wp_set_object_terms( $post_id, (int) $_POST['wp_resume_section'], 'wp_resume_section' );
  	wp_set_object_terms( $post_id, (int) $_POST['wp_resume_organization'], 'wp_resume_organization' );
  	
 	//update the posts date meta
	update_post_meta( $post_id, 'wp_resume_from', $_POST['from'] );
  	update_post_meta( $post_id, 'wp_resume_to', $_POST['to'] );
 		 	  	
  	//If they did not set a menu order, calculate a best guess bassed off of chronology
  	//(menu order uses the posts's menu_order field and is 1 bassed by default)
  	if ($_POST['menu_order'] == 0) {
  		
  		//grab the DB Obj.
  		global $wpdb;
  	
  		//calculate the current timestamp
  		$timestamp = strtotime( $_POST['to'] );
  		if ( !$timestamp ) $timestamp = time();
		
		//set a counter
		$new_post_position = 1;
		
		//loop through posts 
		$section = get_term ($_POST['wp_resume_section'], 'wp_resume_section');
		$args = array(
			'post_type' => 'wp_resume_position',
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'numberposts' => -1,
			'wp_resume_section' =>	$section->slug,
			'exclude' => $post_id
		);
		$posts = get_posts( $args );

		foreach ($posts as $post) {
	
  			//build timestamp of post we're checking
  			$ts_check = strtotime( get_post_meta( $post->ID, 'wp_resume_to', true) );
  			if (!$ts_check) 
  				$ts_check = time();
  			
  			//If we've inserted our new post in the menu_order, increment all subsequent positions
  			if ($new_post_position != 1)
  				//manually update the post b/c calling wp_update_post would create a recurssion loop
		  		$wpdb->update($wpdb->posts,array('menu_order'=>$post->menu_order+1),array('ID'=>$post->ID));
  			
  			//If the new post's timestamp is earlier than the current post, stick the new post here
  			if ($timestamp <= $ts_check && $new_post_position == 1) 
  				$new_post_position = $post->menu_order + 1;	
  		
  		}
  		
		//manually update the post b/c calling wp_update_post would create a recurssion loop
  		$wpdb->update($wpdb->posts,array('menu_order'=>$new_post_position),array('ID'=>$post_id));
  		
   	}

}
add_action( 'save_post', 'wp_resume_save_wp_resume_position' );

/**
 * Function used to parse the date meta and move to human-readable format
 * @since 1.0a
 * @param int $ID post ID to generate date for
 */
function wp_resume_format_date( $ID ) {

	//Grab from and to post meta
	$from = get_post_meta( $ID, 'wp_resume_from', true ); 
	$to = get_post_meta( $ID, 'wp_resume_to', true ); 
	
	//if we have a start date, format as "[from] - [to]" (e.g., May 2005 - May 2006)
	if ( $from ) return $from . ' &ndash; ' . $to;
	
	//if we only have a to, just pass back the to (e.g., "May 2015")
	if ( $to ) return $to;
	
	//If there's no date meta, just pass back an empty string so we dont generate errors
	return '';
}

/**
 * Takes the section term taxonomy and re-keys it to the user specified order
 * @returns array of term objects in user-specified order
 * @since 1.0a
 */
function wp_resume_get_sections( $hide_empty = true ) {

	//init array
	$output = array();

	//get all sections ordered by term_id (order added)
	$sections = get_terms( 'wp_resume_section', array('hide_empty' => $hide_empty ) );
			
	//get the plugin options array to pulll the user-specified order
	$options = wp_resume_get_options();
	
	//pull out the order array (form: term_id => order)
	$section_order = $options[ 'order' ];
	
	//Loop through each section
	foreach( $sections as $ID => $section ) {
		
		//if the term is in our order array
		if ( is_array($section_order) && array_key_exists( $section->term_id, $section_order ) ) { 
		
			//push the term object into the output array keyed to it's order
			$output[ $section_order[$section->term_id] ] = $section;
		
			//pull the term out of the original array
			unset($sections[$ID]);
		
		}
	}
	
	//for those terms that we did not have a user-specified order, stick them at the end of the output array
	foreach($sections as $section) $output[] = $section;
	
	//sort by key
	ksort($output);
				
	//return the new array keyed to order
	return $output;
	
}

/**
 * Queries for all the resume blocks within a given section
 * @params string $section section slug
 * @returns array array of post objects
 * @since 1.0a
 */
function wp_resume_query( $section ) {
	
	//build our query
	$args = array(
		'post_type' => 'wp_resume_position',
		'orderby' => 'menu_order',
		'order' => 'ASC',
		'nopaging' => true,
		'wp_resume_section' => $section,
	);

	//query and return
	$query = new wp_query($args);
	return $query;
}

/**
 * Retrieves the org associated with a given position
 * @since 1.1a
 */
function wp_resume_get_org( $postID ) {

	$organization = wp_get_object_terms( $postID, 'wp_resume_organization' );
	if ( !is_array( $organization ) ) return false;
	return $organization[0];
	
}

/**
 * Get's the options, filters HTML
 * @since 1.2
 */
function wp_resume_get_options() {
	$options = get_option('wp_resume_options');
	$options['title'] = stripslashes( $options['title'] );
	$options['contact_info'] = stripslashes( $options['contact_info'] );
	return $options;
}

/**
 * Adds custom CSS and Javascript to WP's queue
 * @since 1.0a
 */
function wp_resume_enqueue() {
        	
	wp_enqueue_style('wp_resume_stylesheet', plugins_url(  'style.css', __FILE__ ) );

}

add_action( 'wp_print_styles', 'wp_resume_enqueue' );

/**
 * Adds an options submenu to the resume menu in the admin pannel
 * @since 1.0a
 */
function wp_resume_menu() {
	
	add_submenu_page( 'edit.php?post_type=wp_resume_position', 'Resume Options', 'Options', 'manage_options', 'wp_resume_options', 'wp_resume_options' );

}
add_action( 'admin_menu', 'wp_resume_menu' );

/**
 * Tells WP that we're usign a custom setting
 */
function wp_resume_options_int() {
    
    register_setting( 'wp_resume_options', 'wp_resume_options', 'wp_resume_options_validate' );
	$options = wp_resume_get_options();

	//If they just activated, make sure they have some sections
	//This is a work around b/c register_acivation hook is having issues recognizing the custom taxonmomy
	if ( isset($options['just_activated']) && $options['just_activated'] ) {

		//check to see if we have any sections
		if ( sizeof( wp_resume_get_sections(false) ) == 0 ) {
			//add the sections
			wp_insert_term( 'Education', 'wp_resume_section');
			wp_insert_term( 'Experience', 'wp_resume_section' );
			wp_insert_term( 'Awards', 'wp_resume_section' );
		}
		
		//because we changed our function and taxonomy prefix in 1.2, check to see if we need to upgrade the database
		//Also moves from slug to shortcode (1.3)
		wp_resume_upgrade_db();
		
		//get rid of the flag
		$options['just_activated'] = false;

		//set default order if none exists
		$i = 0;
		foreach ( wp_resume_get_sections( false ) as $section) {
			if ( empty ( $options['order'][$section->term_id] ) )
				$options['order'][$section->term_id] = $i++;
		}
		
		//store the new options
		update_option('wp_resume_options',$options);
		
	
	} 
	
	//If we are on the wp_resume_options page, enque the tinyMCE editor
	if ( !empty ($_GET['page'] ) && $_GET['page'] == 'wp_resume_options' ) {
		wp_enqueue_script('editor');
		add_thickbox();
		wp_enqueue_script('media-upload');
		wp_enqueue_script('post');
		add_action( 'admin_print_footer_scripts', 'wp_tiny_mce', 25 );
		wp_enqueue_style('wp_resume_admin_stylesheet', plugins_url(  'admin-style.css', __FILE__ ) );
		wp_enqueue_script( array("jquery", "jquery-ui-core", "interface", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable") );
	}
}

add_action( 'admin_init', 'wp_resume_options_int' );


/**
 * Valdidates options submission data and stores position order
 * @params array $data post data
 * @since 1.5
 * @returns array of validated data (without position order)
 */
function wp_resume_options_validate($data) {
	
	//strip html from title
	$data['title'] = wp_filter_nohtml_kses($data['title']);
	
	//Filter HTML
	$data['contact_info'] = wp_filter_kses($data['contact_info']);
	
	//sanitize section order data
	foreach ($data['order'] as &$section)
		$section = intval( $section );

	//if there is no position_order data (e.g., if this is activation) no need to store
	if ( !isset($data['position_order'] ) ) 
		return $data;
		
	//store position order data
	foreach ($data['position_order'] as $positionID => $order) {
		$post['ID'] = intval( $positionID );
		$post['menu_order'] = intval( $order );
		wp_update_post( $post );
 	}
	unset ( $data['position_order'] );
	
	return $data;
}

/**
 * Creates the options sub-panel
 * @since 1.0a
 */
function wp_resume_options() { 	
?>
<div class="wrap">
	<h2>Resume Options</h2>
	<form method="post" action='options.php' id="wp_resume_form">
<?php 
		
//provide feedback
settings_errors();

//Tell WP that we are on the wp_resume_options page
settings_fields( 'wp_resume_options' ); 

//Pull the existing options from the DB
$options = wp_resume_get_options();

?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Usage</label></th>
			<td>
				<strong>To use WP Resume...</strong>
				<ol>
					<li>Add content to your resume through the menus on the left</li>
					<li>If you wish, add a title, contact information, and order your resume below</li>
					<li>Create a new page as you would normally
					<li>Add the text <code>[wp_resume]</code> to the page's body</li>
					<li>Your resume will now display on that page.</li>
				</ol>
					<i>Note: Although some styling is included by default, you can customize the layout by modifying <a href='theme-editor.php'>your theme's stylesheet</a></i>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wp_resume_options[title]">Resume Title</label></th>
			<td>
				<input name="wp_resume_options[title]" type="text" id="wp_resume_options[title]" value="<?php echo $options['title']; ?>" class="regular-text" /><BR />
				<span class="description">Goes on the top of your resume.  Usually your name, but technically it can be anything you want.</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wp_resume_options[contact_info]">Contact Information</label></th>
			<td id="poststuff">
			<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea">
				<?php the_editor($options['contact_info'], 'wp_resume_options[contact_info]' ); ?>	
			</div>
			<span class="description">Generally used for contact information such as your e-mail address, street address, and phone number, this can be any text or HTML you want and will appear directly below the title on your resume.</div>	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Resume Order</th>
			<td>
			<ul id="sections">
<?php foreach ( wp_resume_get_sections(false) as $section ) { ?>
				<li class="section" id="<?php echo $section->term_id; ?>">
<?php 				echo $section->name; ?>
					<ul class="organizations">
<?php				$current_org='';
					$posts = wp_resume_query( $section->slug );
					if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();
						$organization = wp_resume_get_org( get_the_ID() ); 
						if ($organization && $organization->term_id != $current_org) {
							if ($current_org != '') { 
?>								
									</li>
								</ul>
<?php 						} 
							$current_org = $organization->term_id; 
?>
							<li class="organization" id="<?php echo $organization->term_id; ?>">
								<?php echo $organization->name; ?>
								<ul class="positions">
<?php						}  ?>
								<li class="position" id="<?php the_ID(); ?>">
									<?php echo the_title(); ?> <?php if ($date = wp_resume_format_date( get_the_ID() ) ) echo "($date)"; ?>
								</li>
<?php 				endwhile; ?>
								</ul>				
<?php				endif;	 ?>
					</ul>
				</li>
				<?php } ?>
			</ul>
			<span class="description">New positions are automatically displayed in reverse chronological order, but you can fine tune that order by rearranging the elements in the list above.</span>
			</td>
		</tr>
	</table>
	<script>
	jQuery(document).ready(function($) {
    $("#sections, .positions, .organizations").sortable({
    	axis:'y', 
    	containment: 'parent',
    	opacity: .5,
    	update: function(){},
		placeholder: 'placeholder',
		forcePlaceholderSize: 'true',
    });
    $("#sections").disableSelection();
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
});
	</script>
	<p class="submit">
         <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
</div>
<?php
}

/**
 * Initializes plugin on activation (sets default values, flushes rewrite rules)
 * @since 1.0a
 */
function wp_resume_activate() {
			
	//get current options incase this is a reactivate
	$options = wp_resume_get_options();

	//If they don't have a title set, set it to the blog name
	if ( empty ($options['title'] ) ); 
		$options['title'] = get_bloginfo('name');
	
	//Set the update flag
	$options['just_activated'] = true;
	
	//set our new options
	update_option( 'wp_resume_options', $options);
	
	//flush rewrite rules
	global $wp_rewrite;
   	$wp_rewrite->flush_rules();

} 
   
register_activation_hook( __FILE__, 'wp_resume_activate' );


/**
 * Updates posts, taxonomies, and options from 1.1 to 1.2
 * @since 1.2
 */
function wp_resume_upgrade_db() {
		
	//get the Database object
	global $wpdb;

	//update sections
	$wpdb->query("UPDATE $wpdb->term_taxonomy SET `taxonomy` = 'wp_resume_section' WHERE `taxonomy` = 'wpr_section'");
	
	//update organizations
	$wpdb->query("UPDATE $wpdb->term_taxonomy SET `taxonomy` = 'wp_resume_organization' WHERE `taxonomy` = 'wpr_organization'");
	
	//update positions
	$wpdb->query("UPDATE $wpdb->posts SET `post_type` = 'wp_resume_position' WHERE `post_type` = 'resume_position'");
	
	//update options
	$wpdb->query("UPDATE $wpdb->options SET `option_name` = 'wp_resume_options' WHERE `option_name` = 'wpr_options'");

	//update postmeta
	$wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = 'wp_resume_to' WHERE `meta_key` = 'wpr_to'");
	$wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = 'wp_resume_from' WHERE `meta_key` = 'wpr_from'");
	$wpdb->query("UPDATE $wpdb->postmeta SET `meta_key` = 'wp_resume_timestamp' WHERE `meta_key` = 'wpr_timestamp'");

	//get our options
	$options = wp_resume_get_options();

	//Check to see if they have a slug from <1.2, if so, insert the shortcode into the page if it exists
	if ( !empty( $options['slug'] ) ) {
		
		//lookup the postID bassed on the URL	
		$postID = url_to_postid( get_bloginfo('home') . '/' .  $options['slug'] . '/' );
		
		//If we found a post, insert our shortcode
		if ($postID) {
			  $post['ID'] = $postID;
			  $post['post_content'] = '[wp_resume]';
			  wp_update_post($post);
		}
	}
	
	//Convert our old timestamp sort system into menu order
	
	//Loop through sections
	foreach ( wp_resume_get_sections() as $section) {
	
		//Query DB for all posts w/i that section
		$args = array(
			'post_type' => 'wp_resume_position',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'nopaging' => true,
			'wp_resume_section' => $section->slug,
			'meta_key'=> 'wp_resume_timestamp',
		);
		$posts = get_posts( $args );
		
		//set internal counter
		$i = 1;
		
		//loop through each post within section by timestamp DESC and add a 1-indexed menu order value
		foreach ($posts as $post) {
			
			//update row if necessary
			if ( empty( $post->menu_order ) )
				$wpdb->update($wpdb->posts,array('menu_order'=>$i),array('ID'=>$post->ID)); 
			
			//increment internal counter 	
  			$i++;	
  			
  		}
  	
  	}

}


/**
 * Modifies the add organization page to provide help text and better descriptions
 * @since 1.2
 * @disclaimer it's not pretty, but it get's the job done.
 */
function wp_resume_org_helptext() { ?>
	<script>
		jQuery(document).ready(function($){
			$('#parent, #tag-slug').parent().hide();
			$('#tag-name').siblings('p').text('The name of the organization as you want it to appear');
			$('#tag-description').attr('rows','1').siblings('label').text('Location').siblings('p').text('Traditionally the location of the organization (optional)');
		});
	</script>
	<noscript>
		<h4>Help</h4>
		<p><strong>Name</strong>: The name of the organization</p>
		<p><strong>Parent</strong>: Do not add a parent</p>
		<p><strong>Description</strong>: You can put the location of the organization here (optional)</p>
	</noscript>
<?php }

add_action('wp_resume_organization_add_form','wp_resume_org_helptext');

/**
 * Removes extra fields from add section form
 * @since 1.2
 */
function wp_resume_section_helptext() { ?>
	<script>
		jQuery(document).ready(function($){
			$('#parent').parent().hide();
			$('#tag-description, #tag-slug').parent().hide();
		});
	</script>
<?php }

add_action('wp_resume_section_add_form','wp_resume_section_helptext');

/**
 * Includes resume template on shortcode use 
 * @since 1.3
 */
function wp_resume_shortcode() {
	include('resume.php');
}

add_shortcode('wp_resume','wp_resume_shortcode');
add_action('admin_init', 'wp_resume_activate');
?>