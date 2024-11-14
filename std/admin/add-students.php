<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if the user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        // Get form inputs
        $stuname = $_POST['stuname'];
        $stuemail = $_POST['stuemail'];
        $stuclass = $_POST['stuclass'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $stuid = $_POST['stuid'];
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $connum = $_POST['connum'];
        $uname = $_POST['uname'];
        $password = ($_POST['password']); 
        $address = $_POST['address']; // New Address field

        // Check if username or student ID already exists
        $ret = "SELECT UserName FROM tblstudent WHERE UserName=:uname OR StuID=:stuid";
        $query = $dbh->prepare($ret);
        $query->bindParam(':uname', $uname, PDO::PARAM_STR);
        $query->bindParam(':stuid', $stuid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() == 0) {
            // Insert student data into the database
            $sql = "INSERT INTO tblstudent (StuID, StudentName, StudentEmail, StudentClass, Gender, DOB, FatherName, MotherName, ContactNumber, UserName, Password, Address) 
                    VALUES (:stuid, :stuname, :stuemail, :stuclass, :gender, :dob, :fname, :mname, :connum, :uname, :password, :address)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':stuid', $stuid, PDO::PARAM_STR);
            $query->bindParam(':stuname', $stuname, PDO::PARAM_STR);
            $query->bindParam(':stuemail', $stuemail, PDO::PARAM_STR);
            $query->bindParam(':stuclass', $stuclass, PDO::PARAM_STR); 
            $query->bindParam(':gender', $gender, PDO::PARAM_STR);
            $query->bindParam(':dob', $dob, PDO::PARAM_STR);
            $query->bindParam(':fname', $fname, PDO::PARAM_STR);
            $query->bindParam(':mname', $mname, PDO::PARAM_STR);
            $query->bindParam(':connum', $connum, PDO::PARAM_STR);
            $query->bindParam(':uname', $uname, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);  // Bind Address
            $query->execute();

            // Check for successful insertion
            $LastInsertId = $dbh->lastInsertId();
            if ($LastInsertId > 0) {
                echo '<script>alert("Student has been added.")</script>';
                echo "<script>window.location.href ='add-students.php'</script>";
            } else {
                echo '<script>alert("Something Went Wrong. Please try again")</script>';
            }
        } else {
            echo "<script>alert('Username or Student ID already exists. Please try again');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Management System || Add Students</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="container-scroller">
        <?php include_once('includes/header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title"> Add Students </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> Add Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align: center;">Add Students</h4>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="exampleInputName1">Student Name</label>
                                            <input type="text" name="stuname" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Student Email</label>
                                            <input type="email" name="stuemail" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail3">Student Class</label>
                                            <select name="stuclass" class="form-control" required>
                                                <option value="">Select Class</option>
                                                <?php 
                                                $sql2 = "SELECT * FROM tblclass";
                                                $query2 = $dbh->prepare($sql2);
                                                $query2->execute();
                                                $result2 = $query2->fetchAll(PDO::FETCH_OBJ);
                                                foreach ($result2 as $row1) { ?>  
                                                    <option value="<?php echo htmlentities($row1->ID); ?>">
                                                        <?php echo htmlentities($row1->ClassName); ?> <?php echo htmlentities($row1->Section); ?>
                                                    </option>
                                                <?php } ?> 
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Gender</label>
                                            <select name="gender" class="form-control" required>
                                                <option value="">Choose Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Date of Birth</label>
                                            <input type="date" name="dob" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Student ID</label>
                                            <input type="text" name="stuid" class="form-control" required>
                                        </div>
                                        <h3>Parents/Guardian's details</h3>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Father's Name</label>
                                            <input type="text" name="fname" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Mother's Name</label>
                                            <input type="text" name="mname" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Contact Number</label>
                                            <input type="text" name="connum" class="form-control" required maxlength="15" pattern="[0-9]+">
                                        </div>
                                        <h3>Address</h3>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Address</label>
                                            <textarea name="address" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <h3>Login details</h3>
                                        <div class="form-group">
                                            <label for="exampleInputName1">User Name</label>
                                            <input type="text" name="uname" class="form-control" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputName1">Password</label>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary mr-2" name="submit">Add</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once('includes/footer.php'); ?>
            </div>
        </div>
    </div>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script
