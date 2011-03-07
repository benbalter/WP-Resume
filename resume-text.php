<?php 
/**
 * Plain text template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.5
 */
 
//Retrieve plugin options for later use
$options = wp_resume_get_options();

//determine user
global $post;	
if ( preg_match( '/\[wp_resume user=\"([^\"]*)"]/i', $post->post_content, $matches ) === FALSE) {
	$user = get_userdata($post->post_author);
	$author = $user->user_login; 
} else {
	$author = $matches[1];	
}

//output name and url
echo $options[$author]['name'] . "\r\n";
echo get_permalink() . "\r\n";

//loop through contact info
foreach ($options[$author]['contact_info'] as $field=>$value) { 
	//per hCard specs (http://microformats.org/profile/hcard) adr needs to be an array
	if ( is_array( $value ) ) {
		foreach ($value as $subfield => $subvalue) { 
			 echo wp_filter_nohtml_kses( $subvalue ) . "\r\n"; 
		} 
	} else {
		echo wp_filter_nohtml_kses( $value ) . "\r\n";
	} 
}

//spacer
echo "\r\n";

//echo summary, if one exists
if (! empty( $options[$author]['summary'] ) ) 
	echo $options[$author]['summary'] . "\r\n";

//Loop through each resume section
foreach ( wp_resume_get_sections(null, $author) as $section) {
	
	//Initialize our org. variable 
	$current_org=''; 
	
	//retrieve all posts in the current section using our custom loop query
	$posts = wp_resume_query( $section->slug, $author);

	//loop through all posts in the current section using the standard WP loop
	if ( $posts->have_posts() ) : 
	
	//Output section name, all uppercase
	echo "\r\n" . strtoupper( $section->name ) . "\r\n\r\n";
	
	//loop positions
	while ( $posts->have_posts() ) : $posts->the_post();

		//Retrieve details on the current position's organization
		$organization = wp_resume_get_org( get_the_ID() ); 
				
		//If this is the first organization, or if this org. is different from the previous, format output acordingly
		if ($organization && $organization->term_id != $current_org) {

			//If this is a new org., but not the first, insert a blank line to separate
			if ($current_org != '') echo "\r\n";
			
			//store this org's ID to our internal variable for the next loop
			$current_org = $organization->term_id;

			//Format organization header output
			echo $organization->name . ' - ' . $organization->description . "\r\n";
		
		//end if new org.	
		}
		
		the_title();
		$date = wp_filter_nohtml_kses( str_replace('&ndash;','-', wp_resume_format_date( get_the_ID() ) ) );
		if (strlen($date) > 1)
			echo "($date)";
		echo "\r\n";
		echo wp_filter_nohtml_kses( str_replace("\t", "", str_replace('<li>', '* ', get_the_content() ) ) );
	
	//loop		
	endwhile; endif;
	
} 

	//Reset query so the page displays comments, etc. properly
	wp_reset_query();
?>