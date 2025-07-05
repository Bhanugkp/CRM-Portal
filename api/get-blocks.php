<?php 

include '../db_connect.php';


$id = $_GET['id'];


$save = $conn->prepare("SELECT d.id, d.name FROM `blocks` AS d WHERE d.district_id = ?");
$save->bind_param("i", $id);


$save->execute();

$result = $save->get_result();

$divisions = array();

while($row = $result->fetch_assoc()) {
    $divisions[] = array("id" => $row['id'], "name" => $row['name']);
}

header('Content-Type: application/json');
echo json_encode(array("res" => $divisions));

?>


