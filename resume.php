<?php 
/**
 * Main template file for WP Resume
 *
 * HTML5 and hResume compliant
 *
 * @package wp_resume
 * @author Benjamin J. Balter
 * @since 1.0a
 */

$wp_resume = WP_Resume::$instance;
 
//Retrieve plugin options for later use
$options = $wp_resume->get_options();

//check if we have an author, if not, find one
global $wp_resume_author;
if (!$wp_resume_author) 
	$wp_resume_author = $wp_resume->get_author( $atts );

$author_options = $wp_resume->get_user_options($wp_resume_author);
?>
		<div class="resume hresume">
			<div id="bar"> </div>
			<header class="vcard">
				<h2 class="fn n url" id="name"><a href="<?php get_permalink(); ?>"><?php echo $author_options['name']; ?></a></h2>
				<ul>
					<?php //loop through contact info fields
					if ( isset( $author_options['contact_info']) && is_array($author_options['contact_info'] ) ) {
						foreach ($author_options['contact_info'] as $field=>$value) { ?>
						<?php 
							//per hCard specs (http://microformats.org/profile/hcard) adr needs to be an array
							if ( is_array( $value ) ) { ?>
							<div id="<?php echo $field; ?>">
								<?php foreach ($value as $subfield => $subvalue) { ?>
									<li class="<?php echo $subfield; ?>"><?php echo $subvalue; ?></li>
								<?php } ?>
							</div>
						<?php } elseif ($field == 'email') { ?>
							<li><a href="mailto:<?php echo $value; ?>" class="<?php echo $field; ?>"><?php echo $value; ?></a></li>
						<?php } else { ?>
							<li class="<?php echo $field; ?>"><?php echo $value; ?></li>
						<?php } ?>
					<?php } ?>
				<?php } ?>
				</ul>
			</header>
			<?php if (! empty( $$author_options['summary'] ) ) { ?>
			<summary class="summary">
				<?php echo $author_options['summary']; ?>
			</summary>
			<?php } ?>
<?php 		
			//Loop through each resume section
			foreach ( $wp_resume->get_sections(null, $wp_resume_author) as $section) { 

?>
			<section class="vcalendar" id="<?php echo $section->slug; ?>">
<?php			
				//Initialize our org. variable 
				$current_org=''; 
				
				//retrieve all posts in the current section using our custom loop query
				$posts = $wp_resume->query( $section->slug, $wp_resume_author );
				
				//loop through all posts in the current section using the standard WP loop
				if ( $posts->have_posts() ) :  ?>
				<header><?php echo $section->name; ?></header>
				<?php while ( $posts->have_posts() ) : $posts->the_post();
				
					//Retrieve details on the current position's organization
					$organization = $wp_resume->get_org( get_the_ID() ); 
				
					//If this is the first organization, or if this org. is different from the previous, format output acordingly
					if ($organization && $organization->term_id != $current_org) {
					
						//If this is a new org., but not the first, end the previous org's article tag
						if ($current_org != '') { 
?>
				</article>
<?php 				
						} 
						
						//store this org's ID to our internal variable for the next loop
						$current_org = $organization->term_id; 
						
						//Format organization header output
						?>
				<article class="organization <?php echo $section->slug; ?> vevent" id="<?php echo $organization->slug; ?>">
					<header>
						<div class="orgName summary" id="<?php echo $organization->slug; ?>-name"><?php echo $organization->name; ?></div>
						<div class="location"><?php echo $organization->description; ?></div>
					</header>
<?php 				
					//End if new org
					}  
?>
					<<?php echo ($organization) ? 'section' : 'article'; ?> class="vcard">
						<a href="#name" class="include" title="<?php echo $author_options['name']; ?>"></a>
						<a href="#<?php echo $organization->slug; ?>-name" class="include" title="<?php echo $organization->name; ?>"></a>
						<?php if (!$organization) { ?>
							<header>
						<?php } ?>
						<div class="title"><?php echo the_title(); ?></div>
						<div class="date"><?php echo $wp_resume->format_date( get_the_ID() ); ?></div>
						<?php if (!$organization) { ?>
							</header>
						<?php } ?>
						<div class="details">
						<?php the_content(); ?>
<?php 			//If the current user can edit posts, output the link
				if ( current_user_can( 'edit_posts' ) ) 
					edit_post_link('Edit'); 	
?>
						</div><!-- .details -->
					</<?php echo ($organization) ? 'section' : 'article'; ?>> <!-- .vcard -->
<?php 		
				//End loop
				endwhile; endif;	
?>
<?php 		if ( isset($organization) && $organization ) { ?>
				</article><!-- .organization -->
<?php 		} ?>
			</section><!-- .section -->
<?php } ?> 
		</div><!-- #resume -->
<?php
	//Reset query so the page displays comments, etc. properly
	wp_reset_query();
?>