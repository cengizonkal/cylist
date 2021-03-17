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
    $list = $youtube->getPlaylistItemsByPlaylistIdAdvanced($params, true);


    // start box


    $o = '<div class="cylist-box">';
    $o .= '<div class="col-md-4">
              <div class="card mb-4 box-shadow">
                <img class="card-img-top" data-src="holder.js/100px225?theme=thumb&amp;bg=55595c&amp;fg=eceeef&amp;text=Thumbnail" alt="Thumbnail [100%x225]" style="height: 225px; width: 100%; display: block;" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22348%22%20height%3D%22225%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20348%20225%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_1783fe610d4%20text%20%7B%20fill%3A%23eceeef%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A17pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_1783fe610d4%22%3E%3Crect%20width%3D%22348%22%20height%3D%22225%22%20fill%3D%22%2355595c%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22116.7265625%22%20y%3D%22120.3%22%3EThumbnail%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true">
                <div class="card-body">
                  <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                      <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                    </div>
                    <small class="text-muted">9 mins</small>
                  </div>
                </div>
              </div>
            </div>';
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