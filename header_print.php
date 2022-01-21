<?php
session_start();
require( 'manager/includes/pdo.php' );
require( 'check_login.php' );
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Avo Communication</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>

        <link href="/css/simple-calendar.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style_s.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style_g.css" rel="stylesheet" type="text/css"/>
        <link href="/css/responsive.css" rel="stylesheet" type="text/css"/>
        <link href="/css/print.css" rel="stylesheet" type="text/css"/>
        <script>window.print();</script>
    </head>
    <body>
        <div class="wrapper">

            <div class="clear"></div>

            <div class="" style="display:none;">

                <div id="google_translate_element"></div>

                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.HORIZONTAL}, 'google_translate_element');
                    }
                </script>

                <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
            </div>