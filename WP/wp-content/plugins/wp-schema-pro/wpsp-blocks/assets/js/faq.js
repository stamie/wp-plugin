function WPSPslideUp(wpsptarget, duration) {
    wpsptarget.style.transitionProperty = 'height, margin, padding';
    wpsptarget.style.transitionDuration = duration + 'ms';
    wpsptarget.style.boxSizing = 'border-box';
    wpsptarget.style.height = wpsptarget.offsetHeight + 'px';
    wpsptarget.offsetHeight;
    wpsptarget.style.overflow = 'hidden';
    wpsptarget.style.height = 0;
    wpsptarget.style.paddingTop = 0;
    wpsptarget.style.paddingBottom = 0;
    wpsptarget.style.marginTop = 0;
    wpsptarget.style.marginBottom = 0;
    window.setTimeout(function () {
        wpsptarget.style.display = 'none';
        wpsptarget.style.removeProperty('height');
        wpsptarget.style.removeProperty('padding-top');
        wpsptarget.style.removeProperty('padding-bottom');
        wpsptarget.style.removeProperty('margin-top');
        wpsptarget.style.removeProperty('margin-bottom');
        wpsptarget.style.removeProperty('overflow');
        wpsptarget.style.removeProperty('transition-duration');
        wpsptarget.style.removeProperty('transition-property');
    }, duration);
}

function WPSPslideDown(wpsptarget, duration) {
    wpsptarget.style.removeProperty('display');
    var display = window.getComputedStyle(wpsptarget).display;

    if (display === 'none')
        display = 'block';

    wpsptarget.style.display = display;
    var height = wpsptarget.offsetHeight;
    wpsptarget.style.overflow = 'hidden';
    wpsptarget.style.height = 0;
    wpsptarget.style.paddingTop = 0;
    wpsptarget.style.paddingBottom = 0;
    wpsptarget.style.marginTop = 0;
    wpsptarget.style.marginBottom = 0;
    wpsptarget.offsetHeight;
    wpsptarget.style.boxSizing = 'border-box';
    wpsptarget.style.transitionProperty = "height, margin, padding";
    wpsptarget.style.transitionDuration = duration + 'ms';
    wpsptarget.style.height = height + 'px';
    wpsptarget.style.removeProperty('padding-top');
    wpsptarget.style.removeProperty('padding-bottom');
    wpsptarget.style.removeProperty('margin-top');
    wpsptarget.style.removeProperty('margin-bottom');
    window.setTimeout(function () {
        wpsptarget.style.removeProperty('height');
        wpsptarget.style.removeProperty('overflow');
        wpsptarget.style.removeProperty('transition-duration');
        wpsptarget.style.removeProperty('transition-property');
    }, duration);
}

function slideToggle(wpsptarget, duration) {

    if (window.getComputedStyle(wpsptarget).display === 'none') {
        return WPSPslideDown(wpsptarget, duration);
    } else {
        return WPSPslideUp(wpsptarget, duration);
    }
}

function setupFAQ() {

    var pattern = new RegExp('^[\\w\\-]+$');
    var hashval = window.location.hash.substring(1);
    var expandFirstelements = document.getElementsByClassName('wpsp-faq-expand-first-true');
    var inactiveOtherelements = document.getElementsByClassName('wpsp-faq-inactive-other-false');

    if (pattern.test(hashval)) {

        var elementToOpen = document.getElementById(hashval);
        elementToOpen.getElementsByClassName('wpsp-faq-item')[0].classList.add('wpsp-faq-item-active');
        elementToOpen.getElementsByClassName('wpsp-faq-item')[0].setAttribute('aria-expanded', true);
        WPSPslideDown(elementToOpen.getElementsByClassName('wpsp-faq-content')[0], 500);
    } else {

        for (var item = 0; item < expandFirstelements.length; item++) {
            if (true === expandFirstelements[item].classList.contains('wpsp-faq-layout-accordion')) {

                expandFirstelements[item].querySelectorAll('.wpsp-faq-child__outer-wrap')[0].getElementsByClassName('wpsp-faq-item')[0].classList.add('wpsp-faq-item-active');
                expandFirstelements[item].querySelectorAll('.wpsp-faq-child__outer-wrap')[0].getElementsByClassName('wpsp-faq-item')[0].setAttribute('aria-expanded', true);
                expandFirstelements[item].querySelectorAll('.wpsp-faq-child__outer-wrap')[0].getElementsByClassName('wpsp-faq-item')[0].querySelectorAll('.wpsp-faq-content')[0].style.display = 'block';
            }
        }
    }
    for (var item = 0; item < inactiveOtherelements.length; item++) {
        if (true === inactiveOtherelements[item].classList.contains('wpsp-faq-layout-accordion')) {
            var otherItems = inactiveOtherelements[item].querySelectorAll('.wpsp-faq-child__outer-wrap');

            for (var childItem = 0; childItem < otherItems.length; childItem++) {
                otherItems[childItem].getElementsByClassName('wpsp-faq-item')[0].classList.add('wpsp-faq-item-active');
                otherItems[childItem].getElementsByClassName('wpsp-faq-item')[0].setAttribute('aria-expanded', true);
                otherItems[childItem].getElementsByClassName('wpsp-faq-item')[0].querySelectorAll('.wpsp-faq-content')[0].style.display = 'block';
            }
        }
    }
}

window.addEventListener(
    'load',
    function () {

        setupFAQ();

        var accordionElements = document.getElementsByClassName('wpsp-faq-layout-accordion');
        for (var item = 0; item < accordionElements.length; item++) {
            var questionButtons = accordionElements[item].querySelectorAll('.wpsp-faq-questions-button');

            for (var button = 0; button < questionButtons.length; button++) {

                questionButtons[button].parentElement.addEventListener("click", function (e) {
                    WPSPfaqClick(e, this, questionButtons);
                });
                questionButtons[button].parentElement.addEventListener("keypress", function (e) {
                    WPSPfaqClick(e, this, questionButtons);
                });
            }
        }
    }
);

function WPSPfaqClick(e, wpspfaqItem, questionButtons) {
    if (e.target.tagName == "A") {
        return;
    }
    e.preventDefault();
    if (wpspfaqItem.classList.contains('wpsp-faq-item-active')) {
        wpspfaqItem.classList.remove('wpsp-faq-item-active');
        wpspfaqItem.setAttribute('aria-expanded', false);
        WPSPslideUp(wpspfaqItem.getElementsByClassName('wpsp-faq-content')[0], 500);
    } else {
        var parent = e.currentTarget.closest('.wp-block-wpsp-faq');
        var faqToggle = 'true';
        if (parent.classList.contains('wp-block-wpsp-faq')) {
            faqToggle = parent.getAttribute('data-faqtoggle');
        }
        wpspfaqItem.classList.add('wpsp-faq-item-active');
        wpspfaqItem.setAttribute('aria-expanded', true);
        WPSPslideDown(wpspfaqItem.getElementsByClassName('wpsp-faq-content')[0], 500);
        if ('true' === faqToggle) {
            var questionButtons = parent.querySelectorAll('.wpsp-faq-questions-button');
            for (var buttonChild = 0; buttonChild < questionButtons.length; buttonChild++) {
                buttonItem = questionButtons[buttonChild].parentElement
                if (buttonItem === wpspfaqItem) {
                    continue;
                }
                buttonItem.classList.remove('wpsp-faq-item-active');
                buttonItem.setAttribute('aria-expanded', false);
                WPSPslideUp(buttonItem.getElementsByClassName('wpsp-faq-content')[0], 500);
            }
        }
    }
}