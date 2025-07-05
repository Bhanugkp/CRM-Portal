<?php 
include('db_connect.php') ?>
<?php
$twhere ="";
if($_SESSION['login_role_id'] != 1)
  $twhere = "  ";
?>
<?php if($_SESSION['login_role_id'] == 1): ?>

<div class="row">
  <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
      <div class="inner">
        <h3>
         <?php echo $conn->query('SELECT count(id) as total_users FROM
          users;')->fetch_assoc()['total_users'];?><sup
            style="font-size: 20px"
          ></sup>
        </h3>
        <p>Total Users</p>
      </div>
      <div class="icon">
      <i class="fas fa-user-plus" aria-hidden="true"></i>
      </div>
      <a href="#" class="small-box-footer">
        More info <i class="fas fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
      <div class="inner">
        <h3>
          <?php echo $conn->query('SELECT count(id) as total FROM
          vidhansabha;')->fetch_assoc()['total'];?><sup
            style="font-size: 20px"
          ></sup>
        </h3>
        <p>Total vidhansabha</p>
      </div>
      <div class="icon">
      <i class="fa fa-envelope" aria-hidden="true"></i>
      </div>
      <a href="#" class="small-box-footer">
        More info <i class="fas fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>
          <?php echo $conn->query('SELECT count(id) as total FROM
          members;')->fetch_assoc()['total'];?>
        </h3>
        <p>Total Members</p>
      </div>
      <div class="icon">
        <i class="nav-icon fas fa-users"></i>
      </div>
      <a href="./index.php?page=all_list" class="small-box-footer">
        More info <i class="fas fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>
          <?php echo $conn->query('SELECT COUNT(*) AS members_count
FROM members
WHERE created_at >= NOW() - INTERVAL 1 DAY;
')->fetch_assoc()['members_count']; ?>
        </h3>
        <p>Member added today</p>
      </div>
      <div class="icon">
        <i class="fas fa-chart-pie"></i>
      </div>
      <a href="./index.php?page=list_page" class="small-box-footer">
        More info <i class="fas fa-arrow-circle-right"></i>
      </a>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header border-0">
        <div class="d-flex justify-content-between">
          <h3 class="card-title">सक्रिय लोकसभा</h3>
          <a href="index.php?page=all_list">View Report</a>
        </div>
      </div>
      <div class="card-body">
      <div class="position-relative mb-4">
        <canvas id="pieChart" style="width: 100%; height: 250px;"></canvas>
      </div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header border-0">
        <div class="d-flex justify-content-between">
          <h3 class="card-title">सक्रिय संगठन</h3>
          <a href="javascript:void(0);">View Report</a>
        </div>
      </div>
      <div class="card-body">
        <div class="position-relative mb-4">
          <canvas
            id="activeOrg"
            style="display: block; width: 100%; height: 250px"
            class=""
          ></canvas>
        </div>
      </div>
    </div>
  </div>

</div>

<?php else: ?>
<div class="col-12">
  <div class="card">
    <div class="card-body">
      Welcome
      <?php 
      $auth_name =  $conn->query('SELECT name FROM role WHERE id =
      '.$_SESSION['login_role_id'])->fetch_assoc()['name']; echo
      $_SESSION['login_fname'].' ('.$auth_name.')'; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?php


function getInitials($name) {
  // Split the full name into words
  $words = explode(" ", $name);

  // Get the first letter of each word and join them with a space
  $initials = "";
  foreach ($words as $word) {
      // Use mb_substr to handle multibyte characters like Hindi
      $initials .= mb_strtoupper(mb_substr($word, 0, 3)) . ". ";
  }

  // Trim any trailing spaces and return
  return trim($initials);
}



$query = $conn->query('SELECT 
count(m.id) as total, 
l.name as name
FROM members
AS m inner join vidhansabha as vid on vid.id = m.vidhansabha_id 
inner join loksabha as l on l.id =
m.loksabha_id 
group by l.name');
 $labels = []; 
 $data = [];
while ($row = $query->fetch_assoc()) { 
  $labels[] = $row['name'];
   $data[] = $row['total']; 
}
$labels_json = json_encode($labels);
$data_json = json_encode($data); 

$result = $conn->query('select count(m.id) as total, o.name from members as m
inner join organization as o on o.id = m.organization_id 
group by o.name');
$labels2 = [];
$data2 = [];
while ($row = $result->fetch_assoc()) {
  $labels2[] = $row['name'];
  $data2[] = $row['total'];
}

$labels_org = json_encode($labels2);
$data_org = json_encode($data2);
 ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const ctx = document.getElementById('pieChart');
  const activeOrg = document.getElementById('activeOrg');

  const labels = <?php echo $labels_json; ?>;
  const mdata = <?php echo $data_json; ?>;

  
  new Chart(ctx, {
  type: 'pie',
  data: {
    labels: labels,
    datasets: [{
      label: '# Member',
      data: mdata,
      backgroundColor: [
    'rgba(0, 124, 250, 1)', // Original color 1 (100% opacity)
    'rgba(0, 124, 250, 0.75)', // Original color 2 (75% opacity)
    'rgba(0, 124, 250, 0.50)', // Original color 3 (50% opacity)
    'rgba(0, 124, 250, 0.25)', // Original color 4 (25% opacity)
    
    'rgba(0, 124, 230, 1)',   // Slightly lighter blue (100% opacity)
    'rgba(0, 124, 230, 0.75)', // Slightly lighter blue (75% opacity)
    'rgba(0, 124, 230, 0.50)', // Slightly lighter blue (50% opacity)
    'rgba(0, 124, 230, 0.25)', // Slightly lighter blue (25% opacity)

    'rgba(0, 124, 210, 1)',   // Even lighter blue (100% opacity)
    'rgba(0, 124, 210, 0.75)', // Even lighter blue (75% opacity)
    'rgba(0, 124, 210, 0.50)', // Even lighter blue (50% opacity)
    'rgba(0, 124, 210, 0.25)', // Even lighter blue (25% opacity)

    'rgba(0, 124, 200, 1)',   // A softer shade of blue (100% opacity)
    'rgba(0, 124, 200, 0.75)', // A softer shade of blue (75% opacity)
    'rgba(0, 124, 200, 0.50)', // A softer shade of blue (50% opacity)
    'rgba(0, 124, 200, 0.25)', // A softer shade of blue (25% opacity)
      ],
      borderWidth: 5,
    }]
  },
  options: {
    maintainAspectRatio: false, // Disable the default aspect ratio behavior
  }
});




  // new Chart(activeOrg, {
  //   // type: 'bar',
  //   data: {
      
  //     labels: <?php echo $labels_org; ?>,
  //     datasets: [{
  //       backgroundColor: 'rgba(220,53,69,1)',
  //       type: 'bar',
  //       label: '# Member',
  //       data: <?php echo $data_org; ?>,
  //     }
  //   ]
  //   }
  // })
  new Chart(activeOrg, {
    type: 'bar', 
    data: {
        labels: <?php echo $labels_org; ?>,
        datasets: [
            {
                label: '# Member',
                backgroundColor: 'rgba(0, 123, 255, 1)',
                data: <?php echo $data_org; ?>, 
                barThickness: 30 
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            x: {
                grid: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Organizations' 
                }
            },
            y: {
                grid: {
                    display: true 
                },
                beginAtZero: true, 
                title: {
                    display: true,
                    text: '# Members' 
                }
            }
        },
        plugins: {
            legend: {
                position: 'bottom', 
            },
            tooltip: {
                enabled: true
            }
        }
    }
});

</script>
