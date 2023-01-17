<?php
namespace Pinloader;

class WCPL_Taxonomies {

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
			'name'              => _x( $plural_name, 'taxonomy general name', PINLOADER_TEXT_DOMAIN ),
			'singular_name'     => _x( $single_name, 'taxonomy singular name', PINLOADER_TEXT_DOMAIN ),
			'search_items'      => __( 'Search '.$plural_name, PINLOADER_TEXT_DOMAIN ),
			'all_items'         => __( 'All '.$plural_name, PINLOADER_TEXT_DOMAIN ),
			'parent_item'       => __( 'Parent '.$single_name, PINLOADER_TEXT_DOMAIN ),
			'parent_item_colon' => __( 'Parent '.$single_name.':', PINLOADER_TEXT_DOMAIN ),
			'edit_item'         => __( 'Edit '.$single_name, PINLOADER_TEXT_DOMAIN ),
			'update_item'       => __( 'Update '.$single_name, PINLOADER_TEXT_DOMAIN ),
			'add_new_item'      => __( 'Add New '.$single_name, PINLOADER_TEXT_DOMAIN ),
			'new_item_name'     => __( 'New '.$single_name.' Name', PINLOADER_TEXT_DOMAIN ),
			'menu_name'         => __( $plural_name, PINLOADER_TEXT_DOMAIN),
		);
	}

	/**
	 * [projectCategories description]
	 * @return [type] [description]
	 */
	public function createTaxonomies(){
		$labels = $this->get_labels();

		$post_types_taxonomies = WCPL_PostTypes::$post_types_taxonomies;
		if(!empty($post_types_taxonomies)){
			foreach($post_types_taxonomies as $post_type){
				$post_types = $post_type;
				if(is_array($post_types)){
					foreach($post_types as $cpt_slug => $tax_data){
						foreach($tax_data as $tax_slug => $tax_label){
							$labels = $this->get_labels($tax_label, $tax_label.'s');
							$this->register_taxonomy($tax_slug, $cpt_slug, $labels);
						}
					}
				}else{
					$this->register_taxonomy($post_type.'-cat', $post_types, $labels);
				}
			}
		}

	}

	public function register_taxonomy($tax_slug, $post_types, $labels){
		register_taxonomy($tax_slug, $post_types, array(
			'labels'                => $labels,
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'show_in_nav_menus'     => false, // равен аргументу public
			'show_tagcloud'         => false, // равен аргументу show_ui
			'query_var'             => $tax_slug,
			'show_in_rest'          => true,
			'rest_base'             => $tax_slug,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array('slug' => $tax_slug.'s', 'hierarchical' => false, 'with_front' => false, 'feed' => false),
		));
	}

}
