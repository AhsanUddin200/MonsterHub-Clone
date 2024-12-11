<?php
session_start();
include 'db.php';

// Check if user is logged in and is employee
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email, phone, address, resume_path FROM monster_users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$errors = [];
$success_msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    // Handle file upload if any
    $resume_path = $user['resume_path']; // existing path
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == UPLOAD_ERR_OK) {
        $allowed_extensions = ['pdf','doc','docx'];
        $file_name = $_FILES['resume']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            $errors[] = "Invalid file format. Allowed: pdf, doc, docx.";
        } else {
            // Move the file
            $new_file_name = "resume_".$user_id."_".time().".".$file_ext;
            $upload_dir = __DIR__ . "/uploads/resumes/";
            $full_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($_FILES['resume']['tmp_name'], $full_path)) {
                // Store relative path in DB
                $resume_path = "uploads/resumes/" . $new_file_name;
            } else {
                $errors[] = "Error uploading the resume file.";
            }
        }
    }

    // If no errors, update the database
    if (empty($errors)) {
        $update = $conn->prepare("UPDATE monster_users SET name=?, phone=?, address=?, resume_path=? WHERE id=?");
        $update->bind_param("ssssi", $name, $phone, $address, $resume_path, $user_id);
        if ($update->execute()) {
            $success_msg = "Profile updated successfully.";
            // Refresh user data
            $stmt = $conn->prepare("SELECT name, email, phone, address, resume_path FROM monster_users WHERE id=?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        } else {
            $errors[] = "Error updating profile.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<title>Employee Profile</title>
<style>
/* Reset some default styles */
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
    font-size: 24px;
    font-weight: bold;
}

.container {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    font-size: 22px;
    text-align: center;
    color: #3d2462;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group textarea,
.form-group input[type="file"] {
    width: 100%;
    padding: 10px;
    box-sizing: border-box;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #3d2462;
    outline: none;
}

textarea {
    resize: none;
}

input[type="submit"] {
    background: #3d2462;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    display: block;
    width: 100%;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background: #0056b3;
}

.error {
    color: red;
    margin-bottom: 15px;
    font-weight: bold;
}

.success {
    color: green;
    margin-bottom: 15px;
    font-weight: bold;
}

.logout-link {
    text-align: center;
    margin-top: 20px;
}

.logout-link a {
    color: #3d2462;
    text-decoration: none;
    font-weight: bold;
    transition: color 0.3s;
}

.logout-link a:hover {
    color: #0056b3;
}

.current-resume {
    font-size: 14px;
    margin-top: 10px;
}

.current-resume a {
    color: #3d2462;
    text-decoration: none;
    transition: color 0.3s;
}

.current-resume a:hover {
    color: #0056b3;
}
</style>
</head>
<body>
<header>
    Employee Profile
</header>
<div class="container">
    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    if ($success_msg !== "") {
        echo '<div class="success">'.$success_msg.'</div>';
    }
    ?>
    <h1>Update Your Profile</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name*</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required/>
        </div>
        <div class="form-group">
            <label for="email">Email (Read-only)</label>
            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly/>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"/>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user['address']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="resume">Resume (PDF, DOC, DOCX)</label>
            <input type="file" id="resume" name="resume" />
            <?php if (!empty($user['resume_path'])): ?>
                <p class="current-resume">
                    Current resume: <a href="<?php echo htmlspecialchars($user['resume_path']); ?>" target="_blank">View/Download</a>
                </p>
            <?php endif; ?>
        </div>
        <input type="submit" value="Update Profile"/>
    </form>
    <p class="logout-link"><a href="logout.php">Logout</a></p>
</div>
</body>
</html>

