<?php 

include '../db_connect.php';

$id = $_GET['id'];


$save = $conn->prepare("SELECT n.id, n.name FROM `nagar_nigam` AS n WHERE n.district_id = ?");
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