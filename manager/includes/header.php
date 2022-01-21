<nav id="topnavbar" class="navbar fixed-top navbar-expand-md navbar-dark mb-3">
  <!--<div class="flex-row d-flex">-->
    <button type="button" class="navbar-toggler mr-2 " data-toggle="offcanvas" title="Toggle responsive left sidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <!--<a class="navbar-brand" href="#" title="AFM News">Admin CMS</a>-->
    <a class="navbar-brand" href="/manager/menu.php" title="AFM News">
        <img src="/manager/images/avo_comm_hdr_logo.png" alt="" />
    </a>
  <!--</div>-->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsingNavbar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="navbar-collapse collapse" id="collapsingNavbar">

    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        &nbsp;
      </li>
    </ul>

    <ul class="nav navbar-nav navbar-right">
      <li class="admin_view_button">
          <a target="_blank" href="/manager/view_as_front.php"><button class="btn btn-primary">View As Front User <i style="color: black;" class="fa fa-eye" aria-hidden="true"></i></button></a>
      </li>
      <li>
        <select id="header_period" class="" name="period_id" onChange="window.location.href = '/manager/selPeriod.php?id=' + this.value;">
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
        <!--<div class="custom_period">
            <?php
/*            $sql = 'SELECT id, title FROM periods where lock_month=? ORDER BY year ASC, month ASC';
            $periods = $conn->query($sql, array(1));


            if ($conn->num_rows() > 0) {
                while ($period = $conn->fetch($periods)) {
                    if ((int) $_SESSION['admin_period_id'] == (int) $period['id']) {
                      echo '<h2><span data-val="' . $period['id'] . '">' . stripslashes( ucwords( strtolower( $period['title'] ) ) ) . '</span></h2>';
                    }
                }
            }


            $sql_list = 'SELECT id, title FROM periods where lock_month=? ORDER BY year ASC, month ASC';
            $periods_list = $conn->query($sql_list, array(1));

            echo '<ul>';
            if ($conn->num_rows() > 0) {
                while ($period_list = $conn->fetch($periods_list)) {
                    echo '<li><span data-val="' . $period_list['id'] . '">' . stripslashes(ucwords(strtolower($period_list['title']))) . '</span></li>';
                }
            }
            echo '</ul>';
            */?>
<!--            <style>
                .header_period {
                    display: none;
                }
                .custom_period {
                    position: relative;
                }
                .custom_period h2 {
                    cursor: pointer;
                    margin-bottom: 0px;
                    line-height: 35px;
                    padding: 2px 35px 0px 10px;
                    border-radius: 30px;
                    font-size: 16px;
                    font-family: 'AvantGarde-Demi';
                    border: solid 2px #017dc0;
                    color: #017dc0;
                    text-transform: uppercase;
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    background-color: transparent;
                    background-image: url(images/hdr_month_arrow.png);
                    background-repeat: no-repeat;
                    background-position: right 10px center;
                    min-width: 170px
                }
                .custom_period ul {
                    position: absolute;
                    background: #fff;
                    max-height: 250px;
                    overflow: auto;
                    display: none;
                    padding: 0px;
                    width: 170px;
                }
                .custom_period ul li {
                    list-style: none;
                    cursor: context-menu;
                    color: #017dc0;
                    font-size: 16px;
                    font-family: 'AvantGarde-Demi';
                    text-transform: uppercase;
                    padding: 2px 10px;
                }
                .custom_period ul li:hover {
                    background: #017dc0;
                    color: #fff;
                }
            </style>
        </div>-->
      </li>
      <li class="dropdown hidden-xs">
        <?php if ( $_SESSION['admin_photo'] ) { ?>
        <button class="navbar-account-btn" data-toggle="dropdown" aria-haspopup="true">
          <img class="circle" width="36" height="36" src="/manager/images/<?php echo $_SESSION['admin_avatar']; ?>" alt="<?php echo $_SESSION['admin_name']; ?>"> <?php echo $_SESSION['admin_name']; ?>
          <span class="caret"></span>
        </button>
      <?php } ?>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><h5 class="navbar-company-heading">AFM Communicator</h5></li>
          <li class="divider"></li>
          <li><a href="#">Edit My Profile</a></li>
          <li><a href="/manager/logout.php">Sign out</a></li>
        </ul>
      </li>
    </ul>

  </div>
</nav>