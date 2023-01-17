<?php
class media_uploader_cb{

	function __construct(){
		// attach our function to the correct hook
		add_filter('attachment_fields_to_edit',  array( $this, 'attachment_fields_to_edit' ), 10, 2);
		//save attachment field
		add_filter( 'attachment_fields_to_save', array( $this, 'attachment_fields_to_save' ), 10, 2);
		//add custom css class
		add_filter( 'media_send_to_editor',      array( $this, 'media_send_to_editor' ), 10, 2 );
	}

	/**
	 * Adding our custom checkbox field to the $form_fields array
	 *
	 * @param array $form_fields
	 * @param object $post
	 * @return array
	 */
	function attachment_fields_to_edit($form_fields, $post) {
		$form_fields['add_class']['label'] = __("Add SEO Data");
		$form_fields['add_class']['input'] = 'html';
		$form_fields['add_class']['html'] = '<input type="checkbox" value="1" name="attachments['.$post->ID.'][add_class]" id="attachments['.$post->ID.'][add_class]" '.checked( 1, get_post_meta($post->ID, 'add_class', true), false ).'/>';
		return $form_fields;
	}

	/**
	 * Saving our custom checkbox field
	 * @param array $post
	 * @param array $attachment
	 * @return array
	 */
	function attachment_fields_to_save($post, $attachment) {

		if( isset($attachment['add_class']) ){
			update_post_meta($post['ID'], 'add_class', $attachment['add_class']);
		}
		return $post;
	}

	/**
	 * Adding our custom css class based on checkbox field
	 *
	 * @param string  $html
	 * @param int $id
	 * @return string
	 */
	function media_send_to_editor( $html, $id ) {
		//only add class if the checkbox was checked
		if ( 1 == (int)get_post_meta($id, 'add_class', true) ){
			//change this to whatever you want
			$seo_data_to_add = 'custom="HTML Output Here""';

			// THIS SHOULD BE THE CHECKBOX
			get_post_meta($id, 'add_class', true);

			$attachment_title = get_the_title($id);
			$title = 'title="'.$attachment_title .' by '. get_option('ews_opt_branding') .'"';
			$html = str_replace('<img', '<img '.$title.' '.$seo_data_to_add.'', $html);
		}
		return $html;
	}

}

new media_uploader_cb();