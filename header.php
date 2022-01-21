<?php
session_start();
require( 'manager/includes/pdo.php' );
require( 'check_login.php' );
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Avo Communicator - Avocados From Mexico</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
        <link rel="icon" href="/assets/cropped-favicon-150x150.png" sizes="32x32">
        <link href="/css/simple-calendar.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style_s.css" rel="stylesheet" type="text/css"/>
        <link href="/css/style_g.css" rel="stylesheet" type="text/css"/>
        <link href="/css/responsive.css" rel="stylesheet" type="text/css"/>

    </head>
    <body>
        <div class="wrapper">
            <header>
                <div class="container">
                    <div class="afm_logo">
                        <a href="/main.php"><img src="/images/afm_logo.png" alt="" /></a>
                    </div>
                    <div class="srch_dt_ml noprint">
                        <div class="srch">
                            <form action="/search_results.php" method="GET">
                                <input type="text" name="search" class="txt_scrh" placeholder="Search..."/>
                                <input type="submit" name="srch_btn" value="Go" />
                            </form>
                        </div>
                        <?php if($_SESSION['user_type']=='Normal'){?>
                        <div class="dt">
                            <?php echo '<script>var curPeriodText = "";</script>'; ?>
                            <select name="period_id" onChange="window.location.href = '/selPeriod.php?id=' + this.value;">
                                <!--option>Date</option-->
                                <?php
                                $sql = 'SELECT id, title FROM periods WHERE publish = 1 ORDER BY year ASC, month ASC';
                                $periods = $conn->query( $sql, array() );
                                if ( $conn->num_rows() > 0 ) {
                                    while ( $period = $conn->fetch( $periods ) ) {
                                        if ( (int)$_SESSION['user_period_id'] == (int)$period['id'] ) {
                                            echo '<option SELECTED value="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . '</option>' . "\n";
                                            echo '<script>curPeriodText = "' . $period['title'] . '";</script>';
                                        } else {
                                            echo '<option value="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . '</option>' . "\n";
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <?php } else{?>
                            <div class="dt">
                                <?php echo '<script>var curPeriodText = "";</script>'; ?>
                                <select name="period_id">
                                    <script>curPeriodText = "<?php echo $_SESSION['user_period_title']?>";</script>
                                    <option SELECTED value="<?php echo $_SESSION['user_period_id']?>"><?php echo $_SESSION['user_period_title'];?> </option>
                                </select>
                            </div>

                        <?php }?>

                        <div class="logout_btn">
                            <a href="/logout.php">Logout</a>
                        </div>
                        <div class="ml">
                            <a href="javascript:void(0)" class="en active" onclick="changeLanguageByButtonClicken()">en</a>
                            <a href="javascript:void(0)" class="es" onclick="changeLanguageByButtonClickes()">es</a>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                </div>
            </header>
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
