<?php
namespace Digidez;

class Custom_Post_Types {

	public static $post_types_taxonomies = array();

    public static function initialise(){
        $self = new self();

        // define all action hooks here and document if not self explanatory
        add_action('init', array($self, 'postTypeFunction'), 0);
    }

    private function createLabels($name){
	    $labels = array(
		    'name'                  => _x($name.'s', 'Post Type General Name', THEME_TD),
		    'singular_name'         => _x($name, 'Post Type Singular Name', THEME_TD),
		    'menu_name'             => __($name.'s', THEME_TD),
		    'name_admin_bar'        => __($name.'s', THEME_TD),
		    'archives'              => __($name.'s', THEME_TD),
		    'attributes'            => __('Item Attributes', THEME_TD),
		    'parent_item_colon'     => __('Parent Item:', THEME_TD),
		    'all_items'             => __('All '.$name.'s', THEME_TD),
		    'add_new_item'          => __('Add new '.$name, THEME_TD),
		    'add_new'               => __('Add new '.$name, THEME_TD),
		    'new_item'              => __('New '.$name, THEME_TD),
		    'edit_item'             => __('Edit '.$name, THEME_TD),
		    'update_item'           => __('Update '.$name, THEME_TD),
		    'view_item'             => __('View '.$name, THEME_TD),
		    'view_items'            => __('View items', THEME_TD),
		    'search_items'          => __('Search Item', THEME_TD),
		    'not_found'             => __('Not found', THEME_TD),
		    'not_found_in_trash'    => __('Not found in Trash', THEME_TD),
		    'featured_image'        => __($name.' image', THEME_TD),
		    'set_featured_image'    => __('Set image', THEME_TD),
		    'remove_featured_image' => __('Remove image', THEME_TD),
		    'use_featured_image'    => __('Use as '.$name.' image', THEME_TD),
		    'insert_into_item'      => __('insert into item', THEME_TD),
		    'uploaded_to_this_item' => __('Uploaded to this item', THEME_TD),
		    'items_list'            => __('Items list', THEME_TD),
		    'items_list_navigation' => __('Items list navigation', THEME_TD),
		    'filter_items_list'     => __('Filter items list', THEME_TD),
	    );

	    return $labels;
    }

	public function postTypeFunction(){
		self::create_kleider_cpt();
		//self::create_services_cpt();
		//self::create_rental_cpt();
		//self::create_reference_cpt();
		//self::create_events_cpt();
		//self::create_jobs_cpt();
		//self::create_resources_cpt();
		//self::create_teams_cpt();
		//self::create_courses_cpt();
		//self::create_testimonials_cpt();
		//self::create_portfolio_cpt();
	}

	public function create_kleider_cpt(){
		$cpt = 'kleider';
		$labels = $this->createLabels('Kleider');
		$args = array(
			'label'               => __('Kleider', THEME_TD),
			'description'         => __('Kleiders', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

	public function create_services_cpt(){
		$cpt = 'service';
		$labels = $this->createLabels('Service');
		$args = array(
			'label'               => __('Service', THEME_TD),
			'description'         => __('Services', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'rest_base'           => '',
			'show_in_menu'        => true,
			'exclude_from_search' => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => $cpt,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'query_var'           => true,
			'taxonomies'          => array($cpt.'-cat'),
			//'rewrite'             => array('slug' => $cpt.'s/%'.$cpt.'-cat%', 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
			'rewrite'             => array('slug' => 'leistung', 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
		//self::$post_types_taxonomies[] = array($cpt => __('Service Cat', THEME_TD), 'reference' => __('Service Cat', THEME_TD));
	}

	public function create_rental_cpt(){
		$cpt = 'rental';
		$labels = $this->createLabels('Rental');
		$args = array(
			'label'               => __('Rental', THEME_TD),
			'description'         => __('Rentals', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'editor'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_reference_cpt(){
		$cpt = 'reference';
		$labels = $this->createLabels('Reference');
		$args = array(
			'label'               => __('Reference', THEME_TD),
			'description'         => __('References', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

	public function create_maplocations_cpt(){
		$cpt = 'map_location';
		$labels = $this->createLabels('Map Location');
		$args = array(
			'label'               => __('Map Location', THEME_TD),
			'description'         => __('Map Locations and performances', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'editor'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_partners_cpt(){
		$cpt = 'partner';
		$labels = $this->createLabels('Partner');
		$args = array(
			'label'               => __('Partner', THEME_TD),
			'description'         => __('Partners and performances', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-groups',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_portfolio_cpt(){
		$cpt = 'portfolio';
		$labels = $this->createLabels('Portfolio');
		$args = array(
			'label'               => __('Portfolio', THEME_TD),
			'description'         => __('Portfolios', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'editor', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

	public function create_events_cpt(){
		$cpt = 'event';
		$labels = $this->createLabels('Event');
		$args = array(
			'label'               => __('Event', THEME_TD),
			'description'         => __('Events', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'excerpt', 'editor', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'rest_base'           => '',
			'show_in_menu'        => true,
			'exclude_from_search' => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => $cpt,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'query_var'           => true,
			'taxonomies'          => array($cpt.'-cat'),
			//'rewrite'             => array('slug' => $cpt.'s/%'.$cpt.'-cat%', 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
			'rewrite'             => array('slug' => $cpt, 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

	public function create_jobs_cpt(){
		$cpt = 'job';
		$labels = $this->createLabels('Job');
		$args = array(
			'label'               => __('Job', THEME_TD),
			'description'         => __('Jobs', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'editor', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_resources_cpt(){
		$cpt = 'resource';
		$labels = $this->createLabels('Resource');
		$args = array(
			'label'               => __('Resource', THEME_TD),
			'description'         => __('Resources', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_testimonials_cpt(){
		$cpt = 'testimonial';
		$labels = $this->createLabels('Testimonial');
		$args = array(
			'label'               => __('Testimonial', THEME_TD),
			'description'         => __('Testimonials', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
		);

		register_post_type($cpt, $args);
		//self::$post_types_taxonomies[] = $cpt;
	}

	public function create_teams_cpt(){
		$cpt = 'team';
		$labels = $this->createLabels('Team');
		$args = array(
			'label'               => __('Team', THEME_TD),
			'description'         => __('Teams', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'excerpt', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'rest_base'           => '',
			'show_in_menu'        => true,
			'exclude_from_search' => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => $cpt,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'query_var'           => true,
			'taxonomies'          => array($cpt.'-cat'),
			//'rewrite'             => array('slug' => $cpt.'s/%'.$cpt.'-cat%', 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
			'rewrite'             => array('slug' => $cpt, 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

	public function create_courses_cpt(){
		$cpt = 'course';
		$labels = $this->createLabels('Course');
		$args = array(
			'label'               => __('Course', THEME_TD),
			'description'         => __('Courses', THEME_TD),
			'labels'              => $labels,
			'supports'            => array('title', 'thumbnail', 'excerpt', 'page-attributes'),
			'hierarchical'        => false,
			'public'              => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'rest_base'           => '',
			'show_in_menu'        => true,
			'exclude_from_search' => false,
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => $cpt,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'query_var'           => true,
			'taxonomies'          => array($cpt.'-cat'),
			//'rewrite'             => array('slug' => $cpt.'s/%'.$cpt.'-cat%', 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
			'rewrite'             => array('slug' => $cpt, 'with_front' => false, 'pages' => false, 'feeds' => false, 'feed' => false),
		);

		register_post_type($cpt, $args);
		self::$post_types_taxonomies[] = $cpt;
	}

}
