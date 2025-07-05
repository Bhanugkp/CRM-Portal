<?php
// Secure session start
session_start([
    'cookie_lifetime' => 86400,
    'cookie_secure' => true,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Check authentication
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Get current user data
$user_id = $_SESSION['login_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Nishad Party</title>
    <?php include 'header.php'; ?>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --card-bg: rgba(255, 255, 255, 0.1);
            --card-border: rgba(255, 255, 255, 0.2);
        }

        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .settings-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .settings-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .settings-title {
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }

        .settings-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .card-header {
            background: rgba(0, 0, 0, 0.2);
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--card-border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--card-border);
            color: white;
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
            color: white;
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-danger {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #d91a6d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .avatar-container {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
            margin-right: 1.5rem;
        }

        .avatar-upload {
            display: flex;
            flex-direction: column;
        }

        .tab-content {
            padding: 1.5rem 0;
        }

        .nav-tabs {
            border-bottom: 1px solid var(--card-border);
        }

        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.7);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px 8px 0 0;
            margin-right: 0.5rem;
        }

        .nav-tabs .nav-link.active {
            color: white;
            background: rgba(67, 97, 238, 0.2);
            border-bottom: 3px solid var(--primary-color);
        }

        .nav-tabs .nav-link:hover {
            color: white;
            border-color: transparent;
        }

        .alert {
            border-radius: 8px;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .avatar-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .settings-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="settings-container">
        <div class="settings-header">
            <h1 class="settings-title">Account Settings</h1>
            <div id="save-status" class="d-none alert alert-success">Settings saved successfully!</div>
        </div>

        <div class="settings-card">
            <div class="card-header">
                <h3 class="card-title">Profile Information</h3>
            </div>
            <div class="card-body">
                <form id="profile-form">
                    <div class="avatar-container">
                        <img src="<?php echo htmlspecialchars($user['avatar'] ?? 'assets/img/default-avatar.png'); ?>" 
                             class="avatar" id="profile-avatar" alt="Profile Picture">
                        <div class="avatar-upload">
                            <input type="file" id="avatar-upload" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-primary mb-2" onclick="document.getElementById('avatar-upload').click()">
                                <i class="fas fa-camera mr-2"></i>Change Photo
                            </button>
                            <small class="text-muted">JPG, GIF or PNG. Max size 2MB</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="first-name">First Name</label>
                                <input type="text" class="form-control" id="first-name" 
                                       value="<?php echo htmlspecialchars($user['fname'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="last-name">Last Name</label>
                                <input type="text" class="form-control" id="last-name" 
                                       value="<?php echo htmlspecialchars($user['lname'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary" id="save-profile">
                        <span class="spinner-border spinner-border-sm d-none" id="profile-spinner"></span>
                        Save Profile
                    </button>
                </form>
            </div>
        </div>

        <div class="settings-card">
            <div class="card-header">
                <h3 class="card-title">Security Settings</h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="security-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="password-tab" data-toggle="tab" 
                                data-target="#password-tab-pane" type="button" role="tab">
                            Change Password
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="2fa-tab" data-toggle="tab" 
                                data-target="#2fa-tab-pane" type="button" role="tab">
                            Two-Factor Authentication
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="security-tabs-content">
                    <div class="tab-pane fade show active" id="password-tab-pane" role="tabpanel">
                        <form id="password-form">
                            <div class="form-group password-toggle">
                                <label class="form-label" for="current-password">Current Password</label>
                                <input type="password" class="form-control" id="current-password" required>
                                <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('current-password')"></i>
                            </div>

                            <div class="form-group password-toggle">
                                <label class="form-label" for="new-password">New Password</label>
                                <input type="password" class="form-control" id="new-password" minlength="8" required>
                                <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('new-password')"></i>
                                <small class="form-text text-muted">Minimum 8 characters with at least one number and one special character</small>
                            </div>

                            <div class="form-group password-toggle">
                                <label class="form-label" for="confirm-password">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm-password" required>
                                <i class="fas fa-eye password-toggle-icon" onclick="togglePassword('confirm-password')"></i>
                            </div>

                            <button type="submit" class="btn btn-primary" id="save-password">
                                <span class="spinner-border spinner-border-sm d-none" id="password-spinner"></span>
                                Change Password
                            </button>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="2fa-tab-pane" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Two-factor authentication adds an extra layer of security to your account.
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enable-2fa" 
                                    <?php echo ($user['two_factor_enabled'] ?? 0) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="enable-2fa">
                                    Enable Two-Factor Authentication
                                </label>
                            </div>
                        </div>

                        <div id="2fa-setup" class="<?php echo ($user['two_factor_enabled'] ?? 0) ? '' : 'd-none'; ?>">
                            <div class="text-center my-4">
                                <img src="assets/img/2fa-qr-placeholder.png" id="2fa-qr-code" 
                                     class="img-fluid" style="max-width: 200px;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Backup Codes</label>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Save these codes in a secure place. Each code can be used only once.
                                </div>
                                <div class="row" id="backup-codes">
                                    <div class="col-6 col-md-3 mb-2">ABCD-EFGH</div>
                                    <div class="col-6 col-md-3 mb-2">IJKL-MNOP</div>
                                    <div class="col-6 col-md-3 mb-2">QRST-UVWX</div>
                                    <div class="col-6 col-md-3 mb-2">YZ12-3456</div>
                                </div>
                            </div>

                            <button class="btn btn-danger" id="disable-2fa">
                                <i class="fas fa-lock-open mr-2"></i>Disable 2FA
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="card-header">
                <h3 class="card-title">Danger Zone</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    These actions are irreversible. Please proceed with caution.
                </div>

                <div class="form-group">
                    <button class="btn btn-danger" id="delete-account" data-toggle="modal" data-target="#confirm-delete-modal">
                        <i class="fas fa-trash mr-2"></i>Delete My Account
                    </button>
                    <small class="form-text text-muted">This will permanently delete your account and all associated data.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div class="modal fade" id="confirm-delete-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--card-border);">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Account Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>
                    <div class="form-group">
                        <label for="delete-confirm-password">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="delete-confirm-password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete Account</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(id) {
            const input = document.getElementById(id);
            const icon = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Handle avatar upload
        document.getElementById('avatar-upload').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-avatar').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle 2FA toggle
        document.getElementById('enable-2fa').addEventListener('change', function() {
            const setupDiv = document.getElementById('2fa-setup');
            if (this.checked) {
                setupDiv.classList.remove('d-none');
                // In a real app, you would generate a QR code here
            } else {
                setupDiv.classList.add('d-none');
            }
        });

        // Save profile form
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const spinner = document.getElementById('profile-spinner');
            const saveBtn = document.getElementById('save-profile');
            
            spinner.classList.remove('d-none');
            saveBtn.disabled = true;
            
            // Simulate AJAX save
            setTimeout(() => {
                spinner.classList.add('d-none');
                saveBtn.disabled = false;
                showSaveStatus();
            }, 1500);
        });

        // Save password form
        document.getElementById('password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const spinner = document.getElementById('password-spinner');
            const saveBtn = document.getElementById('save-password');
            
            spinner.classList.remove('d-none');
            saveBtn.disabled = true;
            
            // Simulate AJAX save
            setTimeout(() => {
                spinner.classList.add('d-none');
                saveBtn.disabled = false;
                showSaveStatus();
                this.reset();
            }, 1500);
        });

        // Show save status message
        function showSaveStatus() {
            const statusDiv = document.getElementById('save-status');
            statusDiv.classList.remove('d-none');
            setTimeout(() => {
                statusDiv.classList.add('d-none');
            }, 3000);
        }

        // Delete account confirmation
        document.getElementById('confirm-delete').addEventListener('click', function() {
            const password = document.getElementById('delete-confirm-password').value;
            if (!password) {
                alert('Please enter your password to confirm deletion');
                return;
            }
            
            // In a real app, you would send this to the server for verification
            console.log('Account deletion requested with password:', password);
            $('#confirm-delete-modal').modal('hide');
            alert('Account deletion request received. This would be processed by the server in a real application.');
        });
    </script>
</body>
</html>