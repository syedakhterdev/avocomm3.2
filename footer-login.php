
</div>
<script>
    jQuery(document).ready(function () {

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
</script>
</body>
</html>
