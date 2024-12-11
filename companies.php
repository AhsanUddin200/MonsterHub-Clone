<?php
include 'db.php';

// Fetch all companies
$companies_result = $conn->query("SELECT * FROM monster_companies");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>All Companies</title>
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
    color:#3d2462;
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
.company-list {
    margin-top:20px;
}
.company {
    border-bottom:1px solid #ddd;
    padding:10px 0;
}
.company h3 {
    margin:0 0 5px 0;
    font-size:18px;
}
.company p {
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
    <h2>All Companies</h2>
    <div class="company-list">
        <?php if ($companies_result->num_rows > 0): ?>
            <?php while($company = $companies_result->fetch_assoc()): ?>
                <div class="company">
                    <h3><?php echo htmlspecialchars($company['company_name']); ?></h3>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($company['email']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No companies found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
