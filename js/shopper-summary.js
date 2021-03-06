$('document').ready(function() {

    /*tabs js start */
    $(".tabs li a").click(function(e) {
        e.preventDefault();
        var target = $(this).attr('data-rel');
        $('.tabs li a').removeClass('active');
        $(this).addClass('active');

        $("#" + target).show('tabs-content').siblings(".tabs-content-inner").hide();
        return false;
        // $(".cont").addClass("hello")
    });
    /*tabs js end */

    /*slider css start */
    supporting_slider();
    $(window).resize(function() {
        supporting_slider();
    });

    function supporting_slider() {

        scr_width = $(window).width();

        if (scr_width < 768) {
            $('.tabs-content-inner').addClass('owl-carousel')
            $('.tabs-content-inner').owlCarousel({
                center: true,
                items: 1.4,
                loop: true,
                margin: 0,
            });
        } else {

            $('.tabs-content-inner').trigger('destroy.owl.carousel');
            $('.tabs-content-inner').removeClass('owl-carousel');
        }
    }
    /*slider css end */

    /*slider css start */
    kit_slider();
    $(window).resize(function() {
        kit_slider();
    });

    function kit_slider() {

        scr_width = $(window).width();

        if (scr_width < 768) {
            $('.kit-detail').on('initialized.owl.carousel changed.owl.carousel', function(e) {
                if (!e.namespace) {
                    return;
                }
                var carousel = e.relatedTarget;
                $('#slider-counter').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
            })
            $('.kit-detail').addClass('owl-carousel')
            $('.kit-detail').owlCarousel({
                center: true,
                items: 1.4,
                loop: true,
                margin: 0,
            });
        } else {

            $('.kit-detail').trigger('destroy.owl.carousel');
            $('.kit-detail').removeClass('owl-carousel');
        }
    }
    /*slider css end */

});