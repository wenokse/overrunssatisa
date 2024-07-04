<aside class="main-sidebar" style="background-color: white;">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar" style="background-color: white;">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <!-- <div class="pull-left image">
        <img src="<?php echo (!empty($admin['photo'])) ? '../images/'.$admin['photo'] : '../images/profile.jpg'; ?>" class="img-circle" alt="User Image">
      </div> -->
      <!-- <div class="pull-left info">
        <p><?php echo $admin['firstname'].' '.$admin['lastname']; ?></p>
        <a><i class="fa fa-circle text-success"></i> Online</a>
      </div> -->
    </div>
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
      <?php
        if($admin['type'] > 1){
          echo '
            <li class="header" style="background-color: white;">REPORTS</li>
            <li class="menu-item"><a href="home.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="menu-item"><a href="sales.php"><i class="fa fa-money"></i> <span>&nbsp;Sales</span></a></li>
            <li class="menu-item"><a href="inventory.php"><i class="fa fa-clipboard"></i> <span>&nbsp;Inventory</span></a></li>
            <li class="header">MAINTENANCE</li>
            <li class="menu-item"><a href="category.php"><i class="fa fa-th-list"></i> <span>Category</span></a></li>
            <li class="menu-item"><a href="products.php"><i class="fa fa-barcode"></i> <span>Product</span></a></li>
          ';
        }
        else{
          echo '
            <li class="header" style="background-color: white;">REPORTS</li>
            <li class="menu-item"><a href="home.php"><i class="fa fa-dashboard"></i> <span>&nbsp;Dashboard</span></a></li>
            <li class="menu-item"><a href="sales.php"><i class="fa fa-money"></i> <span>&nbsp;Sales</span></a></li>
            <li class="menu-item"><a href="inventory.php"><i class="fa fa-clipboard"></i> <span>&nbsp;Inventory</span></a></li>
            <li class="header" style="background-color: white;">MAINTENANCE</li>
            <li class="menu-item"><a href="customer.php"><i class="fa fa-users"></i> <span>&nbsp;Customer</span></a></li>
            <li class="menu-item"><a href="rider.php"><i class="fa fa-motorcycle"></i> <span>&nbsp;Rider</span></a></li>
            <li class="menu-item"><a href="category.php"><i class="fa fa-th-list"></i> <span>&nbsp;Category</span></a></li>
            <li class="menu-item"><a href="products.php"><i class="fa fa-barcode"></i> <span>&nbsp;Product</span></a></li>
            <li class="menu-item"><a href="return_product.php"><i class="fa fa-truck"></i> <span>&nbsp;Return</span></a></li>
            <li class="header" style="background-color: white;">SYSTEM</li>
            <li class="menu-item"><a href="admin_edit_image.php"><i class="fa fa-cog"></i> <span>&nbsp;Settings</span></a></li>
          ';
        }
      ?>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>

<style>
  
.main-sidebar {
  background-color: #343a40; /* Dark background for the sidebar */
  color: #ffffff; /* White text */
}

.sidebar-menu .header {
  color: white; /* Light gray text for headers */
  padding: 10px 15px;
  font-size: 50px;
}

.sidebar-menu .menu-item {
  border-radius: 8px; /* Rounded corners */
  background-color: #ffffff; /* White background */
  margin: 5px 10px; /* Margin for spacing between items */
/*  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Light shadow */
 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); /* Darker shadow */
}

.sidebar-menu .menu-item a {
  color: #343a40; /* Dark text for links */
  display: block;
  padding: 10px 8px;
  text-decoration: none; /* Remove underline */
}

.sidebar-menu .menu-item a:hover {
  background-color: #e9ecef; 
  color: #343a40; 
  transition: background-color 0.3s ease; 
  border-radius: 10px;
}
.sidebar-menu .menu-item.active a {
  border-radius: 10px; /* Changed border-radius when active */
  background-color: #e9ecef; 
  color: #343a40; 
}

</style>