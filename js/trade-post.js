$('document').ready(function() {

    /*slider css start */
    trade_slider();
    $(window).resize(function() {
        trade_slider();
    });


    function trade_slider() {

        scr_width = $(window).width();

        if (scr_width < 601) {
            $('.ain-inner').on('initialized.owl.carousel changed.owl.carousel', function(e) {
                if (!e.namespace) {
                    return;
                }
                var carousel = e.relatedTarget;
                $('#slider-counter').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
            })
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