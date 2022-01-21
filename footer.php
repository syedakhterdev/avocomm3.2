<div class="clear"></div>
<footer>
    <div class="container">
        <a href="/main.php" class="ft_afm_logo">
            <img src="/images/afm_logo.png" alt="" />
        </a>
    </div>
</footer>

<div class="clear"></div>
</div>

<!--div class="popup_bg" style="display: none;">
    <div class="container">
        <div class="vdo_popup">
            <div class="pop_hdr">
                <div class="icon">
                    <img src="/images/mp_icon.png" alt="" />
                </div>
                <div class="download">
                    <a href="javascript:void(0)" download>Download</a>
                </div>
                <div class="close">
                    <strong>Close X</strong>
                </div>
                <div class="clear"></div>
            </div>
            <div class="pop_vdo_sec">
                <a href="javascript:void(0)">
                    <img src="/images/video_img.jpg" alt="" />
                </a>
            </div>
            <div class="pop_vdo_cnt">
                <h2>Heading Goes Here</h2>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,Lorem.</p>
            </div>
            <div class="pop_vdo_nav">
                <a href="javascript:void(0)" class="prev">Previous</a>
                <a href="javascript:void(0)" class="next">Next</a>
            </div>
        </div>
    </div>
</div-->

<script src="/js/custom-select.js"></script>

<script>
    var period_id = <?php echo $_SESSION['user_period_id']; ?>;
    $('.select-styled').text(curPeriodText);
    //$('#psel_' + period_id).addClass('active');
    jQuery(document).ready(function () {
        /*jQuery('.vdo_popup .pop_hdr .close strong').click(function () {
            jQuery('.popup_bg').fadeOut();
            return false;
        });
        jQuery('.avo_shpsdou_img a ').click(function () {
            jQuery('.popup_bg').fadeIn();
            jQuery("html, body").animate({scrollTop: 0}, "slow");
            return false;
        });*/

        jQuery('.tabs div.call_action').click(function () {
            jQuery('.fdhubpage .call_action').addClass('deactive');
            jQuery(this).addClass('current').removeClass('deactive');

            var tab_id = jQuery(this).attr('data-tab');

            jQuery('.tabs div').removeClass('current');
            jQuery('.fdhubpg-logos-sec').removeClass('current');

            jQuery(this).addClass('current');
            jQuery("#" + tab_id).addClass('current');

            jQuery('html, body').animate({
                scrollTop: jQuery("#" + tab_id).offset().top
            }, 500);

            return false;
        });

//        jQuery(window).on('load', function () {
//            changeLanguageByButtonClickes();
//        });

        jQuery('.ml .en').click(function() {
            jQuery('.ml a').removeClass('active');
            jQuery('.ml .en').addClass('active');
        });
        jQuery('.ml .es').click(function() {
            jQuery('.ml a').removeClass('active');
            jQuery('.ml .es').addClass('active');
        });

    });

    function changeLanguageByButtonClicken() {
        var language = 'en';
        var selectField = document.querySelector("#google_translate_element select");
        for (var i = 0; i < selectField.children.length; i++) {
            var option = selectField.children[i];
            // find desired langauge and change the former language of the hidden selection-field
            if (option.value == language) {
                selectField.selectedIndex = i;
                // trigger change event afterwards to make google-lib translate this side
                selectField.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
    function changeLanguageByButtonClickes() {
        var language = 'es';
        var selectField = document.querySelector("#google_translate_element select");
        for (var i = 0; i < selectField.children.length; i++) {
            var option = selectField.children[i];
            // find desired langauge and change the former language of the hidden selection-field
            if (option.value == language) {
                selectField.selectedIndex = i;
                // trigger change event afterwards to make google-lib translate this side
                selectField.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
    
    /*jQuery(document).ready(function () {
        
        if(curPeriodText == '') {
            curPeriodText = "September 2019";
            jQuery('[id="psel_9"]').trigger('click');
        }
        
    });*/
    
</script>
</body>
</html>
<?php
$conn->close();
?>