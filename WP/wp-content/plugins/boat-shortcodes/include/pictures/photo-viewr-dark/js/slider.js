window.myNameSpace = window.myNameSpace || {};

jQuery(function () {


    // load images
    jQuery.fn.addImage = function (filename, description) {
        var img = document.createElement('img');
        img.src = "images/" + filename;
        img.alt = description;
        img.className = "thumbnail";
        jQuery(this).append(img);
    }

    // check if element is hidden after scrollbar
    jQuery.fn.overflown = function () {
        var limitLeft = jQuery('.wrapper').offset().left;
        var limitRight = limitLeft + jQuery('.wrapper').width();
        var elemOffsetLeft = jQuery(this[0]).offset().left;
        var elemOffsetRight = elemOffsetLeft + jQuery(this[0]).width() / 2;
        return (elemOffsetRight > limitRight || elemOffsetLeft < limitLeft) ? true : false;
    }

    // scroll to the end of element
    function scrollToElement(el, direction) {
        element_width = el.width();
        scroll_left = jQuery('.wrapper')[0].scrollLeft;
        if (direction == 'next')
            jQuery('.wrapper')[0].scrollTo(scroll_left + element_width, 0);
        else if (direction == 'prev')
            jQuery('.wrapper')[0].scrollTo(scroll_left - element_width, 0);
    }

    function showNextImg() {
        clearInterval(interv);
        interv = setInterval(showNextImg, 3000);

        var el = jQuery('.selected').parent('.wrapper_1');
        var counter = jQuery('.counter');
        if (el.next().length != 0) {
          
            counter.text(el.index() + 1);
            if (el.next().overflown())
                scrollToElement(el, 'next');
                el.children().show();     
                el.next().children().trigger('click');
                

        }
        else {
            jQuery('.thumbnail:first').trigger('click');
            jQuery('.wrapper')[0].scrollTo(0, 0);
            counter.text('1');
        }
        jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/pause_diapo.png');
    }

    function showPrevImg() {
        clearInterval(interv);
        interv = setInterval(showNextImg, 1500);

        var el = jQuery('.selected').parent('.wrapper_1');
        var counter = jQuery('.counter');
        if (el.prev().length != 0) {
            counter.text(el.index() + 1);
            
            if (el.prev().overflown()){
                scrollToElement(el, 'prev');
            }
            el2 = el.prev().children();

            el2.trigger('click');
            el2.show();
        }
        else {
            
            jQuery('.thumbnail:last').trigger('click');
            jQuery('.wrapper')[0].scrollTo(jQuery('.wrapper')[0].scrollWidth, 0);
            counter.text(jQuery('.wrapper_1:last').index() + 1);
        }
        jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/pause_diapo.png');
    }

    function previewImg(e) {
        if (e.originalEvent !== undefined) {
            jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/play_diapo.png');
            interv = clearInterval(interv);
        }
        var wrapper = jQuery('.wrapper');
        var index = jQuery(this).parents('.wrapper_1').index();

        jQuery('.thumbnail.selected').toggleClass('selected');
        jQuery(this).toggleClass('selected')
        jQuery("#caption").text(jQuery('.selected').attr('alt'));
        jQuery('.counter').text(index + 1);
        var src = jQuery(this).attr('src');
        jQuery('#preview').fadeOut('fast', () => {
            jQuery('#preview').attr('src', src);
            jQuery('#preview').fadeIn('fast');
        });
    }

    function toggleDiapo() {
        interv = (interv != null) ? clearInterval(interv) : setInterval(showNextImg, 1500);
        var src = jQuery('.toggleDiapo').attr('src');
        if (src == "../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/play_diapo.png")
            jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/pause_diapo.png');
        else
            jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/play_diapo.png');
    }

    function goFullscreen() {
        
        jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/play_diapo.png');
        interv = clearInterval(interv);

        var selected = jQuery('.selected').attr('src');
        var container = jQuery('.fullscreen-container');
        jQuery('.fullscreen-div').css({
            'background-image': 'url(' + selected + ')',
            'background-position': 'center center',
            'background-size':'200px 100px',
  
        });
        container.fadeIn('slow');
        container.on('click', function () {
            jQuery(this).fadeOut('slow');
        });
    }
    function goFullscreen2() {
        jQuery('.toggleDiapo').attr('src', '../wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/icons/play_diapo.png');
        interv = clearInterval(interv);

        var selected = jQuery('.selected').attr('src');
        var container = jQuery('.fullscreen-container');
        jQuery('.fullscreen-div').css({
            'background-image': 'url(' + selected + ')',
            'background-position': 'center top',
            'background-size':'auto 80%',
            'background-repeat': 'no-repeat',
            
  
        });
        jQuery('.fullscreen-container').removeClass('hidden');
        container.fadeIn('slow');
        container.on('click', function () {
            jQuery(this).fadeOut('slow');
        });
        jQuery('.remove-fullscreen').on('click', function () {
            jQuery('.fullscreen-container').addClass('hidden');
        });
    }

    // show first image
    var first = jQuery('.wrapper_1').children('.thumbnail:first').toggleClass('selected');
    jQuery('.counter').text('1');
    jQuery('#preview').attr('src', first.attr('src'));
    jQuery("#caption").text(first.attr('alt'));
    // start auto diapo
    var interv;
    interv = setInterval(showNextImg, 3000);

    // setup event listeners
    jQuery('.next').on('click', showNextImg);
    jQuery('.prev').on('click', showPrevImg);
    jQuery('.thumbnail').on('click', previewImg);
    jQuery('.toggleDiapo').on('click', toggleDiapo);
    jQuery('.fullscreen').on('click', goFullscreen2);


});
