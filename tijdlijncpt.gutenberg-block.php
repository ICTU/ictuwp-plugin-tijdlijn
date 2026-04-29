<?php
/**
 * Gutenberg block for tijdlijncpt
 * Description: Gutenberg block to embed a tijdlijncpt via ACF and shortcode.
 * Version: 1.3.2
 * Version description: Add possibility to add as Gutenberg block.
 * Author: Paul van Buuren
 * Text Domain: rhswp-timeline
 */

defined( 'ABSPATH' ) || exit;

// ----------------------------------------------
// 1. Register ACF Block
// ----------------------------------------------
add_action( 'acf/init', 'tijdlijncpt_register_acf_block' );
function tijdlijncpt_register_acf_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$dirulr = plugin_dir_url( __FILE__ ) . 'assets/css/block-editor-timeline.css?v=' . RHSWP_TIMELINE_VERSION;

	acf_register_block_type( [
		'name'            => 'tijdlijncpt',
		'title'           => __( 'RHS timeline block', "rhswp-timeline" ),
		'description'     => __( 'Embed a timeline object.', "rhswp-timeline" ),
		'category'        => 'media',
		'icon'            => 'calendar-alt',
		'keywords'        => [ 'tijdlijn', 'timeline', 'rhs' ],
		'render_callback' => 'tijdlijncpt_render_block',
		'enqueue_style'   => $dirulr,
		'supports'        => [
			'align'  => false,
			'anchor' => true,
		],
	] );
}

// ----------------------------------------------
// 2. Register ACF Field Group for the block
// ----------------------------------------------
add_action( 'acf/init', 'tijdlijncpt_register_acf_fields' );
function tijdlijncpt_register_acf_fields() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'                   => 'group_69e8ae4bb2050',
		'title'                 => 'tijdlijncpt Block Fields',
		'fields'                => array(
			array(
				'key'           => 'field_69e8af4c3480e',
				'label'         => __( 'Select a timeline', "rhswp-timeline" ),
				'name'          => 'tijdlijncpt_post',
				'type'          => 'post_object',
				'instructions'  => __( 'Only published timelines can be embedded.', "rhswp-timeline" ),
				'required'      => 1,
				'post_type'     => [ RHSWP_CPT_TIMELINE ],
				'post_status'   => [ 'publish' ],
				'return_format' => 'id',
				'ui'            => 1,
				'allow_null'    => 0,
				'multiple'      => 0,
			),
		),
		'location'              => array(
			array(
				array(
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/tijdlijncpt',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
		'show_in_rest'          => 0,
		'display_title'         => '',
		'allow_ai_access'       => false,
		'ai_description'        => '',
	) );

}

// ----------------------------------------------
// 3. Block render callback
// ----------------------------------------------
function tijdlijncpt_render_block( $block, $content = '', $is_preview = false ) {
	$post_id = (int) get_field( 'tijdlijncpt_post' );

	if ( ! $post_id ) {
		if ( $is_preview ) {
			echo '<p class="tijdlijncpt-block-preview">' .
			     esc_html__( 'Please select a tijdlijncpt.', "rhswp-timeline" ) .
			     '</p>';
		}

		return;
	}

	// In preview mode only show an image of the timeline
	if ( $is_preview ) {
		$shortcode = '[timeline id="' . $post_id . '" preview="true"]';
		echo '<div class="tijdlijncpt-block-preview">';
		echo do_shortcode( $shortcode );
		echo '</div>';

	} else {
		// Generate the shortcode and output it
		$shortcode = '[timeline id="' . $post_id . '"]';
		// Front-end: execute the shortcode
		echo do_shortcode( $shortcode );

	}

	return;

}


