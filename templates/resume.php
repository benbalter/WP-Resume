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

//even if using a custom template file, 
// use this code to get the global resume and template objects
$resume = &$this->parent;
$template = &$resume->templating;

//Retrieve plugin options for later use
$options = $resume->options->get_options();

?>
		<div class="hresume" itemscope itemtype="http://schema.org/Person">
			<div id="bar"> </div>
			<header class="vcard">
				<h2 class="fn n url" id="name" itemprop="name">
					<a href="<?php echo get_permalink(); ?>" itemprop="url">
						<?php echo $template->get_name(); ?>
					</a>
				</h2>
				<ul>
					<?php //loop through contact info fields
					$contact_info = $template->get_contact_info();
					if ( !empty( $contact_info ) ) {
						foreach ( $contact_info as $field => $value) { ?>
						<?php 
							//per hCard specs (http://microformats.org/profile/hcard) adr needs to be an array
							if ( is_array( $value ) ) { ?>
							<div id="<?php echo $field; ?>" <?php $template->contact_info_itemprop( $field ); ?>>
								<?php foreach ($value as $subfield => $subvalue) { ?>
									<li class="<?php echo $subfield; ?>" <?php $template->contact_info_itemprop( $subfield ); ?>><?php echo $subvalue; ?></li>
								<?php } ?>
							</div>
						<?php } elseif ($field == 'email') { ?>
							<li><a href="mailto:<?php echo $value; ?>" class="<?php echo $field; ?>" <?php $template->contact_info_itemprop( $field ); ?>><?php echo $value; ?></a></li>
						<?php } else { ?>
							<li class="<?php echo $field; ?>" <?php $template->contact_info_itemprop( $field ); ?>><?php echo $value; ?></li>
						<?php } ?>
					<?php } ?>
				<?php } ?>
				</ul>
			</header>
			<?php
				$summary = $template->get_summary();
				if ( !empty( $summary ) ) { ?>
			<summary class="summary">
				<?php echo $summary; ?>
			</summary>
			<?php } ?>
<?php 		
			//Loop through each resume section
			foreach ( $resume->get_sections(null, $template->author) as $section) { 

?>
			<section class="vcalendar" id="<?php echo $section->slug; ?>">
<?php			
				//Initialize our org. variable 
				$current_org=''; 
				
				//retrieve all posts in the current section using our custom loop query
				$positions = $resume->query( $section->slug, $template->author );
				
				//loop through all posts in the current section using the standard WP loop
				if ( $positions->have_posts() ) : ?>
				<header><?php echo $template->get_section_name( $section ); ?></header>
				<?php while ( $positions->have_posts() ) : $positions->the_post();
				
					//Retrieve details on the current position's organization
					$org = $resume->get_org( get_the_ID() ); 

					// If this is the first organization, 
					// or if this org. is different from the previous, begin new org
					if ( $org && $resume->get_previous_org() != $org) { ?>
				<article itemprop="affiliation"<?php if ( $section->slug == 'education' ) echo ' itemprop="alumniOf"'; ?> itemscope itemtype="http://schema.org/<?php if ( $section->slug == 'education' ) echo 'Educational'; ?>Organization" class="organization <?php echo $section->slug; ?> vevent" id="<?php echo $org->slug; ?>">
					<header>
						<div class="orgName summary" itemprop="name" id="<?php echo $org->slug; ?>-name"><?php echo $template->get_organization_name( $org ); ?></div>
						<div class="location" itemprop="location" itemprop="workLocation"><?php echo $org->description; ?></div>
					</header>
<?php 			} 	//End if new org ?>
					<<?php echo ( $org ) ? 'section' : 'article'; ?> class="vcard">
						<a href="#name" class="include" title="<?php echo $template->get_name(); ?>"></a>
						<?php if ( $org ) { ?>
							<a href="#<?php echo $org->slug; ?>-name" class="include" title="<?php echo $template->get_organization_name( $org, false ); ?>"></a>
						<?php } else { ?>
							<header>
						<?php } ?>
						<div class="title" itemprop="jobTitle"><?php echo $template->get_title( get_the_ID() ); ?></div>
						<div class="date"><?php echo $template->get_date( get_the_ID() ); ?></div>
						<?php if ( !$org ) { ?>
							</header>
						<?php } ?>
						<div class="details" itemprop="description">
						<?php the_content(); ?>
<?php 			//If the current user can edit posts, output the link
				if ( current_user_can( 'edit_posts' ) ) 
					edit_post_link( 'Edit' ); 	
?>
						</div><!-- .details -->
					</<?php echo ( $org ) ? 'section' : 'article'; ?>> <!-- .vcard -->
<?php  
				if ( $org && $resume->get_next_org() != $org ) { ?>
					</article><!-- .organization -->
				<?php }

				//End loop
				endwhile; endif;	
?>
			</section><!-- .section -->
<?php } ?> 
		</div><!-- #resume -->
<?php
	//Reset query so the page displays comments, etc. properly
	wp_reset_query();
?>