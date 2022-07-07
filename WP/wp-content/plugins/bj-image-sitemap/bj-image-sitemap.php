<?php
/**
Plugin Name: Image Sitemap Generator
Plugin URI: https://wordpress.org/plugins/image-sitemap
Description: Automatically generates Google Image Sitemap and submits it to Google,Bing and Ask.com.
Version: 1.0
Author: WordPress
Author URI: https://wordpress.org/
*/

$siteMapPath = ABSPATH . '/sitemap-image.xml';
$siteMapUrl = get_home_url() . '/sitemap-image.xml';

add_action('admin_menu','bj_isg_admin');
add_action('transition_post_status', 'bj_isg_post_status', 20, 3 );
add_filter('post_image_sitemap_filter', 'bj_isg_attached_images', 10, 2);
add_filter('post_image_sitemap_filter', 'bj_isg_inline_images', 10, 2);
add_filter('post_image_sitemap_filter', 'bj_isg_vc_single_image', 10, 2);

/**
 * Creates an admin menu page in the options menu
 */
function bj_isg_admin() {
    if (function_exists('add_options_page')) {
        add_options_page(__('Image Sitemap Generator'), __('Image Sitemap Generator'), 'manage_options', basename(__FILE__), 'bj_isg_admin_options');
    }
}

/**
 * Displays the content of the options page, and generate the xml file if neccesary
 */
function bj_isg_admin_options() {
    if (isset($_POST['bj_isg_generate'])) {
        $message = bj_isg_generate();
    }
    include "options.template.php";
}

/**
 * Generates the xml by collecting all the pages and posts
 *
 * @return string The state message of the process
 */
function bj_isg_generate() {
    global $siteMapPath;
    $urls = [];

    //Collects all the pages and finds the images on all of them
    $pages = get_pages([
        'post_status' => 'publish'
    ]);
    foreach ($pages as $page) {
        bj_isg_build_post($page, 0.8, $urls );
    }

    //Collects all the pages and finds the images on all of them
    $posts = get_posts([
        'post_type' => 'post',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);
    foreach ($posts as $post) {
        bj_isg_build_post($post, 0.6, $urls );
    }
	
	//Collects all the pages and finds the images on all of them destination
    $posts = get_posts([
        'post_type' => 'destination',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);
    foreach ($posts as $destination) {
        bj_isg_build_post($destination, 0.6, $urls );
    }
	

    //Builds the xml file
    ob_start();
    include 'xml.php';
    $xml = ob_get_clean();
    $result = file_put_contents($siteMapPath, $xml);
    if ($result === false) {
        return '
            <div class="notice notice-error is-dismissible">
                <p>' . __('The file sitemap-image.xml is not writable please check permission of the file.') . '</p>
            </div>
        ';
    }

    //Submits the xml to the search engines
    if (bj_isg_ping()) {
        return '
            <div class="notice notice-success is-dismissible">
                <p>' . __('The file generated successfully..') . '</p>
            </div>
        ';
    } else {
        return '
            <div class="notice notice-error is-dismissible">
                <p>' . __('Althogh the file generated successfully, there was an error during submission.') . '</p>
            </div>
        ';
    }

}

/**
 * Submits the generated xml tho the search engines
 * @return bool Shows whether the submission was successful
 */
function bj_isg_ping() {
    global $siteMapUrl;

    $result = true;
    if (is_wp_error(wp_remote_get( "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($siteMapUrl) ))) {
        $result = false;
    }
    if (is_wp_error(wp_remote_get( "http://www.bing.com/webmaster/ping.aspx?sitemap=" . urlencode($siteMapUrl) ))) {
        $result = false;
    }
    return $result;
}

/**
 * Detects post status changes and builds the xml if neccesary
 *
 * @param $newStatus string The new status of the post
 * @param $oldStatus string The old status of the post
 * @param $post WP_Post The post object
 */
function bj_isg_post_status($newStatus, $oldStatus, $post) {
    if (in_array($post->post_type, ['page', 'post'])) {
        bj_isg_generate();
    }
}

/**
 * Collects all the images of a post
 * @param $post WP_Post The post object
 * @param $priority number The priority of the post
 * @param $urls array The url collector array (by reference)
 */
function bj_isg_build_post($post, $priority, &$urls) {
    $images = [];
    $images = apply_filters('post_image_sitemap_filter', $images, $post);
    if (count($images)) {
        $urls[] = [
            'loc' => get_permalink($post->ID),
            'priority' => $priority,
            'lastmod' => get_post_modified_time('c', false, $post->ID),
            'images' => array_slice($images, 0, 1000)
        ];
    }
}

/**
 * Finds all the images attached to the post
 *
 * @param $images array The already collected images of the post
 * @param $post WP_Post The post object
 * @return array The collected images
 */
function bj_isg_attached_images($images, $post) {
    $attachedImages = get_attached_media('image', $post->ID);
    if (count($attachedImages)) {
        foreach($attachedImages as $image) {
            if ( ctype_space($image->post_excerpt) || $image->post_excerpt == '' ) {
                $caption = htmlspecialchars($image->post_title);
            } else {
                $caption = htmlspecialchars($image->post_excerpt);
            }

            $images[] = [
                'loc' => $image->guid,
                'caption' => $caption,
                'title' => $image->post_title,
            ];

        }
    }
    return $images;
}

/**
 * Finds all the images that are inlined in the post
 *
 * @param $images array The already collected images of the post
 * @param $post WP_Post The post object
 * @return array The collected images
 */
function bj_isg_inline_images($images, $post) {
    if (preg_match_all ("/<img(.*)\/>/ui",$post->post_content, $matches, PREG_SET_ORDER)) {

        for ( $i = 0; $i < count($matches); $i++) {
            $img = [];
            $hasAlt = preg_match("/alt=[\'\"](.*?)[\'\"]/ui", $matches[$i][0], $matches_alt);
            $hasTitle = preg_match("/title=[\'\"](.*?)[\'\"]/ui", $matches[$i][0], $matches_title);

            $alt = $hasAlt ? $matches_alt[1] : '';

            $title = $hasTitle ?
                ((ctype_space($matches_title[1]) || $matches_title[1] == '') ?
                    $img['alt'] :
                    $matches_title[1]) :
                $matches_title[1]
            ;

            if (preg_match("/src=[\'\"](.*?)[\'\"]/ui", $matches[$i][0], $matches_src)) {
                $images[] = [
                    'title' => $title ?: $alt,
                    'caption' => $alt ?: $title,
                    'loc' => htmlspecialchars(trim($matches_src[1]))
                ];
            }
        }
    }
    return $images;
}

/**
 * Finds all the images that are attached with the visual composer single image plugin to the post
 *
 * @param $images array The already collected images of the post
 * @param $post WP_Post The post object
 * @return array The collected images
 */
function bj_isg_vc_single_image($images, $post) {
    if (preg_match_all ("/\[vc_single_image([^\]]*)/ui",$post->post_content, $matches, PREG_SET_ORDER)) {
        foreach($matches as $match) {
            if (preg_match('/image=[\'\"](.*?)[\'\"]/ui', $match[1], $imageData)) {
                
                $image = get_post($imageData[1]);
                if ( ctype_space($image->post_excerpt) || $image->post_excerpt == '' ) {
                    $caption = htmlspecialchars($image->post_title);
                } else {
                    $caption = htmlspecialchars($image->post_excerpt);
                }

                $images[] = [
                    'loc' => $image->guid,
                    'caption' => $caption,
                    'title' => $image->post_title,
                ];
            }
        }
    }
    return $images;
}