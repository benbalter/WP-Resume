<?php
/**
 * Functions for admin UI
 * @author Benjamin J. Balter <ben@balter.com>
 * @package WP_Resume
 */
class WP_Resume_Admin {

	private $parent;

	/**
	 * Register hooks with WordPress API
	 * @param class $parent (reference) the parent class
	 */
	function __construct( &$parent ) {

		if ( !is_admin() )
			return;

		$this->parent = &$parent;

		//admin UI
		add_action( 'admin_menu', array( &$this, 'menu' ) );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( '55', array( &$this, 'org_helptext' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_filter( 'option_page_capability_wp_resume_options', array( &$this, 'cap_filter' ), 10, 1 );
		add_filter( 'wp_resume_enqueue_js', array( &$this, 'maybe_enqueue' ), 10, 3 );
		add_filter( 'wp_resume_enqueue_css', array( &$this, 'maybe_enqueue_css' ), 10, 3 );

		//ajax callbacks
		add_action('wp_ajax_add_wp_resume_section', array( &$this, 'ajax_add') );
		add_action('wp_ajax_add_wp_resume_organization', array( &$this, 'ajax_add') );
		add_action('wp_ajax_wp_resume_hide_donate', array( &$this, 'hide_donate') );

		//edit position screen
		add_action( 'save_post', array( &$this, 'save_wp_resume_position' ) );

		//organization links
		add_action( 'wp_resume_organization_add_form_fields', array( &$this, 'link_field' ) );
		add_action( 'wp_resume_organization_edit_form_fields', array( &$this, 'link_field' ), 10, 2 );
		add_action( 'create_wp_resume_organization', array( &$this, 'save_link_field' ) );
		add_action( 'edited_wp_resume_organization', array( &$this, 'save_link_field' ) );
		
		//i18n support
		add_action( 'plugins_loaded', array( &$this, 'i18n_init' ) );
		
		$this->init_caps();
			
	}
	
	/**
	 * Delay i18ning until all plugins have a chance to load
	 * Adds qTranslate support for translating sections, organizations, etc.
	 */
	function i18n_init() {
		
		if ( !function_exists( 'qtrans_modifyTermFormFor' ) )
			return;
			
		add_action( 'wp_resume_section_add_form', 'qtrans_modifyTermFormFor' );
		add_action( 'wp_resume_section_edit_form', 'qtrans_modifyTermFormFor' );
		add_action( 'wp_resume_organization_add_form', 'qtrans_modifyTermFormFor' );
		add_action( 'wp_resume_organization_edit_form', 'qtrans_modifyTermFormFor' );
		
	}
	
	/**
	 * Map custom capabilities to default roles
	 * Can be overridden by third party plugins
	 */
	function init_caps() {
		$this->parent->capabilities->defaults = array(
			'administrator' => array( 
				'edit_resume'                       => true, //ability to view resume->options
				'edit_others_resume'                => true, //ability to view other's resume->options
				'edit_resume_positions'             => true, //ability to edit individual position...
				'edit_others_resume_positions'      => true,
				'edit_private_resume_positions'     => true,
				'edit_published_resume_positions'   => true,
				'read_resume_positions'             => true, 
				'read_private_resume_positions'     => true,
				'delete_resume_positions'           => true,
				'delete_others_resume_positions'    => true,
				'delete_private_resume_positions'   => true,
				'delete_published_resume_positions' => true,
				'publish_resume_positions'          => true,
				'manage_resume_sections'            => true,
				'manage_resume_organizations'       => true,
				'edit_resume_sections'              => true,
				'edit_resume_organizations'         => true,
				'delete_resume_sections'            => true,
				'delete_resume_organizations'       => true,
				'assign_resume_sections'            => true,
				'assign_resume_organizations'       => true,
				),
			'editor' => array( 
				'edit_resume'                       => true,
				'edit_others_resume'                => true, 
				'edit_resume_positions'             => true,
				'edit_others_resume_positions'      => true,
				'edit_private_resume_positions'     => true,
				'edit_published_resume_positions'   => true,
				'read_resume_positions'             => true, 
				'read_private_resume_positions'     => true,
				'delete_resume_positions'           => true,
				'delete_others_resume_positions'    => true,
				'delete_private_resume_positions'   => true,
				'delete_published_resume_positions' => true,
				'publish_resume_positions'          => true,
				'manage_resume_sections'            => true,
				'manage_resume_organizations'       => true,
				'edit_resume_sections'              => true,
				'edit_resume_organizations'         => true,
				'delete_resume_sections'            => true,
				'delete_resume_organizations'       => true,
				'assign_resume_sections'            => true,
				'assign_resume_organizations'       => true,
				),
			'author' => array( 
				'edit_resume'                       => true,
				'edit_others_resume'                => false, 
				'edit_resume_positions'             => true,
				'edit_others_resume_positions'      => false,
				'edit_private_resume_positions'     => false,
				'edit_published_resume_positions'   => true,
				'read_resume_positions'             => true, 
				'read_private_resume_positions'     => false,
				'delete_resume_positions'           => true,
				'delete_others_resume_positions'    => false,
				'delete_private_resume_positions'   => false,
				'delete_published_resume_positions' => true,
				'publish_resume_positions'          => true,
				'manage_resume_sections'            => true,
				'manage_resume_organizations'       => true,
				'edit_resume_sections'              => true,
				'edit_resume_organizations'         => true,
				'delete_resume_sections'            => false,
				'delete_resume_organizations'       => false,
				'assign_resume_sections'            => true,
				'assign_resume_organizations'       => true,
				),
			'contributor' => array( 
				'edit_resume'                       => true, 
				'edit_others_resume'                => false, 
				'edit_resume_positions'             => true,
				'edit_others_resume_positions'      => false,
				'edit_private_resume_positions'     => false,
				'edit_published_resume_positions'   => false,
				'read_resume_positions'             => true, 
				'read_private_resume_positions'     => false,
				'delete_resume_positions'           => true,
				'delete_others_resume_positions'    => false,
				'delete_private_resume_positions'   => false,
				'delete_published_resume_positions' => false,
				'publish_resume_positions'          => false,
				'manage_resume_sections'            => true,
				'manage_resume_organizations'       => true,
				'edit_resume_sections'              => true,
				'edit_resume_organizations'         => true,
				'delete_resume_sections'            => false,
				'delete_resume_organizations'       => false,
				'assign_resume_sections'            => true,
				'assign_resume_organizations'       => true,
				),
			'subscriber' => array( 
				'edit_resume'                       => false, 
				'edit_others_resume'                => false, 
				'edit_resume_positions'             => false,
				'edit_others_resume_positions'      => false,
				'edit_private_resume_positions'     => false,
				'edit_published_resume_positions'   => false,
				'read_resume_positions'             => true, 
				'read_private_resume_positions'     => false,
				'delete_resume_positions'           => false,
				'delete_others_resume_positions'    => false,
				'delete_private_resume_positions'   => false,
				'delete_published_resume_positions' => false,
				'publish_resume_positions'          => false,
				'manage_resume_sections'            => false,
				'manage_resume_organizations'       => false,
				'edit_resume_sections'              => false,
				'edit_resume_organizations'         => false,
				'delete_resume_sections'            => false,
				'delete_resume_organizations'       => false,
				'assign_resume_sections'            => false,
				'assign_resume_organizations'       => false,
				),
		);

	}

	/**
	 * Customizes the edit screen for our custom post type
	 * @since 1.0a
	 */
	function meta_callback() {

		//pull out the standard post meta box , we don't need it
		remove_meta_box( 'postcustom', 'wp_resume_position', 'normal' );

		//build our own section taxonomy selector using radios rather than checkboxes
		//We use the same callback for both taxonomies and just pass the taxonomy type as an argument
		add_meta_box( 'wp_resume_sectiondiv', __('Section', 'wp-resume'), array( &$this, 'taxonomy_box' ), 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_section') );

		//same with orgs
		add_meta_box( 'wp_resume_organizationdiv', __('Organization', 'wp-resume'), array( &$this, 'taxonomy_box' ), 'wp_resume_position', 'side', 'low', array('type'=>'wp_resume_organization') );

		//build the date meta input box
		add_meta_box( 'dates', __('Date', 'wp-resume'), array( &$this, 'date_box' ), 'wp_resume_position', 'normal', 'high');

		//build custom order box w/ helptext
		add_meta_box( 'pageparentdiv', __('Resume Order', 'wp-resume'), array( &$this, 'order_box' ), 'wp_resume_position', 'side', 'low');

		do_action( 'position_metaboxes' );

	}


	/**
	 * Processes AJAX request to add new terms
	 * @since 1.2
	 */
	function ajax_add() {

		//pull the taxonomy type (section or organization) from the action query var
		$type = substr($_GET['action'], 4);

		//pull up the taxonomy details
		$taxonomy = get_taxonomy($type);

		//check the nonce
		check_ajax_referer( $_GET['action'] , '_ajax_nonce-add-' . $taxonomy->name );

		//check user capabilities
		if ( !current_user_can( $taxonomy->cap->edit_terms ) )
			die('-1');

		//insert term
		$desc = ( isset( $_POST['new_'. $type . '_location'] ) ) ? $_POST['new_'. $type . '_location'] : '';
		$term = wp_insert_term( $_POST['new_'. $type], $type, array('description' => $desc ) );

		//catch errors
		if ( is_wp_error( $term ) ) {
			$this->taxonomy_box( $post, $type );
			exit();
		}

		//associate position with new term
		wp_set_object_terms( $_POST['post_ID'], $term['term_id'], 'wp_resume_section' );

		if ( $type == 'section' ) {
			$user = wp_get_current_user();
			$author = $user->user_nicename;
			$this->parent->cache->delete( $author . '_sections' );
			$this->parent->cache->delete( $author . '_sections_hide_empty' );
			$this->parent->flush_cache();
		}

		//get updated post to send to taxonomy box
		$post = get_post( $_POST['post_ID'] );

		//return the HTML of the updated metabox back to the user so they can use the new term
		$this->taxonomy_box( $post, $type );

		exit();
	}


	/**
	 * Position metabox callback
	 * @param obj $post the post object
	 */
	function order_box($post) {
		$this->parent->template->order_box( compact( 'post' ) );
	}


	/**
	 * Generates the taxonomy radio inputs
	 * @since 1.0a
	 * @params object $post the post object WP passes
	 * @param unknown $type
	 */
	function taxonomy_box( $post, $type ) {

		//pull the type out from the meta box object so it's easier to reference
		if ( is_array( $type) )
			$type = $type['args']['type'];

		//get the taxonomies details
		$taxonomy = get_taxonomy($type);

		//grab an array of all terms within our custom taxonomy, including empty terms
		$terms = get_terms( $type, array( 'hide_empty' => false ) );

		//garb the current selected term where applicable so we can select it
		$current = wp_get_object_terms( $post->ID, $type );

		$this->parent->template->taxonomy_box( compact( 'taxonomy', 'type', 'terms', 'current' ) );

	}


	/**
	 * Generates our date custom metadata box
	 * @since 1.0a
	 * @params object $post the post object WP passes
	 */
	function date_box( $post ) {

		//pull the current values where applicable
		$from = get_post_meta( $post->ID, 'wp_resume_from', true );
		$to = get_post_meta( $post->ID, 'wp_resume_to', true );

		$this->parent->template->date_box( compact( 'from', 'to' ) );

	}


	/**
	 * Saves our custom taxonomies and date metadata on post add/update
	 * @since 1.0a
	 * @params int $post_id the ID of the current post as passed by WP
	 * @return unknown
	 */
	function save_wp_resume_position( $post_id ) {

		//Verify our nonce, also varifies that we are on the edit page and not updating elsewhere
		if ( !isset( $_POST['wp_resume_nonce'] ) || !wp_verify_nonce( $_POST['wp_resume_nonce'], 'wp_resume_taxonomy' , 'wp_resume_nonce' ) )
			return $post_id;

		//If we're autosaving we don't really care all that much about taxonomies and metadata
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		//If this is a post revision and not the actual post, kick
		//(the save_post action hook gets called twice on every page save)
		if ( wp_is_post_revision($post_id) )
			return $post_id;

		//Verify user permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		//Associate the wp_resume_position with our taxonomies
		wp_set_object_terms( $post_id, (int) $_POST['wp_resume_section'], 'wp_resume_section' );
		wp_set_object_terms( $post_id, (int) $_POST['wp_resume_organization'], 'wp_resume_organization' );

		//update the posts date meta
		update_post_meta( $post_id, 'wp_resume_from', wp_filter_nohtml_kses( $_POST['from'] ) );
		update_post_meta( $post_id, 'wp_resume_to', wp_filter_nohtml_kses( $_POST['to'] ) );

		//If they did not set a menu order, calculate a best guess bassed off of chronology
		//(menu order uses the posts's menu_order field and is 1 bassed by default)
		if ($_POST['menu_order'] == 0) {

			//grab the DB Obj.
			global $wpdb;

			//calculate the current timestamp
			$timestamp = strtotime( $_POST['to'] );
			if ( !$timestamp ) $timestamp = time();

			//set a counter
			$new_post_position = 1;

			//loop through posts
			$section = get_term ($_POST['wp_resume_section'], 'wp_resume_section');
			$args = array(
				'post_type'         => 'wp_resume_position',
				'orderby'           => 'menu_order',
				'order'             => 'ASC',
				'numberposts'       => -1,
				'wp_resume_section' => $section->slug,
				'exclude'           => $post_id
			);
			$posts = get_posts( $args );

			foreach ($posts as $post) {

				//build timestamp of post we're checking
				$ts_check = strtotime( get_post_meta( $post->ID, 'wp_resume_to', true) );
				if (!$ts_check)
					$ts_check = time();

				//If we've inserted our new post in the menu_order, increment all subsequent positions
				if ($new_post_position != 1)
					//manually update the post b/c calling wp_update_post would create a recurssion loop
					$wpdb->update($wpdb->posts, array('menu_order'=>$post->menu_order+1), array('ID'=>$post->ID));

				//If the new post's timestamp is earlier than the current post, stick the new post here
				if ($timestamp <= $ts_check && $new_post_position == 1)
					$new_post_position = $post->menu_order + 1;

			}

			//manually update the post b/c calling wp_update_post would create a recurssion loop
			$wpdb->update($wpdb->posts, array('menu_order'=>$new_post_position), array('ID'=>$post_id));

		}

		$user = wp_get_current_user();
		$this->parent->cache->delete(  $user->user_nicename . '_resume' );
		$this->parent->cache->delete(  $post_id . '_organization' );
		$this->parent->flush_cache();

	}


	/**
	 * Adds an options submenu to the resume menu in the admin pannel
	 * @since 1.0a
	 */
	function menu() {

		add_submenu_page( 'edit.php?post_type=wp_resume_position', __('Resume Options', 'wp-resume'), __('Options', 'wp-resume'), 'edit_posts', 'wp_resume_options', array( &$this, 'options' ) );
	}


	/**
	 * Valdidates options submission data and stores position order
	 * @since 1.5
	 * @params array $data post data
	 * @returns array of validated data (without position order)
	 */
	function options_validate($data) {

		//make sure we're POSTing
		if ( empty($_POST) )
			return $data;

		//grab the existing options, we must hand WP back a complete option array
		$options = $this->parent->options->get_options();

		//figure out what user we are acting on
		global $wpdb;
		$authors =  get_users( array( 'blog_id' => $GLOBALS['blog_id'] ) );

		if ( !current_user_can('edit_others_resume') ) {

			$current_author = get_current_user_id();

		} else if ( sizeof($authors) == 1 ) {

				//if there is only one user in the system, it's gotta be him
				$current_author = $authors[0]->ID;

			} else if ( $_POST['old_user'] != $_POST['user'] ) {

				//if this is an auto save as a result of the author dropdown changing,
				//save as old author, not author we're moving to
				$current_author = $_POST['old_user'];

				//Because we post to options.php and then get redirected,
				//trick WP into appending the user as a parameter so we can update the dropdown
				//goes through wp_safe_redirect, so no need to escape, right?
				$_REQUEST['_wp_http_referer'] .= '&user=' . $_POST['user'];

			} else {

			//if this is a normal submit, just grab the author from the dropdown
			$current_author = $_POST['user'];

		}

		$user_options = $this->parent->options->get_user_options( (int) $current_author );

		//first load would return false
		if ( !is_array( $user_options ) )
			$user_options = array();
			
		//start with a blank array to remove empty fields
		$user_options['contact_info'] = array();

		//strip html from fields
		$user_options['name'] = wp_filter_nohtml_kses( $data['name'] );
		$user_options['summary'] = wp_filter_post_kses( $_POST['wp_resume_options_summary'] );

		foreach ($data['contact_info_field'] as $id=>$value) {

			$field = explode('|', $data['contact_info_field'][$id]);

			if ( !$value || !$id )
				continue;

			if ( sizeof($field) == 1)
				$user_options['contact_info'][$field[0]] = wp_filter_post_kses( $data['contact_info_value'][$id] );
			else
				$user_options['contact_info'][$field[0]][$field[1]] = wp_filter_post_kses( $data['contact_info_value'][$id] );

		}


		//sanitize section order data
		$order = array();

		//if there are no positions, don't err out
		if ( array_key_exists( 'order', (array) $data ) )
			foreach ( (array) $data['order'] as $key => $value)
				$order[] = (int) $key ;

		$user_options['order'] = $order;

		//store position order data
		if ( isset($data['position_order'] )  && is_array($data['position_order'] ) ) {
			foreach ($data['position_order'] as $positionID => $order) {
				$post['ID'] = intval( $positionID );
				$post['menu_order'] = intval( $order );
				wp_update_post( $post );
			}
		}

		if ( current_user_can( 'manage_options' ) ) {
			//move site-wide fields to output array
			$fields = array( 'fix_ie', 'rewrite', 'hide-title' );

			foreach ($fields as $field)
				$options[$field] = (int) $data[$field];

			$options = $this->parent->api->apply_filters( 'options', $options );
			$this->parent->options->set_options( $options );

			flush_rewrite_rules();
		}

		//store usermeta
		$user = get_userdata( $current_author );
		$this->parent->options->set_user_options( $user_options,  $user->ID );
		
		$this->parent->cache->delete(  $user->user_nicename . '_sections');
		$this->parent->cache->delete(  $user->user_nicename . '_sections_hide_empty' );
		$this->parent->cache->delete(  $user->user_nicename . '_resume', 'wp_resume' );
		$this->parent->flush_cache();



		return $options;

	}


	/**
	 * Creates the options sub-panel
	 * @since 1.0a
	 */
	function options() {
		global $wpdb;

		//Pull the existing options from the DB
		$options = $this->parent->options->get_options();

		//set up the current author
		$authors = get_users( array( 'blog_id' => $GLOBALS['blog_id'] ) );

		if ( !current_user_can('edit_others_resume') ) {
			$user = wp_get_current_user();
			$current_author = $user->ID;
		} else if ( sizeof($authors) == 1 ) {
				//if there's only one author, that's our author
				$current_author = $authors[0]->ID;
			} else if ( isset($_GET['user'] ) ) {
				//if there's multiple authors, look for post data from author drop down
				$current_author = $_GET['user'];
			} else {
			//otherwise, assume the current user
			$current_user = wp_get_current_user();
			$current_author = $current_user->ID;
		}


		$user_options = $this->parent->options->get_user_options( (int) $current_author );

		$this->parent->template->options( compact( 'user_options', 'authors', 'current_author', 'options' ) );

	}


	/**
	 * Outputs the dragable ordering UI
	 * @since 2.0.5
	 * @uses dragdrop_section
	 *
	 * Structure:
	 *
	 * ul.sections
	 *  li.section
	 *   ul.organizations
	 *   li.organization
	 *    ul.positions
	 *      li.position
	 * @param string $current_author the current author
	 */
	function order_dragdrop( $current_author ) { ?>
		<ul id="sections">
			<?php //loop through the user's non-empty section
		foreach ( $this->parent->get_sections( true, $current_author ) as $section )
			$this->dragdrop_section ( $current_author, $section );
?>
		</ul><!-- #sections -->
	<?php
	}


	/**
	 * Outputs one section of the dragdrop UI
	 * @since 2.0.5
	 * @uses dragrop_position
	 * @uses get_previous_org
	 * @uses get_next_org
	 * @uses dragdrop_org_start
	 * @uses dragdrop_org_end
	 * @param string $current_author the current author
	 * @param object $section the current section
	 */
	function dragdrop_section( $current_author, $section ) { ?>
		<li class="section" id="<?php echo $section->term_id; ?>">
			<a href="<?php echo admin_url( 'edit-tags.php?action=edit&taxonomy=wp_resume_section&tag_ID=' . $section->term_id . '&post_type=wp_resume_position' ); ?>">
				<?php echo $section->name; ?>
			</a>
			<ul class="organizations">
				<?php

		//get all positions in this section and loop
		$posts = $this->parent->query( $section->slug, $current_author );
		if ( $posts->have_posts() ) : while ( $posts->have_posts() ) : $posts->the_post();

			//grab the current position's organization and compare to last
			//if different or this is the first position, output org label and UL
			$org = $this->parent->get_org( get_the_ID() );
		if ( $org && $this->parent->get_previous_org( ) != $org )
			$this->dragdrop_org_start( $org );

		//main position li
		$this->dragdrop_position();

		//next position's organization is not the same as this
		//or this is the last position in the query
		if ( $org && $this->parent->get_next_org() != $org )
			$this->dragdrop_org_end();

		endwhile; endif; ?>
			</ul><!-- .organizations -->
		</li><!-- .section -->
		<?php
	}


	/**
	 * Outputs an individual position LI
	 * @uses the_loop
	 * @since 2.0.5
	 */
	function dragdrop_position() { ?>
		<li class="position" id="<?php the_ID(); ?>">
			<a href="<?php echo admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' ); ?>">
				<?php echo the_title(); ?>
			</a>
			<?php if ($date = $this->parent->templating->get_date( get_the_ID() ) ) echo "($date)"; ?>
		</li><!-- .position -->
	<?php
	}


	/**
	 * Creates the opening LI and UL for organizations
	 * @since 2.0.5
	 * @param object $organization the org
	 */
	function dragdrop_org_start( $organization ) { ?>
		<li class="organization" id="<?php echo $organization->term_id; ?>">
			<a href="<?php echo admin_url( 'edit-tags.php?action=edit&taxonomy=wp_resume_organization&tag_ID=' . $organization->term_id . '&post_type=wp_resume_position' ); ?>">
				<?php echo $organization->name; ?>
			</a>
			<ul class="positions">
		<?php
	}


	/**
	 * Closes the org's UL and LI
	 * @since 2.0.5
	 */
	function dragdrop_org_end( ) { ?>
			</ul><!-- .positions -->
		</li><!-- .organization -->
		<?php
	}


	/**
	 * Modifies the add organization page to provide help text and better descriptions
	 * @since 1.2
	 * @disclaimer it's not pretty, but it get's the job done.
	 */
	function org_helptext() {
		$this->parent->template->org_helptext();
	}


	/**
	 * Adds field to edit organization page to allow linking to organization's site
	 * @param unknown $term
	 * @param unknown $taxonomy (optional)
	 */
	function link_field( $term, $taxonomy = '' ) {

		$tax = get_taxonomy( $taxonomy );

		$edit = ( $taxonomy != '' );
		$value = '';
		if ( $edit && $this->parent->get_org_link( $term->term_id ) )
			$value = $this->parent->get_org_link( $term->term_id );

		$this->parent->template->link_field( compact( 'edit', 'value', 'taxonomy' ) );
	}


	/**
	 * Saves organization link
	 * @param unknown $termID
	 */
	function save_link_field( $termID ) { 

		if ( !isset( $_REQUEST['wp_resume_nonce'] ) )
			return;

		wp_verify_nonce( 'wp_resume_org_link', $_REQUEST['wp_resume_nonce'] );

		$tax = get_taxonomy( 'wp_resume_organization' );

		if ( !current_user_can( $tax->cap->edit_terms ) )
			return;

		$this->parent->set_org_link( $termID, $_REQUEST['org_link'] );

	}


	/**
	 * Tell WP about our setting
	 * @since 1.6
	 */
	function admin_init() {

		register_setting( 'wp_resume_options', 'wp_resume_options', array( &$this, 'options_validate' ) );

	}


	/**
	 * Tells WP to load our javascript files
	 */
	function enqueue_scripts() {

		$screen = get_current_screen();

		if ( !$this->maybe_enqueue( false, null, null ) )
			return;

		//If we are on the wp_resume_options page
		if ( $screen->id == 'wp_resume_position_page_wp_resume_options' ) {

			//load js libraries
			foreach ( array( "jquery", "jquery-ui-core", "jquery-ui-sortable", "wp-lists", "jquery-ui-sortable" ) as $script )
				wp_enqueue_script( $script );

			// enque the tinyMCE editor
			wp_enqueue_script('editor');
			add_thickbox();
			wp_enqueue_script('media-upload');
			wp_enqueue_script('post');

		}

		$this->parent->enqueue->admin_data = array(
			'more'          => __('More', 'wp-resume'),
			'less'          => __('less', 'wp-resume'),
			'yes'           => __('Yes!', 'wp-resume'),
			'no'            => __('No.', 'wp-resume'),
			'hideAdv'       => __('Hide Advanced Options', 'wp-resume'),
			'showAdv'       => __('Show Advanced Options', 'wp-resume'),
			'orgName'       => __('The name of the organization as you want it to appear', 'wp-resume'),
			'orgLoc'        => __('Traditionally the location of the organization (optional)', 'wp-resume'),
			'missingTaxMsg' => __( 'Please make sure that the position is associated with a section before saving', 'wp-resume'),
		);

	}


	/**
	 * Helper function to load contact_info_row template
	 * @since 2.5
	 * @param unknown $value
	 * @param unknown $field_id
	 */
	function contact_info_row( $value, $field_id ) {
		$this->parent->template->contact_info_row( compact( 'field_id', 'value' ) );
	}


	/**
	 * Only load javscript on resume pages
	 * @since 2.5
	 * @param unknown $default
	 * @param unknown $file
	 * @param unknown $name
	 * @return unknown
	 */
	function maybe_enqueue( $default, $file, $name ) {

		$screen = get_current_screen();

		//3.3
		if ( isset( $screen->post_type ) && $screen->post_type == 'wp_resume_position' )
			return true;

		//3.2
		if ( strpos( $screen->base, 'wp_resume_position' ) !== false )
			return true;

		return false;

	}


	/**
	 * Only load css on resume pages and always on front end
	 * @since 2.5
	 * @param unknown $default
	 * @param unknown $file
	 * @param unknown $name
	 * @return unknown
	 */
	function maybe_enqueue_css( $default, $file, $name ) {

		//only filter admin
		if ( $name != 'admin' )
			return $default;

		return $this->maybe_enqueue( $default, $file, $name );

	}


	/**
	 * Allows non-admins to edit their own resume options
	 * @since 2.0.2
	 * @param string $cap the cap to check
	 * @return string edit_post casts
	 */
	function cap_filter( $cap ) {
		return 'edit_resume';
	}
	
	/**
	 * Wrapper function to load the tinyMCE Editor for summary editing
	 * Used to allow fallback to pre-3.3 function
	 * @param string $field the field to load the editor for
	 */
	function summary_editor( $summary ) {
	
		//3.3
		if ( function_exists( 'wp_editor' ) )
			wp_editor( $summary, 'wp_resume_options_summary' );
		
		//3.2
		else
			the_editor( $summary, 'wp_resume_options_summary' );
		
	}


}
