<?php

namespace Pinloader;

class WCPL_Tools {

	public static function initialise(){
		$self = new self();
	}

	public static function inner_section_description(){
		return '';
	}

	public static function raw_html( $args ){
		if(empty($args['html'])){
			return;
		}

		echo $args['html'];
		if(!empty($args['desc'])) : ?>
			<p class="description"><?=$args['desc'];?></p>
		<?php endif;
	}

	public static function link_field($args){
		extract($args, EXTR_SKIP);

		$args = wp_parse_args($args, array(
			'classes' => array('button button-secondary'),
			'target' => '_self',
		));

		if(empty($args['id']) || empty($args['page']))
			return;

		?>
		<a id="<?=esc_attr($args['id']);?>" href="<?=esc_attr($link);?>" target="<?=esc_attr($target);?>" class="<?=implode(' ', $args['classes']);?>"><?=esc_attr($value);?></a>
		<?php if ( ! empty( $desc ) ) : ?>
			<p class="description"><?=$desc;?></p>
		<?php endif;
	}

	public static function button_field($args){
		self::_set_name_and_value($args);
		extract($args, EXTR_SKIP);

		$args = wp_parse_args($args, array(
			'classes' => array('button button-secondary'),
		));

		if(empty($args['id']) || empty($args['page']))
			return;

		?>
		<input type="button" id="<?=esc_attr( $args['id'] ); ?>" name="<?=esc_attr( $name ); ?>" value="<?=esc_attr( $value ); ?>" class="<?=implode( ' ', $args['classes'] ); ?>" />
		<?php if ( ! empty( $desc ) ) : ?>
			<p class="description"><?=$desc;?></p>
		<?php endif;
	}

	public static function text_field($args){
		self::_set_name_and_value($args);
		extract($args, EXTR_SKIP);

		$args = wp_parse_args($args, array(
			'classes' => array(),
		));

		if(empty($args['id']) || empty($args['page']))
			return;

		?>
		<input type="text" id="<?=esc_attr( $args['id'] ); ?>" name="<?=esc_attr( $name ); ?>" value="<?=esc_attr( $value ); ?>" class="<?=implode( ' ', $args['classes'] ); ?>" />
		<?php if ( ! empty( $desc ) ) : ?>
			<p class="description"><?=$desc;?></p>
		<?php endif;
	}

	public static function check_field($args){
		self::_set_name_and_value($args);
		extract($args, EXTR_SKIP);

		$args = wp_parse_args($args, array(
			'classes' => array(),
		));

		if(empty($args['id']) || empty($args['page']))
			return;

		if($value){
			$args['sub_desc'] = date('d.m.Y H:i', $value);
		}
		?>
		<input type="checkbox" id="<?=esc_attr( $args['id'] ); ?>" name="<?=esc_attr($name);?>" value="<?=esc_attr($value);?>" class="<?=implode( ' ', $args['classes'] ); ?>" />
		<?php if(!empty($args['sub_desc'])) echo $args['sub_desc']; ?>
		<?php if(!empty($desc)):?>
			<p class="description"><?=$desc;?></p>
		<?php endif;
	}

	public static function textarea_field( $args ) {
		self::_set_name_and_value( $args );
		extract( $args, EXTR_SKIP );

		$args = wp_parse_args( $args, array(
			'classes' => array(),
			'rows'    => 5,
			'cols'    => 50,
		) );

		if ( empty( $args['id'] ) || empty( $args['page'] ) )
			return;

		?>
		<textarea id="<?=esc_attr( $args['id'] ); ?>" name="<?=esc_attr( $name ); ?>" class="<?=implode( ' ', $args['classes'] ); ?>" rows="<?=absint( $args['rows'] ); ?>" cols="<?=absint( $args['cols'] ); ?>"><?=esc_textarea( $value ); ?></textarea>

		<?php if ( ! empty( $desc ) ) : ?>
			<p class="description"><?=$desc; ?></p>
		<?php endif;
	}

	public static function number_field( $args ) {
		self::_set_name_and_value( $args );
		extract( $args, EXTR_SKIP );

		$args = wp_parse_args( $args, array(
			'classes' => array(),
			'min' => '1',
			'step' => '1',
			'desc' => '',
		) );
		if ( empty( $args['id'] ) || empty( $args['page'] ) )
			return;

		?>
		<input type="number" id="<?=esc_attr( $args['id'] ); ?>" name="<?=esc_attr( $name ); ?>" value="<?=esc_attr( $value ); ?>" class="<?=implode( ' ', $args['classes'] ); ?>" min="<?=$args['min']; ?>" step="<?=$args['step']; ?>" />
		<?php if ( ! empty( $args['sub_desc'] ) ) echo $args['sub_desc']; ?>
		<?php if ( ! empty( $args['desc'] ) ) : ?>
			<p class="description"><?=$args['desc']; ?></p>
		<?php endif;
	}

	public static function select_field( $args ) {
		self::_set_name_and_value($args);
		extract( $args, EXTR_SKIP );

		if(empty($options) || empty($id) || empty($name)){
			return;
		}

		if(!isset($size)){
			$size = 1;
		}

		?>
		<select id="<?=esc_attr( $id ); ?>" name="<?=esc_attr($name); ?>" class="<?=esc_attr($class)?>" size="<?=esc_attr($size)?>">
			<?php foreach ( $options as $name => $label ) : ?>
				<option value="<?=esc_attr( $name ); ?>" <?php selected( $name, (string) $value ); ?>>
					<?=esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if ( ! empty( $desc ) ) : ?>
			<p class="description"><?=$desc; ?></p>
		<?php endif; ?>
		<?php
	}

	public static function yesno_field( $args ) {
		self::_set_name_and_value( $args );
		extract( $args, EXTR_SKIP );

		?>
		<label class="tix-yes-no description"><input type="radio" name="<?=esc_attr( $name ); ?>" value="1" <?php checked( $value, true ); ?>> <?php _e( 'Yes', PINLOADER_TEXT_DOMAIN ); ?></label>
		<label class="tix-yes-no description"><input type="radio" name="<?=esc_attr( $name ); ?>" value="0" <?php checked( $value, false ); ?>> <?php _e( 'No', PINLOADER_TEXT_DOMAIN ); ?></label>

		<?php if ( isset( $args['description'] ) ) : ?>
			<p class="description"><?=$args['description']; ?></p>
		<?php endif; ?>
		<?php
	}

	public static function yesno2_field( $args ) {
		self::_set_name_and_value( $args );
		extract( $args, EXTR_SKIP );

		?>
		<label class="tix-yes-no description"><input type="radio" name="<?=esc_attr( $name ); ?>" value="1" <?php checked( $value, true ); ?>> <?php _e( 'Forced', PINLOADER_TEXT_DOMAIN ); ?></label>
		<label class="tix-yes-no description"><input type="radio" name="<?=esc_attr( $name ); ?>" value="0" <?php checked( $value, false ); ?>> <?php _e( 'Automatically', PINLOADER_TEXT_DOMAIN ); ?></label>

		<?php if ( isset( $args['description'] ) ) : ?>
			<p class="description"><?=$args['description']; ?></p>
		<?php endif; ?>
		<?php
	}

	private static function _set_name_and_value(&$args){
		if(!isset($args['name'])){
			$args['name'] = sprintf('%s[%s]', esc_attr($args['page']), esc_attr($args['id']));
		}

		if(!isset($args['value'])){
			$args['value'] = WCPL_Admin::get_option($args['id']);
		}
	}

}

