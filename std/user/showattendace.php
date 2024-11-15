<?php
session_start();
include('includes/dbconnection.php');

// Check if the session variable is empty or not set
if (!isset($_SESSION['sturecmsuid'])) {
    header('Location: login.php');
    exit();
}

// Get the student ID from the session
$uid = $_SESSION['sturecmsuid'];

// Query to calculate attendance totals for the student
$sql = "SELECT StuID, 
               SUM(TotalWorkingDays) AS TotalWorkingDays, 
               SUM(PresentDays) AS PresentDays 
        FROM tblattendance 
        WHERE StuID = :uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);

$absentDays = 0;
$attendancePercentage = 0;

if ($result && $result->TotalWorkingDays > 0) {
    $totalWorkingDays = $result->TotalWorkingDays;
    $presentDays = $result->PresentDays;

    // Calculate absent days and attendance percentage
    $absentDays = $totalWorkingDays - $presentDays;
    $attendancePercentage = ($presentDays / $totalWorkingDays) * 100;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Management System | Attendance</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style for attendance shortage */
        .shortage {
            background-color: #ffcccc; /* Light red color for shortage */
        }
        .legend {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <!-- Header include -->
        <?php include_once('includes/header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <!-- Sidebar include -->
            <?php include_once('includes/sidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Attendance Details</h4>
                                    <?php if ($result && $result->TotalWorkingDays > 0): ?>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Total Working Days</th>
                                                <th>Present Days</th>
                                                <th>Absent Days</th>
                                                <th>Attendance Percentage (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="<?php echo ($attendancePercentage < 80) ? 'shortage' : ''; ?>">
                                                <td><?php echo htmlentities($result->StuID); ?></td>
                                                <td><?php echo htmlentities($totalWorkingDays); ?></td>
                                                <td><?php echo htmlentities($presentDays); ?></td>
                                                <td><?php echo htmlentities($absentDays); ?></td>
                                                <td><?php echo number_format($attendancePercentage, 2); ?>%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <p>No attendance data available for this student.</p>
                                    <?php endif; ?>

                                    <!-- Legend for attendance shortage -->
                                    <div class="legend">
                                        <p><span class="shortage" style="display: inline-block; width: 20px; height: 20px; margin-right: 5px;"></span> Attendance below 80% (Shortage)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer include -->
                <?php include_once('includes/footer.php'); ?>
            </div>
        </div>
    </div>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>
