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

    $vimeo_data = Get_Vimeo_data($video['url']);

    if ($domain_to_check == 'vimeo.com') {

        // add thumbnail:
        if ($options['facebook_embed'] !== true) {
            echo '<meta property="og:image" content="' . esc_attr($vimeo_data['thumbnail_url']) . '" />' . "\n";
            echo '<meta property="og:image:type" content="image/jpg" />' . "\n";
            echo '<meta property="og:image:width" content="' . esc_attr($vimeo_data['thumbnail_width']) . '">' . "\n";
            echo '<meta property="og:image:height" content="' . esc_attr($vimeo_data['thumbnail_height']) . '">' . "\n";

        }
        // if the video is Vimeo then we process.

        echo '<meta property="og:video:url" content="' . esc_attr($video['player_loc']) . '?autoplay=1" />' . "\n";
        echo '<meta property="og:video:secure_url" content="' . esc_attr($video['player_loc']) . '?autoplay=1" />' . "\n";
        echo '<meta property="og:video:type" content="text/html" />' . "\n";
        echo '<meta property="og:video:width" content="' . esc_attr($vimeo_data['width']) . '">' . "\n";
        echo '<meta property="og:video:height" content="' . esc_attr($vimeo_data['height']) . '">' . "\n";

        $re = '/video\/(\d+)/s';
        preg_match($re, $video['player_loc'], $matches);

        // add more meta for vimeo

        echo '<meta property="og:video:url" content="https://vimeo.com/moogaloop.swf?clip_id=' . $matches[1] . '&amp;autoplay=1">' . "\n";
        echo '<meta property="og:video:secure_url" content="https://vimeo.com/moogaloop.swf?clip_id=' . $matches[1] . '&amp;autoplay=1">' . "\n";
        echo '<meta property="og:video:type" content="application/x-shockwave-flash">' . "\n";
        echo '<meta property="og:video:width" content="' . esc_attr($vimeo_data['width']) . '">' . "\n";
        echo '<meta property="og:video:height" content="' . esc_attr($vimeo_data['height']) . '">' . "\n";

    }

}

/**
 * Gets additional information from Vimeo
 *
 * @return array
 * @author bramburn
 **/
function Get_Vimeo_data($url)
{

    $response = wp_remote_get('https://vimeo.com/api/oembed.json?url=' . esc_url_raw($url));

/* Will result in $api_response being an array of data,
parsed from the JSON response of the API listed above */
    $api_response = json_decode(wp_remote_retrieve_body($response), true);
    // print_r($api_response);
    return $api_response;

}

add_action('wpseo_opengraph', 'icelabz_yoast_seo_hook');
