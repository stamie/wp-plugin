<?php
function get_permalink_shcode($args)
{

    if (isset($args["id"])) {
        $dest_id = intval($args["id"]);
        $ret = get_permalink($dest_id);
        // echo json_encode(array('url' => "$ret"));
        return $ret;
    }
    return '';
}
add_shortcode('get_permalink', 'get_permalink_shcode');
