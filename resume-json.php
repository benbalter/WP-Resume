<?php 
/**
 * JSON template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.5
 */

//Retrieve plugin options for later use
$options = wp_resume_get_options();

//output name and url
$output['name'] = $options['name'];
$output['url'] = get_bloginfo('url');

//loop through contact info
foreach ($options['contact_info'] as $field=>$value) { 
	//per hCard specs (http://microformats.org/profile/hcard) adr needs to be an array
	if ( is_array( $value ) ) {
		foreach ($value as $subfield => $subvalue) { 
			 $output[$subfield] =  $subvalue;
		} 
	} else {
		$output[$field] = $value;
	} 
}

//echo summary, if one exists
if (! empty( $options['summary'] ) ) 
	$output['summary'] = $options['summary'];

//Loop through each resume section
foreach ( wp_resume_get_sections() as $section) {

	//Initialize our org. variable and array 
	$current_org=''; 
	$org = array();
	
	//retrieve all posts in the current section using our custom loop query
	$posts = wp_resume_query( $section->slug );
	
	//loop through all posts in the current section using the standard WP loop
	if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();
		
		//init. array for this position
		$pos = array();
		
		//build pos. data into array
		$pos['title'] = get_the_title();
		$pos['dates'] = wp_resume_format_date( get_the_ID() );
		$pos['details'] = get_the_content();
		
		//push array into our org array
		$org['position'][] = $pos;
		
		//Retrieve details on the current position's organization
		$organization = wp_resume_get_org( get_the_ID() ); 

		//If this is the first organization, or if this org. is different from the previous, format output acordingly
		if ($organization && $organization->term_id != $current_org) {
		
			//push our org array into the output array
			$output[$section->slug] = $org;
			
			//clear the current org array
			$org = array();
			
			//store this org's ID to our internal variable for the next loop
	 		$current_org = $organization->term_id;
			$org['name'] = $organization->name;
			$org['location'] = $organization->description;
		} 
	endwhile; endif;
}

//push json output to browser
echo json_encode($output);

//reset WP query
wp_reset_query();
