<?php
namespace Pinloader;

class _WCPL_Admin {

	private $submenus = array();
	private $metaboxes = array();
	private $taxonomy;
	private $cache_file;

	public static function initialise(){
		$self = new self();

		if(is_admin()){
			$submenus_file = PINLOADER_PLUGIN_DIR.'/inc/backend-submenus.php';
			$metaboxes_file = PINLOADER_PLUGIN_DIR.'/inc/backend-metaboxes.php';

			if(file_exists($submenus_file)){
				self::$submenus = include($submenus_file);
			}

			if(file_exists($metaboxes_file)){
				self::$metaboxes = include($metaboxes_file);
			}

			add_action('init', array($self, 'init'), 10);
			add_action('admin_menu', array(&$self, 'admin_menu'), 10);
			add_action('admin_enqueue_scripts', array($self, 'admin_enqueue_scripts'));
			add_action('cmb2_admin_init', array($self, 'create_metaboxes'));

			add_filter('manage_edit-listing_columns', array($self, 'columns'));
			add_action('manage_listing_posts_custom_column', array($self, 'custom_columns'), 2);
			//add_filter('manage_edit-listing_sortable_columns', array($this, 'sortable_columns'));
			//add_filter('request', array($this, 'sort_columns'));
		}
	}

	public function init(){
		$this->cache_file = WcPinLoader::instance()->cache_file;
		$this->taxonomy = WcPinLoader::instance()->taxonomy;

		//$this->create_metaboxes();
	}

	public function admin_enqueue_scripts(){
		wp_enqueue_style('pinloader-style', PINLOADER_PLUGIN_URL.'/assets/css/backend.css');
		wp_enqueue_style('pinloader-settings', plugins_url('assets/css/settings.css', PINLOADER_PLUGIN__FILE__));

		wp_enqueue_script('pinloader-script', PINLOADER_PLUGIN_URL.'/assets/js/backend.js');
		wp_enqueue_script('pinloader-settings', plugins_url('assets/js/settings.js', PINLOADER_PLUGIN__FILE__), array('jquery'));
	}

	public function admin_menu(){
		add_menu_page(
			WcPinLoader::instance()->plugin_name,
			WcPinLoader::instance()->plugin_name,
			'manage_options',
			WcPinLoader::instance()->plugin_slug,
			array(WcPinLoader_Settings::class, 'display_settings_page'),
			'dashicons-palmtree',
			20
		);

		if($this->submenus !== null){
			foreach($this->submenus as $post_type => $items){
				foreach($items as $k => $options){
					add_submenu_page(
						'edit.php?post_type='.$post_type,
						$options['page_title'],
						$options['menu_title'],
						'manage_options',
						str_replace('%POST_TYPE%', $post_type, $options['page_link_name']),
						array(&$this, $options['function_name'])
					);
				}
			}
		}
	}

	public function create_metaboxes(){
		//WcPinLoader::instance()->_debug($this->metaboxes);
		foreach($this->metaboxes as $metabox){
			if($metabox){
				$cmb = new_cmb2_box($metabox);
				//WcPinLoader::instance()->_debug($cmb); exit;
				foreach($metabox['fields'] as $field){
					if('group' == $field['type']){
						$group_field_id = $cmb->add_field($field);

						foreach($field['group_fields'] as $group_field){
							$cmb->add_group_field($group_field_id, $group_field);
						}
					}else{
						//WcPinLoader::instance()->_debug($field);
						$cmb->add_field($field);
					}
				}
			}
		}

	}

	public function columns($columns){
		//WcPinLoader::instance()->_debug($columns);
		// Make sure we deal with array
		$new_columns = array();

		// Unset some default columns
		//unset($columns['title'], $columns['date'], $columns['author']);

		foreach($columns as $k => $c){
			$new_columns[$k] = $c;
			if($k == 'cb'){
				$new_columns["listing_offer"]	= __('Offer', PINLOADER_TEXT_DOMAIN);
				$new_columns["listing_image"]	= __('Image', PINLOADER_TEXT_DOMAIN);
			}
		}

		// Define our custom columns
		//$new_columns["listing_offer"]	= __('Offer', PINLOADER_TEXT_DOMAIN);
		//$new_columns += $columns;
		//$columns["listing_id"]		= __('ID', PINLOADER_TEXT_DOMAIN);
		//$columns["listing_image"]	= __('Image', PINLOADER_TEXT_DOMAIN);
		//$columns["listing_title"]	= __('Title');
		//$columns["listing_price"]	= __('Price', PINLOADER_TEXT_DOMAIN);
		//$columns["listing_posted"]	= __('Author');

		/*if(class_exists('WPSight_Expire_Listings'))
			$columns["listing_expires"]	= __('Expires', PINLOADER_TEXT_DOMAIN);*/

		//$columns['listing_status']	= __('Status', PINLOADER_TEXT_DOMAIN);;
		//$columns['listing_actions']	= __('Actions', PINLOADER_TEXT_DOMAIN);

		return $new_columns;
	}

	public function custom_columns($column){
		global $post;

		$datef = get_option('date_format');

		switch($column){
			case "listing_offer" :
				$listing_offer = get_post_meta( $post->ID, '_offer', true );
				// Display colored offer badge
				if($listing_offer)
					echo '<span style="background-color:'.esc_attr($this->get_offer_color($listing_offer)).'" class="'.sanitize_html_class($listing_offer). '">'.esc_attr($this->get_offer_label($listing_offer)).'</span>';
				break;
			case "listing_price" :
				// Display listing price
				//wpsight_listing_price($post->ID);
				break;
			case "listing_image":
				// Display listing thumbnail (with edit link if not in trash)
				if($post->post_status !== 'trash' && current_user_can('edit_listing', $post->ID)){
					echo '<a href="'.get_edit_post_link($post->ID). '">'.$this->get_listing_thumbnail($post->ID, array(48,48)). '</a>';
				}else{
					echo $this->get_listing_thumbnail($post->ID, array(48,48));
				}
				break;
			case "listing_title" :
				echo '<div class="listing-info">';
				echo '<div class="listing-title">';
				// Display listing title (with edit link if not in trash)
				if($post->post_status !== 'trash' && current_user_can('edit_listing', $post->ID)){
					echo '<a href="'.admin_url('post.php?post='.$post->ID.'&action=edit').'" class="tips" data-tip="'.__('Edit', PINLOADER_TEXT_DOMAIN). '">'.$post->post_title.'</a>';
				}else{
					echo $post->post_title;
				}

				if($post->post_status !== 'trash' && class_exists('WPSight_Featured_Listings')){
					// Display sticky
					if(wpsight_is_listing_sticky())
						echo ' <span class="listing-sticky">&dash; '.__('Sticky', PINLOADER_TEXT_DOMAIN). '</span>';
					// Display featured
					if(wpsight_is_listing_featured())
						echo ' <span class="listing-featured">&dash; '.__('Featured', PINLOADER_TEXT_DOMAIN). '</span>';
				}
				echo '</div>';
				echo '<div class="listing-taxonomies">';
				$type 	  = wpsight_get_listing_terms('listing-category');
				$location = wpsight_get_listing_terms('location');

				// Display listing type terms
				if($type){
					echo '<div class="listing-type">';
					echo wpsight_get_listing_terms('listing-category', $post->ID, ', ', '', '', false);
					echo '</div>';
				}

				if($type && $location)
					echo '&dash; ';

				// Display listing location terms
				if($location){
					echo '<div class="location">';
					echo wpsight_get_listing_terms('location', $post->ID, ' &rsaquo; ', '', '', false);
					echo '</div>';
				}
				echo '</div>';

				// Display text if item not available
				if(wpsight_is_listing_not_available())
					echo '<span class="listing-not-available">'.__('Item is currently not available', PINLOADER_TEXT_DOMAIN). '</span>';

				// Display listing title actions (edit, view)
				echo '<div class="row-actions">';
				$admin_actions_listing_title = array();
				if(current_user_can('edit_listing', $post->ID)){
					if($post->post_status !== 'trash'){
						$admin_actions_listing_title['edit']   = array(
							'action'  => 'edit',
							'name'    => __('Edit'),
							'url'     => get_edit_post_link($post->ID)
						);
						$admin_actions_listing_title['view']   = array(
							'action'  => 'view',
							'name'    => __('View'),
							'url'     => get_permalink($post->ID),
							'target'  => '_blank'
						);
						$admin_actions_listing_title['delete'] = array(
							'action'   => 'delete',
							'name'     => __('Delete'),
							'url'      => get_delete_post_link($post->ID),
							'cap'	   => 'delete_listing',
						);
					}
				}
				$admin_actions_listing_title = apply_filters('wpsight_admin_actions_listing_title', $admin_actions_listing_title, $post);
				foreach($admin_actions_listing_title as $action){
					echo '<span class="'.$action['action'].'">';
					printf('<a href="%2$s" data-tip="%3$s" target="%5$s">%3$s</a> ', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']), isset($action['target']) ? esc_html($action['target']): false);
					echo '</span>';
				}
				echo '</div>';

				// Display more info when excerpt list mode
				if(isset($_REQUEST['mode'])&& $_REQUEST['mode'] == 'excerpt'){
					echo '<div class="listing-summary">';
					echo '<p>'.wpsight_get_listing_summary().'</p>';
					echo '</div>';

					echo '<div class="listing-excerpt">';
					echo '<p>'.wp_trim_excerpt().'</p>';
					echo '</div>';
				}
				echo '</div>';
				break;
			case "listing_actions" :
				// Define some general classes to be used with action buttons
				$classes = array();
				if(! wpsight_is_listing_pending($post->ID))
					$classes[] = 'listing-approved';
				if(wpsight_is_listing_expired($post->ID))
					$classes[] = 'listing-expired';
				if(wpsight_is_listing_not_available($post->ID))
					$classes[] = 'listing-not-available';
				if(wpsight_is_listing_sticky($post->ID))
					$classes[] = 'listing-sticky';
				if(wpsight_is_listing_featured($post->ID))
					$classes[] = 'listing-featured';
				// Display action buttons
				echo '<div class="actions '.join(' ', $classes). '">';
				$admin_actions = array();
				if($post->post_status !== 'trash'){
					$admin_actions['approve']   = array(
						'action'   => 'approve',
						'name'     => wpsight_is_listing_pending($post->ID)? __('Approve', PINLOADER_TEXT_DOMAIN): __('Unapprove', PINLOADER_TEXT_DOMAIN),
						'url'      =>  wp_nonce_url(add_query_arg('approve_listing', $post->ID), 'approve_listing'),
						'cap'	   => 'publish_listings',
						'priority' => 10
					);
					$admin_actions['unavailable']   = array(
						'action'   => 'unavailable',
						'name'     => wpsight_is_listing_not_available($post->ID)? __('Mark available', PINLOADER_TEXT_DOMAIN): __('Mark unavailable', PINLOADER_TEXT_DOMAIN),
						'url'      =>  wp_nonce_url(add_query_arg('toggle_unavailable', $post->ID), 'toggle_unavailable'),
						'cap'	   => 'publish_listings',
						'priority' => 20
					);
					$admin_actions['delete'] = array(
						'action'   => 'delete',
						'name'     => __('Trash', PINLOADER_TEXT_DOMAIN),
						'url'      => get_delete_post_link($post->ID),
						'cap'	   => 'delete_listing',
						'priority' => 30
					);
				}else{
					$admin_actions['untrash'] = array(
						'action'   => 'untrash',
						'name'     => __('Restore', PINLOADER_TEXT_DOMAIN),
						'url'      => wp_nonce_url(admin_url('post.php?post='.$post->ID.'&action=untrash'), 'untrash-post_'.$post->ID),
						'cap'	   => 'delete_listing',
						'priority' => 10
					);
				}
				$admin_actions = apply_filters('wpsight_admin_actions', $admin_actions, $post);
				// Sort array by priority
				$admin_actions = wpsight_sort_array_by_priority($admin_actions);
				$i = 0;
				foreach($admin_actions as $action){
					$action['cap'] = isset($action['cap'])? $action['cap'] : 'read_listing';
					if(current_user_can($action['cap'], $post->ID)){
						printf('<a class="button tips" href="%2$s" data-tip="%3$s" target="%5$s"><i class="icon icon-%1$s"></i> %4$s</a>', $action['action'], esc_url($action['url']), esc_attr($action['name']), esc_html($action['name']), isset($action['target'])? esc_html($action['target']): false);
						$i++;
					}
				}
				// If no other action is displayed, show view button
				if(0 == $i && $post->post_status == 'publish')
					printf('<a class="button tips" href="%2$s" data-tip="%3$s" target="%5$s"><i class="icon icon-view"></i> %4$s</a>', 'view', esc_url(get_permalink($post->ID)), esc_attr(__('View', PINLOADER_TEXT_DOMAIN)), esc_html(__('View', PINLOADER_TEXT_DOMAIN)), '_blank');
				echo '</div>';
				break;
		} #end while

	}

	public function sortable_columns($columns){

		$custom = array(
			'listing_id'   		=> 'listing_id',
			'listing_title'   	=> 'title',
			'listing_price' 	=> 'listing_price',
			'listing_posted'  	=> 'date'
		);

		return wp_parse_args($custom, $columns);

	}

	public function sort_columns($vars){

		if(isset($vars['orderby'])){

			if('listing_id' === $vars['orderby']){
				$vars = array_merge($vars, array(
					'meta_key' 	=> '_listing_id',
					'orderby' 	=> 'meta_value'
				));
			}

			if('listing_price' === $vars['orderby']){
				$vars = array_merge($vars, array(
					'meta_key' 	=> '_price',
					'orderby' 	=> 'meta_value_num'
				));
			}

		}

		return $vars;

	}


}

