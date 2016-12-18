<?php
/**
 * @package Icelabz_Yoast_SEO_Hook
 * @version 0.1
 */
/*
Plugin Name: Icelabz's Yoast VIMEO SEO hook
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: This plugin/hook creates better opengraph for Vimeo videos so that it can be played on facebook.
Author: Bhavesh Ramburn (Icelabz Survey Ltd)
Version: 0.1
Author URI: https://icelabz.co.uk
 */
/**
 * This function parses the URL to check for VIMEO videos.
 *
 *
 * @return url
 * @author bramburn from icelabz.co.uk
 **/

function icelabz_yoast_seo_hook()
{

    $options = get_option('wpseo_video');
    if ($options['facebook_embed'] !== true) {

        return false;
    }

    if (is_singular()) {
        // get post global info
        global $post;

        if (is_object($post)) {
            // respect original settings
            $disable = WPSEO_Meta::get_value('videositemap-disable', $post->ID);
            if ($disable === 'on') {
                return false;
            }
            // get video data
            $video = WPSEO_Meta::get_value('video_meta', $post->ID);

        }
    }

    // do we have any videos?
    if (!isset($video) || !is_array($video) || !isset($video['player_loc'])) {
        return false;
    }
    // let's check if this is Vimeo video.
    $domain_to_check = parse_url($video['url'], PHP_URL_HOST);

    if ($domain_to_check == 'vimeo.com') {
        // if the video is Vimeo then we process.

        echo '<meta property="og:video:url" content="' . esc_attr($video['player_loc']) . '?autoplay=1" />' . "\n";
        echo '<meta property="og:video:secure_url" content="' . esc_attr($video['player_loc']) . '?autoplay=1" />' . "\n";
    }

}

add_action('wpseo_opengraph', 'icelabz_yoast_seo_hook');
