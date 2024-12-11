<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

$company_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT company_name FROM monster_companies WHERE id=?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$res = $stmt->get_result();
$company = $res->fetch_assoc();
$company_name = $company['company_name'];

if (!isset($_GET['job_id'])) {
    die("No job selected.");
}

$job_id = (int)$_GET['job_id'];

// Verify that the job belongs to this company
$stmt = $conn->prepare("SELECT job_title FROM monster_jobs WHERE id=? AND company_name=?");
$stmt->bind_param("is", $job_id, $company_name);
$stmt->execute();
$job_res = $stmt->get_result();

if ($job_res->num_rows === 0) {
    die("Invalid job or you do not own this job.");
}

$job = $job_res->fetch_assoc();
$job_title = $job['job_title'];

// Sending a message
$errors = [];
$success_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && isset($_POST['to_user_id'])) {
    $message = trim($_POST['message']);
    $to_user_id = (int)$_POST['to_user_id'];
    
    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    } else {
        // Insert into monster_messages
        $stmt = $conn->prepare("INSERT INTO monster_messages (job_id, from_user_id, to_user_id, message) VALUES (?,?,?,?)");
        $stmt->bind_param("iiis", $job_id, $company_id, $to_user_id, $message);
        if ($stmt->execute()) {
            $success_msg = "Message sent successfully.";
        } else {
            $errors[] = "Error sending message.";
        }
    }
}

// Fetch all applicants
$sql = "SELECT u.id as user_id, u.name, u.email, u.phone, u.resume_path, a.applied_at
        FROM monster_applications a
        JOIN monster_users u ON a.user_id = u.id
        WHERE a.job_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$app_res = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Applications for <?php echo htmlspecialchars($job_title); ?></title>
<style>
/* Global Styles */
body {
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    margin: 0;
    padding: 0;
    color: #333;
}

header {
    background: #3d2462;
    color: #fff;
    padding: 20px;
    text-align: center;
    font-size: 22px;
    font-weight: bold;
}

.container {
    max-width: 800px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 24px;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

.applicant {
    border-bottom: 1px solid #ddd;
    padding: 15px 0;
    margin-bottom: 20px;
}

.applicant:last-child {
    border-bottom: none;
}

.applicant h2 {
    font-size: 18px;
    margin: 0 0 10px;
    color: #3d2462;
}

.applicant p {
    margin: 5px 0;
    font-size: 14px;
}

.resume-link {
    color: #3d2462;
    text-decoration: none;
    font-weight: bold;
}

.resume-link:hover {
    text-decoration: underline;
}

.back-link {
    display: block;
    margin: 30px auto 0;
    text-align: center;
    width: 100%;
    max-width: 200px;
    background: #3d2462;
    color: #fff;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
}

.back-link:hover {
    background: #0056b3;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}

.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    resize: none;
    transition: border-color 0.3s ease;
}

.form-group textarea:focus {
    border-color: #3d2462;
    outline: none;
}

input[type="submit"] {
    background: #3d2462;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
}

input[type="submit"]:hover {
    background: #218838;
}

.error {
    color: red;
    font-weight: bold;
    margin-bottom: 15px;
}

.success {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
}
</style>
</head>
<body>
<header>
   Applications for <?php echo htmlspecialchars($job_title); ?>
</header>
<div class="container">
    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    if (!empty($success_msg)) {
        echo '<div class="success">'.$success_msg.'</div>';
    }
    ?>

    <?php if ($app_res->num_rows > 0): ?>
        <?php while($applicant = $app_res->fetch_assoc()): ?>
            <div class="applicant">
                <h2><?php echo htmlspecialchars($applicant['name']); ?></h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($applicant['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($applicant['phone']); ?></p>
                <p><strong>Applied At:</strong> <?php echo htmlspecialchars($applicant['applied_at']); ?></p>
                <?php if (!empty($applicant['resume_path'])): ?>
                    <p><strong>Resume:</strong> <a class="resume-link" href="<?php echo htmlspecialchars($applicant['resume_path']); ?>" target="_blank">View/Download</a></p>
                <?php else: ?>
                    <p><strong>Resume:</strong> Not provided</p>
                <?php endif; ?>

                <!-- Message Form -->
                <form method="post" style="margin-top:15px;">
                    <div class="form-group">
                        <label>Send a Message to <?php echo htmlspecialchars($applicant['name']); ?>:</label>
                        <textarea name="message" rows="3" placeholder="Type your message here..."></textarea>
                    </div>
                    <input type="hidden" name="to_user_id" value="<?php echo $applicant['user_id']; ?>"/>
                    <input type="submit" value="Send Message"/>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No applications for this job yet.</p>
    <?php endif; ?>

    <a class="back-link" href="dashboard_company.php">Back to Dashboard</a>
</div>
</body>
</html>
