 $('document').ready(function() {

        rcp_slider();
        $(window).resize(function () {
            rcp_slider();
        });
        function rcp_slider() {

            scr_width = $(window).width();

            if (scr_width < 768) {
                $('.detail-row').addClass('owl-carousel')
                $('.category-banner .detail-row').owlCarousel({
                    center: true,
                    items: 2.1,
                    loop: true,
                    margin: 30,
                });
            } else {

                $('.detail-row').trigger('destroy.owl.carousel');
                $('.detail-row').removeClass('owl-carousel');
            }
        }

    });