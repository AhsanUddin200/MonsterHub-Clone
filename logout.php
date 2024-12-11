<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Logged Out - Monster</title>
<style>
body {
    margin:0;
    padding:0;
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    display:flex;
    align-items:center;
    justify-content:center;
    height:100vh;
}

.container {
    background:#fff;
    border-radius:5px;
    max-width:400px;
    width:100%;
    padding:30px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
    text-align:center;
}

.container h1 {
    margin-top:0;
    font-size:24px;
    color:#333;
}

.container p {
    color:#666;
    margin:20px 0;
}

.logout-btn {
    display:inline-block;
    padding:10px 20px;
    background:#5C258D; /* Inspired by Monster's purple brand color */
    color:#fff;
    text-decoration:none;
    border-radius:3px;
    font-weight:bold;
    transition:background 0.3s ease;
}

.logout-btn:hover {
    background:#4a1f70;
}
</style>
</head>
<body>
<div class="container">
    <h1>You have been logged out</h1>
    <p>Thank you for visiting! We hope to see you back soon.</p>
    <a class="logout-btn" href="login.php">Login Again</a>
</div>
</body>
</html>
