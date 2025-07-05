<?php 

include '../db_connect.php';

$state_id = $_GET['id'];


$save = $conn->prepare("SELECT d.id, d.name FROM `districts` AS d WHERE d.state_id = ? order by d.name asc");
$save->bind_param("i", $state_id);

$save->execute();


$result = $save->get_result();

$divisions = array();

while($row = $result->fetch_assoc()) {
    $divisions[] = array("id" => $row['id'], "name" => $row['name']);
}

// header('Content-Type: application/json');
echo json_encode(array("res" => $divisions));

?>


