<?php

/**
 * Single Practice Area
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 * @since      1.1.0 - Added attorney subtitle
 * @since      1.1.1 - Some changes in HTML structure
 * @since      1.4.0 - Improved structure
 * @package    Lawpress
 * @subpackage Lawpress/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$id = get_the_ID();
$placeholder = '-';

$lp_practice_area_cases_closed = $placeholder;
$lp_practice_area_successful_cases = $placeholder;
$lp_practice_area_experience = $placeholder;
$lp_practice_area_hide_thumbnail = array();
$lp_practice_area_hide_thumbnail[0] = 'no';
if ( class_exists('ACF') ){
	if ( get_field('lp_practice_area_cases_closed') ){
		$lp_practice_area_cases_closed = get_field('lp_practice_area_cases_closed');
	}
	if ( get_field('lp_practice_area_successful_cases') ){
		$lp_practice_area_successful_cases = get_field('lp_practice_area_successful_cases');
	}
	if ( get_field('lp_practice_area_experience') ){
		$lp_practice_area_experience = get_field('lp_practice_area_experience');
	}
	if ( get_field('lp_practice_area_hide_thumbnail') ){
		$lp_practice_area_hide_thumbnail = get_field('lp_practice_area_hide_thumbnail');
	}
}

//classes
$classes = '';
if ( get_the_post_thumbnail() != '' && $lp_practice_area_hide_thumbnail[0] != 'yes' ){
	$classes = ' has-thumbnail';
}

/* Info Section */
$card_content = '<div class="lp-attorney-card'.$classes.'">';
// thumbnail
if ( get_the_post_thumbnail() && $lp_practice_area_hide_thumbnail[0] != 'yes' ) {
	$card_content .= '<div class="lp-attorney-photo">'.get_the_post_thumbnail().'</div>';
}
$card_content .= '<div class="lp-attorney-content">';

// Cases Closed
$card_content .= '<dt>'.__( 'Cases Closed', 'lawpress' ).'</dt>';
$card_content .= '<dd>'.esc_html($lp_practice_area_cases_closed).'</dd>';

// Successful Cases
$card_content .= '<dt>'.__( 'Successful Cases', 'lawpress' ).'</dt>';
$card_content .= '<dd>'.esc_html($lp_practice_area_successful_cases).'%</dd>';

// Experience
$card_content .= '<dt>'.__( 'Years of Experience', 'lawpress' ).'</dt>';
$card_content .= '<dd>'.esc_html($lp_practice_area_experience).'</dd>';

// Areas of Practice
$card_content .= '<dt>'.__( 'Cases', 'lawpress' ).'</dt>';
$card_content .= '<dd>';
$card_content .= $this->lp_get_link_list_outside( $id, 'case','lp_case_practice_areas' );
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
	$attorney_list .= $plugin_public_solid->lp_get_related_grid_attorney( $title, 'attorney', true, 'lp_attorney_practice_areas', $id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
}
else {
	$attorney_list .= $this->lp_get_related_grid( $title, 'attorney', true, 'lp_attorney_practice_areas', $id, true, $subtitle = __('Attorney', 'lawpress'), $lp_icon_args );
}


// card total
echo $card_content;

// default content
echo $content;

// display attorney list
echo $attorney_list;
