<?php

/**
 * Single Case
 *
 * @link       https://businessupwebsite.com
 * @since      1.2.0
 * @since      1.4.0 - Improved structure
 * @package    Lawpress
 * @subpackage Lawpress/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$id = get_the_ID();
$placeholder = '-';

$lp_case_settlement = $placeholder;
$lp_case_areas = $placeholder;
$lp_case_hide_thumbnail = array();
$lp_case_hide_thumbnail[0] = 'no';
if ( class_exists('ACF') ){
	if ( get_field('lp_case_settlement') ){
		$lp_case_settlement = get_field('lp_case_settlement');
	}
	if ( get_field_object('lp_case_areas' ) ){
		$lp_case_areas = get_field_object('lp_case_areas' );
	}
	if ( get_field('lp_case_hide_thumbnail') ){
		$lp_case_hide_thumbnail = get_field('lp_case_hide_thumbnail');
	}
}

//classes
$classes = '';
if ( get_the_post_thumbnail() != '' && $lp_case_hide_thumbnail[0] != 'yes' ){
	$classes = ' has-thumbnail';
}

/* Info Section */
$card_content = '<div class="lp-attorney-card'.$classes.'">';
// thumbnail
if ( get_the_post_thumbnail() && $lp_case_hide_thumbnail[0] != 'yes' ) {
	$card_content .= '<div class="lp-attorney-photo">'.get_the_post_thumbnail().'</div>';
}
$card_content .= '<div class="lp-attorney-content">';

// Cases Closed
$card_content .= '<dt>'.__( 'Settlement', 'lawpress' ).'</dt>';
$card_content .= '<dd>$<span class="settlement-number">'.esc_html($lp_case_settlement).'</span></dd>';

// Areas of Practice
$card_content .= '<dt>'.__( 'Practice Area', 'lawpress' ).'</dt>';
$card_content .= '<dd>';
$card_content .= $this->lp_get_link_list( 'lp_case_practice_areas', $id );
$card_content .= '</dd>';

$card_content .= '</div>'; // .lp-attorney-card
$card_content .= '</div>'; // .lp-attorney-content

/* Related attorneys */
$attorney_list = '';
$lp_icon_args = array(
	'show_info_icon' 	=> true,
	'info_icon_type' 	=> 'case',    
	'icon_loop' 			=> true,
);
$title = __( 'Related Attorneys', 'lawpress' );


$theme = wp_get_theme(); 
if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
	$lawpress = new Lawpress();
	$plugin_public_solid = new Lawpress_Public_Solid( $lawpress->get_plugin_name(), $lawpress->get_version() );
	$attorney_list .= $plugin_public_solid->lp_get_related_grid_attorney( $title, 'attorney', false, 'lp_case_attorneys', $id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
}
else {
	$attorney_list .= $this->lp_get_related_grid( $title, 'attorney', false, 'lp_case_attorneys', $id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
}

// card total
echo $card_content;

// default content
echo $content;

// display attorney list
echo $attorney_list;
