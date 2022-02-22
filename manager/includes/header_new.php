<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="<?php echo ADMIN_URL?>/images/cropped-favicon-150x150.png" sizes="32x32">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avocado</title>
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/admin_style_new.css">
    <link rel="stylesheet" href="<?php echo ADMIN_URL?>/css/admin_responsive_new.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>


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
                <a href="<?php echo ADMIN_URL?>/view_as_front.php" class="clb">
                    <img src="<?php echo ADMIN_URL?>/images/view-front-end-btn.png" onmouseover="this.src = '<?php echo ADMIN_URL?>/images/view-front-end-btn-hvr.png'" onmouseout="this.src = '<?php echo ADMIN_URL?>/images/view-front-end-btn.png'" alt="logout-submit-btn" />
                </a>
                <a class="avo-mobile" href="<?php echo ADMIN_URL?>"><img src="<?php echo ADMIN_URL?>/images/avo-mobile.png" alt="avo"></a>
                <a href="<?php echo ADMIN_URL?>/logout.php" class="clb">
                    <img src="<?php echo ADMIN_URL?>/images/category-logout-btn.png" onmouseover="this.src = '<?php echo ADMIN_URL?>/images/category-logout-hvr-btn.png'" onmouseout="this.src = '<?php echo ADMIN_URL?>/images/category-logout-btn.png'" alt="logout-submit-btn" />
                </a>
                <a href="<?php echo ADMIN_URL?>/logout.php" class="clmb"><img src="<?php echo ADMIN_URL?>/images/category-logout-mobile-btn.png" alt=""></a>
            </div>
            <img class="line1" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
            <img class="line2" src="<?php echo ADMIN_URL?>/images/line1.png" alt="line1">
        </div>
    </header>
    <!-- header sec end -->

    <div class="dashboard-sec">
        <div class="container">
            <div class="month_sec">

                    <select id="header_period" class="date" name="period_id" onChange="window.location.href = '<?php echo ADMIN_URL?>/selPeriod.php?id=' + this.value;">
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
                    <li <?php if(@$title=='dashboard'){?> class="active" <?php }?>>
                        <a rel="noopener" href="<?php echo ADMIN_URL?>/menu.php">Dashboard</a>
                    </li>
                    <li <?php if(@$title=='trades'){?> class="active" <?php }?>>
                        <a href="<?php echo ADMIN_URL?>/vendors">Trade</a>
                    </li>
                    <li <?php if(@$title=='shoppers'){?> class="active" <?php }?>>
                        <a href="<?php echo ADMIN_URL?>/shopper_program_entries">Shopper</a>
                    </li>
                    <li <?php if(@$title=='news'){?> class="active" <?php }?>>
                        <a href="<?php echo ADMIN_URL?>/news">News</a>
                    </li>
                    <li <?php if(@$title=='reports'){?> class="active" <?php }?>>
                        <a href="<?php echo ADMIN_URL?>/reports">Reports</a>
                    </li>
                    <li <?php if(@$title=='administrative'){?> class="active" <?php }?>>
                        <a href="<?php echo ADMIN_URL?>/users/">Administrative</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>