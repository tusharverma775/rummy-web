<?php
$servername = "localhost";
$username = "atsgagxz_ats";
$password = "ats2024@";
$database = "atsgagxz_ats";
// Create connection
$conn = new mysqli($servername, $username, $password,$database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>