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

     avn_slider();
     $(window).resize(function() {
         avn_slider();
     });

     function avn_slider() {

         scr_width = $(window).width();
         if (scr_width < 768) {

             $('.avo-industry-detail').on('initialized.owl.carousel changed.owl.carousel', function(e) {
                 if (!e.namespace) {
                     return;
                 }
                 var carousel = e.relatedTarget;
                 $('#slider-counter').text(carousel.relative(carousel.current()) + 1 + '/' + carousel.items().length);
             })
             $('.avo-industry-detail').addClass('owl-carousel')
             $('.avo-industry-detail').owlCarousel({
                 center: true,
                 items: 1.4,
                 loop: true,
                 margin: 20,
             });
         } else {

             $('.avo-industry-detail').trigger('destroy.owl.carousel');
             $('.avo-industry-detail').removeClass('owl-carousel');
         }
     }

    });