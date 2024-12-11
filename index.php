<?php
include 'db.php';

// Fetch all jobs to display on home page
$jobs_result = $conn->query("SELECT * FROM monster_jobs");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Home - Monster</title>
<style>
body {
    margin:0;
    padding:0;
    font-family: Arial, sans-serif;
    background:#f4f4f4;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
header {
    background:#3d2462;
    color:#fff;
    padding:10px 0;
}
.header-container {
    max-width:1200px;
    margin:0 auto;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 15px;
}
.logo {
    display: flex;
    align-items: center;
}
.logo img {
    max-height: 50px;
    margin-right: 10px;
}
header h1 {
    margin:0;
    font-size:24px;
}
.navbar {
    display:flex;
    gap:20px;
}
.navbar a {
    color:#fff;
    text-decoration:none;
    padding:10px 15px;
    transition:background 0.3s ease;
    border-radius:3px;
}
.navbar a:hover {
    background:rgba(255,255,255,0.2);
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
    flex-grow:1;
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
footer {
    background:#3d2462;
    color:#fff;
    padding:20px 0;
    margin-top:30px;
}
.footer-container {
    max-width:1200px;
    margin:0 auto;
    display:flex;
    justify-content:space-between;
    padding:0 15px;
}
.footer-column {
    flex:1;
}
.footer-column h4 {
    margin-bottom:15px;
    font-size:16px;
}
.footer-column ul {
    list-style-type:none;
    padding:0;
}
.footer-column ul li {
    margin-bottom:10px;
}
.footer-column ul li a {
    color:#fff;
    text-decoration:none;
    transition:color 0.3s ease;
}
.footer-column ul li a:hover {
    color:#ddd;
}
.footer-bottom {
    background:#222;
    color:#fff;
    text-align:center;
    padding:10px 0;
    margin-top:20px;
}
</style>
</head>
<body>
<header>
    <div class="header-container">
        <div class="logo">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSe1qQG0IkS6VBcmIS3DvCuX1L3GxMwmx_aOcyYBKsscU1A3iDqWokl5tsvO351tkoEO6M&usqp=CAU" alt="Monster Jobs Logo">
            
        </div>
        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="jobs.php">Jobs</a>
            <a href="companies.php">Companies</a>
            <a href="career.php">Career Advice</a>
        </nav>
        <div class="nav-buttons">
            <!-- Clicking these will take user to signin.php where they select their role -->
            <a href="signup.php">Job Seeker</a>
            <a href="signup.php">Hiring</a>
        </div>
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
<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h4>Job Seekers</h4>
            <ul>
                <li><a href="#">Browse Jobs</a></li>
                <li><a href="#">Create Resume</a></li>
                <li><a href="#">Career Advice</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Employers</h4>
            <ul>
                <li><a href="#">Post a Job</a></li>
                <li><a href="#">Search Candidates</a></li>
                <li><a href="#">Recruitment Solutions</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>About Monster</h4>
            <ul>
                <li><a href="#">Company Info</a></li>
                <li><a href="#">Contact Us</a></li>
                <li><a href="#">Privacy Policy</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        © 2024 Monster Jobs. All Rights Reserved.
    </div>
</footer>
</body>
</html>