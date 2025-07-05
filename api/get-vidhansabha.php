<?php 
include '../db_connect.php';

// var_dump($conn);

$state_id = $_GET['id'];


$save = $conn->prepare("SELECT v.id, v.name FROM `vidhansabha` AS v WHERE v.loksabha_id = ?");
$save->bind_param("i", $state_id);

$save->execute();


$result = $save->get_result();


$divisions = array();

while($row = $result->fetch_assoc()) {
    $divisions[] = array("id" => $row['id'], "name" => $row['name']);
}

header('Content-Type: application/json');
echo json_encode(array("res" => $divisions));

?>


