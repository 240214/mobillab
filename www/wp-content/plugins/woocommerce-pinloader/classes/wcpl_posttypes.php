<?php
namespace Pinloader;

class WCPL_PostTypes {

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
		self::update_products_cpt();
		//self::create_events_cpt();
		//self::create_faqs_cpt();
		//self::create_jobs_cpt();
		//self::create_resources_cpt();
		//self::create_teams_cpt();
		//self::create_courses_cpt();
		//self::create_testimonials_cpt();
	}

	public function update_products_cpt(){
		$cpt = 'product';
		//self::$post_types_taxonomies[] = $cpt;
		self::$post_types_taxonomies[] = array(
			$cpt => array(
				'suppliers' => 'Supplier'
			)
		);
	}


}
