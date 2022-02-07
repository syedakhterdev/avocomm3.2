<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avocado</title>
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/admin_style_new.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/admin_responsive_new.css">
</head>

<body>
<div class="category">
    <!-- header sec start -->
    <header>
        <div class="container">
            <div class="header-inner">
                <a class="logo" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/logo.png" alt="logo"></a>
                <a><img class="line3" src="<?php echo ADMIN_URL?>/images/line2.png" alt="line2"></a>
                <a class="avo" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/avo.png" alt="avo"></a>
                <a href="<?php echo ADMIN_URL?>/manager/view_as_front.php" class="clb">
                    <img src="<?php echo ADMIN_URL?>/images/view-front-end-btn.png" onmouseover="this.src = '<?php echo ADMIN_URL?>/images/view-front-end-btn-hvr.png'" onmouseout="this.src = '<?php echo ADMIN_URL?>/images/view-front-end-btn.png'" alt="logout-submit-btn" />
                </a>
                <a class="avo-mobile" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/avo-mobile.png" alt="avo"></a>
                <a href="<?php echo ADMIN_URL?>/manager/logout.php" class="clb">
                    <img src="<?php echo ADMIN_URL?>/images/category-logout-btn.png" onmouseover="this.src = 'images/category-logout-hvr-btn.png'" onmouseout="this.src = 'images/category-logout-btn.png'" alt="logout-submit-btn" />
                </a>
                <a href="<?php echo ADMIN_URL?>/manager/logout.php" class="clmb"><img src="images/category-logout-mobile-btn.png" alt=""></a>
            </div>
            <img class="line1" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
            <img class="line2" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
        </div>
    </header>
    <!-- header sec end -->

    <div class="dashboard-sec">
        <div class="container">
            <div class="month_sec">

                    <select id="header_period" class="date" name="period_id" onChange="window.location.href = '<?php echo ADMIN_URL?>/manager/selPeriod.php?id=' + this.value;">
                        <?php
                        $sql = 'SELECT * FROM periods where active =? ORDER BY year ASC, month ASC';
                        $periods = $conn->query( $sql, array(1) );

                        if ( $conn->num_rows() > 0 ) {
                        while ( $period = $conn->fetch( $periods ) ) {
                        $disabled =   '';
                        if($period['lock_month']==1){
                            $disabled =   'disabled';
                        }
                            if ( (int)$_SESSION['admin_period_id'] == (int)$period['id'] )
                                echo '<option '.$disabled.' value="' . $period['id'] . '" SELECTED>' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . '</option>' . "\n";
                            else
                                echo '<option '.$disabled.' value="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . '</option>' . "\n";
                            }
                        }
                        ?>
                </select>
            </div>
            <div class="menu_sec">
                <ul>
                    <li class="active">
                        <a rel="noopener" href="javascript:void(0)">Dashboard</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">Trade</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">Shopper</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">News</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">Reports</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">Administrative</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>