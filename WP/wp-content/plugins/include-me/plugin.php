<?php
/*
  Plugin Name: Include Me
  Plugin URI: https://www.satollo.net/plugins/include-me
  Description: Include external HTML or PHP in any post or page.
  Version: 1.2.1
  Author: Stefano Lissa
  Author URI: https://www.satollo.net
 */

if (is_admin()) {
    include __DIR__ . '/admin/admin.php';
} else {

    function includeme_call($attrs, $content = null) {

        if (isset($attrs['file'])) {
            $file = trim(strip_tags($attrs['file']));
            if (empty($file)) {
                return '<p>Include me shortcode: the file attribute is empty</p>';
            }
            if ($file[0] != '/') {
                $file = ABSPATH . $file;
            }

            if (!file_exists($file)) {
                if (current_user_can('administrator')) {
                    return '<p>Include Me shortcode: file not found "' . $file . '" (the file is shown only to administrator)</p>';
                }
                return '<p>Include Me shortcode: file not found</p>';
            }

            ob_start();
            include($file);
            $buffer = ob_get_clean();
            $options = get_option('includeme', []);
            if (isset($options['shortcode'])) {
                $buffer = do_shortcode($buffer);
            }
            return $buffer;
        }

        if (isset($attrs['post_id'])) {
            $post = get_post($attrs['post_id']);
            $options = get_option('includeme', []);
            $buffer = $post->post_content;
            if (isset($options['shortcode'])) {
                $buffer = do_shortcode($buffer);
            }
            return $buffer;
        }

        if (isset($attrs['field'])) {
            global $post;
            $buffer = get_post_meta($post->ID, $attrs['field'], true);
            if (isset($options['php'])) {
                ob_start();
                eval('?>' . $buffer);
                $buffer = ob_get_clean();
            }
            if (isset($options['shortcode'])) {
                $buffer = do_shortcode($buffer);
            }
            return $buffer;
        }

        if (isset($attrs['src'])) {
            $tmp = '';
            foreach ($attrs as $key => $value) {
                if ($key == 'src') {
                    $value = strip_tags($value);
                }
                $value = str_replace('&amp;', '&', $value);
                if ($key == 'src') {
                    $value = strip_tags($value);
                }
                $tmp .= ' ' . $key . '="' . esc_attr($value) . '"';
            }
            $buffer = '<iframe' . $tmp . '></iframe>';
            return $buffer;
        }
    }

    add_shortcode('includeme', 'includeme_call');
}
