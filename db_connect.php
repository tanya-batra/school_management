<?php
$servername = "localhost";   
$username = "designin_littlelegends";          
$password = "1AvgtpcGW[#B";             
$dbname = "designin_littlelegends";         
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>