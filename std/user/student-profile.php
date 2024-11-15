<?php
session_start();
include('includes/dbconnection.php');

// Check if student is logged in
if (!isset($_SESSION['sturecmsuid'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['sturecmsuid'];

// Fetch the student's details from the database
$sql = "SELECT * FROM tblstudent WHERE StuID = :uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$student = $query->fetch(PDO::FETCH_OBJ);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css"> <!-- Ensure you have the icon library -->
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container-scroller">
        <?php include_once('includes/header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Student Profile</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Personal Information</h5>
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Student Name</th>
                                                    <td><?php echo htmlentities($student->StudentName); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Student Email</th>
                                                    <td><?php echo htmlentities($student->StudentEmail); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Class</th>
                                                    <td><?php echo htmlentities($student->StudentClass); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Gender</th>
                                                    <td><?php echo htmlentities($student->Gender); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Date of Birth</th>
                                                    <td><?php echo htmlentities($student->DOB); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Father's Name</th>
                                                    <td><?php echo htmlentities($student->FatherName); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Mother's Name</th>
                                                    <td><?php echo htmlentities($student->MotherName); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Contact Number</th>
                                                    <td><?php echo htmlentities($student->ContactNumber); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Address</th>
                                                    <td><?php echo htmlentities($student->Address); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
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
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>

</html>
