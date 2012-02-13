<?php
/**
 * Functions for templating the Resume front end
 *
 * @author Benjamin J. Balter <ben@balter.com>
 * @package WP_Resume
 */

class WP_Resume_Templating {

	public $author;
	private $parent;

	/**
	 * Stores parent and author within class
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		$this->parent = &$parent;

		$this->author = &$parent->author;

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
	 * @since 1.0a
	 * @param int $ID post ID to generate date for
	 * @return unknown
	 */
	function get_date( $ID ) {

		//Grab from and to post meta
		$from = get_post_meta( $ID, 'wp_resume_from', true );
		$to = get_post_meta( $ID, 'wp_resume_to', true );

		//if we have a start date, format as "[from] - [to]" (e.g., May 2005 - May 2006)
		if ( $from )
		{
			$date = '<span class="dtstart" title="' . date( 'Y-m-d', strtotime( $from ) ) . '">';
            if( date( 'Y-m-d', strtotime( $from ) ) != "1970-01-01" )
                $date .= date_i18n( 'F Y', strtotime( $from ) );
			else
                $date .= $from;
			$date .=  '</span> &ndash;';
            $date .= ' <span class="dtend" title="' . date( 'Y-m-d', strtotime( $to ) ) . '">';
            if( date( 'Y-m-d', strtotime( $to ) ) != "1970-01-01" )
            {
                $date .= date_i18n( 'F Y', strtotime( $to ) );
                if( strtotime( $to ) > time() )
                    $date .= " (" . __("estimated") . ")";
            }
            elseif( strtolower( $to ) == "present" )
                $date .= __("Present");
            else
                $date .= $to;
            $date .= '</span>';
		}

		//if we only have a to, just pass back the to (e.g., "May 2015")
        else if ( $to )
        {
            $date = '<span class="dtend" title="' . date( 'Y-m-d', strtotime( $to ) ) . '">';
            if( date( 'Y-m-d', strtotime( $to ) ) != "1970-01-01" )
            {
                $date .= date_i18n( 'D Y', strtotime( $to ) );
                if( strtotime( $to ) > time() )
                    $date .= " (" . __("estimated") . ")";
            }
            elseif( strtolower( $to ) == "present" )
                $date .= __("Present");
            else
                $date .= $to;
            $date .= '</span>';
        }
			//If there's no date meta, just pass back an empty string so we dont generate errors
			else
				$date = '';

			return $this->parent->api->apply_filters( 'wp_resume_date', $date, $ID, $from, $to );

	}


}
