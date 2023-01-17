<?php
namespace Digidez;

class Custom_Taxonomies {

	/*
	 * Static function must be called after require within functions.inc.php
	 * This will setup all action hooks
	 */
	public static function initialise(){
		$self = new self();

		// define all action hooks here and document if not self explanatory
		add_action('init', array($self, 'createTaxonomies'), 1);
	}


	public function get_labels($single_name = "Category", $plural_name = "Categories"){
		return array(
			'name'              => _x( $plural_name, 'taxonomy general name', THEME_TD ),
			'singular_name'     => _x( $single_name, 'taxonomy singular name', THEME_TD ),
			'search_items'      => __( 'Search '.$plural_name, THEME_TD ),
			'all_items'         => __( 'All '.$plural_name, THEME_TD ),
			'parent_item'       => __( 'Parent '.$single_name, THEME_TD ),
			'parent_item_colon' => __( 'Parent '.$single_name.':', THEME_TD ),
			'edit_item'         => __( 'Edit '.$single_name, THEME_TD ),
			'update_item'       => __( 'Update '.$single_name, THEME_TD ),
			'add_new_item'      => __( 'Add New '.$single_name, THEME_TD ),
			'new_item_name'     => __( 'New '.$single_name.' Name', THEME_TD ),
			'menu_name'         => __( $plural_name, THEME_TD),
		);
	}

	/**
	 * [projectCategories description]
	 * @return [type] [description]
	 */
	public function createTaxonomies(){
		$labels = $this->get_labels();

		$post_types_taxonomies = Custom_Post_Types::$post_types_taxonomies;
		if(!empty($post_types_taxonomies)){
			foreach($post_types_taxonomies as $post_type){
				$post_types = $post_type;
				if(is_array($post_types)){
					$post_type = array_keys($post_types)[0];
					$labels = $this->get_labels($post_types[$post_type], $post_types[$post_type].'s');
					$post_types = array_keys($post_types);
				}
				register_taxonomy($post_type.'-cat', $post_types, array(
					'labels'                => $labels,
					'hierarchical'          => true,
					'public'                => true,
					'show_ui'               => true,
					'show_admin_column'     => true,
					'show_in_nav_menus'     => false, // равен аргументу public
					'show_tagcloud'         => false, // равен аргументу show_ui
					'query_var'             => $post_type,
					'show_in_rest'          => true,
					'rest_base'             => $post_type,
					'update_count_callback' => '_update_post_term_count',
					'rewrite'               => array('slug' => $post_type.'s', 'hierarchical' => false, 'with_front' => false, 'feed' => false),
				));
			}
		}

		//...

	}

}
