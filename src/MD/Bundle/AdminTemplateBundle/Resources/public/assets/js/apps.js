$(function () {
    /** SIDEBAR FUNCTION **/
    $('.sidebar-left ul.sidebar-menu li a').click(function () {
        "use strict";
        $('.sidebar-left li').removeClass('active');
        $(this).closest('li').addClass('active');
        var checkElement = $(this).next();
        if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
            $(this).closest('li').removeClass('active');
            checkElement.slideUp('fast');
        }
        if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
            $('.sidebar-left ul.sidebar-menu ul:visible').slideUp('fast');
            checkElement.slideDown('fast');
        }
        if ($(this).closest('li').find('ul').children().length == 0) {
            return true;
        } else {
            return false;
        }
    });

    if ($(window).width() < 1025) {
        $(".sidebar-left").removeClass("sidebar-nicescroller");
        $(".sidebar-right").removeClass("sidebar-nicescroller");
        $(".nav-dropdown-content").removeClass("scroll-nav-dropdown");
    }
    /** END SIDEBAR FUNCTION **/


    /** BUTTON TOGGLE FUNCTION **/
    $(".btn-collapse-sidebar-left").click(function () {
        "use strict";
        $(".top-navbar").toggleClass("toggle");
        $(".sidebar-left").toggleClass("toggle");
        $(".page-content").toggleClass("toggle");
        $(".icon-dinamic").toggleClass("rotate-180");
    });
    $(".btn-collapse-nav").click(function () {
        "use strict";
        $(".icon-plus").toggleClass("rotate-45");
    });
    /** END BUTTON TOGGLE FUNCTION **/


    /** BEGIN PREETY PRINT **/
    $(window).load(function () {
        "use strict";
        prettyPrint();
    })
    /** END PREETY PRINT **/


    /** BEGIN TOOLTIP FUNCTION **/
    $('.tooltips').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    })
    $('.popovers').popover({
        selector: "[data-toggle=popover]",
        container: "body"
    })
    /** END TOOLTIP FUNCTION **/


    /** NICESCROLL AND SLIMSCROLL FUNCTION **/
    $(".sidebar-nicescroller").niceScroll({
        cursorcolor: "#121212",
        cursorborder: "0px solid #fff",
        cursorborderradius: "0px",
        cursorwidth: "0px"
    });
    $(".sidebar-nicescroller").getNiceScroll().resize();
    $(".right-sidebar-nicescroller").niceScroll({
        cursorcolor: "#111",
        cursorborder: "0px solid #fff",
        cursorborderradius: "0px",
        cursorwidth: "0px"
    });
    $(".right-sidebar-nicescroller").getNiceScroll().resize();

    $(function () {
        "use strict";
        $('.scroll-nav-dropdown').slimScroll({
            height: '350px',
            position: 'right',
            size: '4px',
            railOpacity: 0.3
        });
    });

    $(function () {
        "use strict";
        $('.scroll-chat-widget').slimScroll({
            height: '330px',
            position: 'right',
            size: '4px',
            railOpacity: 0.3,
            railVisible: true,
            alwaysVisible: true,
            start: 'bottom'
        });
    });
    if ($(window).width() < 768) {
        $(".chat-wrap").removeClass("scroll-chat-widget");
    }
    /** END NICESCROLL AND SLIMSCROLL FUNCTION **/




    /** BEGIN PANEL HEADER BUTTON COLLAPSE **/
    $(function () {
        "use strict";
        $('.collapse').on('show.bs.collapse', function () {
            var id = $(this).attr('id');
            $('button.to-collapse[data-target="#' + id + '"]').html('<i class="fa fa-chevron-up"></i>');
        });
        $('.collapse').on('hide.bs.collapse', function () {
            var id = $(this).attr('id');
            $('button.to-collapse[data-target="#' + id + '"]').html('<i class="fa fa-chevron-down"></i>');
        });

        $('.collapse').on('show.bs.collapse', function () {
            var id = $(this).attr('id');
            $('a.block-collapse[href="#' + id + '"] span.right-icon').html('<i class="glyphicon glyphicon-minus icon-collapse"></i>');
        });
        $('.collapse').on('hide.bs.collapse', function () {
            var id = $(this).attr('id');
            $('a.block-collapse[href="#' + id + '"] span.right-icon').html('<i class="glyphicon glyphicon-plus icon-collapse"></i>');
        });
    });
    /** END PANEL HEADER BUTTON COLLAPSE **/




    /** BEGIN DATATABLE EXAMPLE **/
    if ($('#datatable-example').length > 0) {
        $('#datatable-example').dataTable();
    }



    /** BEGIN OWL CAROUSEL **/
    if ($('#owl-lazy-load').length > 0) {
        $("#owl-lazy-load").owlCarousel({
            items: 5,
            lazyLoad: true,
            navigation: true
        });
    }

    if ($('#owl-lazy-load-autoplay').length > 0) {
        $("#owl-lazy-load-autoplay").owlCarousel({
            autoPlay: 3000,
            items: 5,
            lazyLoad: true
        });
    }

    if ($('#owl-lazy-load-gallery').length > 0) {
        $("#owl-lazy-load-gallery").owlCarousel({
            items: 5,
            lazyLoad: true,
            navigation: true
        });
    }


    var Owltime = 7;
    var $progressBar,
            $bar,
            $elem,
            isPause,
            tick,
            percentTime;

    if ($('#owl-single-progress-bar').length > 0) {
        $("#owl-single-progress-bar").owlCarousel({
            slideSpeed: 500,
            paginationSpeed: 500,
            singleItem: true,
            afterInit: progressBar,
            afterMove: moved,
            startDragging: pauseOnDragging
        });
    }

    function progressBar(elem) {
        $elem = elem;
        buildProgressBar();
        start();
    }

    function buildProgressBar() {
        $progressBar = $("<div>", {
            id: "OwlprogressBar"
        });
        $bar = $("<div>", {
            id: "Owlbar"
        });
        $progressBar.append($bar).prependTo($elem);
    }

    function start() {
        percentTime = 0;
        isPause = false;
        tick = setInterval(interval, 10);
    }
    ;

    function interval() {
        if (isPause === false) {
            percentTime += 1 / Owltime;
            $bar.css({
                width: percentTime + "%"
            });
            if (percentTime >= 100) {
                $elem.trigger('owl.next')
            }
        }
    }

    function pauseOnDragging() {
        isPause = true;
    }

    function moved() {
        clearTimeout(tick);
        start();
    }
    /** END OWL CAROUSEL **/

    /** BEGIN REAL ESTATE DESIGN JS FUNCTION **/
    var imagesync1 = $("#imagesync1");
    var imagesync2 = $("#imagesync2");

    imagesync1.owlCarousel({
        singleItem: true,
        slideSpeed: 1000,
        navigation: false,
        pagination: false,
        afterAction: syncPosition,
        lazyLoad: true,
        responsiveRefreshRate: 200
    });

    imagesync2.owlCarousel({
        items: 5,
        itemsDesktop: [1199, 5],
        itemsDesktopSmall: [979, 4],
        itemsTablet: [768, 3],
        itemsMobile: [479, 2],
        pagination: false,
        responsiveRefreshRate: 100,
        afterInit: function (el) {
            el.find(".owl-item").eq(0).addClass("synced");
        }
    });

    function syncPosition(el) {
        var current = this.currentItem;
        $("#imagesync2")
                .find(".owl-item")
                .removeClass("synced")
                .eq(current)
                .addClass("synced")
        if ($("#imagesync2").data("owlCarousel") !== undefined) {
            center(current)
        }
    }
    if ($('#imagesync2').length > 0) {
        $("#imagesync2").on("click", ".owl-item", function (e) {
            e.preventDefault();
            var number = $(this).data("owlItem");
            imagesync1.trigger("owl.goTo", number);
        });
    }
    function center(number) {
        var imagesync2visible = imagesync2.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for (var i in imagesync2visible) {
            if (num === imagesync2visible[i]) {
                var found = true;
            }
        }

        if (found === false) {
            if (num > imagesync2visible[imagesync2visible.length - 1]) {
                imagesync2.trigger("owl.goTo", num - imagesync2visible.length + 2)
            } else {
                if (num - 1 === -1) {
                    num = 0;
                }
                imagesync2.trigger("owl.goTo", num);
            }
        } else if (num === imagesync2visible[imagesync2visible.length - 1]) {
            imagesync2.trigger("owl.goTo", imagesync2visible[1])
        } else if (num === imagesync2visible[0]) {
            imagesync2.trigger("owl.goTo", num - 1)
        }

    }

    if ($('#property-slide-1').length > 0) {
        $("#property-slide-1").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
    }

    if ($('#property-slide-2').length > 0) {
        $("#property-slide-2").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
    }

    if ($('#property-slide-3').length > 0) {
        $("#property-slide-3").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
    }

    if ($('#property-slide-4').length > 0) {
        $("#property-slide-4").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
    }

    if ($('#property-slide-5').length > 0) {
        $("#property-slide-5").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 300,
            paginationSpeed: 400,
            singleItem: true
        });
    }

    if ($('#property-slide-7').length > 0) {
        $("#property-slide-7").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 4000,
            transitionStyle: 'goDown'
        });
    }

    if ($('#property-slide-8').length > 0) {
        $("#property-slide-8").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3000,
            transitionStyle: 'fadeUp'
        });
    }
    /** END REAL ESTATE DESIGN JS FUNCTION **/



    /** BEGIN BLOG DESIGN JS FUNCTION **/
    if ($('#blog-slide-1').length > 0) {
        $("#blog-slide-1").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3000,
            transitionStyle: 'goDown'
        });
    }
    if ($('#blog-slide-2').length > 0) {
        $("#blog-slide-2").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true
        });
    }
    /** END BLOG DESIGN JS FUNCTION **/


    /** BEGIN STORE DESIGN JS FUNCTION **/
    if ($('#store-item-carousel-1').length > 0) {
        $("#store-item-carousel-1").owlCarousel({
            autoPlay: 4000,
            items: 4,
            itemsDesktop: [1199, 4],
            itemsDesktopSmall: [979, 3],
            itemsTablet: [768, 2],
            itemsMobile: [479, 1],
            lazyLoad: true,
            autoHeight: true
        });
    }
    if ($('#store-item-carousel-2').length > 0) {
        $("#store-item-carousel-2").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true
        });
    }
    if ($('#store-item-carousel-3').length > 0) {
        $("#store-item-carousel-3").owlCarousel({
            autoPlay: 4000,
            items: 4,
            itemsDesktop: [1199, 4],
            itemsDesktopSmall: [979, 3],
            itemsTablet: [768, 2],
            itemsMobile: [479, 1],
            lazyLoad: true,
            autoHeight: true,
            navigation: false,
            pagination: false
        });
    }
    /** END STORE DESIGN JS FUNCTION **/



    /** BEGIN TILES JS FUNCTION **/
    if ($('#tiles-slide-1').length > 0) {
        $("#tiles-slide-1").owlCarousel({
            navigation: true,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3000,
            theme: "my-reminder",
            navigationText: ["&larr;", "&rarr;"],
        });
    }
    if ($('#tiles-slide-2').length > 0) {
        $("#tiles-slide-2").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3000,
            transitionStyle: 'backSlide',
            stopOnHover: true
        });
    }
    if ($('#tiles-slide-3').length > 0) {
        $("#tiles-slide-3").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3235,
            stopOnHover: true
        });
    }
    /** END TILES JS FUNCTION **/



    /** BEGIN WIDGET MORRIS JS FUNCTION **/
    if ($('#morris-widget-1').length > 0) {
        Morris.Line({
            element: 'morris-widget-1',
            data: [
                {y: '2000', a: 34},
                {y: '2001', a: 55},
                {y: '2002', a: 60},
                {y: '2003', a: 65},
                {y: '2004', a: 20},
                {y: '2005', a: 75},
                {y: '2006', a: 55},
                {y: '2007', a: 77},
                {y: '2008', a: 90},
                {y: '2009', a: 125},
                {y: '2010', a: 100},
                {y: '2011', a: 15},
                {y: '2012', a: 20},
                {y: '2013', a: 85}
            ],
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Earning (in 10K USD)'],
            resize: true,
            lineColors: ['#1F91BD'],
            pointFillColors: ['#fff'],
            pointStrokeColors: ['#3EAFDB'],
            gridTextColor: ['#fff'],
            pointSize: 3,
            grid: false
        });
    }

    if ($('#morris-widget-2').length > 0) {
        //MORRIS
        Morris.Bar({
            element: 'morris-widget-2',
            data: [
                {y: 'Indonesia', a: 952},
                {y: 'India', a: 985},
                {y: 'United Kingdom', a: 421},
                {y: 'United States', a: 725},
                {y: 'Taiwan', a: 350},
                {y: 'New Zealand', a: 120},
                {y: 'Germany', a: 85},
                {y: 'Thailand', a: 20},
                {y: 'Korea', a: 65},
                {y: 'Malaysia', a: 955},
                {y: 'China', a: 785},
                {y: 'Philipina', a: 700},
                {y: 'Autralia', a: 601},
                {y: 'Japan', a: 50},
                {y: 'Singapore', a: 124}
            ],
            xkey: 'y',
            ykeys: ['a'],
            labels: ['Companies'],
            resize: true,
            barColors: ['#E6563C'],
            gridTextColor: ['#E6563C'],
            gridTextSize: 11,
            grid: false,
            axes: false
        });
    }


    if ($('#morris-widget-3').length > 0) {
        Morris.Area({
            element: 'morris-widget-3',
            data: [
                {y: '2006', a: 100, b: 90},
                {y: '2007', a: 75, b: 65},
                {y: '2008', a: 50, b: 40},
                {y: '2009', a: 75, b: 65},
                {y: '2010', a: 50, b: 40},
                {y: '2011', a: 75, b: 65},
                {y: '2012', a: 100, b: 90}
            ],
            xkey: 'y',
            ykeys: ['a', 'b'],
            labels: ['Series A', 'Series B'],
            resize: true,
            grid: false,
            axes: false,
            lineColors: ['#967ADC', '#D770AD']
        });
    }
    /** END WIDGET MORRIS JS FUNCTION **/


    /** BEGIN MY PHOTOS COLLECTION FUNCTION **/
    if ($('#photo-collection-1').length > 0) {
        $("#photo-collection-1").owlCarousel({
            navigation: false,
            pagination: false,
            slideSpeed: 1000,
            paginationSpeed: 400,
            singleItem: true,
            autoPlay: 3000,
            transitionStyle: 'fadeUp'
        });
    }
    /** BEGIN MY PHOTOS COLLECTION FUNCTION **/



    /** BEGIN WIDGET PIE FUNCTION **/
    if ($('.widget-easy-pie-1').length > 0) {
        $('.widget-easy-pie-1').easyPieChart({
            easing: 'easeOutBounce',
            barColor: '#3BAFDA',
            lineWidth: 10,
            onStep: function (from, to, percent) {
                $(this.el).find('.percent').text(Math.round(percent));
            }
        });
    }
    if ($('.widget-easy-pie-2').length > 0) {
        $('.widget-easy-pie-2').easyPieChart({
            easing: 'easeOutBounce',
            barColor: '#F6BA48',
            lineWidth: 10,
            onStep: function (from, to, percent) {
                $(this.el).find('.percent').text(Math.round(percent));
            }
        });
    }
    /** END WIDGET PIE FUNCTION **/



    if ($('#realtime-chart-widget').length > 0) {
        var data1 = [];
        var totalPoints = 250;
        function GetData() {
            "use strict";
            data1.shift();
            while (data1.length < totalPoints) {
                var prev = data1.length > 0 ? data1[data1.length - 1] : 50;
                var y = prev + Math.random() * 10 - 5;
                y = y < 0 ? 0 : (y > 100 ? 100 : y);
                data1.push(y);
            }
            var result = [];
            for (var i = 0; i < data1.length; ++i) {
                result.push([i, data1[i]])
            }
            return result;
        }
        var updateInterval = 500;
        var plot = $.plot($("#realtime-chart-widget #realtime-chart-container-widget"), [
            GetData()], {
            series: {
                lines: {
                    show: true,
                    fill: false
                },
                shadowSize: 0
            },
            yaxis: {
                min: 0,
                max: 100,
                ticks: 10,
                show: false
            },
            xaxis: {
                show: false
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#f9f9f9",
                borderWidth: 0,
                borderColor: "#eeeeee"
            },
            colors: ["#699B29"],
            tooltip: false,
            tooltipOpts: {
                defaultTheme: false
            }
        });
        function update() {
            "use strict";
            plot.setData([GetData()]);
            plot.draw();
            setTimeout(update, updateInterval);
        }
        update();
    }

    if ($('.chosen-select, .chosen-select-deselect, .chosen-select-no-single, .chosen-select-no-results, .chosen-select-width').length > 0) {
        "use strict";
        var configChosen = {
            '.chosen-select': {},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 10},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "100%"}
        }
        for (var selector in configChosen) {
            $(selector).chosen(configChosen[selector]);
        }
    }
    /** BEGIN TIMEPICKER **/
    if ($('.timepicker').length > 0) {
        $('.timepicker').timepicker();
    }
    /** END TIMEPICKER **/

});