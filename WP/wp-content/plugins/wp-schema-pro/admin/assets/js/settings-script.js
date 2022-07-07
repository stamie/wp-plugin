(function ($) {
    const { __ } = wp.i18n;
    const temp = {
        "person": __('Website Owner Name'),
        "organization": __('Organization Name', 'wp-schema-pro'),
        "Webshop": __('Webshop Name', 'wp-schema-pro'),
        "personblog": __('Website Owner Name', 'wp-schema-pro'),
        "Smallbusiness": __('Blog Website Name', 'wp-schema-pro'),
        "Otherbusiness": __('Business Name', 'wp-schema-pro')
    };

    /**
    * AJAX Request Queue
    *
    * - add()
    * - remove()
    * - run()
    * - stop()
    *
    * @since 1.2.0.8
    */
    var WPSPAjaxQueue = (function () {

        var requests = []

        return {

			/**
			 * Add AJAX request
			 *
			 * @since 1.2.0.8
			 */
            add: function (opt) {
                requests.push(opt)
            },

			/**
			 * Remove AJAX request
			 *
			 * @since 1.2.0.8
			 */
            remove: function (opt) {
                if (jQuery.inArray(opt, requests) > -1)
                    requests.splice($.inArray(opt, requests), 1)
            },

			/**
			 * Run / Process AJAX request
			 *
			 * @since 1.2.0.8
			 */
            run: function () {
                var self = this,
                    oriSuc

                if (requests.length) {
                    oriSuc = requests[0].complete

                    requests[0].complete = function () {
                        if (typeof (oriSuc) === "function") oriSuc()
                        requests.shift()
                        self.run.apply(self, [])
                    }

                    jQuery.ajax(requests[0])

                } else {

                    self.tid = setTimeout(function () {
                        self.run.apply(self, [])
                    }, 1000)
                }
            },

			/**
			 * Stop AJAX request
			 *
			 * @since 1.2.0.8
			 */
            stop: function () {

                requests = []
                clearTimeout(this.tid)
            }
        }

    }())
    /**
     * AIOSRS Frontend
     *
     * @class WP_Schema_Pro_Settings
     * @since 1.0
     */
    WP_Schema_Pro_Settings = {

        init: function () {

            var self = this;
            this.customFieldDependecy();
            this.customImageSelect();
            this.initRepeater();
            this.toolTips();
            this.regenerateSchema();
            /**
			 * Run / Process AJAX request
			 */
            WPSPAjaxQueue.run()

            $(document).on("click", ".wpsp-activate-widget", WP_Schema_Pro_Settings._activate_widget)
            $(document).on("click", ".wpsp-deactivate-widget", WP_Schema_Pro_Settings._deactivate_widget)

            $(document).on("click", ".wpsp-activate-all", WP_Schema_Pro_Settings._bulk_activate_widgets)
            $(document).on("click", ".wpsp-deactivate-all", WP_Schema_Pro_Settings._bulk_deactivate_widgets)
            $('select.wp-select2').each(function (index, el) {

                self.init_target_rule_select2(el);
            });

        },
        regenerateSchema: function () {

            $("#wpsp-regenerate-schema").click(function () {

                $(this).next('span.spinner').addClass('is-active');

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    dataType: 'json',
                    data: {
                        action: 'regenerate_schema',
                        nonce: $(this).data('nonce')
                    }
                }).success(function (response) {

                    $("#wpsp-regenerate-schema")
                        .next('span.spinner')
                        .removeClass('is-active');

                    $("#wpsp-regenerate-notice").show().delay(2000).fadeOut();
                });
            });
        },
        toolTips: function () {

            $(document).on('click', '.wp-schema-pro-tooltip-icon', function (e) {

                e.preventDefault();
                $('.wp-schema-pro-tooltip-wrapper').removeClass('activate');
                $(this).parent().addClass('activate');
            });

            $(document).on('click', function (e) {

                if (!$(e.target).hasClass('wp-schema-pro-tooltip-description') && !$(e.target).hasClass('wp-schema-pro-tooltip-icon') && $(e.target).closest('.wp-schema-pro-tooltip-description').length == 0) {
                    $('.wp-schema-pro-tooltip-wrapper').removeClass('activate');
                }
            });
        },

        customImageSelect: function () {

            var file_frame;
            window.inputWrapper = '';

            $(document.body).on('click', '.image-field-wrap .aiosrs-image-select', function (e) {

                e.preventDefault();

                window.inputWrapper = $(this).closest('td');

                // Create the media frame.
                file_frame = wp.media({
                    button: {
                        text: 'Select Image',
                        close: false
                    },
                    states: [
                        new wp.media.controller.Library({
                            title: 'Select Custom Image',
                            library: wp.media.query({ type: 'image' }),
                            multiple: false,
                        })
                    ]
                });

                // When an image is selected, run a callback.
                file_frame.on('select', function () {

                    var attachment = file_frame.state().get('selection').first().toJSON();

                    var image = window.inputWrapper.find('.image-field-wrap img');
                    if (image.length == 0) {
                        window.inputWrapper.find('.image-field-wrap').append('<a href="#" class="aiosrs-image-select img"><img src="' + attachment.url + '" /></a>');
                    } else {
                        image.attr('src', attachment.url);
                    }
                    window.inputWrapper.find('.image-field-wrap').addClass('bsf-custom-image-selected');
                    window.inputWrapper.find('.single-image-field').val(attachment.id);

                    file_frame.close();
                });

                file_frame.open();
            });


            $(document).on('click', '.aiosrs-image-remove', function (e) {

                e.preventDefault();
                var parent = $(this).closest('td');
                parent.find('.image-field-wrap').removeClass('bsf-custom-image-selected');
                parent.find('.single-image-field').val('');
                parent.find('.image-field-wrap img').removeAttr('src');
            });

            var file_frame;
            window.inputWrapper = '';
        },

        customFieldDependecy: function () {
            jQuery(document).on('change', '#post-body-content .wp-schema-pro-custom-option-select, .aiosrs-pro-setup-wizard-content.general-setting-content-wrap .wp-schema-pro-custom-option-select', function () {
                var custom_wrap = jQuery(this).next('.custom-field-wrapper');

                custom_wrap.css('display', 'none');
                if ('custom' == jQuery(this).val()) {
                    custom_wrap.css('display', '');
                }
            });

            jQuery(document).on('change', 'select[name="wp-schema-pro-general-settings[site-represent]"]', function () {
                var wrapper = jQuery(this).closest('table'),
                    logo_wrap = wrapper.find('.wp-schema-pro-site-logo-wrap'),
                    company_name_wrap = wrapper.find('.wp-schema-pro-site-name-wrap'),
                    person_name_wrap = wrapper.find('.wp-schema-pro-person-name-wrap');

                company_name_wrap.css('display', 'none');
                person_name_wrap.css('display', 'none');
                if ('' != jQuery(this).val()) {

                    if ('organization' == jQuery(this).val() || 'Webshop' == jQuery(this).val() || 'Smallbusiness' == jQuery(this).val() || 'Otherbusiness' == jQuery(this).val()) {
                        logo_wrap.css('display', '');
                        company_name_wrap.css('display', '');
                    } else {
                        person_name_wrap.css('display', '');
                        logo_wrap.css('display', '');
                    }
                }
            });
            jQuery(document).on('change', 'select[name="wp-schema-pro-general-settings[site-represent]"]', function () {
                var organization_type = jQuery(this).val()
                if ('' != jQuery(this).val()) {
                    if (organization_type in temp) {
                        $('.wpsp-organization-label').text(temp[organization_type]);
                    }
                }

            });
            jQuery(document).on('change', 'select[name="wp-schema-pro-corporate-contact[contact-type]"]', function () {
                var wrapper = jQuery(this).closest('table'),
                    contact_point_wrap = wrapper.find('.wp-schema-pro-other-wrap');
                contact_point_wrap.css('display', 'none');
                if ('' != jQuery(this).val()) {

                    if ('other' == jQuery(this).val()) {

                        contact_point_wrap.css('display', '');
                    }
                }
            });
            $('#add-row').on('click', function () {
                var row = $('.empty-row.screen-reader-text').clone(true);
                row.removeClass('empty-row screen-reader-text');
                row.insertBefore('#repeatable-fieldset-one >tr:last');
                return false;
            });

            $('.remove-row').on('click', function () {
                $(this).parents('tr').remove();
                return false;
            });
        },

        initRepeater: function () {

            $(document).on('click', '.bsf-repeater-add-new-btn', function (event) {
                event.preventDefault();

                var selector = $(this),
                    parent_wrap = selector.closest('.bsf-aiosrs-schema-type-wrap'),
                    total_count = parent_wrap.find('.aiosrs-pro-repeater-table-wrap').length,
                    template = parent_wrap.find('.aiosrs-pro-repeater-table-wrap').first().clone();

                template.find('input, textarea, select').each(function (index, el) {
                    $(this).val('');

                    var field_name = 'undefined' != typeof $(this).attr('name') ? $(this).attr('name').replace('[0]', '[' + total_count + ']') : '',
                        field_class = 'undefined' != typeof $(this).attr('class') ? $(this).attr('class').replace('-0-', '-' + total_count + '-') : '',
                        field_id = 'undefined' != typeof $(this).attr('id') ? $(this).attr('id').replace('-0-', '-' + total_count + '-') : '';

                    $(this).attr('name', field_name);
                    $(this).attr('class', field_class);
                    $(this).attr('id', field_id);
                });

                template.insertBefore(selector);
            });

            $(document).on('click', '.bsf-repeater-close', function (event) {
                event.preventDefault();

                var selector = $(this),
                    parent_wrap = selector.closest('.bsf-aiosrs-schema-type-wrap'),
                    repeater_count = parent_wrap.find('> .aiosrs-pro-repeater-table-wrap').length;

                if (repeater_count > 1) {
                    selector.closest('.aiosrs-pro-repeater-table-wrap').remove();
                }
            });
        },
        init_target_rule_select2: function (selector) {
            $(selector).select2({

                placeholder: "Search Fields...",

                ajax: {
                    url: ajaxurl,
                    dataType: 'json',
                    method: 'post',
                    delay: 250,
                    data: function (params) {
                        return {
                            nonce_ajax: AIOSRS_search.search_field,
                            q: params.term, // search term
                            page: params.page,
                            action: 'bsf_get_specific_pages'
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2,
            });
        },
        /**
 * Activate All Widgets.
 */
        _bulk_activate_widgets: function (e) {
            var button = $(this)

            var data = {
                action: "wpsp_bulk_activate_widgets",
                nonce: AIOSRS_search.ajax_nonce,
            }

            if (button.hasClass("updating-message")) {
                return
            }

            $(button).addClass("updating-message")

            WPSPAjaxQueue.add({
                url: ajaxurl,
                type: "POST",
                data: data,
                success: function (data) {

                    // Bulk add or remove classes to all modules.
                    $(".wpsp-widget-list").children("li").addClass("activate").removeClass("deactivate")
                    $(".wpsp-widget-list").children("li").find(".wpsp-activate-widget")
                        .addClass("wpsp-deactivate-widget")
                        .text(AIOSRS_search.deactivate)
                        .removeClass("wpsp-activate-widget")
                    $(button).removeClass("updating-message")
                }
            })
            e.preventDefault()
        },

		/**
		 * Deactivate All Widgets.
		 */
        _bulk_deactivate_widgets: function (e) {
            var button = $(this)

            var data = {
                action: "wpsp_bulk_deactivate_widgets",
                nonce: AIOSRS_search.ajax_nonce,
            }

            if (button.hasClass("updating-message")) {
                return
            }
            $(button).addClass("updating-message")

            WPSPAjaxQueue.add({
                url: ajaxurl,
                type: "POST",
                data: data,
                success: function (data) {

                    console.log(data)
                    // Bulk add or remove classes to all modules.
                    $(".wpsp-widget-list").children("li").addClass("deactivate").removeClass("activate")
                    $(".wpsp-widget-list").children("li").find(".wpsp-deactivate-widget")
                        .addClass("wpsp-activate-widget")
                        .text(AIOSRS_search.activate)
                        .removeClass("wpsp-deactivate-widget")
                    $(button).removeClass("updating-message")
                }
            })
            e.preventDefault()
        },

		/**
		 * Activate Module.
		 */
        _activate_widget: function (e) {
            var button = $(this),
                id = button.parents("li").attr("id")

            var data = {
                block_id: id,
                action: "wpsp_activate_widget",
                nonce: AIOSRS_search.ajax_nonce,
            }

            if (button.hasClass("updating-message")) {
                return
            }

            $(button).addClass("updating-message")

            WPSPAjaxQueue.add({
                url: ajaxurl,
                type: "POST",
                data: data,
                success: function (data) {

                    // Add active class.
                    $("#" + id).addClass("activate").removeClass("deactivate")
                    // Change button classes & text.
                    $("#" + id).find(".wpsp-activate-widget")
                        .addClass("wpsp-deactivate-widget")
                        .text(AIOSRS_search.deactivate)
                        .removeClass("wpsp-activate-widget")
                        .removeClass("updating-message")
                }
            })

            e.preventDefault()
        },

		/**
		 * Deactivate Module.
		 */
        _deactivate_widget: function (e) {
            var button = $(this),
                id = button.parents("li").attr("id")
            var data = {
                block_id: id,
                action: "wpsp_deactivate_widget",
                nonce: AIOSRS_search.ajax_nonce,
            }

            if (button.hasClass("updating-message")) {
                return
            }

            $(button).addClass("updating-message")

            WPSPAjaxQueue.add({
                url: ajaxurl,
                type: "POST",
                data: data,
                success: function (data) {

                    // Remove active class.
                    $("#" + id).addClass("deactivate").removeClass("activate")

                    // Change button classes & text.
                    $("#" + id).find(".wpsp-deactivate-widget")
                        .addClass("wpsp-activate-widget")
                        .text(AIOSRS_search.activate)
                        .removeClass("wpsp-deactivate-widget")
                        .removeClass("updating-message")
                }
            })
            e.preventDefault()
        },

    }
    var load_default_values = function () {

        var field = jQuery('select[name="wp-schema-pro-general-settings[site-represent]"]'),
            wrapper = field.closest('table'),
            logo_wrap = wrapper.find('.wp-schema-pro-site-logo-wrap'),
            company_name_wrap = wrapper.find('.wp-schema-pro-site-name-wrap'),
            person_name_wrap = wrapper.find('.wp-schema-pro-person-name-wrap');


        company_name_wrap.css('display', 'none');
        person_name_wrap.css('display', 'none');
        if ('' != field.val()) {

            if ('organization' == field.val() || 'Webshop' == field.val() || 'Smallbusiness' == field.val() || 'Otherbusiness' == field.val()) {
                logo_wrap.css('display', '');
                company_name_wrap.css('display', '');
            } else {
                person_name_wrap.css('display', '');
                logo_wrap.css('display', '');
            }
        }
    }
    var load_default_organization_label = function () {
        var field = jQuery('select[name="wp-schema-pro-general-settings[site-represent]"]'),
            organization_type = field.val();
        if ('' != field) {
            if (organization_type in temp) {
                $('.wpsp-organization-label').text(temp[organization_type]);
            }
        }
    }


    $(document).ready(function () {
        $('.wp-select2').select2();
        $('.wpsp-setup-configuration-settings').select2();
        load_default_values();
        load_default_organization_label();

    });


    /* Initializes the AIOSRS Frontend. */
    $(function () {

        WP_Schema_Pro_Settings.init();


    });
})(jQuery);