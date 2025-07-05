<?php
session_start();

// Error handling configuration
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

class Action
{
    private $db;

    public function __construct()
    {
        include 'db_connect.php';
        $this->db = $conn;

        if (!$this->db) {
            throw new RuntimeException('Database connection failed');
        }
    }

    public function __destruct()
    {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Secure login with password hashing and CSRF protection
     */
    public function login()
    {
        // Verify CSRF token first (already handled by ajax.php)

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        }

        // Rest of your login logic...
        $qry = $this->db->prepare("SELECT id, fname, lname, email, phone, role_id, organization_id, area_id 
                                FROM users 
                                WHERE email = ? AND password = ? AND is_active = 1");
        $hashed_password = md5($password); // Note: Consider upgrading to password_hash()
        $qry->bind_param("ss", $email, $hashed_password);

        if ($qry->execute()) {
            $result = $qry->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                foreach ($user as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }
                // Regenerate session ID after successful login
                session_regenerate_id(true);
                return json_encode(['status' => 'success', 'redirect' => 'index.php?page=home']);
            }
        }

        return json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
    }

    public function logout()
    {
        // Clear session data
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        header("Location: login.php");
        exit;
    }

    /**
     * Save member with validation and sanitization
     */
    public function save_member(): array {
        // 1. Initialize response
        $response = [
            'status' => 'error',
            'message' => 'Unknown error occurred',
            'data' => null
        ];

        // 2. CSRF Protection (Uncomment if needed)
        // if (!$this->validateCsrfToken()) {
        //     $response['message'] = 'Invalid CSRF token';
        //     return $response;
        // }

        // 3. Sanitize and Validate Inputs
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? 0;
        $fname = $this->sanitizeInput($_POST['fname'] ?? '');
        $lname = $this->sanitizeInput($_POST['lname'] ?? '');
        $phone = $this->sanitizePhone($_POST['phone'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';

        // Validate required fields
        if (empty($fname) || empty($lname) || empty($phone)) {
            $response['message'] = 'First name, last name, and phone are required';
            return $response;
        }

        // Validate phone format
        if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
            $response['message'] = 'Invalid phone number format';
            return $response;
        }

        // Validate email (if provided)
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format';
            return $response;
        }

        // 4. Process other fields (with defaults where needed)
        $gender = filter_input(INPUT_POST, 'gender', FILTER_VALIDATE_INT) ?? 1;
        // $qualification = (int)$_POST['qualification'] ?? 8;
        $qualification = !empty($_POST['qualification']) ? (int)$_POST['qualification'] : 8;
        $marital_status = filter_input(INPUT_POST, 'marital_status', FILTER_VALIDATE_INT) ?? 1;
        
        // Date of Birth (convert from d/m/Y to Y-m-d)
        $dob = "1956-01-01"; // Default fallback
        if (!empty($_POST['dob'])) {
            $date = DateTime::createFromFormat('d/m/Y', $_POST['dob']);
            $dob = $date ? $date->format('Y-m-d') : $dob;
        }

        // Geographical data
        $state = filter_input(INPUT_POST, 'state', FILTER_VALIDATE_INT);
        $state = ($state !== false && $state !== null) ? $state : 3;

        $district = filter_input(INPUT_POST, 'district', FILTER_VALIDATE_INT);
        $district = ($district !== false && $district !== null) ? $district : 76;

        $block = filter_input(INPUT_POST, 'block', FILTER_VALIDATE_INT);
        $block = ($block !== false && $block !== null) ? $block : 838;

        $loksabha = filter_input(INPUT_POST, 'loksabha', FILTER_VALIDATE_INT);
        $loksabha = ($loksabha !== false && $loksabha !== null) ? $loksabha : 81;

        $vidhansabha = filter_input(INPUT_POST, 'vidhansabha', FILTER_VALIDATE_INT);  // fixed typo from 'vishansabha'
        $vidhansabha = ($vidhansabha !== false && $vidhansabha !== null) ? $vidhansabha : 404;

        $village = isset($_POST['village']) ? trim($_POST['village']) : '';

        $locality = filter_input(INPUT_POST, 'locality', FILTER_VALIDATE_INT);
        $locality = ($locality !== false && $locality !== null) ? $locality : 0;

        $bodies_name = isset($_POST['bodies_name']) ? trim($_POST['bodies_name']) : '';

        $urban_body = filter_input(INPUT_POST, 'urban_bodies', FILTER_VALIDATE_INT);
        $urban_body = ($urban_body !== false && $urban_body !== null) ? $urban_body : null;


        // References
        $refby = isset($_SESSION['login_id']) ? (int)$_SESSION['login_id'] : 0;

        $auth = filter_input(INPUT_POST, 'authority', FILTER_VALIDATE_INT);
        $auth = ($auth !== false && $auth !== null) ? $auth : 37;

        $organization = filter_input(INPUT_POST, 'organization', FILTER_VALIDATE_INT);
        $organization = ($organization !== false && $organization !== null) ? $organization : 7;

        // 5. Handle Photo Upload
        $photo_filename = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo = $_FILES['photo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB

            // Validate file
            if ($photo['size'] > $max_size) {
                $response['message'] = 'Photo exceeds maximum size (2MB)';
                return $response;
            }

            if (!in_array($photo['type'], $allowed_types)) {
                $response['message'] = 'Only JPG, PNG, and GIF images are allowed';
                return $response;
            }

            // Generate safe filename
            $photo_filename = time() . '_' . preg_replace('/[^a-z0-9\.]/i', '', $photo['name']);
            $target_dir = "uploads/members_photos/";
            $target_file = $target_dir . $photo_filename;

            if (!move_uploaded_file($photo['tmp_name'], $target_file)) {
                $response['message'] = 'Failed to upload photo';
                return $response;
            }
        }

        // 6. Check for duplicate phone (skip for current member)
        $stmt_check_phone = $this->db->prepare("SELECT id FROM members WHERE phone = ? AND id != ?");
        $stmt_check_phone->bind_param("si", $phone, $id);
        $stmt_check_phone->execute();

        if ($stmt_check_phone->get_result()->num_rows > 0) {
            $response['message'] = 'Phone number already registered to another member';
            return $response;
        }

        // 7. Prepare SQL (Insert or Update)
        if ($id > 0) {
            // Update existing member
            $stmt = $this->db->prepare("
                UPDATE members SET
                    fname = ?, lname = ?, phone = ?, email = ?, gender = ?,
                    education_id = ?, marital_status = ?, dob = ?, state_id = ?,
                    district_id = ?, block_id = ?, loksabha_id = ?, vidhansabha_id = ?,
                    locality = ?, village = ?, urban_body = ?, bodies_name = ?,
                    role_id = ?, organization_id = ?, photo = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->bind_param(
                "ssssiiisiiiiiisisiisi",
                $fname, $lname, $phone, $email, $gender,
                $qualification, $marital_status, $dob, $state,
                $district, $block, $loksabha, $vidhansabha,
                $locality, $village, $urban_body, $bodies_name,
                $auth, $organization, $photo_filename, $id
            );
        } else {
            // Insert new member
        //     echo $organization."  INSERT INTO members (
        //             fname, lname, phone, email, gender,
        //             education_id, marital_status, dob, state_id,
        //             district_id, block_id, loksabha_id, vidhansabha_id,
        //             locality, village, urban_body, bodies_name,
        //             role_id, organization_id, photo, ref_by
        //         ) VALUES ($fname, $lname, $phone, $email, $gender,
        //         $qualification, $marital_status, $dob, $state,
        //         $district, $block, $loksabha, $vidhansabha,
        //         $locality, $village, $urban_body, $bodies_name,
        //         $auth, $organization, $photo_filename, $refby)
        //    ";
            $stmt = $this->db->prepare("
                INSERT INTO members (
                    fname, lname, phone, email, gender,
                    education_id, marital_status, dob, state_id,
                    district_id, block_id, loksabha_id, vidhansabha_id,
                    locality, village, urban_body, bodies_name,
                    role_id, organization_id, photo, ref_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssiiisiiiiiisisiisi",
                $fname, $lname, $phone, $email, $gender,
                $qualification, $marital_status, $dob, $state,
                $district, $block, $loksabha, $vidhansabha,
                $locality, $village, $urban_body, $bodies_name,
                $auth, $organization, $photo_filename, $refby
            );
        }

        // 8. Execute and return response
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = $id > 0 ? 'Member updated successfully' : 'Member added successfully';
            $response['data'] = [
                'id' => $id > 0 ? $id : $stmt->insert_id,
                'name' => "$fname $lname",
                'phone' => $phone
            ];
        } else {
            $response['message'] = 'Database error: ' . $stmt->error;
            error_log("Database Error: " . $stmt->error);
        }

        return $response;
    }

    /**
     * Delete member with validation
     */
    public function delete_member()
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            return ['status' => 'error', 'message' => 'Invalid member ID'];
        }

        // Use prepared statement to prevent SQL injection
        $stmt = $this->db->prepare("DELETE FROM members WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Member deleted successfully'];
        } else {
            error_log("Delete error: " . $stmt->error);
            return ['status' => 'error', 'message' => 'Failed to delete member'];
        }
    }


    function save_user() {
        // Initialize response
        $response = [
            'status' => 'error',
            'message' => 'Unknown error occurred',
            'code' => 0
        ];

        try {
            // Validate required fields
            $required = ['fname', 'lname', 'phone', 'email', 'password', 'auth_id', 'org_id'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Required field '$field' is missing");
                }
            }

            // Sanitize inputs
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $fname = $this->sanitizeInput($_POST['fname']);
            $lname = $this->sanitizeInput($_POST['lname']);
            $phone = $this->sanitizePhone($_POST['phone']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = md5($_POST['password']);

            $role = (int)$_POST['auth_id'];
            $org = (int)$_POST['org_id'];

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Validate phone number (10 digits)
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                throw new Exception("Phone number must be 10 digits");
            }

            // Get working circle from role
            $stmt = $this->db->prepare("SELECT working_circle FROM role WHERE id = ?");
            $stmt->bind_param("i", $role);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Invalid role specified");
            }
            
            $val = $result->fetch_assoc();
            $working_circle = $val['working_circle'];

            // Determine area based on working circle
            $area = null;
            switch ($working_circle) {
                case '2':
                    $area = isset($_POST['state']) ? (int)$_POST['state'] : null;
                    break;
                case '4':
                    $area = isset($_POST['district']) ? (int)$_POST['district'] : null;
                    break;
                case '5':
                    $area = isset($_POST['block']) ? (int)$_POST['block'] : null;
                    break;
                default:
                    $area = isset($_POST['locality']) ? (int)$_POST['locality'] : null;
                    break;
            }

            // Check if user already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->bind_param("s", $phone);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows > 0) {
                $response['code'] = 2;
                throw new Exception("Phone number already registered");
            }

            // Verify member exists if ID is provided
            if ($id > 0) {
                $stmt = $this->db->prepare("SELECT id FROM members WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                if ($stmt->get_result()->num_rows === 0) {
                    $response['code'] = 3;
                    throw new Exception("No matching member found");
                }
            }

            // Insert new user
            $login_id = $_SESSION['login_id'] ?? 0;
            $stmt = $this->db->prepare("INSERT INTO users 
                (fname, lname, email, password, phone, area_id, role_id, organization_id, member_id, ref_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
            
            $stmt->bind_param("sssssiiiii", $fname, $lname, $email, $password, $phone, $area, $role, $org, $id, $login_id);

            if ($stmt->execute()) {
                $response = [
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'code' => 1,
                    'user_id' => $stmt->insert_id
                ];
            } else {
                throw new Exception("Database error: " . $stmt->error);
            }

        } catch (Exception $e) {
            error_log("User save error: " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    function delete_user() {
        // Initialize response array
        $response = [
            'status' => 'error',
            'message' => 'Unknown error occurred',
            'code' => 0
        ];

        try {
            // Validate input
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("User ID is required");
            }

            // Sanitize input
            $user_id = (int)$_POST['id'];
            $current_user_id = $_SESSION['login_id'] ?? 0;

            // Prevent self-deletion
            if ($user_id == $current_user_id) {
                $response['code'] = 3;
                throw new Exception("You cannot deactivate your own account");
            }

            // Check if user exists
            $check_stmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $check_stmt->bind_param("i", $user_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                $response['code'] = 2;
                throw new Exception("User not found");
            }

            // Prepare update statement
            $update_stmt = $this->db->prepare("UPDATE users SET is_active = 0, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $update_stmt->bind_param("i", $user_id);

            // Execute update
            if ($update_stmt->execute()) {
                if ($update_stmt->affected_rows > 0) {
                    $response = [
                        'status' => 'success',
                        'message' => 'User deactivated successfully',
                        'code' => 1,
                        'user_id' => $user_id
                    ];
                } else {
                    throw new Exception("No changes made - user may already be deactivated");
                }
            } else {
                throw new Exception("Database error: " . $update_stmt->error);
            }

        } catch (Exception $e) {
            error_log("User deactivation error: " . $e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    // Helper functions (add to your class)
    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    private function sanitizePhone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }
    private function handleFileUpload($field, $targetDir, $allowedTypes)
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES[$field];

        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            throw new RuntimeException('Invalid file type');
        }

        // Create target directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $targetPath = $targetDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Failed to move uploaded file');
        }

        return $filename;
    }

    private function validateCsrfToken()
    {
        // Implement CSRF token validation if needed
        return true;
    }
}