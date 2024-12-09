<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="">


  <div class="">
    <br><br>
    <section class="">
      <div class="">
        <div class="col-xs-12">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Database Management</h3>
            </div>
            <div class="box-body text-center">
              <p class="lead">Manage your database with the following options:</p>
              <a href="export_database" class="btn btn-lg btn-success">
                <i class="fa fa-database"></i> Export Database
              </a>
              <a href="view_database" class="btn btn-lg btn-info">
                <i class="fa fa-table"></i> View Database Tables
              </a>
              <form action="import_database" method="post" enctype="multipart/form-data" style="margin-top: 15px;">
                <input type="file" name="import_file" required>
                <button type="submit" class="btn btn-lg btn-primary">
                  <i class="fa fa-upload"></i> Import Database
                </button>
              </form>
              <form action="drop_database" method="post" style="margin-top: 15px;">
                <button type="submit" class="btn btn-lg btn-danger" onclick="return confirm('Are you sure you want to drop the database? This action is irreversible!');">
                  <i class="fa fa-trash"></i> Drop Database
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      
    </section>
  </div>

 
</div>
<?php include 'includes/scripts.php'; ?>


</body>
</html>
