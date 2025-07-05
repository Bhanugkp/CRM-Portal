<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark bg-primary shadow-sm">
  <!-- Left navbar links -->
  <div class="navbar-brand-wrapper d-flex align-items-center">
    <?php if(isset($_SESSION['login_id'])): ?>
    <button class="btn btn-link text-white" data-widget="pushmenu">
      <i class="fas fa-bars"></i>
    </button>
    <?php endif; ?>
    <a class="navbar-brand text-white ml-2" href="./">
      <span class="font-weight-bold" style="font-size: 1.2rem;">Nishad Party</span>
    </a>
  </div>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto align-items-center">
    <!-- Fullscreen Toggle -->
    <li class="nav-item">
      <a class="nav-link text-white" data-widget="fullscreen" href="#" role="button" title="Toggle Fullscreen">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- Notifications Dropdown (commented out but styled) -->
    <!-- <li class="nav-item dropdown">
      <a class="nav-link text-white position-relative" data-toggle="dropdown" href="#" title="Notifications">
        <i class="far fa-bell"></i>
        <span class="badge badge-danger badge-pill position-absolute" style="top: 5px; right: 5px; font-size: 0.6rem;">23</span>
      </a>
      <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right border-0 shadow">
        <span class="dropdown-header">23 Notifications</span>
        <div class="dropdown-divider"></div>
        <?php
        // Notification items would go here
        ?>
        <a href="./index.php?page=notification" class="dropdown-item">
          <div class="d-flex align-items-center">
            <i class="fas fa-envelope mr-2 text-primary"></i>
            <div>
              <div class="font-weight-bold">Notification Title</div>
              <small class="text-muted">3 mins ago</small>
            </div>
          </div>
        </a>
        <div class="dropdown-divider"></div>
        <a href="./index.php?page=notification" class="dropdown-item dropdown-footer text-center py-2">
          View All Notifications
        </a>
      </div>
    </li> -->

    <!-- User Profile Dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="d-flex align-items-center">
          <div class="avatar-circle-sm bg-white text-primary d-flex align-items-center justify-content-center mr-2">
            <?php echo strtoupper(substr($_SESSION['login_fname'], 0, 1)); ?>
          </div>
          <span class="font-weight-bold mr-1"><?php echo ucwords($_SESSION['login_fname']); ?></span>
          <!-- <i class="fas fa-caret-down ml-1"></i> -->
        </div>
      </a>
      <div class="dropdown-menu dropdown-menu-right border-0 shadow" aria-labelledby="userDropdown" style="min-width: 200px;">
        <div class="dropdown-header">
          <div class="d-flex align-items-center">
            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2">
              <?php echo strtoupper(substr($_SESSION['login_fname'], 0, 1)); ?>
            </div>
            <div>
              <div class="font-weight-bold"><?php echo ucwords($_SESSION['login_fname']); ?></div>
              <small class="text-muted">Administrator</small>
            </div>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item d-flex align-items-center" href="./profile">
          <i class="fas fa-cog mr-2 text-muted"></i> Manage Account
        </a>
        <a class="dropdown-item d-flex align-items-center" href="ajax.php?action=logout">
          <i class="fas fa-sign-out-alt mr-2 text-muted"></i> Logout
        </a>
      </div>
    </li>
  </ul>
</nav>

<style>
  /* Custom styles for the navbar */
  .navbar {
    padding: 0.5rem 1rem;
    min-height: 60px;
  }
  
  .navbar-brand-wrapper {
    height: 100%;
  }
  
  .avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .avatar-circle-sm {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.9rem;
  }
  
  .dropdown-header {
    padding: 0.5rem 1rem;
    background-color: #f8f9fa;
  }
  
  .dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s;
  }
  
  .dropdown-item:hover {
    background-color: #f8f9fa;
  }
  
  .bg-primary {
    background-color:rgb(44, 44, 44) !important;
  }
  
  .btn-link.text-white:hover {
    color: rgba(255,255,255,0.8) !important;
    text-decoration: none;
  }
</style>

<script>
  // Add active class to current nav item
  $(document).ready(function() {
    var url = window.location;
    $('ul.navbar-nav a').filter(function() {
      return this.href == url;
    }).parent().addClass('active');
  });
</script>