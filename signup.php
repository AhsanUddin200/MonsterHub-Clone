    <?php
    include 'db.php';

    $name = $email = $password = $role = "";
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $role = $_POST['role'];
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        if ($role === "employee") {
            $name = trim($_POST['name']);
            if (empty($name) || empty($email) || empty($_POST['password'])) {
                $errors[] = "All fields are required.";
            } else {
                $stmt = $conn->prepare("INSERT INTO monster_users (name, email, password, role) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss", $name, $email, $password, $role);
                if ($stmt->execute()) {
                    header('Location: login.php?registered=1');
                    exit;
                } else {
                    $errors[] = "Email already registered or an error occurred.";
                }
            }
        } elseif ($role === "company") {
            $company_name = trim($_POST['company_name']);
            if (empty($company_name) || empty($email) || empty($_POST['password'])) {
                $errors[] = "All fields are required.";
            } else {
                $stmt = $conn->prepare("INSERT INTO monster_companies (company_name, email, password, role) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss", $company_name, $email, $password, $role);
                if ($stmt->execute()) {
                    header('Location: login.php?registered=1');
                    exit;
                } else {
                    $errors[] = "Email already registered or an error occurred.";
                }
            }
        } else {
            $errors[] = "Please select a valid role.";
        }
    }
    ?>
<?php
include 'db.php';

$name = $email = $password = $role = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if ($role === "employee") {
        $name = trim($_POST['name']);
        if (empty($name) || empty($email) || empty($_POST['password'])) {
            $errors[] = "All fields are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO monster_users (name, email, password, role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = "Email already registered or an error occurred.";
            }
        }
    } elseif ($role === "company") {
        $company_name = trim($_POST['company_name']);
        if (empty($company_name) || empty($email) || empty($_POST['password'])) {
            $errors[] = "All fields are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO monster_companies (company_name, email, password, role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $company_name, $email, $password, $role);
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $errors[] = "Email already registered or an error occurred.";
            }
        }
    } else {
        $errors[] = "Please select a valid role.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Signup</title>
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
.form-group input, 
.form-group select {
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
}
.login-link {
    text-align: center; 
    margin-top: 20px;
}
.login-link a {
    color: #3d2462; 
    text-decoration: none; 
    font-weight: bold;
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
}
input[type="submit"]:hover {
    background-color: #2c1a47;
}
</style>
<script>
function toggleFields(role) {
    var employeeFields = document.getElementById('employeeFields');
    var companyFields = document.getElementById('companyFields');
    if (role === 'employee') {
        employeeFields.style.display = 'block';
        companyFields.style.display = 'none';
    } else if (role === 'company') {
        employeeFields.style.display = 'none';
        companyFields.style.display = 'block';
    } else {
        employeeFields.style.display = 'none';
        companyFields.style.display = 'none';
    }
}
</script>
</head>
<body>
<div class="container">
    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSe1qQG0IkS6VBcmIS3DvCuX1L3GxMwmx_aOcyYBKsscU1A3iDqWokl5tsvO351tkoEO6M&usqp=CAU" alt="Logo" class="logo">
    <h1>Signup</h1>
    <?php
    if (!empty($errors)) {
        echo '<div class="error">'.implode('<br>', $errors).'</div>';
    }
    ?>
    <form method="post">
        <div class="form-group">
            <label for="role">I am a:</label>
            <select name="role" id="role" onchange="toggleFields(this.value)">
                <option value="">--Select--</option>
                <option value="employee" <?php if($role=='employee') echo 'selected';?>>Employee</option>
                <option value="company" <?php if($role=='company') echo 'selected';?>>Company</option>
            </select>
        </div>
        
        <div id="employeeFields" style="display:none;">
            <div class="form-group">
                <label for="name">Full Name (Employee)</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>"/>
            </div>
        </div>
        
        <div id="companyFields" style="display:none;">
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" name="company_name" id="company_name"/>
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>"/>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password"/>
        </div>
        
        <input type="submit" value="Signup"/>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>

<script>
// If role was previously selected (on form error), ensure correct fields are shown
document.addEventListener('DOMContentLoaded', function() {
    var currentRole = document.getElementById('role').value;
    toggleFields(currentRole);
});
</script>
</body>
</html>