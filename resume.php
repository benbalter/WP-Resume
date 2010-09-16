<?php 
/**
 * Main template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.0a
 */
get_header();
$options = get_option('wpr_options');
?>
<div id='container'>
<div id='content' role="main">
<div class='resume'>
<div id='bar'>&nbsp;</div>
<div id='header'>
	<h2><?php echo $options['title']; ?></h2>
	<?php echo $options['contact_info']; ?>
	</div>
<?php 

$sections = wpr_get_sections();

foreach ($sections as $section) { ?>
	<div class="section" id="<?php echo $section->slug; ?>">
		<div class="label"><?php echo $section->name; ?></div>
<?php
		$current_org='';
		foreach ( wpr_get_blocks( $section->slug ) as $block) {
			$organization = wp_get_object_terms( $block->ID, 'wpr_organization' );
			
			if ($organization[0]->term_id != $current_org) {
				if ($current_org != '') { ?>
	</div>
<?php }	
				$current_org = $organization[0]->term_id; ?>
		<div class="organization" id="<?php echo $organization[0]->slug; ?>">
			<div class="orgName"><?php echo $organization[0]->name; ?></div>
			<div class="location"><?php echo $organization[0]->description; ?></div>
<?php 		}  ?>
			<div class="title"><?php echo $block->post_title; ?></div>
			<div class="date"><?php echo wpr_format_date($block->ID); ?></div>
			<div class="details">
<?php 		echo $block->post_content; ?>
			</div>
<?php 	}  ?>
		</div>
	</div>
<?php 
}
?>
</div></div>
<?php 
if ( $options['sidebar'] ) 
	get_sidebar(); 
get_footer(); ?>