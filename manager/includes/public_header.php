<nav id="topnavbar" class="navbar fixed-top navbar-expand-md navbar-dark mb-3">
  <div class="flex-row d-flex">
    <button type="button" class="navbar-toggler mr-2 " data-toggle="offcanvas" title="Toggle responsive left sidebar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#" title="AFM News">Admin CMS</a>
  </div>
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
      <li class="dropdown hidden-xs">
        <button class="navbar-account-btn" data-toggle="dropdown" aria-haspopup="true">
          <img class="circle" width="36" height="36" src="/manager/images/<?php echo $_SESSION['admin_avatar']; ?>" alt="<?php echo $_SESSION['admin_name']; ?>"> <?php echo $_SESSION['admin_name']; ?>
          <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
          <li><h5 class="navbar-company-heading"><?php echo $_SESSION['admin_company_name']; ?></h5></li>
          <li class="divider"></li>
          <li><a href="#">Edit My Profile</a></li>
          <li><a href="/manager/logout.php">Sign out</a></li>
        </ul>
      </li>
    </ul>

  </div>
</nav>