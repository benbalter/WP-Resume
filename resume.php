<?php 
/**
 * Main template file for WP Resume
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.0a
 */
define('WP_USE_THEMES', false); 
get_header();
$options = wp_resume_get_options();
?>
<div id='container'>
	<div id='content' role="main">
		<div class='resume'>
			<div id='bar'>&nbsp;</div>
			<div id='header'>
				<h2><?php echo $options['title']; ?></h2>
<?php 			echo $options['contact_info']; ?>
			</div>
<?php 		foreach ( wp_resume_get_sections() as $section) { ?>
			<div class="section" id="<?php echo $section->slug; ?>">
				<div class="label"><?php echo $section->name; ?></div>
<?php			$current_org='';
				wp_resume_query( $section->slug );
				if ( have_posts() ) : while ( have_posts() ) : the_post();
				$organization = wp_resume_get_org( $post->ID );
					if ($organization->term_id != $current_org) {
						if ($current_org != '') { ?>
				</div>
<?php 				}	
					$current_org = $organization->term_id; ?>
				<div class="organization" id="<?php echo $organization->slug; ?>">
					<div class="orgName"><?php echo $organization->name; ?></div>
					<div class="location"><?php echo $organization->description; ?></div>
<?php 				}  ?>
					<div class="title"><?php echo the_title(); ?></div>
					<div class="date"><?php echo wp_resume_format_date($post->ID); ?></div>
					<div class="details">
						<?php the_content(); ?>
<?php if ( current_user_can( 'edit_posts' ) ) 
	edit_post_link('Edit'); ?>
					</div>
<?php 	endwhile; endif; ?>
				</div>
			</div>
<?php 
	}
?>
		</div>
	</div>
<?php 
if ( $options['sidebar'] ) 
	get_sidebar(); 
get_footer(); ?>