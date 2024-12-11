<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

// Get company name
$company_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT company_name FROM monster_companies WHERE id=?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$res = $stmt->get_result();
$company = $res->fetch_assoc();
$company_name = $company['company_name'];

$errors = [];
$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_title = trim($_POST['job_title']);
    $location = trim($_POST['location']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);

    if (empty($job_title) || empty($location) || empty($category) || empty($description)) {
        $errors[] = "All fields are required.";
    } else {
        // Insert job
        $stmt = $conn->prepare("INSERT INTO monster_jobs (job_title, company_name, location, category, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $job_title, $company_name, $location, $category, $description);
        if ($stmt->execute()) {
            $success_msg = "Job posted successfully.";
        } else {
            $errors[] = "Error posting the job.";
        }
    }
}

// Fetch jobs posted by this company
$stmt = $conn->prepare("SELECT * FROM monster_jobs WHERE company_name=?");
$stmt->bind_param("s", $company_name);
$stmt->execute();
$jobs_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Company Dashboard</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: Arial, sans-serif;
    background: #f4f4f4;
    line-height: 1.6;
}
.navbar {
    background-color: #3d2462;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
}
.navbar-menu a {
    color: white;
    text-decoration: none;
    margin-left: 15px;
    transition: color 0.3s ease;
}
.navbar-menu a:hover {
    color: #e0e0e0;
}
.container {
    max-width: 800px;
    margin: 30px auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.section-title {
    color: #3d2462;
    border-bottom: 2px solid #3d2462;
    padding-bottom: 10px;
    margin-bottom: 20px;
    text-align: center;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #3d2462;
    font-weight: bold;
}
.form-group input, 
.form-group select, 
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #3d2462;
    border-radius: 5px;
}
input[type="submit"] {
    background-color: #3d2462;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
input[type="submit"]:hover {
    background-color: #2c1a47;
}
.error {
    background-color: #ffeeee;
    color: red;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.success {
    background-color: #eeffee;
    color: green;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}
.job-list {
    margin-top: 30px;
}
.job {
    border-bottom: 1px solid #eee;
    padding: 20px 0;
}
.job h2 {
    color: #3d2462;
    margin-bottom: 10px;
}
.view-applications {
    display: inline-block;
    background-color: #3d2462;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}
.view-applications:hover {
    background-color: #2c1a47;
}
.logout-link {
    text-align: center;
    margin-top: 20px;
}
.logout-link a {
    color: #3d2462;
    text-decoration: none;
    font-weight: bold;
}
</style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-brand">Company Dashboard</div>
    <div class="navbar-menu">
        <a href="#">Home</a>
        <a href="#">Jobs</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <h2 class="section-title">Post a New Job</h2>
    
    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    if ($success_msg !== "") {
        echo '<div class="success">'.$success_msg.'</div>';
    }
    ?>
    
    <form method="post">
        <div class="form-group">
            <label for="job_title">Job Title</label>
            <input type="text" name="job_title" id="job_title"/>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" id="location"/>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select name="category" id="category">
                <option value="IT">IT</option>
                <option value="Marketing">Marketing</option>
                <option value="Finance">Finance</option>
            </select>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="5"></textarea>
        </div>
        <input type="submit" value="Post Job"/>
    </form>

    <div class="job-list">
        <h2 class="section-title">Your Posted Jobs</h2>
        <?php if ($jobs_result->num_rows > 0): ?>
            <?php while($j = $jobs_result->fetch_assoc()): ?>
                <div class="job">
                    <h2><?php echo htmlspecialchars($j['job_title']); ?></h2>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($j['location']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($j['category']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($j['description'])); ?></p>
                    <a class="view-applications" href="view_applications.php?job_id=<?php echo $j['id']; ?>">View Applications</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't posted any jobs yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>