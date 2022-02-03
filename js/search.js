$('document').ready(function() {

    /*tabs js start */
    $(".tabs li a").click(function(e) {
        e.preventDefault();
        var target = $(this).attr('data-rel');
        $('.tabs li a').removeClass('active');
        $(this).addClass('active');

        $("#" + target).show('search-tabs-content').siblings(".stc-inner").hide();
        return false;
        // $(".cont").addClass("hello")
    });
    /*tabs js end */

});