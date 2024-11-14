<?php
session_start();
error_reporting(E_ALL); // Enable all error reporting
ini_set('display_errors', 1); // Display errors on the page
include('includes/dbconnection.php');

// Check if the user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Handle attendance marking
    if (isset($_POST['submit'])) {
        $date = $_POST['attendance_date'];
        $totalWorkingDays = 0;

        // Fetch all students
        $studentsQuery = $dbh->query("SELECT StuID FROM tblstudent");
        $students = $studentsQuery->fetchAll(PDO::FETCH_OBJ);

        foreach ($students as $student) {
            $stuID = $student->StuID;
            $isPresent = isset($_POST['present'][$stuID]) ? 1 : 0;

            // Update attendance for each student
            $attendanceSql = "INSERT INTO tblattendance (StuID, AttendanceDate, PresentDays, TotalWorkingDays) 
                              VALUES (:stuID, :date, :isPresent, 1)
                              ON DUPLICATE KEY UPDATE 
                              PresentDays = PresentDays + :isPresent, 
                              TotalWorkingDays = TotalWorkingDays + 1";
            
            $query = $dbh->prepare($attendanceSql);
            $query->bindParam(':stuID', $stuID, PDO::PARAM_STR);
            $query->bindParam(':date', $date, PDO::PARAM_STR);
            $query->bindParam(':isPresent', $isPresent, PDO::PARAM_INT);
            $query->execute();

            // Count total working days (assuming each attendance marking counts as a working day)
            if ($isPresent) {
                $totalWorkingDays++;
            }
        }

        echo "<script>alert('Attendance marked successfully');</script>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Attendance Management</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container-scroller">
        <?php include_once('includes/header.php');?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php');?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Mark Attendance</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Mark Attendance</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="attendance_date">Attendance Date:</label>
                                            <input type="date" name="attendance_date" required>
                                        </div>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="font-weight-bold">Student ID</th>
                                                    <th class="font-weight-bold">Student Name</th>
                                                    <th class="font-weight-bold">Present</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch all students to mark attendance
                                                $studentsQuery = $dbh->query("SELECT StuID, StudentName FROM tblstudent");
                                                $students = $studentsQuery->fetchAll(PDO::FETCH_OBJ);
                                                foreach ($students as $student) { ?>
                                                    <tr>
                                                        <td><?php echo htmlentities($student->StuID); ?></td>
                                                        <td><?php echo htmlentities($student->StudentName); ?></td>
                                                        <td>
                                                            <input type="checkbox" name="present[<?php echo htmlentities($student->StuID); ?>]" value="1">
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <button type="submit" name="submit" class="btn btn-primary">Submit Attendance</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include_once('includes/footer.php');?>
            </div>
        </div>
    </div>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>
<?php } ?>
