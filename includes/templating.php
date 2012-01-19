<?php

class WP_Resume_Templating {

	static $parent;
	public $author;

	function __construct( &$instance ) {
		
		//create or store parent instance
		if ( $instance === null ) 
			self::$parent = new Plugin_Boilerplate;
		else
			self::$parent = &$instance;
			
		$this->author = &$instance->author;
		
	}
	
	/** 
	 * Applies filter and returns author's name
	 * @uses $author
	 * @returns string the author's name
	 */
	function get_name() {
		
		$name = self::$parent->options->get_user_option( 'name', $this->author );
		
		$name = self::$parent->api->apply_deprecated_filters( 'name', '3.0', 'resume_name', $name );
		return self::$parent->api->apply_filters( 'name', $name );
		
	}
	
	/**
	 * Returns the title of the postition, or if rewriting is enabled, a link to the position
	 * @param int $ID the position ID
	 * @return string the title, or the title link
	 */
	function get_title( $ID ) {
	
		$options = 
		
		if ( !$this->options->get_option( 'rewrite' ) ) {
			$title = get_the_title();
		} else {
			$title = '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
			$title = self::$parent->api->apply_deprecated_filters( 'title_link', '3.0', 'resume_title_link', $title );
			$title = self::$parent->api->apply_filters( 'title_link', $title );
		}
		
		$title = self::$parent->api->apply_deprecated_filters( 'position_title', '3.0', 'resume_position_title', $title );
		return self::$parent->api->apply_filters( 'position_title', $title );
		 
	}
	
	/**
	 * Returns the section name, or a link to the section if rewrites are on
	 * @param object $section the section object
	 * @param bool $link whether to link if possible	
	 * @returns string the section name or link to section
	 */
	function get_section_name( $section, $link = true ) {
	
		return $this->get_taxonomy_name( $section, 'section', $link );
			
	}
	
	/**
	 * Returns the organization name, or a link to the organization if rewrites are on
	 * @param object $organization the organization object
	 * @param bool $link whether to link if possible
	 * @returns string the organization name or link to organization
	 */	
	function get_organization_name( $organization, $link = true ) {

		return $this->get_taxonomy_name( $organization, 'organization', $link );
		
	}
	
	/**
	 * Given a taxonomy object and taxonomy type, returns either the taxnomy name or a link to the taxnomy
	 * @param object $object the taxonomy object
	 * @param string $taxnonomy the taxnomy slug after "resume_"
	 * @param bool $link whether to link if possible
	 * @returns string the formatted taxonomy name/link
	 */
	function get_taxonomy_name( $object, $taxonomy, $link ) {
		global $post;
		
		$options = $this->get_options();
		
		if ( !$link ) {
			$name = self::$parent->api->apply_deprecated_filters( '{$taxonomy}_name', '3.0', 'resume_{$taxonomy}_name', $object->name );
			return self::$parent->api->apply_filters( '{$taxonomy}_name', $name );
		}
		
		//org link
		if ( $taxonomy == 'organization' && $this->get_org_link( $object->term_id ) ) {
			$link = $this->get_org_link( $object->term_id );
		
		//rewrite links
		} else if ( isset( $options['rewrite'] ) && $options['rewrite'] ) {
			$link = get_term_link( $object, "resume_{$taxonomy}" );
		
		//no link
		} else {
			$name = self::$parent->api->apply_deprecated_filters( '{$taxonomy}_name', '3.0', 'resume_{$taxonomy}_name', $object->name );
			return self::$parent->api->apply_filters( '{$taxonomy}_name', $name );
		}

		$title = '<a href="' . $link . '">' . $object->name . '</a>';
		
		
		$title = self::$parent->api->apply_deprecated_filters( '{$taxonomy}_link', '3.0', 'resume_{$taxonomy}_link', $name );
		$title = self::$parent->api->apply_filters( '{$taxonomy}_link', $name );
		$title = self::$parent->api->apply_deprecated_filters( '{$taxonomy}_name', '3.0', 'resume_{$taxonomy}_name', $name );
		$title = self::$parent->api->apply_filters( '{$taxonomy}_name', $name );
		
		return $title;
		
	}
	
	/**
	 * Returns the author's contact info
	 * @uses $author
	 * @returns array of contact info
	 */
	function get_contact_info() {
	
		$contact_info = self::$parent->options->get_user_option( 'contact_info', $this->author );
		$contact_info = self::$parent->api->apply_deprecated_filters( 'contact_info', '3.0', 'resume_contact_info', $contact_info );	
		return self::$parent->api->apply_filters( 'contact_info', $contact_info );	

	}
	
	/**
	 * Returns the resume summary, if any
	 * @uses $author
	 * @returns string the resume summary
	 */
	function get_summary() {

		$summary = self::$parent->options->get_user_option( 'summary', $this->author );
		$summary = self::$parent->api->apply_deprecated_filters( 'summary', '3.0', 'resume_summary', $summary );
		return self::$parent->api->apply_filters( 'summary', $summary );

	}
	
	/**
	 * Function used to parse the date meta and move to human-readable format
	 * @since 1.0a
	 * @param int $ID post ID to generate date for
	 */
	function get_date( $ID ) {

		//Grab from and to post meta
		$from = get_post_meta( $ID, 'wp_resume_from', true ); 
		$to = get_post_meta( $ID, 'wp_resume_to', true ); 
		
		//if we have a start date, format as "[from] - [to]" (e.g., May 2005 - May 2006)
		if ( $from ) 
			$date = '<span class="dtstart" title="' . date( 'Y-m-d', strtotime( $from ) ) . '">' . $from . '</span> &ndash; <span class="dtend" title="' . date( 'Y-m-d', strtotime( $to ) ) . '">' . $to . '</span>';
		
		//if we only have a to, just pass back the to (e.g., "May 2015")
		else if ( $to ) 
			$date= '<span class="dtend" title="' . date( 'Y-m-d', strtotime( $to ) ) . '">' . $to . '</span>';
		
		//If there's no date meta, just pass back an empty string so we dont generate errors
		else 
			$date = '';
			
		return self::$parent->api->apply_filters( 'wp_resume_date', $date, $ID, $from, $to );
		
	}	

}