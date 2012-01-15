<?php 
/**
 * JSON template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.5
 */
 
$wp_resume = WP_Resume::$instance;

//Retrieve plugin options for later use
$options = $wp_resume->get_options();
$author_options = $wp_resume->get_user_options( $wp_resume->author );

//output name and url
$output['name'] = $author_options['name'];
$output['url'] = get_permalink();

//loop through contact info
foreach ($author_options['contact_info'] as $field=>$value) { 
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
if (! empty( $author_options['summary'] ) ) 
	$output['summary'] = $author_options['summary'];

//Loop through each resume section
foreach ( $wp_resume->get_sections( null, $wp_resume->author ) as $section) {

	//Initialize our org. variable and array 
	$current_org=''; 
	$org = array();
	
	//retrieve all posts in the current section using our custom loop query
	$positions = $wp_resume->query( $section->slug, $wp_resume->author );
	
	//loop through all posts in the current section using the standard WP loop
	if ( $positions->have_posts() ) : while ( $positions->have_posts() ) : $positions->the_post();

		//init. array for this position
		$pos = array();
		
		//Retrieve details on the current position's organization
		$organization = $wp_resume->get_org( get_the_ID() ); 

		//init org, if necessary
		if ( $organization && $organization->term_id != $current_org ) {
			$org = array();
			$org['name'] = $organization->name;
			$org['location'] = $organization->description;
		}
			
		//build pos. data into array
		$pos['title'] = get_the_title();
		$pos['dates'] = wp_filter_nohtml_kses( str_replace('&ndash;','-', $wp_resume->format_date( get_the_ID() ) ) );
		$pos['details'] = get_the_content();
		
		//push array into our org array
		$org['positions'][] = $pos;
				
	
		//If this is the first organization, or if this org. is different from the previous, format output acordingly
		if ( $organization && $organization->term_id != $current_org ) {
		
			//push our org array into the output array
			$output['sections'][$section->slug][] = $org;
			
			//store this org's ID to our internal variable for the next loop
	 		$current_org = $organization->term_id;
	
		} else if ( !$organization ) {
			//position is not associated with an organization
			$output['sections'][$section->slug][][] = $pos;
		}
		
	endwhile; endif;
}

$output = apply_filters( 'json_resume', $output );

//push json output to browser
echo json_encode($output);

//reset WP query
wp_reset_query();
