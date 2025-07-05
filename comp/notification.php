<?php include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_type'] != 1)
  $twhere = "  ";
?>

<?php if($_SESSION['login_type'] == 1): ?>

<div class="row">
  <div class="col-lg-12">
    <div class="card card-outline card-primary">
      <div class="card-header">
        <div class="card-tools">
        </div>
      </div>
      <div class="card-body">
      <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Type</th>

                    </tr>
                </thead>
                <tbody>
                    <?php 
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>

                    </tr>
                    <?php 
                    ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
<div class="col-12">
  <div class="card">
    <div class="card-body">Unauthorise Access</div>
  </div>
</div>

<?php endif; ?>
