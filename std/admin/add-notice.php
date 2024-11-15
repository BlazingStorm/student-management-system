<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check if admin is logged in
if (strlen($_SESSION['sturecmsaid'] == 0)) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {
        // Get notice details and student ID from the form
        $nottitle = $_POST['nottitle'];
        $notmsg = $_POST['notmsg'];
        $stuID = $_POST['stuID']; // Student ID to whom the notice will be assigned
        
        // Insert the notice into the tblnotice table
        $sql = "INSERT INTO tblnotice (NoticeTitle, StuID, NoticeMsg) VALUES (:nottitle, :stuID, :notmsg)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':nottitle', $nottitle, PDO::PARAM_STR);
        $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
        $query->bindParam(':notmsg', $notmsg, PDO::PARAM_STR);
        $query->execute();
        
        $LastInsertId = $dbh->lastInsertId();
        if ($LastInsertId > 0) {
            echo '<script>alert("Notice has been added successfully.")</script>';
            // Perform PHP redirect to avoid "Not Found" error
            header('Location: add-notice.php');
            exit(); // Stop further script execution
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System | Add Personal Notice</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="container-scroller">
        <!-- Header and Sidebar -->
        <?php include_once('includes/header.php');?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php');?>

            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Add Personal Notice</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Add Personal Notice</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align: center;">Add Personal Notice</h4>
                                    <form class="forms-sample" method="post">
                                        <div class="form-group">
                                            <label for="nottitle">Notice Title</label>
                                            <input type="text" name="nottitle" class="form-control" required="true">
                                        </div>

                                        <div class="form-group">
                                            <label for="notmsg">Notice Message</label>
                                            <textarea name="notmsg" class="form-control" required="true"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="stuID">Select Student</label>
                                            <select name="stuID" class="form-control" required="true">
                                                <?php
                                                    // Fetch all students from tblstudent
                                                    $sql = "SELECT StuID, StudentName FROM tblstudent ORDER BY StudentName";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $students = $query->fetchAll(PDO::FETCH_OBJ);

                                                    foreach ($students as $student) {
                                                        echo "<option value='" . $student->StuID . "'>" . $student->StudentName . "</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>

                                        <button type="submit" name="submit" class="btn btn-primary mr-2">Add Notice</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer -->
                <?php include_once('includes/footer.php');?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>
