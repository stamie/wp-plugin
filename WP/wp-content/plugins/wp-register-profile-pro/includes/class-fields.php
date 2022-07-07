<?php

if (!class_exists('RWWS_Fields_Class')) {
    class RWWS_Fields_Class {

        public $fields = array(
            'title' => 'Title',
            'text' => 'Text Field',
            'textarea' => 'Textarea',
            'select' => 'Select Drop Down',
            'checkbox' => 'Check Boxes',
            'radio' => 'Radio Buttons',
            'date' => 'Date Picker',
            'time' => 'Time Picker',
            'file' => 'File Upload',
        );

        public function __construct() {
            add_action('admin_init', array($this, 'field_data_handle'));
            add_action('wprpp_save_data', array($this, 'save_default_fields'));
            add_action('wprpp_save_register_form_data', array($this, 'save_register_form_fields'), 10, 1);
        }

        public function field_data_handle() {

            if (isset($_POST['option']) and $_POST['option'] == "addNewField") {
                $rfc = new Register_Fields_Class;
                $field = sanitize_text_field($_POST['field']);
                $rfc->new_field_form($field);
                exit;
            }

            if (isset($_POST['option']) and $_POST['option'] == "saveField") {
                $rfc = new Register_Fields_Class;
                $args = array();

                $args['field_type'] = sanitize_text_field(@$_REQUEST['field_type']);
                $args['field_label'] = sanitize_text_field(@$_REQUEST['field_label']);
                $args['field_name'] = str_replace(" ", "_", strtolower(trim(sanitize_text_field(@$_REQUEST['field_name']))));
                $args['field_desc'] = sanitize_text_field(@$_REQUEST['field_desc']);
                $args['field_desc_position'] = sanitize_text_field(@$_REQUEST['field_desc_position']);
                $args['field_placeholder'] = sanitize_text_field(@$_REQUEST['field_placeholder']);
                $args['field_required'] = sanitize_text_field(@$_REQUEST['field_required']);
                $args['field_title'] = sanitize_text_field(@$_REQUEST['field_title']);
                $args['field_pattern'] = sanitize_text_field(@$_REQUEST['field_pattern']);
                $args['field_show_register'] = sanitize_text_field(@$_REQUEST['field_show_register']);
                $args['field_show_profile'] = sanitize_text_field(@$_REQUEST['field_show_profile']);
                $args['field_values'] = implode(',', array_map('trim', explode(',', sanitize_text_field(@$_REQUEST['field_values']))));

                echo $rfc->added_field($args);
                exit;
            }
        }

        public function save_default_fields() {

            $field_names = @$_REQUEST['field_names'];
            $field_labels = @$_REQUEST['field_labels'];
            $field_types = @$_REQUEST['field_types'];
            $field_descs = @$_REQUEST['field_descs'];
            $field_desc_positions = @$_REQUEST['field_desc_positions'];
            $field_placeholders = @$_REQUEST['field_placeholders'];
            $field_requireds = @$_REQUEST['field_requireds'];
            $field_titles = @$_REQUEST['field_titles'];
            $field_patterns = @$_REQUEST['field_patterns'];
            $field_show_registers = @$_REQUEST['field_show_registers'];
            $field_show_profiles = @$_REQUEST['field_show_profiles'];
            $field_values_array = @$_REQUEST['field_values_array'];

            $extra_fields = array();

            if (is_array($field_names)) {
                foreach ($field_names as $key => $value) {
                    if ($value) {
                        $extra_fields[] = array(
                            'field_name' => str_replace(" ", "_", strtolower(trim(sanitize_text_field(@$value)))),
                            'field_label' => sanitize_text_field(@$field_labels[$key]),
                            'field_type' => sanitize_text_field(@$field_types[$key]),
                            'field_desc' => sanitize_text_field(@$field_descs[$key]),
                            'field_desc_position' => sanitize_text_field(@$field_desc_positions[$key]),
                            'field_placeholder' => sanitize_text_field(@$field_placeholders[$key]),
                            'field_required' => sanitize_text_field(@$field_requireds[$key]),
                            'field_title' => sanitize_text_field(@$field_titles[$key]),
                            'field_pattern' => sanitize_text_field(@$field_patterns[$key]),
                            'field_show_register' => sanitize_text_field(@$field_show_registers[$key]),
                            'field_show_profile' => sanitize_text_field(@$field_show_profiles[$key]),
                            'field_values' => implode(',', array_map('trim', explode(',', sanitize_text_field(@$field_values_array[$key])))),
                        );
                    }
                }
            }

            if (isset($extra_fields)) {
                update_option('extra_fields', $extra_fields);
            } else {
                delete_option('extra_fields');
            }

        }

        public function save_register_form_fields($post_id) {
            $field_names = @$_REQUEST['field_names'];
            $field_labels = @$_REQUEST['field_labels'];
            $field_types = @$_REQUEST['field_types'];
            $field_descs = @$_REQUEST['field_descs'];
            $field_desc_positions = @$_REQUEST['field_desc_positions'];
            $field_placeholders = @$_REQUEST['field_placeholders'];
            $field_requireds = @$_REQUEST['field_requireds'];
            $field_titles = @$_REQUEST['field_titles'];
            $field_patterns = @$_REQUEST['field_patterns'];
            $field_show_registers = @$_REQUEST['field_show_registers'];
            $field_show_profiles = @$_REQUEST['field_show_profiles'];
            $field_values_array = @$_REQUEST['field_values_array'];

            $extra_fields = array();

            if (is_array($field_names)) {
                foreach ($field_names as $key => $value) {
                    if ($value) {
                        $extra_fields[] = array(
                            'field_name' => str_replace(" ", "_", strtolower(trim(sanitize_text_field(@$value)))),
                            'field_label' => sanitize_text_field(@$field_labels[$key]),
                            'field_type' => sanitize_text_field(@$field_types[$key]),
                            'field_desc' => sanitize_text_field(@$field_descs[$key]),
                            'field_desc_position' => sanitize_text_field(@$field_desc_positions[$key]),
                            'field_placeholder' => sanitize_text_field(@$field_placeholders[$key]),
                            'field_required' => sanitize_text_field(@$field_requireds[$key]),
                            'field_title' => sanitize_text_field(@$field_titles[$key]),
                            'field_pattern' => sanitize_text_field(@$field_patterns[$key]),
                            'field_show_register' => sanitize_text_field(@$field_show_registers[$key]),
                            'field_show_profile' => sanitize_text_field(@$field_show_profiles[$key]),
                            'field_values' => implode(',', array_map('trim', explode(',', sanitize_text_field(@$field_values_array[$key])))),
                        );
                    }
                }
            }

            if (isset($extra_fields)) {
                update_post_meta($post_id, 'extra_fields', $extra_fields);
            } else {
                delete_post_meta($post_id, 'extra_fields');
            }
        }

        public static function removeslashes($string) {
            $string = implode("", explode("\\", $string));
            return stripslashes(trim($string));
        }

        public function gen_field($args = array()) {
            $field = $args['field_type'];
            $name = $args['field_name'];
            $id = $args['field_name'];
            $value = $args['field_value'];
            $desc = $args['field_decs'];
            $decs_position = $args['field_desc_position'];
            $placeholder = $args['field_placeholder'];
            $options = $args['field_values'];
            $required = $args['field_required'];
            $title = $args['field_title'];
            $pattern = $args['field_pattern'];

            $class = '';
            $patt = '';
            $plsh = '';
            $titl = '';

            $placeholder = $this->get_field_placeholder($placeholder);
            if ($required == true) {$required = 'required';} else { $required = '';}

            if (!empty($pattern)) {
                $patt = 'pattern="' . $pattern . '"';
            }
            if (!empty($placeholder)) {
                $plsh = 'placeholder="' . $placeholder . '"';
            }
            if (!empty($title)) {
                $titl = 'title="' . $title . '"';
            }

            if (isset($args['field_class'])) {
                $class = $args['field_class'];
            }

            switch ($field) {
            case 'text':

                $value = self::removeslashes($value);
                echo $this->get_field_desc($args, 'before_field');
                echo '<input type="text" name="' . $name . '" class="' . $class . '" id="' . $id . '" value="' . $value . '" ' . $plsh . ' ' . $required . ' ' . $titl . ' ' . $patt . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                echo $this->get_field_desc($args, 'after_field');
                break;
            case 'textarea':
                $value = self::removeslashes($value);
                echo $this->get_field_desc($args, 'before_field');
                echo '<textarea name="' . $name . '" class="' . $class . '" id="' . $id . '" ' . $plsh . ' ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>' . $value . '</textarea>';
                echo $this->get_field_desc($args, 'after_field');
                break;
            case 'select':
                $options = self::removeslashes($options);
                $options = explode(",", $options);
                $options = array_map('trim', $options);

                echo $this->get_field_desc($args, 'before_field');
                echo '<select name="' . $name . '" class="' . $class . '" id="' . $id . '" ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                if (is_array($options)) {
                    foreach ($options as $val) {
                        if ($value == $val) {
                            echo '<option value="' . $val . '" selected="selected">' . $val . '</option>';
                        } else {
                            echo '<option value="' . $val . '">' . $val . '</option>';
                        }
                    }
                }
                echo '</select>';
                echo $this->get_field_desc($args, 'after_field');
                break;
            case 'checkbox':
                $options = self::removeslashes($options);
                $options = explode(",", $options);
                $options = array_map('trim', $options);
                if (is_array($options)) {
                    echo $this->get_field_desc($args, 'before_field');
                    foreach ($options as $val) {
                        if (is_array($value) and in_array($val, $value)) {
                            echo '<label class="rpcb"><input type="checkbox" name="' . $name . '[]" id="' . $id . '" value="' . $val . '" checked="checked" ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $val) . '/>' . $val . '</label>';
                        } else {
                            echo '<label class="rpcb"><input type="checkbox" name="' . $name . '[]" id="' . $id . '" value="' . $val . '" ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $val) . '/>' . $val . '</label>';
                        }
                    }
                    if ($required == 'required') {
                        $this->checkbox_js_call($name);
                    }
                    echo $this->get_field_desc($args, 'after_field');
                }
                break;
            case 'radio':
                $options = self::removeslashes($options);
                $options = explode(",", $options);
                $options = array_map('trim', $options);
                if (is_array($options)) {
                    echo $this->get_field_desc($args, 'before_field');
                    foreach ($options as $val) {
                        if ($value == $val) {
                            echo '<label class="rprb"><input type="radio" name="' . $name . '" id="' . $id . '" value="' . $val . '" checked="checked" ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $val) . '/>' . $val . '</label>';
                        } else {
                            echo '<label class="rprb"><input type="radio" name="' . $name . '" id="' . $id . '" value="' . $val . '" ' . $required . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $val) . '/>' . $val . '</label>';
                        }
                    }
                    echo $this->get_field_desc($args, 'after_field');
                }
                break;
            case 'date':
                echo $this->get_field_desc($args, 'before_field');
                echo '<input type="text" name="' . $name . '" class="' . $class . ' wp_reg_date" id="' . $id . '" value="' . $value . '" ' . $required . ' ' . $plsh . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                $this->date_js_call();
                echo $this->get_field_desc($args, 'after_field');
                break;
            case 'time':
                $value = self::removeslashes($value);
                echo $this->get_field_desc($args, 'before_field');
                echo '<input type="text" name="' . $name . '" class="' . $class . ' wp_reg_time" id="' . $id . '" value="' . $value . '" ' . $required . ' ' . $plsh . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                $this->date_js_call();
                echo $this->get_field_desc($args, 'after_field');
                break;
            case 'file':
                echo $this->get_field_desc($args, 'before_field');
                echo '<input type="file" name="' . $name . '" class="' . $class . '" id="' . $id . '" ' . ($required != '' && $value == '' ? 'required' : '') . ' ' . $titl . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                echo $this->get_field_desc($args, 'after_field');
                if ($value) {
                    echo '<p><a href="' . $value . '" target="_blank">' . $this->get_file_name($value) . '</a><br>';
                    echo '<input type="checkbox" name="' . $name . '_remove" id="' . $id . '_remove" value="Yes" /> ' . __('Check to remove this file.', 'wp-register-profile-with-shortcode') . '</p>';
                }
                break;
            default:
                $value = self::removeslashes($value);
                if (!empty($pattern)) {
                    $patt = 'pattern="' . $pattern . '"';
                }
                echo $this->get_field_desc($args, 'before_field');
                echo '<input type="text" name="' . $name . '" class="' . $class . '" id="' . $id . '" value="' . $value . '" ' . $required . ' ' . $plsh . ' ' . $titl . ' ' . $patt . ' ' . apply_filters('rpwsp_field_' . $name, $value) . '>';
                echo $this->get_field_desc($args, 'after_field');
                break;
            }
        }

        public function get_file_name($file = '') {
            if ($file == '') {
                return;
            }
            return basename($file);
        }

        public function get_field_placeholder($placeholder = '') {
            if ($placeholder) {
                $placeholder = self::removeslashes($placeholder);
                return __($placeholder, 'wp-register-profile-with-shortcode');
            }
        }

        public function get_field_desc($args, $position = 'after_field') {
            if ($args['field_desc_position'] != $position) {
                return;
            }
            $desc = $args['field_decs'];
            $classes = array('description', $args['field_name']);
            $classes = implode(' ', $classes);
            if ($desc) {
                $desc = self::removeslashes($desc);
                return '<div class="' . $classes . '">' . nl2br(__(sanitize_text_field($desc)), 'wp-register-profile-with-shortcode') . '</div>';
            }
        }

        public function get_field_desc_default($field, $which = '') {
            global $wprpp_default_fields_array;
            $desc = '';
            if ($which == 2) {
                if (isset($wprpp_default_fields_array[$field]['field_desc_2'])) {
                    $desc = $wprpp_default_fields_array[$field]['field_desc_2'];
                }

            } else {
                if (isset($wprpp_default_fields_array[$field]['field_desc'])) {
                    $desc = $wprpp_default_fields_array[$field]['field_desc'];
                }

            }
            if ($desc == '') {
                return;
            }
            $classes = array('description', $wprpp_default_fields_array[$field]['field_name']);
            $classes = implode(' ', $classes);

            $desc = self::removeslashes($desc);
            return '<div class="' . $classes . '">' . nl2br(__(sanitize_text_field($desc)), 'wp-register-profile-with-shortcode') . '</div>';
        }

        public function is_field_enabled($value, $form_id = '') {
            if ($form_id == '') {
                $data = get_option($value);
            } else {
                $data = get_post_meta($form_id, $value, true);
            }
            if ($data == 'Yes') {
                return true;
            } else {
                return false;
            }
        }

        public function is_field_required($value, $form_id = true) {
            if ($form_id == '') {
                $data = get_option($value);
            } else {
                $data = get_post_meta($form_id, $value, true);
            }
            if ($data == 'Yes') {
                return 'required="required"';
            } else {
                return '';
            }
        }

        public function new_field_form($field) {
            echo '<div class="custom-field-new-form">';

            echo '<h3 id="new-field-title">' . __('New Field') . ' - ' . $this->fields[$field] . '</h3>';

            switch ($field) {
            case 'title':
                include WRPP_DIR_PATH . '/view/admin/fields/add-title.php';
                break;
            case 'text':
                include WRPP_DIR_PATH . '/view/admin/fields/add-text.php';
                break;
            case 'textarea':
                include WRPP_DIR_PATH . '/view/admin/fields/add-textarea.php';
                break;
            case 'select':
                include WRPP_DIR_PATH . '/view/admin/fields/add-select.php';
                break;
            case 'checkbox':
                include WRPP_DIR_PATH . '/view/admin/fields/add-checkbox.php';
                break;
            case 'radio':
                include WRPP_DIR_PATH . '/view/admin/fields/add-radio.php';
                break;
            case 'date':
                include WRPP_DIR_PATH . '/view/admin/fields/add-date.php';
                break;
            case 'time':
                include WRPP_DIR_PATH . '/view/admin/fields/add-time.php';
                break;
            case 'file':
                include WRPP_DIR_PATH . '/view/admin/fields/add-file.php';
                break;
            default:
                include WRPP_DIR_PATH . '/view/admin/fields/add-text.php';
                break;
            }

            include WRPP_DIR_PATH . '/view/admin/fields/add-buttons.php';

            echo '</div>';
        }

        public function field_list() {
            $ret = '';
            foreach ($this->fields as $key => $value) {
                $ret .= '<button class="button buttop-ap-margin-bottom" onclick="selectField(this)" value="' . $key . '">' . $value . '</button>';
                $ret .= ' ';
            }
            return $ret;
        }

        public function added_field($args) {

            $field_type = self::removeslashes($args['field_type']);
            $field_label = self::removeslashes($args['field_label']);
            $field_name = self::removeslashes($args['field_name']);
            $field_desc = self::removeslashes($args['field_desc']);
            $field_desc_position = self::removeslashes($args['field_desc_position']);
            $field_placeholder = self::removeslashes($args['field_placeholder']);
            $field_required = self::removeslashes($args['field_required']);
            $field_title = self::removeslashes($args['field_title']);
            $field_pattern = self::removeslashes($args['field_pattern']);
            $field_show_register = self::removeslashes($args['field_show_register']);
            $field_show_profile = self::removeslashes($args['field_show_profile']);
            $field_values = self::removeslashes($args['field_values']);

            $ret = '<div class="custom-field-box">';
            $ret .= '<div class="custom-field-box-info">';

            $ret .= '<h3 id="new-field-title">' . $field_label . ' - ' . $this->fields[$field_type] . '</h3>';

            $ret .= '<span class="custom-field-label">' . __('Label', 'wp-register-profile-with-shortcode') . '</span>: ' . $field_label;
            $ret .= ',&nbsp;';
            $ret .= '<span class="custom-field-label">' . __('Name', 'wp-register-profile-with-shortcode') . '</span>: ' . $field_name;
            $ret .= ',&nbsp;';
            $ret .= '<span class="custom-field-label">' . __('Type', 'wp-register-profile-with-shortcode') . '</span>: ' . $field_type;
            $ret .= ',&nbsp;';

            if ($field_required == 'Yes') {
                $ret .= '<span class="custom-field-label">' . __('Required', 'wp-register-profile-with-shortcode') . '</span>';
                $ret .= ',&nbsp;';
                $ret .= '<span class="custom-field-label">' . __('Required Message') . ':</span> ' . $field_title;
                $ret .= ',&nbsp;';
            }
            if ($field_show_register == 'Yes') {
                $ret .= '<span class="custom-field-label">' . __('On Registration', 'wp-register-profile-with-shortcode') . '</span>';
                $ret .= ',&nbsp;';
            }
            if ($field_show_profile == 'Yes') {
                $ret .= '<span class="custom-field-label">' . __('On Profile', 'wp-register-profile-with-shortcode') . '</span>';
                $ret .= ',&nbsp;';
            }

            $ret .= '<span class="custom-field-label">' . __('Desc', 'wp-register-profile-with-shortcode') . '</span>: ' . $this->restrict_text($field_desc, 30);

            $ret .= '<p><input type="button" name="edit" value="' . __('Edit') . '" style="margin-right:2px;" class="button button-primary button-large" onclick="editField(this);">';

            $ret .= '<input type="button" name="del" value="' . __('Delete') . '" class="button button-primary button-large" onclick="delField(this);"></p>';

            $ret .= '</div>';

            $ret .= '<div class="custom-field-box-form">';

            switch ($field_type) {
            case 'title':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-title.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'text':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-text.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'textarea':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-textarea.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'select':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-select.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'checkbox':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-checkbox.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'radio':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-radio.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'date':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-date.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'time':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-time.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            case 'file':

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-file.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            default:

                ob_start();
                include WRPP_DIR_PATH . '/view/admin/fields/edit-text.php';
                $ret .= ob_get_contents();
                ob_end_clean();

                break;
            }

            ob_start();
            include WRPP_DIR_PATH . '/view/admin/fields/edit-buttons.php';
            $ret .= ob_get_contents();
            ob_end_clean();

            $ret .= '</div>';

            $ret .= '</div>';

            return $ret;
        }

        public function restrict_text($data = '', $limit = 100) {
            $len = strlen($data);
            if ($len <= $limit) {
                return $data;
            }
            return substr($data, 0, $limit) . '..';
        }

        public function load_field_js() {
            include WRPP_DIR_PATH . '/view/admin/fields/field-scripts.php';
        }
    }

}