$('document').ready(function() {

    /*slider css start */
    report_slider();
    $(window).resize(function() {
        report_slider();
    });

    function report_slider() {

        scr_width = $(window).width();

        if (scr_width < 768) {
            $('.report-inner').addClass('owl-carousel')
            $('.report-inner').owlCarousel({
                center: true,
                items: 1.4,
                loop: true,
                margin: 0,
            });
        } else {

            $('.report-inner').trigger('destroy.owl.carousel');
            $('.report-inner').removeClass('owl-carousel');
        }
    }
    /*slider css end */

});