<?php
// Example using PHP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    echo json_encode(['status' => 'success']);
}

?>