<ul>
    <li <?php if(@$subtitle=='shopper_entries'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/shopper_program_entries/index.php">MANAGE SHOPPER ENTRIES</a>
    </li>
    <li <?php if(@$subtitle=='shopper_partner'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/shopper_partners/index.php">MANAGE SHOPPER PARTNERS</a>
    </li>
    <li <?php if(@$subtitle=='shopper_program'){?> class="active" <?php }?>>
        <a href="<?php echo ADMIN_URL?>/shopper_programs/index.php">PROGRAMS</a>
    </li>
</ul>