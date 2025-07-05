<?php 
$conn= new mysqli('localhost','root','','ncrm_db') or die("Could not connect to mysql".mysqli_error($con));
$conn->set_charset("utf8mb4");