<?php

header('Content-Type: application/json');
// $searchTerm = $_GET['q'];
require '../db_connect.php';
$result = $conn->query('SELECT * FROM members');
$ret = [];
    
while($row = $result->fetch_assoc()) {
    $ret[] = $row['fname'];
}


$results = $ret;
echo json_encode($results);
?>