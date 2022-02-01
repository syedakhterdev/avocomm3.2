$('document').ready(function() {

    /*slider css start */
    trade_slider();
    $(window).resize(function() {
        trade_slider();
    });

    function trade_slider() {

        scr_width = $(window).width();

        if (scr_width < 768) {
            $('.ain-inner').addClass('owl-carousel')
            $('.ain-inner').owlCarousel({
                center: true,
                items: 1.4,
                loop: true,
                margin: 0,
            });
        } else {

            $('.ain-inner').trigger('destroy.owl.carousel');
            $('.ain-inner').removeClass('owl-carousel');
        }
    }
    /*slider css end */

});