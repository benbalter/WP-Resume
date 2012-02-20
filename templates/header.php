<?php 
/**
 * Template for front-end header, adds hcard format and html5shiv if toggled
 * @package WP_Resume
 */
?><link rel="profile" href="http://microformats.org/profile/hcard" />
<?php if ( $this->parent->options->get_option( 'fix_ie' ) ) { ?>
<!--[if lt IE 9]>
	<script type="text/javascript" src="<?php echo plugins_url( 'js/html5.js', dirname( __FILE__ ) ); ?>"></script>
<![endif]-->
<?php } ?>