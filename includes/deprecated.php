<?php
/**
 * Provides support for pre 2.0 templating functions
 * @since 2.5.3
 */

/** 
 * Returns the global $wp_resume object
 */
function wp_resume_get_object() {

	if ( !class_exists( 'WP_Resume' ) )
		wp_die( 'WP Resume templating function called, but plugin not activated.' );

	global $wp_resume;
	
	//somebody overwrite the global
	if ( !$wp_resume )
		$wp_resume = &WP_Resume::$instance;
		
	return $wp_resume;
	
}

function wp_resume_get_options() {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_get_options', '2.0 of WP Resume', '$wp_resume->options->get_options()' );
	return $resume->options->get_options();
}

function wp_resume_get_sections() {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_get_sections', '2.0 of WP Resume', '$wp_resume->get_sections()' );
	return $resume->get_sections( null, $resume->templating->author );
}

function wp_resume_query( $slug ) {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_query', '2.0 of WP Resume', '$wp_resume->query()' );
	$resume->query( $slug, $resume->templating->author );	
} 

function wp_resume_get_org( $id ) {
	$resume->query( slug, $resume->templating->author );
	_deprecated_function( 'wp_resume_get_org', '2.0 of WP Resume', '$wp_resume->get_org()' );
	$resume->get_org( $id );
} 

function wp_resume_format_date( $id ) {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_format_date', '2.0 of WP Resume', '$wp_resume->get_date()' );
	return $resume->get_date( $id );
}

function wp_resume_get_author( ) {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_get_author', '2.0 of WP Resume', '$wp_resume->templating->author' );
	return $resume->templating->author;
}

function wp_resume_get_user_options() {
	$resume = wp_resume_get_object();
	_deprecated_function( 'wp_resume_get_user_options', '2.0 of WP Resume', '$wp_resume->get_contact_info()' );
	return array( 'contact_info' => $resume->templating->get_contact_info() );
}