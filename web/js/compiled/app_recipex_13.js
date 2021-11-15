(function ($) {
    "use strict";

    /* ...................................................................................
     * Nice Scroll Function
     * http://slicknav.com/
     * ....................................................................................*/
    $('.primary-nav .primary-menu').slicknav({
        label: '',
        prependTo: ".mobile-menu",
        allowParentLinks: true
    });

    $('.primary-menu li').hover(
        function () {
            $(this).children('.sub-menu').stop(true, true).slideDown(200);
        },
        function () {
            $(this).children('.sub-menu').slideUp(200);
        }
    );


    /* ...................................................................................
     * Swipebox
     * ....................................................................................*/
    $(".swipebox").swipebox();
    $('a[data-rel]').each(function () {
        $(this).attr('rel', $(this).data('rel'));
    });


    /* ...................................................................................
     * Carousels
     * ....................................................................................*/
    $('.home-carousel-one, .editors-carousel').owlCarousel({
        loop: true,
        margin: 0,
        items: 1,
        animateOut: 'slideOutUp',
        animateIn: 'slideInDown',
        nav: true,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
    });

    // Gallery Post Carousel
    $('.gallery-post-carousel').slick({
        autoplay: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        prevArrow: '<div class="left-arrow"><i class="fa fa-angle-left"></i></div>',
        nextArrow: '<div class="right-arrow"><i class="fa fa-angle-right"></i></div>'
    });

    // Single Recipe Carousel
    $('.single-recipe-carousel').slick({
        autoplay: true,
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        asNavFor: '.single-recipe-carousel-nav'
    });

    $('.single-recipe-carousel-nav').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        asNavFor: '.single-recipe-carousel',
        focusOnSelect: true
    });


    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        nextText: '<i class="fa fa-angle-right"></i>',
        prevText: '<i class="fa fa-angle-left"></i>'
    });

    /* ...................................................................................
     * Isotope
     * ....................................................................................*/

    var $container = $('.recipe-filters-wrapper');
    $container.isotope();
    $('.recipe-filters a').on('click', function (event) {
        event.preventDefault();
        var $this = $(this),
            filterValue = $this.attr('data-filter');
        $this.addClass('current').siblings('a').removeClass('current');
        $container.isotope({filter: filterValue});
    });


    /* ...................................................................................
     * Form Validation
     * ....................................................................................*/

    /* ...................................................................................
     * Wow
     * ....................................................................................*/
    var wow = new WOW({
        mobile: false
    });
    wow.init();


    /* ...................................................................................
     * Chosen
     * ....................................................................................*/
    $(".chosen-select").chosen({
        disable_search_threshold: 10,
        width: "100%"
    });

    /* ...................................................................................
     * Two color page style
     * ....................................................................................*/
    function twoColorStyle() {
        if ($(window).width() > 991) {
            $('#left-layer').css({
                width: (( $('#page-layer-styled').outerWidth() - $('.container').outerWidth() ) / 2 ) + $('.col-sidebar').outerWidth()
            });
        }
    }

    twoColorStyle();

    /* ...................................................................................
     * Top Search
     * ....................................................................................*/
    $('#top-search-button').on('click', function () {
        $('#top-search-form').fadeToggle(250);
        return false;
    });

    /* ...................................................................................
     * Footer Two
     * ....................................................................................*/
    function footerTwoStyle() {
        if ($(window).width() > 991) {
            $('#footer-left-layer').css({
                width: ((  $('.footer-top').width() - $('.footer-top .container').outerWidth() ) / 2) + $('.footer-widget').outerWidth()
            });
        }
    }

    footerTwoStyle();

    $(window).on('resize', function () {
        twoColorStyle();
        footerTwoStyle();
    });

    /* ...................................................................................
     * Advance Search
     * ....................................................................................*/
    $('#advance-search').on('click', function () {
        $('.recipe-form-checkboxes').slideToggle();
    });

    /* ...................................................................................
     * Tabs
     * ....................................................................................*/
    var tabsList = $('.mT li');
    tabsList.on('click', function () {
        var $this = $(this);
        tabsList.removeClass('active');
        $this.addClass('active');
        $('.tab-block').hide().eq($this.index()).show();
    });

    /* ...................................................................................
     * JQuery UI Slider
     * ....................................................................................*/
    function recipexSlider($slider, $output, $temp) {
        var slider = $($slider),
            output = $($output),
            temp = $($temp);

        slider.slider({
            min: 5,
            max: 720, // 1440 for 24 Hours
            step: 5,
            slide: function (e, ui) {
                var recipeHours = Math.floor(ui.value / 60),
                    recipeMinutes = ui.value - ( recipeHours * 60 );

                if (recipeHours.toString().length == 1) {
                    recipeHours = '0' + recipeHours;
                }

                if (recipeMinutes.toString().length == 1) {
                    recipeMinutes = '0' + recipeMinutes;
                }

                var set_time = recipeHours + ':' + recipeMinutes;

                temp.html(set_time);
                output.val(set_time);
            }
        });
        output.val("00:0" + slider.slider("value"));
        temp.html("00:0" + slider.slider("value"));
    }
    if (jQuery().slider) {
        recipexSlider("#preparation-time-slider", "#recipe-preparation-time", "#preparation-time");
        recipexSlider("#cook-time-slider", "#recipe-cook-time", "#cook-time");
    }


    /* ...................................................................................
     * Share This
     * ....................................................................................*/
    var shareIcons = $(this).prev('#share-icons');
    var shareIconss = $(this).prev('.share-icons');
    $('#share-this, .share-this').on('click', function(e) {
        $(this).prev('#share-icons').fadeToggle();
        $(this).prev('.share-icons').fadeToggle();
        $('.share-icons').not($(this).prev('.share-icons')).fadeOut();
        return false;
    });

    $('#close-share-icons, .close-share-icons').on('click', function(e) {
        $(this).parent().parent('#share-icons').hide();
        $(this).parent().parent('.share-icons').hide();
    });

    /* ...................................................................................
     * Add To Favorites
     * ....................................................................................*/
    $('#bookmark-this').on( 'click', function(e) {
        var bookmarkURL = window.location.href,
            bookmarkTitle = document.title;

        if (window.sidebar && window.sidebar.addPanel) {
            // Firefox version < 23
            window.sidebar.addPanel(bookmarkTitle, bookmarkURL, '');
        } else if ((window.sidebar && /Firefox/i.test(navigator.userAgent)) || (window.opera && window.print)) {
            // Firefox version >= 23 and Opera Hotlist
            $(this).attr({
                href: bookmarkURL,
                title: bookmarkTitle,
                rel: 'sidebar'
            }).off(e);
            return true;
        } else if (window.external && ('AddFavorite' in window.external)) {
            // IE Favorite
            window.external.AddFavorite(bookmarkURL, bookmarkTitle);
        } else {
            // Other browsers (mainly WebKit - Chrome/Safari)
            alert('Press ' + (/Mac/i.test(navigator.userAgent) ? 'Cmd' : 'Ctrl') + '+D to bookmark this page.');
        }

        return false;
    });
})(jQuery);

