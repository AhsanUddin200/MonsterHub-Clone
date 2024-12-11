<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch messages sent to this user
$sql = "SELECT m.message, m.created_at, j.job_title, c.company_name
        FROM monster_messages m
        JOIN monster_jobs j ON m.job_id = j.id
        JOIN monster_companies c ON m.from_user_id = c.id
        WHERE m.to_user_id = ?
        ORDER BY m.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Your Messages</title>
<style>
body {
    font-family: Arial,sans-serif;
    background:#f4f4f4; 
    margin:0; 
    padding:0;
}
header {
    background:#333; 
    color:#fff; 
    padding:10px; 
    text-align:center;
}
.container {
    max-width:600px;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:5px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
h1 {
    text-align:center;
    margin-top:0;
}
.message-item {
    border-bottom:1px solid #ddd;
    padding:10px 0;
}
.message-item:last-child {
    border-bottom:none;
}
.back-link {
    display:inline-block;
    margin-top:20px;
    text-align:center;
    width:100%;
    background:#333;
    color:#fff;
    padding:10px;
    border-radius:3px;
    text-decoration:none;
}
.back-link:hover {
    background:#555;
}
</style>
</head>
<body>
<header>
   <h1>Your Messages</h1>
</header>
<div class="container">
    <?php if ($res->num_rows > 0): ?>
        <?php while($msg = $res->fetch_assoc()): ?>
            <div class="message-item">
                <p><strong>From Company:</strong> <?php echo htmlspecialchars($msg['company_name']); ?></p>
                <p><strong>Regarding Job:</strong> <?php echo htmlspecialchars($msg['job_title']); ?></p>
                <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                <p><em>Sent at: <?php echo htmlspecialchars($msg['created_at']); ?></em></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No messages yet.</p>
    <?php endif; ?>

    <a class="back-link" href="dashboard_employee.php">Back to Dashboard</a>
</div>
</body>
</html>
