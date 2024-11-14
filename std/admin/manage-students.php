<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

// Check if the user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Code for deletion
    if (isset($_GET['delid'])) {
        $rid = $_GET['delid']; // Since StuID is VARCHAR, treat it as a string
        
        // Debugging: Log the ID being deleted and check if it's received correctly
        error_log("Attempting to delete Student ID: " . $rid);

        try {
            // Prepare the DELETE query
            $sql = "DELETE FROM tblstudent WHERE StuID = :rid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':rid', $rid, PDO::PARAM_STR); // Bind as a string since StuID is VARCHAR
            
            // Debugging: Log the SQL query
            error_log("SQL Query: " . $sql);
            
            $query->execute();

            // Check if any row was deleted
            if ($query->rowCount() > 0) {
                echo "<script>alert('Data deleted successfully.');</script>";
            } else {
                // Log if no rows were affected, to understand why it's not deleting
                error_log("No record found with Student ID: " . $rid);
                echo "<script>alert('No record found with that Student ID.');</script>";
            }

            // Redirect to manage-students.php
            echo "<script>window.location.href = 'manage-students.php'</script>";     
        } catch (PDOException $e) {
            // Log any exceptions
            error_log("Deletion failed: " . $e->getMessage());
            echo "<script>alert('Error occurred: " . $e->getMessage() . "');</script>";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Management System || Manage Students</title>
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
                        <h3 class="page-title">Manage Students</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Manage Students</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-sm-flex align-items-center mb-4">
                                        <h4 class="card-title mb-sm-0">Manage Students</h4>
                                        <a href="#" class="text-dark ml-auto mb-3 mb-sm-0">View all Students</a>
                                    </div>
                                    <div class="table-responsive border rounded p-1">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="font-weight-bold">S.No</th>
                                                    <th class="font-weight-bold">Student ID</th>
                                                    <th class="font-weight-bold">Student Name</th>
                                                    <th class="font-weight-bold">Student Email</th>
                                                    <th class="font-weight-bold">Student Class</th>
                                                    <th class="font-weight-bold">Gender</th>
                                                    <th class="font-weight-bold">DOB</th>
                                                    <th class="font-weight-bold">Days Marked</th>
                                                    <th class="font-weight-bold">Attendance Percentage</th>
                                                    <th class="font-weight-bold">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (isset($_GET['pageno'])) {
                                                    $pageno = $_GET['pageno'];
                                                } else {
                                                    $pageno = 1;
                                                }
                                                $no_of_records_per_page = 15;
                                                $offset = ($pageno - 1) * $no_of_records_per_page;

                                                // Ensure offset and records per page are integers
                                                $offset = (int)$offset;
                                                $no_of_records_per_page = (int)$no_of_records_per_page;

                                                // Fetch student details with pagination, joining with tblclass for class name and section
                                                $sql = "SELECT s.StuID, s.StudentName, s.StudentEmail, c.ClassName, c.Section, s.Gender, s.DOB,
                                                               COALESCE(SUM(a.TotalWorkingDays), 0) as TotalDaysMarked,
                                                               COALESCE(SUM(a.PresentDays), 0) as PresentDays,
                                                               COALESCE((SUM(a.PresentDays) / SUM(a.TotalWorkingDays)) * 100, 0) as AttendancePercentage
                                                        FROM tblstudent s
                                                        LEFT JOIN tblclass c ON s.StudentClass = c.ID
                                                        LEFT JOIN tblattendance a ON s.StuID = a.StuID
                                                        GROUP BY s.StuID
                                                        LIMIT :offset, :no_of_records_per_page";

                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':offset', $offset, PDO::PARAM_INT);
                                                $query->bindParam(':no_of_records_per_page', $no_of_records_per_page, PDO::PARAM_INT);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                                if ($query->rowCount() > 0) {
                                                    $cnt = 1;
                                                    foreach ($results as $row) { ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($row->StuID); ?></td>
                                                            <td><?php echo htmlentities($row->StudentName); ?></td>
                                                            <td><?php echo htmlentities($row->StudentEmail); ?></td>
                                                            <td><?php echo htmlentities($row->ClassName . " " . $row->Section); ?></td>
                                                            <td><?php echo htmlentities($row->Gender); ?></td>
                                                            <td><?php echo htmlentities($row->DOB); ?></td>
                                                            <td><?php echo htmlentities($row->TotalDaysMarked); ?></td>
                                                            <td><?php echo number_format($row->AttendancePercentage, 2); ?>%</td>
                                                            <td>
                                                                <div>
                                                                    <a href="edit-student-detail.php?editid=<?php echo htmlentities($row->StuID); ?>"><i class="icon-eye"></i></a>
                                                                    ||
                                                                    <a href="manage-students.php?delid=<?php echo htmlentities($row->StuID); ?>" onclick="return confirm('Do you really want to Delete?');"> <i class="icon-trash"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php $cnt = $cnt + 1;
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='10'>No records found</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div align="left">
                                        <ul class="pagination">
                                            <li><a href="?pageno=1"><strong>First</strong></a></li>
                                            <li class="<?php if ($pageno <= 1) { echo 'disabled'; } ?>">
                                                <a href="<?php if ($pageno <= 1) { echo '#'; } else { echo "?pageno=" . ($pageno - 1); } ?>"><strong>Prev</strong></a>
                                            </li>
                                            <li class="<?php if ($pageno >= $total_pages) { echo 'disabled'; } ?>">
                                                <a href="<?php if ($pageno >= $total_pages) { echo '#'; } else { echo "?pageno=" . ($pageno + 1); } ?>"><strong>Next</strong></a>
                                            </li>
                                            <li><a href="?pageno=<?php echo $total_pages; ?>"><strong>Last</strong></a></li>
                                        </ul>
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
<?php } ?>
