<?php
session_start();
error_reporting(E_ALL);  // Enable all error reporting for debugging
ini_set('display_errors', 1); // Show errors

include('includes/dbconnection.php');

// Check if the form is submitted
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // SQL query to fetch student details based on username and password
    $sql = "SELECT StuID FROM tblstudent WHERE UserName=:username AND Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    // Debug: Check if query executed correctly
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['sturecmsuid'] = $result->StuID; // Set session variable for student ID
        }

        // Handle "Remember me" functionality
        if (!empty($_POST["remember"])) {
            setcookie("student_username", $_POST["username"], time() + (10 * 365 * 24 * 60 * 60)); // Store username in cookie
            setcookie("student_password", $_POST["password"], time() + (10 * 365 * 24 * 60 * 60)); // Store password in cookie
        } else {
            if (isset($_COOKIE["student_username"])) {
                setcookie("student_username", "", time() - 3600); // Expire cookie immediately
                setcookie("student_password", "", time() - 3600); // Expire cookie immediately
            }
        }

        $_SESSION['login'] = $_POST['username']; // Set session for login
        
        // Debug: Check if session variable is set
        echo "Session is set. Redirecting to dashboard...";
        echo "<pre>";
        var_dump($_SESSION);
        echo "</pre>";
        header("Location: dashboard.php");  // Redirect to dashboard after successful login
        exit;
    } else {
        echo "<script>alert('Invalid Details');</script>"; // Show error message for invalid login
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('../images/br2.jpg ') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            text-align: center;
            width: 300px;
            z-index: 2;
        }

        .login-container img {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
        }

        h2 {
            color: black;
            margin-bottom: 20px;
            font-weight: normal;
            font-size: 24px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.8);
            font-size: 16px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        label {
            color: white;
            font-size: 14px;
        }

        .forgot-password {
            float: right;
            color: white;
            font-size: 12px;
            text-decoration: none;
        }

        .login-btn {
            width: 100%;
            padding: 10px;
            background-color: #0056b3;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer; /* Change from grab to pointer */
            margin-top: 20px;
        }

        .login-btn:hover {
            background-color: #003d80;
        }

        .remember-me {
            margin-top: 15px; /* Increased space */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="nerd.jpeg" alt="User Icon">
        <h2>LOGIN</h2>
        <form method="post" name="login">
            <div class="form-group">
                <input type="text" placeholder="Enter your username" required name="username" value="<?php if (isset($_COOKIE["student_username"])) { echo $_COOKIE["student_username"]; } ?>">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter your password" required name="password" value="<?php if (isset($_COOKIE["student_password"])) { echo $_COOKIE["student_password"]; } ?>">
            </div>
            <div class="remember-me" style="text-align: left;">
                <input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["student_username"])) { ?> checked <?php } ?> />
                <label for="remember">Keep me signed in</label>
                <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
            </div>
            <button type="submit" name="login" class="login-btn">LOGIN</button>
        </form>
        <div style="margin-top: 10px;">
            <i class="icon-placeholder" style="font-size: 24px; color: white;">🔒</i>
        </div>
    </div>
</body>
</html>
