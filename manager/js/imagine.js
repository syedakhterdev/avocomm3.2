$('[data-toggle=offcanvas]').click(function () {
    $('.row-offcanvas').toggleClass('active');
    $("html, body").animate({scrollTop: 0});
});

jQuery(document).ready(function () {

    jQuery('button[data-target="#collapsingNavbar"]').click(function () {
        if (jQuery('#collapsingNavbar').hasClass('show')) {
            jQuery('#main > div.row.row-offcanvas.row-offcanvas-left').removeClass('active-right');
        } else {
            jQuery('#main > div.row.row-offcanvas.row-offcanvas-left').addClass('active-right');
        }
    });

    jQuery('.custom_period').on('click', 'h2', function () {
        jQuery('.custom_period ul').toggle();
    });

    jQuery('.custom_period').on('click', 'ul li', function () {
        jQuery('.custom_period ul').toggle();
        jQuery('.custom_period h2 span').text(jQuery('span', this).text());
        jQuery('.custom_period h2 span').attr('data-val',jQuery('span', this).attr('data-val'));
        jQuery('.header_period').val(jQuery('span', this).attr('data-val')).change();
    });

    var temp = new URLSearchParams(window.location.search);
    var sortby = '';
    if(temp.has('sort')) {
        sortby = temp.get('sort')

        if(sortby == 'date_created') {
            jQuery('.col.main .mgr_body .table-responsive table tr th').eq(1).addClass('sortby_desc');
        } else if(sortby == 'title') {
            jQuery('.col.main .mgr_body .table-responsive table tr th').eq(0).addClass('sortby_asc');
        }
    }



});