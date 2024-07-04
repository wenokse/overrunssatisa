<header class="main-header">
  <!-- Logo -->
  <a href="home.php" class="logo">
    <span class="logo-lg" style="font-size: 12px;"><b>Overruns Sa Tisa Online Shop</b></span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
         <!-- Notifications Menu -->
         <li class="dropdown notifications-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-bell-o"></i>
            <?php
            $conn = $pdo->open();
            $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM sales WHERE status = 0");
            $stmt->execute();
            $row = $stmt->fetch();
            $pending_count = $row['pending_count'];
            $pdo->close();
            
            if($pending_count > 0):
            ?>
            <span class="label label-danger"><?php echo $pending_count; ?></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu">
            <li class="header">You have <?php echo $pending_count; ?> pending orders</li>
            <li>
              <ul class="menu">
                <li>
                  <a href="sales.php">
                    <i class="fa fa-shopping-cart text-yellow"></i> <?php echo $pending_count; ?> new orders
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer"><a href="sales.php">View all</a></li>
          </ul>
        </li>
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo (!empty($admin['photo'])) ? '../images/'.$admin['photo'] : '../images/profile.jpg'; ?>" class="user-image" alt="User Image">
            <span class="hidden-xs"><?php echo $admin['firstname'].' '.$admin['lastname']; ?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="<?php echo (!empty($admin['photo'])) ? '../images/'.$admin['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">

              <p>
                <?php echo $admin['firstname'].' '.$admin['lastname']; ?>
                <small>Member since <?php echo date('M. Y', strtotime($admin['created_on'])); ?></small>
              </p>
            </li>
            <li class="user-footer">
              <div class="pull-left">
                <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" style="border-radius: 8px;" id="admin_profile">Update</a>
              </div>
              <div class="pull-right">
                <a href="../logout.php" class="btn btn-default btn-flat" style="border-radius: 8px;">Log out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
<?php include 'includes/profile_modal.php'; ?>