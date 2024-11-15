<?php
session_start();
include('includes/dbconnection.php');

// Check if the admin is logged in
if (strlen($_SESSION['sturecmsaid'] == 0)) {
    header('location:logout.php');
} else {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System | Fee Status</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container-scroller">
        <!-- Include header -->
        <?php include_once('includes/header.php'); ?>
        
        <div class="container-fluid page-body-wrapper">
            <!-- Include sidebar -->
            <?php include_once('includes/sidebar.php'); ?>
            
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="font-weight-semibold">Fee Status Summary</h5>
                                    <?php 
                                    // Query to get all students' fee details
                                    $sql = "SELECT StudentName, TuitionFees, HostelFees, PaidTuitionFees, 
                                                   PaidHostelFees, RemainingTuitionFees, RemainingHostelFees,
                                                   (RemainingTuitionFees + RemainingHostelFees) AS TotalRemainingFees 
                                            FROM tblstudent";
                                    $query = $dbh->prepare($sql);
                                    $query->execute();
                                    $results = $query->fetchAll(PDO::FETCH_ASSOC);

                                    if (count($results) > 0) {
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Student Name</th>
                                                    <th>Tuition Fees</th>
                                                    <th>Hostel Fees</th>
                                                    <th>Paid Tuition Fees</th>
                                                    <th>Paid Hostel Fees</th>
                                                    <th>Remaining Tuition Fees</th>
                                                    <th>Remaining Hostel Fees</th>
                                                    <th>Total Remaining Fees</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                foreach ($results as $row) {
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlentities($row['StudentName']); ?></td>
                                                    <td><?php echo number_format($row['TuitionFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['HostelFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['PaidTuitionFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['PaidHostelFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['RemainingTuitionFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['RemainingHostelFees'], 2); ?></td>
                                                    <td><?php echo number_format($row['TotalRemainingFees'], 2); ?></td>
                                                </tr>
                                                <?php 
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php 
                                    } else {
                                        echo "<p>No fee data found for students.</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Include footer -->
                <?php include_once('includes/footer.php'); ?>
            </div>
        </div>
    </div>

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>
<?php } ?>
