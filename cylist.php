<?php

/*
Plugin Name: Cylist
Plugin URI: https://github.com/cengizonkal/cylist
Description: Show youtube list items
Version: v1.0.0
Author: Cengiz Önkal <onkal.cengiz@gmail.com>
Author URI: https://github.com/cengizonkal
License: MIT
*/


require 'vendor/autoload.php';


add_action('wp_enqueue_scripts', 'cylist_enqueue');
function cylist_enqueue($hook)
{
    wp_enqueue_script(
        'ajax-script',
        plugins_url('/js/cylist.js', __FILE__),
        ['jquery'],
        '1.0.0',
        true
    );

    wp_localize_script(
        'ajax-script',
        'cylist_ajax_obj',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cylist_ajax'),
        )
    );
}


add_action('wp_ajax_cylist', 'cylist_ajax_handler');
function cylist_ajax_handler()
{
    check_ajax_referer('cylist_ajax');

    $args = [
        'page_token' => sanitize_text_field($_POST['page_token']),
        'you_tube_list_id' => sanitize_text_field($_POST['you_tube_list_id'])
    ];

    echo cylist_get_videos($args['you_tube_list_id'], $args['page_token']);
    wp_die();
}


/**
 * The [cylist] shortcode.
 *
 * Accepts a title and will display a box.
 *
 * @param  array  $atts  Shortcode attributes. Default empty.
 * @param  string  $content  Shortcode content. Default null.
 * @param  string  $tag  Shortcode tag (name). Default empty.
 * @return string Shortcode output.
 * @throws Exception
 */
function cylist_shortcode($atts = [], $content = null, $tag = '')
{
    // normalize attribute keys, lowercase
    $atts = array_change_key_case((array)$atts, CASE_LOWER);

    // override default attributes with user attributes
    $cylist_atts = shortcode_atts(
        ['youtube_list_id' => 'enter_youtube_list_id'],
        $atts,
        $tag
    );
    return cylist_get_videos($cylist_atts['youtube_list_id'], null);
}


function cylist_get_videos($youtubeListId, $pageToken)
{
    $config = include __DIR__.'/config.php';
    $youtube = new Madcoda\Youtube\Youtube(['key' => $config['api_key']]);
    $params = array(
        'playlistId' => $youtubeListId,
        'part' => 'id, snippet, contentDetails, status',
        'maxResults' => 5,
        'pageToken' => $pageToken
    );
    $youtubeList = $youtube->getPlaylistItemsByPlaylistIdAdvanced($params, true);
    $o = '<div class="row" id="cylist_container">';

    foreach ($youtubeList['results'] as $item) {
        $o .= '
        <div class="col-md-4">   
        <iframe width="560" height="315" src="https://www.youtube.com/embed/'.$item->snippet->resourceId->videoId.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>';
    }

    $o .= '</div>';
    $o .= '<div class="row"><div class="col-md-12"><button id="cylist_load_more" class="btn btn-info btn-lg" onclick="loadMoreVideos(\''.$youtubeList['info']['nextPageToken'].'\',\''.$youtubeListId.'\')">Load More</button></div></div>';

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