<?php
/*
Plugin Name: WP Resume
Plugin URI: http://ben.balter.com/resume/
Description: Out-of-the-box plugin which utilizes custom post types and taxonomys to add a snazzy resume to your personal blog. 
Version: 1.0a
Author: <a href='mailto:ben@balter.com'>Benjamin J. Balter</a>
Author URI: http://ben.balter.com/
*/

/**
 * @author Benjamin J. Balter
 * @shoutout Andrew Nacin (http://andrewnacin.com)
 * @license Creative Commons BY-NC-SA 3.0 (http://creativecommons.org/licenses/by-nc-sa/3.0/)
 * @todo AJAX add of custom terms on add position page
 * @todo AJAX Inline editing
 * @todo edit buttons within template
 * @todo Import from LinkedIn via API
 * @todo revise text on add term pages (description should be location)
 */

/**
 * Registers the "resume block" custom post type and the the section and organization custom taxonomy
 * @since 1.0a
 */
function wpr_register_cpt_and_t() {
	
	//Custom post type labels array
	$labels = array(
    'name' => 'Resume',
    'singular_name' => 'Resume',
    'add_new' => _x('Add New Position', 'resume_position'),
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
    'register_meta_box_cb' => 'wpr_meta_callback',
    'supports' => array( 'title', 'editor', 'revisions', 'custom-fields'),
    'taxonomies' => array('section', 'organization')
  ); 
  
  //Register the "resume_position" custom post type
  register_post_type( 'resume_position', $args );
  
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
	register_taxonomy( 'wpr_section', 'resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
	
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
	register_taxonomy( 'wpr_organization', 'resume_position', array( 'hierarchical' => true, 'labels' => $labels,  'query_var' => true, 'rewrite' => false ) );
	
}
add_action( 'init', 'wpr_register_cpt_and_t', 10 );


/**
 * Customizes the edit screen for our custom post type
 * @since 1.0a
 */
function wpr_meta_callback() {

	//pull out the standard post meta box, we don't need it
	remove_meta_box( 'postcustom', 'resume_position', 'normal' );
	
	//build our own section taxonomy selector using radios rather than checkboxes
	//We use the same callback for both taxonomies and just pass the taxonomy type as an argument
	add_meta_box( 'wpr_sectiondiv', 'Section', 'wpr_taxonomy_box', 'resume_position', 'side', 'low', array('type'=>'wpr_section') );
	
	//same with orgs 
	add_meta_box( 'wpr_organizationdiv', 'Organization', 'wpr_taxonomy_box', 'resume_position', 'side', 'low', array('type'=>'wpr_organization') ); 
	
	//build the date meta input box
	add_meta_box( 'dates', 'Date', 'wpr_date_box', 'resume_position', 'normal', 'high');
	
}

/**
 * Generates the taxonomy radio inputs 
 * @since 1.0a
 * @params object $post the post object WP passes
 * @params object $box the meta box object WP passes (with our arg stuffed in there)
 * @todo AJAX add of new terms
 */
function wpr_taxonomy_box( $post, $box ) {
	
	//pull the type out from the meta box object so it's easier to reference
	$type = $box['args']['type'];
	
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
		echo '> <label for="'.$term->slug.'">' . $term->name . '</label><br />';
	}
		echo '<input type="radio" name="'.$type.'" value="" id="none" ';
		checked( empty($current[0]->term_id) );
		echo '/> <label for="none">None</label><br />';
		
		echo '<input type="text" name="add-'.$type.'" id="add-'.$type.'"  disabled="disabled" /><label fo="add-'.$type.'"> <input type="button" value="Add New" disabled="disabled" />';

	//nonce is a funny word
	wp_nonce_field('wpr_taxonomy', 'wpr_nonce');

}

/**
 * Generates our date custom metadata box
 * @since 1.0a
 * @params object $post the post object WP passes
 */

function wpr_date_box( $post ) {	

	//pull the current values where applicable
	$from = get_post_meta( $post->ID, 'wpr_from', true );
	$to = get_post_meta( $post->ID, 'wpr_to', true );
	
	//format and spit out
	echo '<label for="from">From</label> <input type="text" name="from" id="from" value="'.$from.'" /> ';
	echo '<label for="to">to</label> <input type="text" name="to" id="to" value="'.$to.'" />';

}

/**
 * Saves our custom taxonomies and date metadata on post add/update
 * @since 1.0a
 * @params int $post_id the ID of the current post as passed by WP
 * @todo user permission specific to post type
 */
function wpr_save_resume_position( $post_id ) {

	//Verify our nonce 	
  	if ( !wp_verify_nonce( $_POST['wpr_nonce'], 'wpr_taxonomy' , 'wpr_nonce' ) )
  	  	return $post_id;
  	
  	//If we're autosaving we don't really care all that much about taxonomies and metadata
  	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
  	  	return $post_id;
  	
  	//Verify user permissions
  	if ( !current_user_can( 'edit_post', $post_id ) )
  	    return $post_id;
  	
  	//Associate the resume_position with our taxonomies
  	wp_set_object_terms( $post_id, (int) $_POST['section'], 'wpr_section' );
  	wp_set_object_terms( $post_id, (int) $_POST['organization'], 'wpr_organization' );
  	
  	//Convert the "to" date to a unix timestamp so we can sort blocks chronologically
 	$timestamp = strtotime( $_POST['to']  );
 	
 	//if the "to" field isn't a date (e.g., present) put it at the top
 	if ( !$timestamp )  $timestamp = (string) time();

 	//update the posts date meta
	update_post_meta( $post_id, 'wpr_timestamp', $timestamp );
	update_post_meta( $post_id, 'wpr_from', $_POST['from'] );
  	update_post_meta( $post_id, 'wpr_to', $_POST['to'] );
 
}
add_action( 'save_post', 'wpr_save_resume_position' );

/**
 * Function used to parse the date meta and move to human-readable format
 * @since 1.0a
 * @param int $ID post ID to generate date for
 */
function wpr_format_date( $ID ) {

	//Grab from and to post meta
	$from = get_post_meta( $ID, 'wpr_from', true ); 
	$to = get_post_meta( $ID, 'wpr_to', true ); 
	
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
function wpr_get_sections() {

	//get all sections ordered by term_id (order added)
	$sections = get_terms( 'wpr_section' );
	
	//get the plugin options array to pulll the user-specified order
	$options = get_option( 'wpr_options' );
	
	//pull out the order array (form: term_id => order)
	$section_order = $options[ 'order' ];
	
	//Loop through each section
	foreach( $sections as $ID => $section ) {
	
		//if the term is in our order array
		if ( array_key_exists( $section->term_id, $section_order ) ) { 
		
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
function wpr_get_blocks( $section ) {
	
	//build our query
	$args = array(
		'post_type' => 'resume_position',
		'orderby' => 'meta_value_num',
		'order' => 'DESC',
		'nopaging' => true,
		'wpr_section' => $section,
		'meta_key'=> 'wpr_timestamp'
	);

	//query and return
	return get_posts($args);
	
}

/**
 * Adds custom CSS and Javascript to WP's queue
 * @since 1.0a
 * @todo lots
 */
function wpr_enqueue() {
	
	//Verify that the stylesheet file exists, if so, register it and queue it up
    if ( file_exists( WP_PLUGIN_DIR . '/wp_resume/style.css' ) ) {
        wp_enqueue_style('wpr_stylesheet', plugins_url(  'style.css', __FILE__ ) );
    }

}

/**
 * Adds an options submenu to the resume menu in the admin pannel
 * @since 1.0a
 */
function wpr_menu() {
	
	add_submenu_page( 'edit.php?post_type=resume_position', 'Resume Options', 'Options', 'manage_options', 'wpr_options', 'wpr_options' );

}
add_action( 'admin_menu', 'wpr_menu' );

/**
 * Tells WP that we're usign a custom setting
 */
function wpr_options_int() {
    register_setting( 'wpr_options', 'wpr_options' );
}

add_action( 'admin_init', 'wpr_options_int' );

/**
 * Creates the options sub-panel
 * @since 1.0a
 */
function wpr_options() { ?>
<div class="wrap">
	<h2>Resume Options</h2>
	<form method="post" action='options.php'>
<?php 

//provide feedback
settings_errors();

//Tell WP that we are on the wpr_options page
settings_fields( 'wpr_options' ); 

//Pull the existing options from the DB
$options = get_option( 'wpr_options' );
?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wpr_options[title]">Resume Title</label></th>
			<td>
				<input name="wpr_options[title]" type="text" id="wpr_options[title]" value="<?php echo $options['title']; ?>" class="regular-text" />
				<span class="description">Usually your name, but can be anything you want</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpr_options[slug]">Resume Slug</label></th>
			<td>
				<?php echo bloginfo('home'); ?>/<input name="wpr_options[slug]" type="text" id="wpr_options[slug]" value="<?php echo $options['slug']; ?>" size="15"/>/
				<span class="description">URL to access your resume.  <br /> Hint: If you create a page with the same slug, it makes administration easier <br /> (the resume template override the page, but menu order, title, etc. will remain true)</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpr_options[contact_info]">Contact Information</label></th>
			<td>
				<textarea name="wpr_options[contact_info]" id="wpr_options[contact_info]" class="large-text" rows="10"><?php echo $options['contact_info']; ?></textarea>
				<span class="description">Contact information to display below title (accepts HTML)</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Section Order</th>
			<td>
			<?php $sections = get_terms('wpr_section'); 
			foreach ( $sections as $section ) { ?>
			<input type="text" class="small-text" name="wpr_options[order][<?php echo $section->term_id; ?>]" id="<?php echo $section->slug; ?>" value="<?php echo $options['order'][$section->term_id]; ?>" />
			<label for="<?php echo $section->slug; ?>"><?php echo $section->name; ?></label><br />
			<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wpr_options[sidebar]">Display Sidebar</label></th>
			<td>
				<input name="wpr_options[sidebar]" type="radio" id="wpr_options[sidebar-yes]" <?php checked($options['sidebar'], '1'); ?> value="1" /> <label for="wpr_options['sidebar-yes']">Yes</label> 	<br />
				<input name="wpr_options[sidebar]" type="radio" id="wpr_options[sidebar-no]" value="0"<?php checked($options['sidebar'], '0'); ?> /> <label for="wpr_options['sidebar-no']">No</label>
			</td>
		</tr>
	</table>
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
 * @todo don't overwrite options if they exist
 */
function wpr_activate() {

	update_option( 'wpr_title',get_bloginfo('name'));
	update_option( 'wpr_slug','resume');
	add_filter( 'init', 'wpr_flushRules');

}    
register_activation_hook( __FILE__, 'wpr_activate' );

/**
 * Flushes rewrite rules on plugin activation so we can put our cutsom slug in
 * @since 1.0a
 */

function wpr_flushRules(){

	global $wp_rewrite;
   	$wp_rewrite->flush_rules();

}

/**
 * Establish our slug as a rewrite rule
 * @since 1.0a
 * @params array $rules existing rules
 * @returns array updated rules
 */
function wpr_rewrite_rules( $rules ) {
	
	//get the slug from options
	$options = get_option('wpr_options');
	
	//rewrite our slug to keep the page but append our custom var
	$newrules[ $options['slug'].'/?$'] = 'index.php?wpr_resume=1&pagename=' . $options['slug'];
	
	//push the new rules back
	return $newrules + $rules;

}
add_filter('rewrite_rules_array','wpr_rewrite_rules');

/**
 * If our custom query var is detected, load our template
 * @since 1.0a
 */
function wpr_intercept() {
 		
 	global $wp_query;
 	if( isset($wp_query->query_vars['wpr_resume']) ) {	
 		
 		add_action( 'wp_print_styles', 'wpr_enqueue' );
		add_filter('template_include','wpr_template_filter');
	
	}

}

add_action('template_redirect','wpr_intercept');

/**
 * Redirets all template calls to our template
 */
function wpr_template_filter( $template ) {
	
	return dirname( __FILE__ ) . '/resume.php';
	
}

/**
 * Establishes a custom query var so we can detect our slug
 * @since 1.0a
 * @param array $vars current query vars
 * @returns array updated query vars
 */
function wpr_query_var( $vars ) {

    $vars[] = "wpr_resume";    
    return $vars;

}

add_filter('query_vars', 'wpr_query_var');

?>