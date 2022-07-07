<?php

if (!class_exists('Register_Fields_Class')) {
    class Register_Fields_Class extends RWWS_Fields_Class {

        public function __construct() {
            parent::__construct();
        }

        public function date_js_call() {
            include WRPP_DIR_PATH . '/view/frontend/fields/date-scripts.php';
        }

        public function checkbox_js_call($name = '') {
            include WRPP_DIR_PATH . '/view/frontend/fields/checkbox-scripts.php';
        }

        public function default_registration_fields($form_id = '') {
            start_session_if_not_started();
            include WRPP_DIR_PATH . '/view/frontend/fields/default-registration-fields.php';
        }

        public function default_profile_fields($current_user, $form_id = '') {
            include WRPP_DIR_PATH . '/view/frontend/fields/default-profile-fields.php';
        }

        public function extra_registration_fields($form_id = '') {
            start_session_if_not_started();
            global $wprpp_default_fields_array;

            if ($form_id == '') {
                $extra_fields = get_option('extra_fields');
            } else {
                $extra_fields = get_post_meta($form_id, 'extra_fields', true);
            }

            if (is_array($wprpp_default_fields_array)) {
                foreach ($wprpp_default_fields_array as $key => $value) {
                    $def_field_names[] = $key;
                }
            }

            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if (is_array($def_field_names) and in_array($value['field_name'], $def_field_names)) {

                        if ($value['field_name']) {
                            $checked1 = 'is_' . $value['field_name'] . '_required';
                            $checked2 = $value['field_name'] . '_in_registration';
                            $req = $this->is_field_required($checked1, $form_id);

                            if ($value['field_name'] == 'username') {
                                if ($this->is_field_enabled($checked2, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/username.php';
                                }
                            } elseif ($value['field_name'] == 'useremail') {
                                include WRPP_DIR_PATH . '/view/frontend/fields/useremail.php';
                            } else if ($value['field_name'] == 'password') {
                                if ($this->is_field_enabled($checked2, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/password.php';
                                }
                            } else if ($value['field_name'] == 'profileimage') {
                                if ($this->is_field_enabled($checked2, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/profile-image.php';
                                }
                            } else {
                                if ($this->is_field_enabled($checked2, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/other-fields.php';
                                }
                            }
                        }
                    } else {
                        if ($value['field_show_register'] == 'Yes') {
                            if ($value['field_type'] == 'title') {
                                include WRPP_DIR_PATH . '/view/frontend/fields/title.php';
                            } else {
                                $required = $value['field_required'] == 'Yes' ? true : false;
                                include WRPP_DIR_PATH . '/view/frontend/fields/custom-fields.php';
                            }
                        }
                    }
                }
            } else {
                $this->default_registration_fields($form_id);
            }
        }

        public function extra_profile_fields($form_id = '') {
            global $wprpp_default_fields_array;

            if ($form_id == '') {
                $extra_fields = get_option('extra_fields');
            } else {
                $extra_fields = get_post_meta($form_id, 'extra_fields', true);
            }

            $current_user = wp_get_current_user();
            $user_id = get_current_user_id();

            if (is_array($wprpp_default_fields_array)) {
                foreach ($wprpp_default_fields_array as $key => $value) {
                    $def_field_names[] = $key;
                }
            }

            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if (is_array($def_field_names) and in_array($value['field_name'], $def_field_names)) {
                        if ($value['field_name']) {
                            $checked1 = 'is_' . $value['field_name'] . '_required';
                            $checked3 = $value['field_name'] . '_in_profile';
                            $req = $this->is_field_required($checked1, $form_id);
                            if ($value['field_name'] == 'username') {
                                include WRPP_DIR_PATH . '/view/frontend/fields/username-edit.php';
                            } elseif ($value['field_name'] == 'useremail') {
                                include WRPP_DIR_PATH . '/view/frontend/fields/useremail-edit.php';
                            } else if ($value['field_name'] == 'profileimage') {
                                if ($this->is_field_enabled($checked3, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/profile-image-edit.php';
                                }
                            } else {
                                if ($this->is_field_enabled($checked3, $form_id)) {
                                    include WRPP_DIR_PATH . '/view/frontend/fields/other-fields-edit.php';
                                }
                            }
                        }
                    } else {
                        if ($value['field_show_profile'] == 'Yes') {
                            if ($value['field_type'] == 'title') {
                                include WRPP_DIR_PATH . '/view/frontend/fields/title-edit.php';
                            } else {
                                $required = $value['field_required'] == 'Yes' ? true : false;
                                include WRPP_DIR_PATH . '/view/frontend/fields/custom-fields-edit.php';
                            }
                        }
                    }
                }
            } else {
                $this->default_profile_fields($current_user, $form_id);
            }
        }
    }
}