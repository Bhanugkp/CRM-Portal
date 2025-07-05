<?php
require_once 'db_connect.php';
$result = $conn->query("
  SELECT concat(fname,' ',lname) as name, 
  u.email,
  u.phone,
  d.name as role 
  FROM users AS u
  INNER JOIN role AS d ON d.id = u.role_id
  WHERE u.id = " . $_SESSION['login_id']);
$row = $result->fetch_assoc();

// Get counts
$member_count = $conn->query('SELECT count(id) as total_users FROM members WHERE ref_by = ' . $_SESSION['login_id'])->fetch_assoc()['total_users'];
$user_count = $conn->query('SELECT count(id) as total_users FROM users WHERE ref_by = ' . $_SESSION['login_id'])->fetch_assoc()['total_users'];
?>

<style>
  .profile-card {
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: none;
    overflow: hidden;
  }
  .profile-header {
    background: linear-gradient(135deg, #1d74f7 0%, #3a8dff 100%);
    color: white;
    padding: 20px 0;
    text-align: center;
  }
  .profile-avatar {
    width: 120px;
    height: 120px;
    border: 4px solid rgba(255,255,255,0.3);
    margin: 0 auto;
  }
  .profile-name {
    font-weight: 600;
    margin-top: 15px;
    font-size: 1.5rem;
  }
  .profile-role {
    font-size: 0.9rem;
    opacity: 0.9;
  }
  .stats-card {
    border-radius: 10px;
    margin-top: 20px;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  .stats-item {
    border-left: none;
    border-right: none;
    padding: 15px;
    transition: all 0.3s;
  }
  .stats-item:hover {
    background-color: #f8f9fa;
  }
  .stats-label {
    font-weight: 500;
    color: #6c757d;
  }
  .stats-value {
    font-weight: 600;
    color: #1d74f7;
    font-size: 1.1rem;
  }
  .about-card {
    border-radius: 10px;
    margin-top: 20px;
    border: none;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  .about-header {
    background: white;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    font-weight: 600;
    padding: 15px 20px;
  }
  .about-item {
    padding: 15px 20px;
  }
  .about-icon {
    color: #1d74f7;
    margin-right: 10px;
    width: 20px;
    text-align: center;
  }
  .about-title {
    font-weight: 500;
    color: #495057;
  }
  .about-content {
    color: #6c757d;
    margin-top: 5px;
  }
  .divider {
    border-top: 1px solid rgba(0,0,0,0.05);
    margin: 10px 0;
  }
</style>

<div class="row">
  <div class="col-md-12">
    <!-- Profile Card -->
    <div class="card profile-card">
      <div class="profile-header">
        <img class="profile-avatar img-fluid img-circle"
          src="https://ui-avatars.com/api/?size=128&background=1d74f7&color=fff&name=<?php echo urlencode($row['name']); ?>&font-size=0.4&length=2&rounded=true"
          alt="User profile picture" />
        <h3 class="profile-name"><?php echo htmlspecialchars($row['name']); ?></h3>
        <p class="profile-role"><?php echo htmlspecialchars($row['role']); ?></p>
      </div>
      
      <!-- Stats Card -->
      <div class="card stats-card">
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item stats-item d-flex justify-content-between align-items-center">
              <span class="stats-label">Total Members Added</span>
              <span class="badge bg-primary stats-value"><?php echo $member_count; ?></span>
            </li>
            <li class="list-group-item stats-item d-flex justify-content-between align-items-center">
              <span class="stats-label">Total Users Added</span>
              <span class="badge bg-primary stats-value"><?php echo $user_count; ?></span>
            </li>
          </ul>
        </div>
      </div>

      <!-- About Card -->
      <div class="card about-card">
        <div class="about-header">
          <h3 class="card-title mb-0">About Me</h3>
        </div>
        <div class="card-body">
          <div class="about-item">
            <div class="d-flex align-items-center">
              <i class="fas fa-phone about-icon"></i>
              <span class="about-title">Phone</span>
            </div>
            <p class="about-content mt-2"><?php echo htmlspecialchars($row['phone']); ?></p>
          </div>
          
          <div class="divider"></div>
          
          <div class="about-item">
            <div class="d-flex align-items-center">
              <i class="fas fa-envelope about-icon"></i>
              <span class="about-title">Email</span>
            </div>
            <p class="about-content mt-2"><?php echo htmlspecialchars($row['email']); ?></p>
          </div>
          
          <div class="divider"></div>
          
          <div class="about-item">
            <div class="d-flex align-items-center">
              <i class="fas fa-info-circle about-icon"></i>
              <span class="about-title">Notes</span>
            </div>
            <p class="about-content mt-2">Nirbal India Soshit Hamara Aam Dal - NISHAD PARTY</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>