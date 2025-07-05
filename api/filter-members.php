<?php

include '../db_connect.php';

// $userType = $_SESSION['login_type'];
$state = isset($_POST['state'])?$_POST['state']:'';
$district = isset($_POST['district'])?$_POST['district']:'';
$block = isset($_POST['block'])?$_POST['block']:'';
$locality = isset($_POST['locality'])?$_POST['locality']:'';
$role = isset($_POST['authority'])?$_POST['authority']:'';
$organization = isset($_POST['organization'])?$_POST['organization']:'';
$loksabha = isset($_POST['loksabha'])?$_POST['loksabha']:'';
$vidhansabha = isset($_POST['vidhansabha'])?$_POST['vidhansabha']:'';



// $query = "SELECT 
// m.id,
// concat(m.fname,' ', m.lname) as name,
// phone, 
// email, 
// s.name as state,
// dis.name as district,
// m.village as village,
// b.name as block,
// lk.name as loksabha,
// v.name as vidhansabha,
// r.name as role, 
// o.name as org,
// (select fname from users where m.ref_by = users.id) as ref_by
// FROM member_excel AS m 
// INNER JOIN states as s on s.id = m.state
// INNER JOIN districts as dis on dis.id = m.district
// INNER JOIN blocks as b on b.id = m.block
// INNER JOIN role as r on r.id = m.designation
// INNER JOIN organization AS o ON o.id = m.organization
// INNER JOIN vidhansabha AS v ON v.id = m.vidhansabha
// INNER JOIN loksabha AS lk ON lk.id = m.loksabha
// WHERE 1=1";

// if($state) $query .= " AND m.state = $state";
// if($district) $query .= " AND m.district = $district";
// if($block) $query .= " AND m.block = $block";
// if($locality) $query .= " AND m.village = '$locality'";
// if($role) $query .= " AND m.designation = $role";
// if($organization) $query .= " AND m.organization = $organization";
// if($loksabha) $query .= " AND m.loksabha = $loksabha";
// if($vidhansabha) $query .= " AND m.vidhansabha = $vidhansabha";


$query = "SELECT 
m.id,
CONCAT(m.fname, IF(m.lname IS NOT NULL, CONCAT(' ', m.lname), '')) AS name,
phone, 
email, 
s.name as state,
dis.name as district,
m.village as village,
b.name as block,
lk.name as loksabha,
v.name as vidhansabha,
r.name as role, 
o.name as org,
(select fname from users where m.ref_by = users.id) as ref_by
FROM members AS m 
INNER JOIN states as s on s.id = m.state_id
INNER JOIN districts as dis on dis.id = m.district_id
INNER JOIN blocks as b on b.id = m.block_id
INNER JOIN role as r on r.id = m.role_id
INNER JOIN organization AS o ON o.id = m.organization_id 
INNER JOIN vidhansabha AS v ON v.id = m.vidhansabha_id
INNER JOIN loksabha AS lk ON lk.id = m.loksabha_id 
WHERE 1=1";

if($state) $query .= " AND m.state_id = $state";
if($district) $query .= " AND m.district_id = $district";
if($block) $query .= " AND m.block_id = $block";
if($locality) $query .= " AND m.village = '$locality'";
if($role) $query .= " AND m.role_id = $role";
if($organization) $query .= " AND m.organization_id = $organization";
if($loksabha) $query .= " AND m.loksabha_id = $loksabha";
if($vidhansabha) $query .= " AND m.vidhansabha_id = $vidhansabha";


$result = $conn->query($query);

$users = []; //container hai
while($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
