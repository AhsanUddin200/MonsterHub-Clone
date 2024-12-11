<?php
include 'db.php';

// Fetch all jobs
$jobs_result = $conn->query("SELECT * FROM monster_jobs");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>All Jobs</title>
<style>
body {
    margin:0;
    padding:0;
    font-family: Arial, sans-serif;
    background:#f4f4f4;
}
header {
    background:#3d2462;
    color:#fff;
    padding:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
header h1 {
    margin:0;
    font-size:24px;
}
.nav-buttons {
    display:flex;
    gap:10px;
}
.nav-buttons a {
    background:#fff;
    color:#333;
    text-decoration:none;
    padding:8px 12px;
    border-radius:3px;
    font-weight:bold;
    transition:background 0.3s ease;
}
.nav-buttons a:hover {
    background:#ddd;
}
.container {
    max-width:800px;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:5px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
h2 {
    margin-top:0;
    text-align:center;
}
.job-list {
    margin-top:20px;
}
.job {
    border-bottom:1px solid #ddd;
    padding:10px 0;
}
.job h3 {
    margin:0 0 5px 0;
    font-size:18px;
}
.job p {
    margin:5px 0;
    color:#555;
}
</style>
</head>
<body>
<header>
    <h1>Monster</h1>
    <div class="nav-buttons">
        <a href="home.php">Home</a>
        <a href="jobs.php">Jobs</a>
        <a href="companies.php">Companies</a>
        <a href="signup.php">Sign In</a>
    </div>
</header>
<div class="container">
    <h2>All Job Listings</h2>
    <div class="job-list">
        <?php if ($jobs_result->num_rows > 0): ?>
            <?php while($job = $jobs_result->fetch_assoc()): ?>
                <div class="job">
                    <h3><?php echo htmlspecialchars($job['job_title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 150))); ?>...</p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs available at the moment.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
