<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Check if file was uploaded properly
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] != UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'File upload error!']));
}

// Validate session
if (!isset($_SESSION['login_id'])) {
    die(json_encode(['success' => false, 'message' => 'Session expired. Please login again.']));
}

$fileTmpPath = $_FILES['excelFile']['tmp_name'];

try {
    // Load the spreadsheet
    $spreadsheet = IOFactory::load($fileTmpPath);
    $worksheet = $spreadsheet->getActiveSheet();

    // Get dimensions
    $highestRow = $worksheet->getHighestDataRow(); // Changed to getHighestDataRow to ignore empty rows
    $highestColumn = $worksheet->getHighestDataColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    // Prepare main insert statement
    $sql = "INSERT INTO members (
        fname, lname, phone, email, gender, education_id, marital_status, 
        state_id, district_id, block_id, loksabha_id, vidhansabha_id, 
        locality, village, urban_body, bodies_name, role_id, organization_id, ref_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Prepare all search statements
    $searchQueries = [
        'state' => "SELECT id FROM states WHERE name = ? LIMIT 1",
        'district' => "SELECT id FROM districts WHERE name = ? AND state_id = ? LIMIT 1",
        'block' => "SELECT id FROM blocks WHERE name = ? AND district_id = ? LIMIT 1",
        'loksabha' => "SELECT id FROM loksabha WHERE name = ? LIMIT 1",
        'vidhansabha' => "SELECT id FROM vidhansabha WHERE name = ? LIMIT 1",
        'qualification' => "SELECT id FROM qualification WHERE name = ? LIMIT 1",
        'organization' => "SELECT id FROM organization WHERE name = ? LIMIT 1",
        'role' => "SELECT id FROM role WHERE name = ? LIMIT 1"
    ];

    $searchStmts = [];
    foreach ($searchQueries as $key => $query) {
        $searchStmts[$key] = $conn->prepare($query);
        if (!$searchStmts[$key]) {
            throw new Exception("Prepare failed for $key: " . $conn->error);
        }
    }

    // Helper function to get foreign key ID with context
    function getForeignKeyId($conn, $stmt, $value, $default, $context = null)
    {
        if (empty($value))
            return $default;

        try {
            if ($context) {
                $stmt->bind_param("ss", $value, $context);
            } else {
                $stmt->bind_param("s", $value);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                return $row['id'];
            }
            return $default;
        } catch (Exception $e) {
            error_log("Error getting foreign key: " . $e->getMessage());
            return $default;
        }
    }

    // Track results
    $successCount = 0;
    $errorCount = 0;
    $errors = [];

    // Process each row
    for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = [];

        // Get all cell values for the row
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $rowData[] = $cell->getValue();
        }

        // Validate required fields
        if (empty($rowData[0])) {
            $errors[] = "Row $row: First name is required";
            $errorCount++;
            continue;
        }
        if (empty($rowData[1])) {
            $errors[] = "Row $row: Last name is required";
            $errorCount++;
            continue;
        }
        if (empty($rowData[2])) {
            $errors[] = "Row $row: Phone number is required";
            $errorCount++;
            continue;
        }

        // Get foreign keys with proper context
        $qualificationId = getForeignKeyId($conn, $searchStmts['qualification'], $rowData[5] ?? null, 8);
        $stateId = getForeignKeyId($conn, $searchStmts['state'], $rowData[7] ?? null, 3);
        $districtId = getForeignKeyId($conn, $searchStmts['district'], $rowData[8] ?? null, 76, $stateId);
        $blockId = getForeignKeyId($conn, $searchStmts['block'], $rowData[9] ?? null, 838, $districtId);
        $loksabhaId = getForeignKeyId($conn, $searchStmts['loksabha'], $rowData[10] ?? null, 81);
        $vidhansabhaId = getForeignKeyId($conn, $searchStmts['vidhansabha'], $rowData[11] ?? null, 404);
        $organizationId = getForeignKeyId($conn, $searchStmts['organization'], $rowData[17] ?? null, 7);
        $roleId = getForeignKeyId($conn, $searchStmts['role'], $rowData[16] ?? null, 37);

        // Bind parameters
        $stmt->bind_param(
            "ssssssssssssisisssi",
            $rowData[0],
            $rowData[1],
            $rowData[2],
            $rowData[3] ?? null,
            $rowData[4] ?? null,
            $qualificationId,
            $rowData[6] ?? null,
            $stateId,
            $districtId,
            $blockId,
            $loksabhaId,
            $vidhansabhaId,
            $rowData[12] ?? null,
            $rowData[13] ?? null,
            $rowData[14] ?? null,
            $rowData[15] ?? null,
            $roleId,
            $organizationId,
            $_SESSION['login_id']
        );

        // Execute
        if ($stmt->execute()) {
            $successCount++;
        } else {
            $errorCount++;
            $errors[] = "Row $row: " . $stmt->error;
        }
    }

    // Close statements
    $stmt->close();
    foreach ($searchStmts as $stmt) {
        $stmt->close();
    }

    // Return results
    $response = [
        'success' => true,
        'message' => "Import completed: $successCount successful, $errorCount failed",
        'total' => ($successCount + $errorCount),
        'imported' => $successCount,
        'failed' => $errorCount
    ];

    if ($errorCount > 0) {
        $response['errors'] = $errors;
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error processing file',
        'error' => $e->getMessage()
    ]);
} finally {
    $conn->close();
}