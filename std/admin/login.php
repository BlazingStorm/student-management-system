<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT ID FROM tbladmin WHERE UserName=:username AND Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['sturecmsaid'] = $result->ID;
        }

        if (!empty($_POST["remember"])) {
            setcookie("user_login", $_POST["username"], time() + (10 * 365 * 24 * 60 * 60));
            setcookie("userpassword", $_POST["password"], time() + (10 * 365 * 24 * 60 * 60));
        } else {
            if (isset($_COOKIE["user_login"])) {
                setcookie("user_login", "");
                setcookie("userpassword", "");
            }
        }
        
        $_SESSION['login'] = $_POST['username'];
        echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
    } else {
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
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
                <input type="text" placeholder="Enter your username" required name="username" value="<?php if (isset($_COOKIE["user_login"])) { echo $_COOKIE["user_login"]; } ?>">
            </div>
            <div class="form-group">
                <input type="password" placeholder="Enter your password" required name="password" value="<?php if (isset($_COOKIE["userpassword"])) { echo $_COOKIE["userpassword"]; } ?>">
            </div>
            <div class="remember-me" style="text-align: left;">
                <input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["user_login"])) { ?> checked <?php } ?> />
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
