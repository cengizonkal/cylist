<?php

/*
Plugin Name: Cylist
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Show youtube list items
Version: 1.0
Author: Cengiz Önkal <onkal.cengiz@gmail.com>
Author URI: http://URI_Of_The_Plugin_Author
License: MIT
*/


require 'vendor/autoload.php';


/**
 * The [cylist] shortcode.
 *
 * Accepts a title and will display a box.
 *
 * @param  array  $atts  Shortcode attributes. Default empty.
 * @param  string  $content  Shortcode content. Default null.
 * @param  string  $tag  Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 */
function cylist_shortcode($atts = [], $content = null, $tag = '')
{
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $cylist_atts = shortcode_atts(
        [
            'api_key' => 'your_api_key',
            'youtube_list_id' => 'enter_youtube_list_id',
        ],
        $atts,
        $tag
    );

    $youtube = new Madcoda\Youtube\Youtube(['key' => $cylist_atts['api_key']]);
    $params = array(
        'playlistId' => $cylist_atts['youtube_list_id'],
        'part' => 'id, snippet, contentDetails, status',
        'maxResults' => 50
    );
    $youtubeList = $youtube->getPlaylistItemsByPlaylistIdAdvanced($params, true);

//    echo '<pre>';
//    var_dump($youtubeList['results'][0]);
    // start box


    $o = '<div class="row">';

    foreach ($youtubeList['results'] as $item) {
        $o .= '
        <div class="col-md-4">   
        <iframe width="560" height="315" src="https://www.youtube.com/embed/'.$item->snippet->resourceId->videoId.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>';
    }

    // title
    $o .= '</div>';


    return $o;
}

/**
 * Central location to create all shortcodes.
 */
function cylist_shortcodes_init()
{
    add_shortcode('cylist', 'cylist_shortcode');
}

add_action('init', 'cylist_shortcodes_init');