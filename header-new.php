<?php
include('config.php');
require( 'manager/includes/pdo.php' );
require( 'check_login.php' );

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avo Communicator - Avocados From Mexico</title>
    <link rel="stylesheet" href="<?php echo SITE_URL?>/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL?>/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL?>/css/style_new.css">
    <link rel="stylesheet" href="<?php echo SITE_URL?>/css/responsive_new.css">
    <script src="<?php echo SITE_URL?>/js/jquery.min.js"></script>
    <script src="<?php echo SITE_URL?>/js/owl.carousel.min.js"></script>
</head>

<body>
<div class="category">
    <!-- header sec start -->
    <header>
        <div class="container">
            <div class="header-inner">
                <a class="logo" href="<?php echo SITE_URL?>"><img src="<?php echo SITE_URL?>/images/logo.png" alt="logo"></a>
                <img class="line3" src="<?php echo SITE_URL?>/images/line2.png" alt="line2">
                <a class="avo" href="<?php echo SITE_URL?>"><img src="<?php echo SITE_URL?>/images/avo.png" alt="avo"></a>
                <img class="line3" src="images/line2.png" alt="line2">
                <a class="avo-mobile" href="<?php echo SITE_URL?>"><img src="<?php echo SITE_URL?>/images/avo-mobile.png" alt="avo"></a>
                <a href="<?php echo SITE_URL?>/logout.php" class="clb">
                    <img src="<?php echo SITE_URL?>/images/category-logout-btn.png" onmouseover="this.src='<?php echo SITE_URL?>/images/category-logout-hvr-btn.png'" onmouseout="this.src='<?php echo SITE_URL?>/images/category-logout-btn.png'" alt="logout-submit-btn" />
                </a>
                <a href="<?php echo SITE_URL?>/logout.php" class="clmb"><img src="<?php echo SITE_URL?>/images/category-logout-mobile-btn.png" alt=""></a>
            </div>
            <img class="line1" src="<?php echo SITE_URL?>/images/line1.png" alt="line1">
            <img class="line2" src="<?php echo SITE_URL?>/images/line1.png" alt="line1">
        </div>
    </header>
    <!-- header sec end -->

    <!-- category-menu sec start -->
    <div class="category-menu">
        <div class="container">
            <form autocomplete="off" action="<?php echo SITE_URL?>/search_results.php" method="GET">
                <?php if($_SESSION['user_type']=='Normal'){?>
                <div class="form-group">
                    <?php echo '<script>var curPeriodText = "";</script>'; ?>
                    <select class="date" name="period_id" onChange="window.location.href = '/selPeriod.php?id=' + this.value;">
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
                <div class="form-group">
                    <select class="date" name="period_id">
                        <script>curPeriodText = "<?php echo $_SESSION['user_period_title']?>";</script>
                        <option SELECTED value="<?php echo $_SESSION['user_period_id']?>"><?php echo $_SESSION['user_period_title'];?> </option>
                    </select>
                </div>
                <?php }?>
                <div class="form-group">
                    <input type="search" onautocomplete="off" class="search" name="search" placeholder="What Are You Looking For?">
                    <img class="search-btn" src="images/search.png" alt="search">
                </div>
                <button class="category-btn" type="submit">Go!</button>
            </form>
        </div>
    </div>
    <!-- category-menu sec end -->