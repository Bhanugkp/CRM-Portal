<?php include('db_connect.php') ?>
<style>
  .dashboard-card {
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
  }
  .card-title {
    font-weight: 600;
  }
  .small-box {
    border-radius: 10px;
    color: white !important;
  }
  .small-box .icon {
    font-size: 70px;
    opacity: 0.3;
    transition: all 0.3s;
  }
  .small-box:hover .icon {
    opacity: 0.5;
  }
  .bg-custom-1 {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
  }
  .bg-custom-2 {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
  }
  .bg-custom-3 {
    background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
  }
  .bg-custom-4 {
    background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
  }
  .widget-user-header {
    height: 120px;
    padding-top: 1rem;
    border-top-left-radius: 10px !important;
    border-top-right-radius: 10px !important;
  }
  .widget-user-image {
    top: 80px;
  }
  .widget-user-desc {
    font-weight: 300;
  }
  .chart-container {
    position: relative;
    height: 250px;
  }
  .user-avatar {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
  }
  .nato {
    font-family: "Noto Sans Devanagari", sans-serif;
  }
  .stat-card {
    border-left: 4px solid;
  }
  .stat-card.primary {
    border-left-color: #4e73df;
  }
  .stat-card.success {
    border-left-color: #1cc88a;
  }
  .stat-card.warning {
    border-left-color: #f6c23e;
  }
  .stat-card.danger {
    border-left-color: #e74a3b;
  }
</style>

<?php if($_SESSION['login_role_id'] == 1): ?>
<div class="container-fluid">
  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
      <div class="small-box bg-custom-1 dashboard-card">
        <div class="inner">
          <h3><?php echo $conn->query('SELECT count(id) as total_users FROM users')->fetch_assoc()['total_users']; ?></h3>
          <p class="nato">कुल यूजर</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="#" class="small-box-footer nato">
          अधिक जानकारी <i class="fas fa-arrow-circle-right ml-1"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="small-box bg-custom-2 dashboard-card">
        <div class="inner">
          <h3><?php echo $conn->query('SELECT count(id) as total FROM vidhansabha')->fetch_assoc()['total']; ?></h3>
          <p class="nato">कुल विधानसभा</p>
        </div>
        <div class="icon">
          <i class="fas fa-landmark"></i>
        </div>
        <a href="#" class="small-box-footer nato">
          अधिक जानकारी <i class="fas fa-arrow-circle-right ml-1"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="small-box bg-custom-3 dashboard-card">
        <div class="inner">
          <h3><?php echo $conn->query('SELECT count(id) as total FROM members')->fetch_assoc()['total']; ?></h3>
          <p class="nato">कुल सदस्य</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-friends"></i>
        </div>
        <a href="./index.php?page=all_list" class="small-box-footer nato">
          अधिक जानकारी <i class="fas fa-arrow-circle-right ml-1"></i>
        </a>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
      <div class="small-box bg-custom-4 dashboard-card">
        <div class="inner">
          <h3><?php echo $conn->query('SELECT COUNT(*) AS members_count FROM members WHERE created_at >= NOW() - INTERVAL 1 DAY')->fetch_assoc()['members_count']; ?></h3>
          <p class="nato">आज जोड़े गए सदस्य</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <a href="./index.php?page=list_page" class="small-box-footer nato">
          अधिक जानकारी <i class="fas fa-arrow-circle-right ml-1"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- User Profile and Activity Chart -->
  <div class="row mb-4">
    <div class="col-md-4 mb-4">
      <div class="card card-widget widget-user dashboard-card">
        <div class="widget-user-header bg-danger">
          <h3 class="widget-user-username nato"><?php echo $_SESSION['login_fname'] ?></h3>
          <h5 class="widget-user-desc nato">प्रशासक</h5>
        </div>
        <div class="widget-user-image">
          <img class="img-circle elevation-2 bg-white" src="./uploads/members_photos/default.png" alt="User Avatar" />
        </div>
        <div class="card-footer bg-white">
          <div class="row">
            <div class="col-sm-6 border-right">
              <div class="description-block">
                <h5 class="description-header"><?php echo $conn->query('SELECT count(id) as total FROM users')->fetch_assoc()['total']; ?></h5>
                <span class="description-text nato">यूजर</span>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="description-block">
                <h5 class="description-header"><?php echo $conn->query('SELECT count(id) as total FROM members')->fetch_assoc()['total']; ?></h5>
                <span class="description-text nato">सदस्य</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-8 mb-4">
      <div class="card dashboard-card">
        <div class="card-header border-0">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title nato">सदस्य गतिविधि</h3>
            <a href="javascript:void(0);" class="nato">विस्तृत रिपोर्ट</a>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-3">
            <div>
              <span class="text-bold text-lg nato"><?php echo $conn->query('SELECT count(id) as total FROM members')->fetch_assoc()['total']; ?></span>
              <span class="text-muted nato" style="font-size: 12px">कुल सदस्य</span>
            </div>
            <div class="text-right">
              <span class="text-success">
                <i class="fas fa-arrow-up"></i> 12.5%
              </span>
              <span class="text-muted nato">पिछले सप्ताह की अपेक्षा</span>
            </div>
          </div>
          <div class="chart-container">
            <canvas id="visitors-chart"></canvas>
          </div>
          <div class="d-flex justify-content-end mt-2">
            <span class="mr-2 nato">
              <i class="fas fa-square text-primary"></i> वर्तमान सप्ताह
            </span>
            <span class="nato">
              <i class="fas fa-square text-gray"></i> पिछला सप्ताह
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Row -->
  <div class="row mb-4">
    <div class="col-md-6 mb-4">
      <div class="card dashboard-card">
        <div class="card-header border-0">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title nato">लोकसभा वितरण</h3>
            <a href="index.php?page=all_list" class="nato">विस्तृत रिपोर्ट</a>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="pieChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6 mb-4">
      <div class="card dashboard-card">
        <div class="card-header border-0">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title nato">संगठन अनुसार सदस्य</h3>
            <a href="javascript:void(0);" class="nato">विस्तृत रिपोर्ट</a>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container">
            <canvas id="activeOrg"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Top Users Table -->
  <?php 
  $sql = 'SELECT u.id, u.fname, r.name as role, COUNT(m.id) as total_member, n.photo AS photo 
          FROM users AS u
          LEFT JOIN members AS m ON u.id = m.ref_by
          LEFT JOIN members AS n ON u.member_id = n.id
          LEFT JOIN role AS r ON r.id = u.role_id
          GROUP BY u.id, u.fname ORDER BY COUNT(m.id) desc LIMIT 5';
  $result = $conn->query($sql); 
  if($result->num_rows > 0): ?>
  <div class="row">
    <div class="col-12">
      <div class="card dashboard-card">
        <div class="card-header border-0">
          <h3 class="card-title nato">शीर्ष सक्रिय यूजर</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-hover table-striped">
            <thead class="bg-light">
              <tr>
                <th class="nato">नाम</th>
                <th class="nato">पद</th>
                <th class="nato">जोड़े गए सदस्य</th>
                <th class="nato">कार्यवाही</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <img src="uploads/members_photos/<?php echo $row['photo'] ?? 'default.png'; ?>" 
                       class="user-avatar mr-2" 
                       alt="User Image">
                  <?php echo $row['fname'] ?? 'N/A'; ?>
                </td>
                <td><?php echo $row['role'] ?? 'N/A'; ?></td>
                <td>
                  <span class="badge bg-primary"><?php echo $row['total_member']; ?></span>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> देखें
                  </button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php else: ?>
<div class="col-12">
  <div class="card dashboard-card">
    <div class="card-body text-center py-5">
      <h3 class="nato">स्वागत है 
        <?php 
        $auth_name = $conn->query('SELECT name FROM role WHERE id = '.$_SESSION['login_role_id'])->fetch_assoc()['name']; 
        echo $_SESSION['login_fname'].' ('.$auth_name.')'; 
        ?>
      </h3>
      <p class="text-muted nato mt-2">आपके डैशबोर्ड में आपका स्वागत है</p>
      <a href="./index.php?page=member_list" class="btn btn-primary mt-3 nato">
        <i class="fas fa-users mr-1"></i> सदस्य प्रबंधन
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<?php
// Prepare data for charts
$query = $conn->query('SELECT count(m.id) as total, l.name as name FROM members AS m 
                      INNER JOIN vidhansabha as vid ON vid.id = m.vidhansabha_id 
                      INNER JOIN loksabha as l ON l.id = m.loksabha_id 
                      GROUP BY l.name'); 
$labels = []; 
$data = [];
while ($row = $query->fetch_assoc()) {
  $labels[] = $row['name'];
  $data[] = $row['total']; 
} 
$labels_json = json_encode($labels); 
$data_json = json_encode($data); 

$result = $conn->query('SELECT count(m.id) as total, o.name 
                       FROM members as m 
                       INNER JOIN organization as o ON o.id = m.organization_id 
                       GROUP BY o.name'); 
$labels2 = []; 
$data2 = []; 
while ($row = $result->fetch_assoc()) {
  $labels2[] = $row['name']; 
  $data2[] = $row['total']; 
} 
$labels_org = json_encode($labels2); 
$data_org = json_encode($data2); 

$sql = "SELECT d.date_added, COALESCE(COUNT(m.id), 0) AS total_users
        FROM (
          SELECT CURDATE() - INTERVAL (n.n) DAY AS date_added
          FROM (
            SELECT 0 AS n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL 
            SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
          ) n
        ) d
        LEFT JOIN members m ON DATE(m.created_at) = d.date_added
        GROUP BY d.date_added
        ORDER BY d.date_added";
$result = $conn->query($sql); 
$labels3 = []; 
$data3 = []; 
while ($row = $result->fetch_assoc()) {
  $labels3[] = date('D, M j', strtotime($row['date_added'])); 
  $data3[] = $row['total_users']; 
} 
$date_added = json_encode($labels3); 
$total_members = json_encode($data3); 
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
  // Pie Chart for Lok Sabha Distribution
  const ctx = document.getElementById('pieChart');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: <?php echo $labels_json; ?>,
      datasets: [{
        data: <?php echo $data_json; ?>,
        backgroundColor: [
          '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', 
          '#e74a3b', '#858796', '#5a5c69', '#00aaff',
          '#00cc99', '#ff9933', '#ff6666', '#9933ff'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
          labels: {
            font: {
              family: '"Noto Sans Devanagari", sans-serif'
            }
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });

  // Bar Chart for Active Organizations
  const activeOrg = document.getElementById('activeOrg');
  new Chart(activeOrg, {
    type: 'bar',
    data: {
      labels: <?php echo $labels_org; ?>,
      datasets: [{
        label: 'सदस्य संख्या',
        data: <?php echo $data_org; ?>,
        backgroundColor: '#4e73df',
        borderColor: '#4e73df',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'सदस्य संख्या',
            font: {
              family: '"Noto Sans Devanagari", sans-serif'
            }
          },
          grid: {
            display: false
          }
        },
        x: {
          title: {
            display: true,
            text: 'संगठन',
            font: {
              family: '"Noto Sans Devanagari", sans-serif'
            }
          },
          grid: {
            display: false
          }
        }
      },
      plugins: {
        legend: {
          display: false
        }
      }
    }
  });

  // Line Chart for Member Activity
  new Chart(document.getElementById('visitors-chart'), {
    type: 'line',
    data: {
      labels: <?php echo $date_added; ?>,
      datasets: [
        {
          label: 'वर्तमान सप्ताह',
          data: <?php echo $total_members; ?>,
          backgroundColor: 'rgba(78, 115, 223, 0.05)',
          borderColor: '#4e73df',
          pointBackgroundColor: '#4e73df',
          pointBorderColor: '#fff',
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#4e73df',
          pointHoverBorderColor: '#fff',
          pointHitRadius: 10,
          pointBorderWidth: 2,
          borderWidth: 2,
          fill: true
        },
        {
          label: 'पिछला सप्ताह',
          data: [0, 0, 0, 0, 0, 0, 0],
          backgroundColor: 'rgba(108, 117, 125, 0.05)',
          borderColor: '#6c757d',
          pointBackgroundColor: '#6c757d',
          pointBorderColor: '#fff',
          pointHoverRadius: 5,
          pointHoverBackgroundColor: '#6c757d',
          pointHoverBorderColor: '#fff',
          pointHitRadius: 10,
          pointBorderWidth: 2,
          borderWidth: 2,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          mode: 'index',
          intersect: false
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.1)'
          }
        }
      }
    }
  });
});
</script>