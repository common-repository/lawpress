<?php

/**
 * Single Attorney Area
 *
 * @link       https://businessupwebsite.com
 * @since      1.0.0
 * @since      1.4.0 - Improved structure
 *
 * @package    Lawpress
 * @subpackage Lawpress/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $lawpress_options;
$id = get_the_ID();
$placeholder = '-';

$lp_attorney_niche = 'Attorney';
$lp_attorney_phone = $placeholder;
$lp_attorney_email = $placeholder;
$has_one_social_link = false;

$lp_attorney_practice_areas = $placeholder;
if ( class_exists('ACF') ){
	if ( get_field('lp_attorney_niche') ){
		$lp_attorney_niche = get_field('lp_attorney_niche');
	}
	if ( get_field('lp_attorney_phone') ){
		$lp_attorney_phone = get_field('lp_attorney_phone');
	}
	if ( get_field('lp_attorney_email') ){
		$lp_attorney_email = get_field('lp_attorney_email');
	}
}



//classes
$classes = '';
if ( get_the_post_thumbnail() != '' ){
	$classes = ' has-thumbnail';
}

/* Info Section */
$card_content = '<div class="lp-attorney-card'.$classes.'">';
if ( get_the_post_thumbnail() ) {
	$card_content .= '<div class="lp-attorney-photo">'.get_the_post_thumbnail().'</div>';
}
$card_content .= '<div class="lp-attorney-content">';

// Profession
$card_content .= '<dt>'.__( 'Profession', 'lawpress' ).'</dt>';
$card_content .= '<dd>'.esc_html__($lp_attorney_niche).'</dd>';

// phone 
$card_content .= '<dt>'.__( 'Phone', 'lawpress' ).'</dt>';
if ( $lp_attorney_phone != $placeholder ) : 
	$card_content .= '<dd><a href="tel:'.esc_attr($lp_attorney_phone).'">'.esc_html($lp_attorney_phone).'</a></dd>';
else : 	
	$card_content .= '<dd>'.$placeholder.'</dd>';
endif; 

// email
$card_content .= '<dt>'.__( 'Email', 'lawpress' ).'</dt>';
if ( $lp_attorney_email != $placeholder ) : 
	$card_content .= '<dd><a href="mailto:'.esc_attr($lp_attorney_email).'">'. esc_html($lp_attorney_email).'</a></dd>';
else : 	
	$card_content .= '<dd>'.$placeholder.'</dd>';
endif;

// social
$card_content .= '<dt>'.__( 'Social Links', 'lawpress' ).'</dt>';
if ( $lp_attorney_email != $placeholder ) : 
	$card_content .= '<dd>';
	if ( get_field('lp_attorney_facebook') ){
		$lp_attorney_facebook = get_field('lp_attorney_facebook');
		$card_content .= '<a href="'.esc_url( $lp_attorney_facebook ).'" class="lp-social-link" target="_blank"><i class="fab fa-facebook"></i></a>';
		$has_one_social_link = true;
	}
	if ( get_field('lp_attorney_twitter') ){
		$lp_attorney_twitter = get_field('lp_attorney_twitter');
		$card_content .= '<a href="'.esc_url( $lp_attorney_twitter ).'" class="lp-social-link" target="_blank"><i class="fab fa-twitter"></i></a>';
		$has_one_social_link = true;
	}
	if ( get_field('lp_attorney_linkedin') ){
		$lp_attorney_linkedin = get_field('lp_attorney_linkedin');
		$card_content .= '<a href="'.esc_url( $lp_attorney_linkedin ).'" class="lp-social-link" target="_blank"><i class="fab fa-linkedin"></i></a>';
		$has_one_social_link = true;
	}
	if ( ! $has_one_social_link ){
		$card_content .= $placeholder;
	}
	$card_content .= '</dd>';
else : 	
	$card_content .= '<dd>'.$placeholder.'</dd>';
endif;

// Areas of Practice
$card_content .= '<dt>'.__( 'Areas of Practice', 'lawpress' ).'</dt>';
$card_content .= '<dd>';
$card_content .= $this->lp_get_link_list( 'lp_attorney_practice_areas', $id);
$card_content .= '</dd>';

$card_content .= '</div>'; // .lp-attorney-card
$card_content .= '</div>'; // .lp-attorney-content

// Retaled Cases
$case_list = '';
$lp_icon_args = array(
	'show_info_icon' 		=> true,
	'info_icon_type' 		=> 'case',    
	'icon_loop' 				=> false,
	'info_icon_field' 	=> 'case_settlement',
	'icon_position' 		=> 'left',
	'icon' 							=> 'fa-dollar-sign'
);
$title = __( 'Related Cases', 'lawpress' );

$theme = wp_get_theme(); 
if (('LawPress Solid' == $theme->parent_theme) || ('LawPress Solid' == $theme->name) ){
	$lawpress = new Lawpress();
	$plugin_public_solid = new Lawpress_Public_Solid( $lawpress->get_plugin_name(), $lawpress->get_version() );
	$case_list .= $plugin_public_solid->lp_get_related_grid( $title, 'case', true, 'lp_case_attorneys', $id, false, $subtitle = '', $lp_icon_args );
}
else {
	$case_list .= $this->lp_get_related_grid( $title, 'case', true, 'lp_case_attorneys', $id, false, $subtitle = '', $lp_icon_args );
}

wp_reset_postdata();

// card total
echo $card_content;

// default content
echo $content;

// cases
echo $case_list;