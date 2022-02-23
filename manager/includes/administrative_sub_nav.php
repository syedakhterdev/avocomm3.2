<ul>
    <li <?php if(@$subtitle=='users'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/users">Users</a>
    </li>
    <li <?php if(@$subtitle=='events'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/event_categories">Event Categories</a>
    </li>
    <li <?php if(@$subtitle=='periods'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/periods">Periods</a>
    </li>
    <li <?php if(@$subtitle=='activity_log'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/activity_log">Activity Log</a>
    </li>
    <li <?php if(@$subtitle=='admin_activity_log'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/admin_logs">Admin Activity Log</a>
    </li>
    <li <?php if(@$subtitle=='admins'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/Admins">Admins</a>
    </li>
    <li <?php if(@$subtitle=='broadcast'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/broadcast">Broadcast</a>
    </li>
</ul>