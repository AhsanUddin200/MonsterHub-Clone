<?php
$host = "localhost";
$user = "root";
$pass = ""; // your DB password if any
$db   = "monster";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
