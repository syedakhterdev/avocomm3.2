<div class="col-md-3 col-lg-3 sidebar-offcanvas pl-0" id="sidebar" role="navigation">
    <ul class="nav flex-column sticky-top">
        <li class="nav-item dashboard">
            <a href="/manager/menu.php" class="nav-link">Dashboard</a>
        </li>


        <?php if ((int) $_SESSION['admin_permission_trade']) { ?>
            <li class="nav-item trade">
                <a href="#submenu1" class="nav-link" style="border-bottom: 1px solid #3E495A;" data-toggle="collapse" data-target="#submenu_tr">Trade</a>
                <ul class="list-unstyled flex-column pl-3 collapse submenu" id="submenu_tr" aria-expanded="false">

                    <li class="nav-item"><a href="/manager/trade_vendor_entries/index.php" class="nav-link">Entries *</a></li>
                    <li class="nav-item"><a href="/manager/trade_vendor_entries/add.php" class="nav-link">Add an Entry</a></li>

                    <?php if ((int) $_SESSION['admin_sa']) { ?>
                        <li class="nav-item"><a href="/manager/vendors/index.php" class="nav-link">Vendors *</a></li>
                    <?php } ?>

                </ul>
            </li>
        <?php } ?>



        <li class="nav-item shopper">
            <?php if ((int) $_SESSION['admin_permission_shopper_hub']) { ?>
                <a href="#submenu1" class="nav-link" style="border-bottom: 1px solid #3E495A;" data-toggle="collapse" data-target="#submenu_shopper">Shopper</a>
                <ul class="list-unstyled flex-column pl-3 collapse submenu" id="submenu_shopper" aria-expanded="false">

                    <li class="nav-item"><a href="/manager/shopper_program_entries/index.php" class="nav-link">Entries *</a></li>
                    <li class="nav-item"><a href="/manager/shopper_program_entries/add.php" class="nav-link">Add an Entry</a></li>

                    <li class="nav-item"><a href="/manager/shopper_partners/index.php" class="nav-link">Partners</a></li>

                    <?php if ((int) $_SESSION['admin_sa']) { ?>
                        <li class="nav-item"><a href="/manager/shopper_programs/index.php" class="nav-link">Programs</a></li>
                    <?php } ?>

                </ul>
            <?php } ?>
        </li>

        <!--li class="nav-item foodservice">
            <?php if ((int) $_SESSION['admin_permission_fs_hub']) { ?>
                <a href="#submenu1" class="nav-link" style="border-bottom: 1px solid #3E495A;" data-toggle="collapse" data-target="#submenu_fs">Foodservice</a>
                <ul class="list-unstyled flex-column pl-3 collapse submenu" id="submenu_fs" aria-expanded="false">

                    <li class="nav-item"><a href="/manager/fs_program_entries/index.php" class="nav-link">Entries *</a></li>
                    <li class="nav-item"><a href="/manager/fs_program_entries/add.php" class="nav-link">Add an Entry</a></li>

                    <?php if ((int) $_SESSION['admin_sa']) { ?>
                        <li class="nav-item"><a href="/manager/fs_programs/" class="nav-link">Programs</a></li>
                    <?php } ?>

                </ul>
            <?php } ?>
        </li-->

        <?php if ((int) $_SESSION['admin_permission_news']) { ?>
            <li class="nav-item news">
                <a href="/manager/news/index.php" class="nav-link">News *</a>
            </li>
        <?php } ?>

        <?php if ((int) $_SESSION['admin_permission_events']) { ?>
            <li class="nav-item event">
                <a href="/manager/events/index.php" class="nav-link">Events</a>
            </li>
        <?php } ?>

        <?php if ((int) $_SESSION['admin_permission_reports']) { ?>
            <li class="nav-item report">
                <a href="/manager/reports/index.php" class="nav-link">Reports *</a>
            </li>
        <?php } ?>

        <li class="nav-item admin">
            <a href="#submenu1" class="nav-link" style="border-bottom: 1px solid #3E495A;" data-toggle="collapse" data-target="#submenu_adm">Administrative</a>
            <ul class="list-unstyled flex-column pl-3 collapse submenu" id="submenu_adm" aria-expanded="false">

                <?php if ((int) $_SESSION['admin_permission_users']) { ?>
                    <li class="nav-item"><a href="/manager/users/index.php" class="nav-link">Users</a></li>
                <?php } ?>

                <?php if ((int) $_SESSION['admin_permission_events']) { ?>
                    <li class="nav-item"><a href="/manager/event_categories/index.php" class="nav-link">Event Categories</a></li>
                <?php } ?>

                <?php if ((int) $_SESSION['admin_permission_periods']) { ?>
                    <li class="nav-item"><a href="/manager/periods/index.php" class="nav-link">Periods</a></li>
                <?php } ?>

                <?php if ((int) $_SESSION['admin_sa']) { ?>
                    <li class="nav-item"><a href="/manager/activity_log/index.php" class="nav-link">Activity Log</a></li>
                <?php } ?>

                <?php if ((int) $_SESSION['admin_sa']) { ?>
                    <li class="nav-item"><a href="/manager/admin_logs/index.php" class="nav-link">Admin Activity Log</a></li>
                <?php } ?>

                <?php if ((int) $_SESSION['admin_sa']) { ?>
                    <li class="nav-item"><a href="/manager/Admins/index.php" class="nav-link">Admins</a></li>
                <?php } ?>
                <?php if ((int) $_SESSION['admin_sa']) { ?>
                    <li class="nav-item"><a href="/manager/broadcast/index.php" class="nav-link">BroadCast</a></li>
                <?php } ?>

            </ul>

            <!-- /.nav-second-level -->
        </li>
        <li class="nav-item logout"><a href="/manager/logout.php" class="nav-link">Logout</a></li>
    </ul>
    <!-- /#side-menu -->
</div>
<!-- /#sidebar -->
