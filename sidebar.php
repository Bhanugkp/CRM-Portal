<?php 
require 'db_connect.php';
$auth = $_SESSION['login_role_id'];
$area_id = $_SESSION['login_area_id'];
?>

<style>
  /* Custom Sidebar Styles */
  .main-sidebar {
    background: linear-gradient(180deg,rgb(44, 44, 44) 0%,rgb(44, 44, 44) 100%);
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  }
  
  .brand-link {
    padding: 1.2rem 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    background: rgba(0,0,0,0.2);
  }
  
  .brand-link h3 {
    color: white;
    font-size: 1.4rem;
    letter-spacing: 1px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }
  
  .nav-sidebar .nav-item {
    margin: 0.3rem 0;
  }
  
  .nav-sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    border-radius: 0;
    padding: 0.8rem 1rem;
    transition: all 0.3s ease;
    position: relative;
    font-weight: 500;
  }
  
  .nav-sidebar .nav-link:hover {
    color: white;
    background: rgba(255,255,255,0.1);
  }
  
  .nav-sidebar .nav-link.active {
    color: white;
    background: rgba(255,255,255,0.15);
    border-left: 4px solid #1d74f7;
  }
  
  .nav-sidebar .nav-link.active .nav-icon {
    color: #1d74f7;
  }
  
  .nav-sidebar .nav-icon {
    margin-right: 0.5rem;
    font-size: 1.1rem;
    min-width: 20px;
    text-align: center;
  }
  
  .nav-sidebar .menu-open > .nav-link {
    color: white;
    background: rgba(255,255,255,0.1);
  }
  
  .nav-treeview {
    background: rgba(0,0,0,0.2);
    padding-left: 0;
  }
  
  .nav-treeview .nav-link {
    padding-left: 2.5rem;
    font-size: 0.95rem;
  }
  
  .nav-treeview .nav-icon {
    font-size: 0.8rem;
  }
  
  .sidebar {
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.2) transparent;
  }
  
  .sidebar::-webkit-scrollbar {
    width: 6px;
  }
  
  .sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255,255,255,0.2);
    border-radius: 3px;
  }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <div class="brand-link text-center">
    <?php if($_SESSION['login_role_id'] == 1): ?>
    <h3 class="p-0 m-0"><i class="fas fa-shield-alt mr-2"></i><b>ADMIN PANEL</b></h3>
    <?php else: ?>
    <h3 class="p-0 m-0"><i class="fas fa-user-tie mr-2"></i><b>OFFICER PANEL</b></h3>
    <?php endif; ?>
  </div>

  <!-- Sidebar -->
  <div class="sidebar pb-4 mb-4">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dashboard -->
        <li class="nav-item">
          <a href="./" class="nav-link nav-home">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <?php if($_SESSION['login_role_id'] == 1): ?>
        <!-- Admin Specific Menu -->
        <li class="nav-item">
          <a href="./all_list" class="nav-link nav-all_list">
            <i class="nav-icon fas fa-list-ol"></i>
            <p>All List</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link nav-edit_user">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>
              Officers
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./create_user" class="nav-link nav-create_user tree-item">
                <i class="fas fa-plus-circle nav-icon"></i>
                <p>Add New</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./user_list" class="nav-link nav-user_list tree-item">
                <i class="fas fa-list nav-icon"></i>
                <p>Officers List</p>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Member Management -->
        <li class="nav-item">
          <a href="#" class="nav-link nav-edit_member">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Member Management
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="./new_member" class="nav-link nav-new_member tree-item">
                <i class="fas fa-user-plus nav-icon"></i>
                <p>Add New Member</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./bulk_insert" class="nav-link nav-bulk_insert tree-item"> 
                <i class="fas fa-file-import nav-icon"></i>
                <p>Bulk Insert</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./member_list" class="nav-link nav-member_list tree-item">
                <i class="fas fa-list-ul nav-icon"></i>
                <p>Members List</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </div>
</aside>

<script>
  $(document).ready(function(){
    // Highlight active menu item
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
    
    if(s != '') {
      page = page + '_' + s;
    }
    
    if($('.nav-link.nav-'+page).length > 0){
      $('.nav-link.nav-'+page).addClass('active');
      
      if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
        $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active');
        $('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open');
      }
      
      if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
        $('.nav-link.nav-'+page).parent().addClass('menu-open');
      }
    }
    
    // Add smooth transition when opening submenus
    $('.nav-link').on('click', function() {
      if($(this).hasClass('collapsed')) {
        $(this).next('.nav-treeview').slideDown(200);
      } else {
        $(this).next('.nav-treeview').slideUp(200);
      }
    });
  });
</script>