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
                margin: 20,
            });
        } else {

            $('.tabs-content-inner').trigger('destroy.owl.carousel');
            $('.tabs-content-inner').removeClass('owl-carousel');
        }
    }
    /*slider css end */

});