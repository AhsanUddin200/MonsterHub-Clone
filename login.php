<?php
include 'db.php';

$email = $password = "";
$errors = [];

if (isset($_GET['registered'])) {
    $registered = "Account created successfully. Please log in.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check employee table first
    $stmt = $conn->prepare("SELECT id, password, role FROM monster_users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Employee login
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard_employee.php");
            exit;
        } else {
            $errors[] = "Incorrect password.";
        }
    } else {
        // Check companies table
        $stmt = $conn->prepare("SELECT id, password, role FROM monster_companies WHERE email=?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $company = $res->fetch_assoc();
            if (password_verify($password, $company['password'])) {
                // Company login
                session_start();
                $_SESSION['user_id'] = $company['id'];
                $_SESSION['role'] = $company['role'];
                header("Location: dashboard_company.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login</title>
<style>
body {
    font-family: Arial, sans-serif; 
    background: #3d2462; 
    margin: 0; 
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.container {
    max-width: 400px; 
    background: #fff; 
    padding: 30px; 
    border-radius: 10px; 
    box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    text-align: center;
}
.logo {
    max-width: 150px;
    margin-bottom: 20px;
}
.container h1 {
    text-align: center; 
    margin-bottom: 20px;
    color: #3d2462;
}
.form-group {
    margin-bottom: 15px;
    text-align: left;
}
.form-group label {
    display: block; 
    margin-bottom: 5px;
    font-weight: bold;
    color: #3d2462;
}
.form-group input {
    width: 100%; 
    padding: 10px; 
    box-sizing: border-box;
    border: 1px solid #3d2462;
    border-radius: 5px;
}
.error {
    color: red; 
    margin-bottom: 20px;
    background: #ffeeee;
    padding: 10px;
    border-radius: 5px;
    text-align: left;
}
.success {
    color: green; 
    margin-bottom: 20px;
    background: #eeffee;
    padding: 10px;
    border-radius: 5px;
    text-align: left;
}
input[type="submit"] {
    background-color: #3d2462;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s;
    margin-top: 10px;
}
input[type="submit"]:hover {
    background-color: #2c1a47;
}
.signup-link {
    text-align: center; 
    margin-top: 20px;
}
.signup-link a {
    color: #3d2462; 
    text-decoration: none; 
    font-weight: bold;
}
.signup-link a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSe1qQG0IkS6VBcmIS3DvCuX1L3GxMwmx_aOcyYBKsscU1A3iDqWokl5tsvO351tkoEO6M&usqp=CAU" alt="Logo" class="logo">
    <h1>Login</h1>
    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    if (isset($registered)) {
        echo '<div class="success">'.$registered.'</div>';
    }
    ?>
    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"/>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"/>
        </div>
        <input type="submit" value="Login"/>
    </form>
    <div class="signup-link">
        <p>Don't have an account? <a href="signup.php">Signup</a></p>
    </div>
</div>
</body>
</html>