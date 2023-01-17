<?php
if(!defined('ABSPATH')) exit;

class _WCPL_PostTypes {

	private $cpt_slug;
	private $tax_slug;
	private $geo_tax_slug;
	private $plugin_name;

	public function __construct(){
		add_action('init', array($this, 'init'), 0);
	}

	public function init(){
		$this->plugin_name = WcPinLoader::instance()->plugin_name;
		$this->tax_slug = WcPinLoader::instance()->taxonomy;
		$this->geo_tax_slug = WcPinLoader::instance()->geo_taxonomy;
		$this->cpt_slug = WcPinLoader::instance()->plugin_slug;

		add_action('init', array($this, '_register_taxonomies'), 10);
		add_action('init', array($this, '_register_post_type'), 10);
	}

	public function _register_taxonomies(){

		// Creating Category tax
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Categories' , PINLOADER_TEXT_DOMAIN),
			'all_items'         => __( 'All Categories' , PINLOADER_TEXT_DOMAIN),
			'parent_item'       => __( 'Parent Category' , PINLOADER_TEXT_DOMAIN),
			'parent_item_colon' => __( 'Parent Category:' , PINLOADER_TEXT_DOMAIN),
			'edit_item'         => __( 'Edit Category' , PINLOADER_TEXT_DOMAIN),
			'update_item'       => __( 'Update Category' , PINLOADER_TEXT_DOMAIN),
			'add_new_item'      => __( 'Add New Category' , PINLOADER_TEXT_DOMAIN),
			'new_item_name'     => __( 'New Category Name' , PINLOADER_TEXT_DOMAIN),
			'menu_name'         => __( 'Categories' , PINLOADER_TEXT_DOMAIN),
		);
		$categories_args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array('slug' => $this->tax_slug),
		);
		// Register taxonomy
		register_taxonomy($this->tax_slug, array($this->cpt_slug), $categories_args);

		// Creating Category tax
		$labels = array(
			'name'              => _x( 'Geotargets', 'taxonomy general name' ),
			'singular_name'     => _x( 'Geotarget', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Geotargets' , PINLOADER_TEXT_DOMAIN),
			'all_items'         => __( 'All Geotargets' , PINLOADER_TEXT_DOMAIN),
			'parent_item'       => __( 'Parent Geotarget' , PINLOADER_TEXT_DOMAIN),
			'parent_item_colon' => __( 'Parent Geotarget:' , PINLOADER_TEXT_DOMAIN),
			'edit_item'         => __( 'Edit Geotarget' , PINLOADER_TEXT_DOMAIN),
			'update_item'       => __( 'Update Geotarget' , PINLOADER_TEXT_DOMAIN),
			'add_new_item'      => __( 'Add New Geotarget' , PINLOADER_TEXT_DOMAIN),
			'new_item_name'     => __( 'New Geotarget Name' , PINLOADER_TEXT_DOMAIN),
			'menu_name'         => __( 'Geotargets' , PINLOADER_TEXT_DOMAIN),
		);
		$categories_args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array('slug' => $this->geo_tax_slug),
		);
		// Register taxonomy
		register_taxonomy($this->geo_tax_slug, array($this->cpt_slug), $categories_args);

	}

	public function _register_post_type(){

		$s = strtolower($this->plugin_name);
		$labels = array(
			'name' 				 => _x( $this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'singular_name' 	 => _x( $this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'add_new' 			 => _x( 'Add New', $s, PINLOADER_TEXT_DOMAIN ),
			'add_new_item' 		 => _x( 'Add New '.$this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'edit_item' 		 => _x( 'Edit '.$this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'new_item' 			 => _x( 'New '.$this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'view_item' 		 => _x( 'View '.$this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'search_items' 		 => _x( 'Search '.$this->plugin_name.'es', $s, PINLOADER_TEXT_DOMAIN ),
			'not_found' 		 => _x( 'No '.$s.'s found', $s, PINLOADER_TEXT_DOMAIN ),
			'not_found_in_trash' => _x( 'No '.$s.'s found in Trash', $s, PINLOADER_TEXT_DOMAIN ),
			'menu_name' 		 => _x( $this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
		);

		$args = array(
			'label'               => _x( $this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'description'         => _x( $this->plugin_name, $s, PINLOADER_TEXT_DOMAIN ),
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'      => false,
			'query_var'          => true,
			'capability_type'    => 'post',
			'exclude_from_search' => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-welcome-widgets-menus',
			'map_meta_cap'		  => true,
			'can_export' 		  => true,
			'supports'           => array('title', 'excerpt'),
			'rewrite'            => array('slug' => $this->cpt_slug, 'with_front' => false, 'pages' => false, 'feeds'=>false),
		);

		register_post_type($this->cpt_slug, $args);

	}
	



}
