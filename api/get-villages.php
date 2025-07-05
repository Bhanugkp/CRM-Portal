<?php 
include '../db_connect.php';


$block_id = $_GET['id'];

$save = $conn->prepare("SELECT village FROM members WHERE block_id = ? group by village;");
$save->bind_param("i", $block_id);


$save->execute();

$result = $save->get_result();


$divisions = array();


while($row = $result->fetch_assoc()) {
    $divisions[] = array("id" => $row['village'], "name" => $row['village']);
}

// header('Content-Type: application/json');
echo json_encode(array("res" => $divisions));

?>


