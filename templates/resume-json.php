<?php 
/**
 * JSON template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.5
 */
 
$this->parent = WP_Resume::$instance;

//Retrieve plugin options for later use
$author_options = $this->parent->options->get_user_options( $this->parent->author );

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
foreach ( $this->parent->get_sections( null, $this->parent->author ) as $section) {

	//Initialize our org. variable and array 
	$current_org=''; 
	$org = array();
	
	//retrieve all posts in the current section using our custom loop query
	$positions = $this->parent->query( $section->slug, $this->parent->author );
	
	//loop through all posts in the current section using the standard WP loop
	if ( $positions->have_posts() ) : while ( $positions->have_posts() ) : $positions->the_post();

		//init. array for this position
		$pos = array();
		
		//Retrieve details on the current position's organization
		$organization = $this->parent->get_org( get_the_ID() ); 

		//init org, if necessary
		if ( $organization && $organization->term_id != $current_org ) {
			$org = array();
			$org['name'] = $organization->name;
			$org['location'] = $organization->description;
		}
			
		//build pos. data into array
		$pos['title'] = get_the_title();
		$pos['dates'] = wp_filter_nohtml_kses( str_replace('&ndash;','-', $this->parent->templating->get_date( get_the_ID() ) ) );
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

//check for callback and sanitize
//http://stackoverflow.com/a/10900911/1082542
$callback = ( isset( $_GET['callback'] ) ) ? $_GET['callback'] : false;

if ( preg_match( '/[^0-9a-zA-Z\$_]|^(abstract|boolean|break|byte|case|catch|char|class|const|continue|debugger|default|delete|do|double|else|enum|export|extends|false|final|finally|float|for|function|goto|if|implements|import|in|instanceof|int|interface|long|native|new|null|package|private|protected|public|return|short|static|super|switch|synchronized|this|throw|throws|transient|true|try|typeof|var|volatile|void|while|with|NaN|Infinity|undefined)$/', $callback) )
	$callback = false;

//push json output to browser
$json = json_encode($output);
echo ( $callback ) ? "{$callback}($json)" : $json;

//reset WP query
wp_reset_query();
