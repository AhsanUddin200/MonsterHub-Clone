<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$errors = [];
$success_msg = "";

// If the user applies to a job
if (isset($_GET['apply'])) {
    $job_id = (int)$_GET['apply'];

    // Check if user already applied to this job
    $check = $conn->prepare("SELECT id FROM monster_applications WHERE user_id=? AND job_id=?");
    $check->bind_param("ii", $user_id, $job_id);
    $check->execute();
    $check_result = $check->get_result();
    if ($check_result->num_rows > 0) {
        $errors[] = "You have already applied to this job.";
    } else {
        // Insert application
        $stmt = $conn->prepare("INSERT INTO monster_applications (user_id, job_id) VALUES (?,?)");
        $stmt->bind_param("ii", $user_id, $job_id);
        if ($stmt->execute()) {
            $success_msg = "You have successfully applied to this job.";
        } else {
            $errors[] = "Error applying to the job.";
        }
    }
}

// Fetch jobs based on search and category
$sql = "SELECT * FROM monster_jobs WHERE 1=1";

if ($search != '') {
    $search_esc = $conn->real_escape_string($search);
    $sql .= " AND (job_title LIKE '%$search_esc%' OR company_name LIKE '%$search_esc%' OR location LIKE '%$search_esc%')";
}

if ($category != '' && $category != 'all') {
    $sql .= " AND category='".$conn->real_escape_string($category)."'";
}

$jobs_result = $conn->query($sql);

// Fetch applications for the current user
$applications = [];
$app_sql = $conn->prepare("SELECT job_id FROM monster_applications WHERE user_id=?");
$app_sql->bind_param("i", $user_id);
$app_sql->execute();
$app_res = $app_sql->get_result();
while ($row = $app_res->fetch_assoc()) {
    $applications[] = $row['job_id'];
}

// Fetch messages for the current user
$msg_sql = "
    SELECT m.message, m.created_at, j.job_title, c.company_name
    FROM monster_messages m
    JOIN monster_jobs j ON m.job_id = j.id
    JOIN monster_companies c ON m.from_user_id = c.id
    WHERE m.to_user_id = ?
    ORDER BY m.created_at DESC
";
$msg_stmt = $conn->prepare($msg_sql);
$msg_stmt->bind_param("i", $user_id);
$msg_stmt->execute();
$messages_res = $msg_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<title>Employee Dashboard</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f9f9fb;
    margin: 0; 
    padding: 0;
    line-height: 1.6;
    color: #333;
}

header {
    background: #3d2462; 
    color: #fff; 
    padding: 15px 20px; 
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

header h1 {
    margin:0;
}

.header-buttons {
    display: flex;
    gap: 10px;
}

.header-buttons a {
    background: #fff;
    color: #3d2462;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 5px;
    font-weight: bold;
    transition: background 0.3s;
    font-size:14px;
}

.header-buttons a:hover {
    background: #ddd;
}

.container {
    max-width: 900px; 
    margin: 30px auto; 
    background: #fff; 
    padding: 25px; 
    border-radius: 8px; 
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #3d2462;
    margin-bottom: 20px;
}

.filter-form {
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-bottom: 20px;
    justify-content: space-between;
}

.filter-form input[type="text"],
.filter-form select {
    flex: 1;
    padding: 10px; 
    border: 1px solid #ddd; 
    border-radius: 5px;
    font-size: 14px;
}

.filter-form button {
    flex: 0 0 auto;
    padding: 10px 20px;
    background: #3d2462; 
    color: #fff; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s;
}

.filter-form button:hover {
    background: #0056b3;
}

.job-list {
    margin-top: 20px;
}

.job {
    padding: 15px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    transition: box-shadow 0.3s;
}

.job:hover {
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

.job h2 {
    font-size: 20px;
    margin-bottom: 10px;
    color: #3d2462;
}

.job p {
    margin: 5px 0;
    font-size: 14px;
}

.apply-btn {
    display: inline-block;
    padding: 8px 12px;
    background: #3d2462; 
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s;
    font-size: 14px;
}

.apply-btn:hover {
    background: #218838;
}

.applied-label {
    color: #999;
    font-style: italic;
    font-size: 14px;
}

.error {
    color: #d9534f; 
    background: #f9d6d5;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

.success {
    color: #28a745; 
    background: #d7f8d7;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
}

.messages-section {
    margin-top: 40px;
}

.message-item {
    padding: 15px 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 15px;
    background: #f9f9fb;
}

.message-item strong {
    color: #3d2462;
}

.logout-link a {
    color: #007bff;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
}

.logout-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<header>
    <h1>Employee Dashboard</h1>
    <div class="header-buttons">
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</header>
<div class="container">
    <h2>Welcome!</h2>
    <p>Here you can search and apply to available jobs. Use the filters below to find the right job for you.</p>

    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    if ($success_msg !== "") {
        echo '<div class="success">'.$success_msg.'</div>';
    }
    ?>

    <form class="filter-form" method="get">
        <input type="text" name="search" placeholder="Search by title, company, location..." value="<?php echo htmlspecialchars($search); ?>"/>
        <select name="category">
            <option value="all">All Categories</option>
            <option value="IT" <?php if($category=='IT') echo 'selected';?>>IT</option>
            <option value="Marketing" <?php if($category=='Marketing') echo 'selected';?>>Marketing</option>
            <option value="Finance" <?php if($category=='Finance') echo 'selected';?>>Finance</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="job-list">
        <h2>Available Jobs</h2>
        <?php if ($jobs_result->num_rows > 0): ?>
            <?php while($job = $jobs_result->fetch_assoc()): ?>
                <div class="job">
                    <h2><?php echo htmlspecialchars($job['job_title']); ?></h2>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                    <p><?php echo htmlspecialchars(substr($job['description'], 0, 150)); ?>...</p>
                    <?php if (in_array($job['id'], $applications)): ?>
                        <span class="applied-label">Already Applied</span>
                    <?php else: ?>
                        <a class="apply-btn" href="?apply=<?php echo $job['id']; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>">Apply Now</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs found.</p>
        <?php endif; ?>
    </div>

    <!-- Messages Section -->
    <div class="messages-section">
        <h2>Messages from Companies</h2>
        <?php if ($messages_res->num_rows > 0): ?>
            <?php while($msg = $messages_res->fetch_assoc()): ?>
                <div class="message-item">
                    <p><strong>From Company:</strong> <?php echo htmlspecialchars($msg['company_name']); ?></p>
                    <p><strong>Regarding Job:</strong> <?php echo htmlspecialchars($msg['job_title']); ?></p>
                    <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    <p><em>Sent at: <?php echo htmlspecialchars($msg['created_at']); ?></em></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You have no messages yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
