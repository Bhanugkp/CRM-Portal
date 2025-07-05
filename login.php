<?php
// Start session with secure settings
session_start([
  'cookie_lifetime' => 86400, // 1 day
  'cookie_secure' => true,
  'cookie_httponly' => true,
  'cookie_samesite' => 'Strict'
]);

// Redirect if already logged in
if (isset($_SESSION['login_id'])) {
  header("Location: index.php?page=home");
  exit();
}

// Include database connection with error handling
$db_file = './db_connect.php';
if (!file_exists($db_file)) {
  die("System configuration error");
}
include($db_file);

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login | Nishad Party</title>
  <!-- Security headers -->
  <meta http-equiv="Content-Security-Policy"
    content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:">
  <?php include 'header.php'; ?>
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3a0ca3;
      --accent-color: #f72585;
      --light-color: #f8f9fa;
      --dark-color: #212529;
    }
    
    body {
      background: linear-gradient(135deg, #961313 0%,rgb(0, 0, 0) 100%);
      color: white;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .login-box {
      width: 100%;
      max-width: 420px;
      margin: 0 auto;
      animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .login-logo {
      text-align: center;
      margin-bottom: 1rem;
      margin-top: 5rem;
    }
    
    .login-logo img {
      width: 80px;
      height: auto;
      margin-bottom: 0.5rem;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }
    
    .login-logo a {
      font-size: 1.8rem;
      font-weight: 700;
      color: white;
      text-decoration: none;
      display: block;
      margin-top: 0.5rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .login-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }
    
    .card-body {
      padding: 1rem;
    }
    
    .input-group {
      margin-bottom: 1rem;
      position: relative;
    }
    
    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      height: 50px;
      border-radius: 8px;
      padding-left: 45px;
      transition: all 0.3s;
    }
    
    .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
      color: white;
    }
    
    .input-group-text {
      background: transparent;
      border: none;
      position: absolute;
      left: 0;
      top: 0;
      height: 100%;
      z-index: 4;
      color: rgba(255, 255, 255, 0.7);
      padding: 0 15px;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      font-weight: 600;
      padding: 12px;
      border-radius: 8px;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
      transform: translateY(-2px);
    }
    
    .btn-block {
      display: block;
      width: 100%;
    }
    
    .icheck-primary input:checked ~ label::before {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .forgot-password {
      color: rgba(255, 255, 255, 0.7);
      text-decoration: none;
      transition: color 0.3s;
      display: inline-block;
      margin-top: 1rem;
    }
    
    .forgot-password:hover {
      color: white;
      text-decoration: underline;
    }
    
    .alert {
      border-radius: 8px;
    }
    
    #attempts-warning {
      background-color: rgba(220, 53, 69, 0.2);
      border-color: rgba(220, 53, 69, 0.3);
      color: white;
    }
    
    @media (max-width: 576px) {
      .login-box {
        padding: 0 20px;
      }
      
      .card-body {
        padding: 1rem;
      }
    }
  </style>
</head>

<body>
  <div class="login-box">
    <div class="login-logo">
      <div class="logo">
        <img src="assets/img/logo.png" width="120" alt="Nishad Party Logo" loading="lazy">
      </div>
      <a href="#"><b>Nishad Party</b></a>
    </div>

    <!-- Login Card -->
    <div class="card login-card">
      <div class="card-body">
        <form id="login-form" autocomplete="on">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          
          <!-- Email Field -->
          <div class="input-group mb-4">
            <input type="email" class="form-control" name="email" required placeholder="Email" autocomplete="username"
              inputmode="email">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>

          <!-- Password Field -->
          <div class="input-group mb-4">
            <input type="password" class="form-control" name="password" required placeholder="Password"
              autocomplete="current-password" minlength="4">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>

          <!-- Remember Me & Submit -->
          <div class="row align-items-center mb-3">
            <div class="col-6">
              <div class="icheck-primary">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me</label>
              </div>
            </div>
            <div class="col-6 text-right">
              <button type="submit" class="btn btn-primary btn-block" id="login-btn">
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                <span class="btn-text">Sign In</span>
              </button>
            </div>
          </div>

          <!-- Forgot Password Link -->
          <!-- <div class="text-center">
            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
          </div> -->
        </form>

        <!-- Error Message Container -->
        <div id="error-message" class="mt-3"></div>
      </div>
    </div>

    <!-- Login Attempts Warning -->
    <div class="alert alert-warning mt-3 d-none" id="attempts-warning">
      <i class="fas fa-exclamation-triangle mr-2"></i>Too many login attempts. Please try again later.
    </div>
  </div>

  <script>
    $(document).ready(function () {
      // Rate limiting variables
      let loginAttempts = 0;
      const maxAttempts = 5;
      let lastAttemptTime = 0;
      const attemptDelay = 30000; // 30 seconds

      $('#login-form').submit(function(e) {
        e.preventDefault();
        
        // Check rate limiting
        const now = Date.now();
        if (loginAttempts >= maxAttempts && (now - lastAttemptTime) < attemptDelay) {
          $('#attempts-warning').removeClass('d-none');
          return;
        }
        
        // Show loading state
        $('#login-btn').prop('disabled', true);
        $('#login-btn .spinner-border').removeClass('d-none');
        $('#login-btn .btn-text').text('Signing In...');
        
        $.ajax({
          url: 'ajax.php?action=login',
          method: 'POST',
          data: $(this).serialize(),
          dataType: 'json'
        })
        .done(function(resp) {
          if (resp.status === 'success') {
            // Reset attempts on success
            loginAttempts = 0;
            location.href = resp.redirect;
          } else {
            loginAttempts++;
            lastAttemptTime = Date.now();
            
            // Show error message
            $('#error-message').html(
              `<div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle mr-2"></i>${resp.message || 'Invalid credentials'}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>`
            );
            
            // Shake animation for error
            $('.login-card').css('animation', 'shake 0.5s');
            setTimeout(() => {
              $('.login-card').css('animation', '');
            }, 500);
          }
        })
        .fail(function() {
          $('#error-message').html(
            `<div class="alert alert-danger">
              <i class="fas fa-exclamation-circle mr-2"></i>Network error occurred. Please try again.
            </div>`
          );
        })
        .always(function() {
          // Reset button state
          $('#login-btn').prop('disabled', false);
          $('#login-btn .spinner-border').addClass('d-none');
          $('#login-btn .btn-text').text('Sign In');
        });
      });

      // Clear attempts warning after delay
      setInterval(function () {
        const now = Date.now();
        if (loginAttempts >= maxAttempts && (now - lastAttemptTime) >= attemptDelay) {
          loginAttempts = 0;
          $('#attempts-warning').addClass('d-none');
        }
      }, 1000);
    });
  </script>

  <?php include 'footer.php'; ?>
</body>

</html>