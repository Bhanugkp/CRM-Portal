<?php 

include '../db_connect.php';


$state_id = $_GET['id'];


$save = $conn->prepare("SELECT l.id, l.name FROM `loksabha` AS l WHERE l.state_id = ?");
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


