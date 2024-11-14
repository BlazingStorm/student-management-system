<?php
session_start();
// error_reporting(0);
include('includes/dbconnection.php');

// Check if the session variable is empty or not set
if (empty($_SESSION['sturecmsstuid'])) {
    header('location:logout.php');
    exit(); // Always call exit() after a header redirect to prevent further execution
} else {
    // Fetch all public notices
    $sql = "SELECT ID, NoticeTitle, NoticeMessage, CreationDate FROM tblpublicnotice ORDER BY CreationDate DESC"; 
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Management System | View Notice</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- Layout styles -->
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include_once('includes/header.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <?php include_once('includes/sidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">View Notice</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Notice</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    if ($query->rowCount() > 0) {
                                        echo '<table border="1" class="table table-bordered mg-b-0">';
                                        foreach ($results as $row) {
                                            echo '<tr align="center" class="table-warning">
                                                    <td colspan="4" style="font-size:20px;color:blue">Notice</td>
                                                  </tr>
                                                  <tr class="table-info">
                                                      <th>Notice Announced Date</th>
                                                      <td>' . $row->CreationDate . '</td>
                                                  </tr>
                                                  <tr class="table-info">
                                                      <th>Notice Title</th>
                                                      <td>' . $row->NoticeTitle . '</td>
                                                  </tr>
                                                  <tr class="table-info">
                                                      <th>Message</th>
                                                      <td>' . $row->NoticeMessage . '</td>
                                                  </tr>';
                                        }
                                        echo '</table>';
                                    } else {
                                        echo '<p style="color:red;">No Notice Found</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <?php include_once('includes/footer.php'); ?>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>

<?php
}
?>
