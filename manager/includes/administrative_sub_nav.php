<ul>
    <li <?php if(@$subtitle=='users'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/users/index.php">Users</a>
    </li>
    <li <?php if(@$subtitle=='events'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/event_categories/index.php">Event Categories</a>
    </li>
    <li <?php if(@$subtitle=='periods'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/periods/index.php">Periods</a>
    </li>
    <li <?php if(@$subtitle=='activity_log'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/activity_log/index.php">Activity Log</a>
    </li>
    <li <?php if(@$subtitle=='admin_activity_log'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/admin_logs/index.php">Admin Activity Log</a>
    </li>
    <li <?php if(@$subtitle=='admins'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/Admins/index.php">Admins</a>
    </li>
    <li <?php if(@$subtitle=='broadcast'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/broadcast/index.php">Broadcast</a>
    </li>
</ul>