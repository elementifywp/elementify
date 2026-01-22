<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Elementify
 */

namespace Elementify;

//use Elementify\Inc\Utils;
use Elementify\Inc\Utils;

if (! defined('ABSPATH')) {
    exit;
}
?>

<!doctype html>

<?php
/**
 * Functions hooked into elementify/before_html action
 *
 */
do_action('elementify/before_html');
?>

<html <?php language_attributes(); ?> <?php elementify_html_attributes(); ?> <?php Utils::the_microdata('html'); ?>>

<head>

    <?php
    /**
     * Functions hooked into elementify/head_top action
     *
     */
    do_action('elementify/head_top');
    ?>

    <?php
    /**
     * Functions hooked into elementify/head action
     *
     * @hooked elementify_head_meta - 10
     */
    do_action('elementify/head');
    ?>

    <?php
    /**
     * Functions hooked into elementify/head_bottom action
     *
     * @hooked elementify_wp_head - 10
     */
    do_action('elementify/head_bottom');
    ?>

</head>

<body <?php
        body_class();
        /**
         * Functions hooked into elementify/body_attributes action
         *
         * @hooked elementify_body_attributes - 10
         */
        do_action('elementify/body_attributes');
        ?>>

    <?php
    // $advance_customize = elementify_framework_get_options();
    // // Remove sticky and transparent
    // unset($advance_customize['header'], $advance_customize['footer']);
    // echo '<pre>';
    // print_r($advance_customize);
    // echo '</pre>';


    // echo '<pre>';
    // print_r($options);
    // echo '</pre>';

    // foreach ($options as $option) {
    //     echo $file = ELEMENTIFY_FRAMEWORK_ABSPATH . "inc/customizer/builder/header/options/class-{$option}-option.php";
    //     // if (file_exists($file)) {
    //     //     echo $file;
    //     // }
    // }

    // //echo elementify_get_post_id();
    // echo '<pre>';
    // print_r($options);
    // echo '</pre>';
    /**
     * Functions hooked into elementify/body_top action
     *
     * @hooked elementify_wp_body_open - 10
     */
    do_action('elementify/body_top');
    ?>
    <div id="page" class="site ele-position-relative ele-position-absolute-after">

        <?php
        /**
         * Functions hooked into elementify/before_header action
         *
         * @hooked elementify_skip_link - 10
         */
        do_action('elementify/before_header');
        ?>

        <?php
        /**
         * Functions hooked into elementify/header action
         *
         * @hooked elementify_header - 10
         */
        do_action('elementify/header');
        //use Elementify\Inc\Generated_Styles;
        // $google_font_subsets = Fonts::add_google_fonts();
        // echo $google_font_subset = Fonts::get_google_font_url();
        //$enable = get_theme_mod('elementify_framework_fonts_base_typo');
        // $enable = elementify_framework_get_options();
        // //echo elementify_get_post_id();
        // echo '<pre>';
        // print_r($enable);
        // echo '</pre>';
        // foreach ($enable as $key => $value) {
        //     if (is_array($value)) {
        //         foreach ($value as $sub_key => $sub_value) {
        //             if ($sub_value) {
        //                 echo 'options/' . $key . '/class-' . str_replace("_", "-", $sub_key) . '-option.php<br/>';
        //             }
        //         }
        //     }
        // }
        // echo Generated_Styles::dimensions(
        // 		[':root'],
        // 		'elementify_framework_buttons_padding',
        // 		'',
        // 		'--ele-button-padding'
        // 	);

        $base_responsive = get_theme_mod('footer_bottom_link_colors_responsive',[]);
        $base = get_theme_mod('footer_bottom_background',[]);

        echo '<pre>';
        print_r($base);
        echo '<br/>';
        print_r($base_responsive);
        echo '</pre>';
        ?>

        <?php
        /**
         * Functions hooked into elementify/after_header action
         *
         */
        do_action('elementify/after_header');