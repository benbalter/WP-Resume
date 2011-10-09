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

global $wp_resume;
if ( !$wp_resume )
	$wp_resume = &WP_Resume::$instance;
 
//Retrieve plugin options for later use
$options = $wp_resume->get_options();
?>
		<div class="hresume">
			<div id="bar"> </div>
			<header class="vcard">
				<h2 class="fn n url" id="name">
					<a href="<?php get_permalink(); ?>">
						<?php echo $wp_resume->get_name(); ?>
					</a>
				</h2>
				<ul>
					<?php //loop through contact info fields
					$contact_info = $wp_resume->get_contact_info();
					if ( !empty( $contact_info ) ) {
						foreach ( $contact_info as $field => $value) { ?>
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
			<?php
				$summary = $wp_resume->get_summary();
				if ( !empty( $summary ) ) { ?>
			<summary class="summary">
				<?php echo $summary; ?>
			</summary>
			<?php } ?>
<?php 		
			//Loop through each resume section
			foreach ( $wp_resume->get_sections(null, $wp_resume->author) as $section) { 

?>
			<section class="vcalendar" id="<?php echo $section->slug; ?>">
<?php			
				//Initialize our org. variable 
				$current_org=''; 
				
				//retrieve all posts in the current section using our custom loop query
				$posts = $wp_resume->query( $section->slug, $wp_resume->author );
				
				//loop through all posts in the current section using the standard WP loop
				if ( $posts->have_posts() ) : ?>
				<header><?php echo $wp_resume->get_section_name( $section ); ?></header>
				<?php while ( $posts->have_posts() ) : $posts->the_post();
				
					//Retrieve details on the current position's organization
					$organization = $wp_resume->get_org( get_the_ID() ); 
				
					//If this is the first organization, or if this org. is different from the previous, format output acordingly
					if ( $organization && $organization->term_id != $current_org) {
					
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
						<div class="orgName summary" id="<?php echo $organization->slug; ?>-name"><?php echo $wp_resume->get_organization_name( $organization ); ?></div>
						<div class="location"><?php echo $organization->description; ?></div>
					</header>
<?php 				
					//End if new org
					}  
?>
					<<?php echo ( $organization ) ? 'section' : 'article'; ?> class="vcard">
						<a href="#name" class="include" title="<?php echo $wp_resume->get_name(); ?>"></a>
						<a href="#<?php echo $organization->slug; ?>-name" class="include" title="<?php echo $wp_resume->get_organization_name( $organization ); ?>"></a>
						<?php if ( !$organization ) { ?>
							<header>
						<?php } ?>
						<div class="title"><?php echo $wp_resume->get_title( get_the_ID() ); ?></div>
						<div class="date"><?php echo $wp_resume->get_date( get_the_ID() ); ?></div>
						<?php if ( !$organization ) { ?>
							</header>
						<?php } ?>
						<div class="details">
						<?php the_content(); ?>
<?php 			//If the current user can edit posts, output the link
				if ( current_user_can( 'edit_posts' ) ) 
					edit_post_link( 'Edit' ); 	
?>
						</div><!-- .details -->
					</<?php echo ( $organization ) ? 'section' : 'article'; ?>> <!-- .vcard -->
<?php 		
				//End loop
				endwhile; endif;	
?>
<?php 		if ( isset( $organization ) && $organization ) { ?>
				</article><!-- .organization -->
<?php 		} ?>
			</section><!-- .section -->
<?php } ?> 
		</div><!-- #resume -->
<?php
	//Reset query so the page displays comments, etc. properly
	wp_reset_query();
?>