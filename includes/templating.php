<?php
/**
 * Functions for templating the Resume front end
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package WP_Resume
 */

class WP_Resume_Templating {

	public $author;
	public $date_format = 'F Y';
	public $future_signifier;
	private $parent;

	/**
	 * Stores parent and author within class
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		$this->author = &$parent->author;

		add_action( 'plugins_loaded', array( &$this, 'i18n_init' ) );
	}

	/**
	 * Delay i18ning until all plugins have a chance to load
	 */
	function i18n_init() {

		//i18n: string appended to future date when translated
		$this->future_signifier = __( ' (Anticipated)', 'wp-resume' );

		if ( defined('QTRANS_INIT') || $this->parent->api->apply_filters( 'translate_date', false ) )
			add_filter( 'wp_resume_date', array( &$this, 'translate_date' ), 10, 2 );

	}


	/**
	 * Applies filter and returns author's name
	 * @uses $author
	 * @returns string the author's name
	 */
	function get_name() {

		$name = $this->parent->options->get_user_option( 'name', $this->author );

		$name = $this->parent->api->apply_deprecated_filters( 'resume_name', '2.5', 'name', $name );
		return $this->parent->api->apply_filters( 'name', $name );

	}


	/**
	 * Returns the title of the postition, or if rewriting is enabled, a link to the position
	 * @param int $ID the position ID
	 * @return string the title, or the title link
	 */
	function get_title( $ID ) {

		if ( !$this->parent->options->get_option( 'rewrite' ) ) {
			$title = get_the_title();
		} else {
			$title = '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
			$title = $this->parent->api->apply_deprecated_filters( 'resume_title_link', '2.5', 'title_link', $title );
			$title = $this->parent->api->apply_filters( 'title_link', $title );
		}

		$title = $this->parent->api->apply_deprecated_filters( 'resume_position_title', '2.5', 'position_title', $title );
		return $this->parent->api->apply_filters( 'position_title', $title );

	}


	/**
	 * Returns the section name, or a link to the section if rewrites are on
	 * @param object $section the section object
	 * @param bool $link (optional) whether to link if possible
	 * @returns string the section name or link to section
	 */
	function get_section_name( $section, $link = true ) {

		return $this->get_taxonomy_name( $section, 'section', $link );

	}


	/**
	 * Returns the organization name, or a link to the organization if rewrites are on
	 * @param object $organization the organization object
	 * @param bool $link (optional) whether to link if possible
	 * @returns string the organization name or link to organization
	 */
	function get_organization_name( $organization, $link = true ) {

		return $this->get_taxonomy_name( $organization, 'organization', $link );

	}


	/**
	 * Given a taxonomy object and taxonomy type, returns either the taxnomy name or a link to the taxnomy
	 * @param object $object the taxonomy object
	 * @param unknown $taxonomy
	 * @param bool $link whether to link if possible
	 * @returns string the formatted taxonomy name/link
	 */
	function get_taxonomy_name( $object, $taxonomy, $link ) {
		global $post;

		$rewrite = $this->parent->options->get_option( 'rewrite' );

		if ( !$link ) {
			$name = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $object->name );
			return $this->parent->api->apply_filters( "{$taxonomy}_name", $name );
		}

		//org link
		if ( $taxonomy == 'organization' && $this->parent->get_org_link( $object->term_id ) ) {
			$link = $this->parent->get_org_link( $object->term_id );

			//rewrite links
		} else if ( $rewrite ) {
				$link = get_term_link( $object, "resume_{$taxonomy}" );

				//no link
			} else {
			$name = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $object->name );
			return $this->parent->api->apply_filters( "{$taxonomy}_name", $name );
		}

		$title = '<a href="' . $link . '">' . $object->name . '</a>';


		$title = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_link", '2.5', "{$taxonomy}_link", $title );
		$title = $this->parent->api->apply_filters( '{$taxonomy}_link', $title );
		$title = $this->parent->api->apply_deprecated_filters( "resume_{$taxonomy}_name", '2.5', "{$taxonomy}_name", $title );
		$title = $this->parent->api->apply_filters( '{$taxonomy}_name', $title );

		return $title;

	}


	/**
	 * Returns the author's contact info
	 * @uses $author
	 * @returns array of contact info
	 */
	function get_contact_info() {

		$contact_info = $this->parent->options->get_user_option( 'contact_info', $this->author );
		$contact_info = $this->parent->api->apply_deprecated_filters( 'resume_contact_info', '2.5', 'contact_info', $contact_info );
		return $this->parent->api->apply_filters( 'contact_info', $contact_info );

	}


	/**
	 * Returns the resume summary, if any
	 * @uses $author
	 * @returns string the resume summary
	 */
	function get_summary() {

		$summary = $this->parent->options->get_user_option( 'summary', $this->author );
		$summary = $this->parent->api->apply_deprecated_filters( 'resume_summary', '2.5', 'summary', $summary );
		return $this->parent->api->apply_filters( 'summary', $summary );

	}


	/**
	 * Function used to parse the date meta and move to human-readable format
	 * 
	 * Both from and to are option, and if both are present, will be joined by an &ndash;
	 *
	 * @since 1.0a
	 * @uses resume_date
	 * @uses resume_date_formatted
	 * @param int $ID post ID to generate date for
	 * @return string the formatted date(s)
	 */
	function get_date( $ID ) {

		$date = '';
		
		foreach( array( 'from' => 'dtstart', 'to' => 'dtend' ) as $field => $class ) {
			
			$value = get_post_meta( $ID, "wp_resume_{$field}", true );

			//we don't have this field, skip
			if ( !$value)
				continue;
			
			//to ensure compliance with hResume format, span should reflect ability to parse date
			//@link https://github.com/benbalter/WP-Resume/issues/7
			
			//if we can parse the date, append the proper class and formatted date to span
			if ( strtotime( $value ) ) 
				$date .= '<span class="' . $class . '" title="' . date( 'Y-m-d', strtotime( $value ) ). '">';
			
			//if the position is current, append todays date to span
			else if ( $value == 'Present' )
				$date .= '<span title="' . date( 'Y-m-d' ) . '">';
				
			//if we can't parse the date, just output a standard span
			else
				$date .= '<span>';
	
			$date .= $this->parent->api->apply_filters( 'date', $value, $field );
			
			$date .= '</span>';		
			
			//this is the from field and there is a to field, append the dash
			//it's okay that we're calling get_post_meta twice on "to" because it's cached automatically
			if ( $field == 'from' && get_post_meta( $ID, 'wp_resume_to', true ) )
				$date .= ' &ndash; ';
				
		}

		return $this->parent->api->apply_filters( 'date_formatted', $date, $ID );

	}


	/**
	 * Always dates to be translated and localized, e.g., by qTranslate
	 *
	 * @param string $date the date as stored in post_meta
	 * @param string $type the type, either "from" or "to"
	 * @param string $from the from date
	 * @param string $to the to date
	 * @return string the i18n'd date
	 */
	function translate_date( $date, $type ) {

		//unix timestamp of date, false if not parsable
		$timestamp = strtotime( $date );

		//default date format
		$date_format = $this->parent->api->apply_filters( 'date_format', $this->date_format );

		//allow present to be translatable
		if ( strtolower( trim( $date ) ) == 'Present' )
			$date = __( 'Present', 'wp-resume' );

		//not parsable, can't translate so return whatever we've got
		if ( !$timestamp )
			return $date;

		//i18n date
		$date = date_i18n( $date_format, strtotime( $date ) );

		//we don't do anything else to start dates, so kick
		if ( $type == 'from' )
			return $date;

		//to date is not in the future, again, can't do anything, so kick
		if ( $timestamp < time() )
			return $date;

		//append e.g, ' (Anticipated)' to future dates
		//note: this string won't appear in .POT files, but should still hit qTranslate when run (I hope)
		$date .= __( $this->parent->api->apply_filters( 'future_signifier', $this->future_signifier ) );

		return $date;

	}


}
