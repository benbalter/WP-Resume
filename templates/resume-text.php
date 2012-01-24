<?php 
/**
 * Plain text template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.5
 */

/**
 * Helper function to create linebreaks
 * @param int $num number of linebreaks to output
 */
function _rn( $num = 1 ) { _spacer( $num, "\r\n"); }

/**
 * Generates a spacer of specified chars using specified charecter
 * @param int $num the number of chars
 * @param string $char the charecter to use
 */
function _spacer( $num, $char = '=' ) { 
	$char = apply_filters( 'resume_spacer_char', $char, $num );
	for( $i=0; $i<$num; $i++)
		echo "$char";
}

//Retrieve plugin options for later use
$author_options = $this->parent->options->get_user_options( $this->parent->author );

//output name
echo $author_options['name']; _rn();
_spacer( strlen( $author_options['name'] ), '=' ); _rn();
	
$spacer = apply_filters( 'resume_plaintext_contact_info_spacer', ' | ' );
echo implode( $spacer, $this->parent->plaintext->contact_info( $author_options['contact_info'] ) );

_rn();

//echo summary, if one exists
if (! empty( $author_options['summary'] ) ) { 
	echo $author_options['summary']; _rn(2);
}

//Loop through each resume section
foreach ( $this->parent->get_sections(null, $this->parent->author) as $section) {
	
	//Initialize our org. variable 
	$current_org = ''; 
	
	//retrieve all posts in the current section using our custom loop query
	$positions = $this->parent->query( $section->slug, $this->parent->author);

	//loop through all posts in the current section using the standard WP loop
	if ( $positions->have_posts() ) : 
	
	//Output section name, all uppercase
	_rn(); echo $section->name; _rn();
	_spacer( strlen( $section->name ), '-' ); _rn();
	
	//loop positions
	while ( $positions->have_posts() ) : $positions->the_post();

		//Retrieve details on the current position's organization
		$organization = $this->parent->get_org( get_the_ID() ); 
				
		//If this is the first organization, or if this org. is different from the previous, format output acordingly
		if ($organization && $organization->term_id != $current_org) {
			
			//store this org's ID to our internal variable for the next loop
			$current_org = $organization->term_id;

			//Format organization header output
			_rn();
			echo strtoupper( $organization->name );
			echo apply_filters( 'resume_plaintext_location', $organization->description );
		
		} //end if new org.	
		
		_rn(); 	echo apply_filters( 'resume_plaintext_title', get_the_title() );
				echo apply_filters( 'resume_plaintext_date', $this->parent->templating->get_date( get_the_ID() ) );
		_rn();	echo apply_filters( 'resume_plaintext_content', get_the_content() ); _rn(1);
	
	//loop		
	endwhile; endif;
	
} 

//Reset query so the page displays comments, etc. properly
wp_reset_query();