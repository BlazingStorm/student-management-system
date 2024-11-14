<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Initialize variables
    $sdata = '';

    // Code for search functionality
    if (isset($_POST['search'])) {
        $sdata = $_POST['searchdata'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Management System ||| Search Students</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/flag-icon-css/css/flag-icon.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container-scroller">
        <?php include_once('includes/header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include_once('includes/sidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Search Student</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Search Student</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post">
                                        <div class="form-group">
                                            <strong>Search Student:</strong>
                                            <input id="searchdata" type="text" name="searchdata" required="true" class="form-control" placeholder="Search by Student ID" value="<?php echo htmlentities($sdata); ?>">
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="search" id="submit">Search</button>
                                    </form>

                                    <div class="d-sm-flex align-items-center mb-4">
                                        <?php if ($sdata): ?>
                                            <h4 align="center">Result against "<?php echo htmlentities($sdata); ?>" keyword</h4>
                                    </div>
                                    <div class="table-responsive border rounded p-1">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="font-weight-bold">S.No</th>
                                                    <th class="font-weight-bold">Student ID</th>
                                                    <th class="font-weight-bold">Student Class</th>
                                                    <th class="font-weight-bold">Student Name</th>
                                                    <th class="font-weight-bold">Student Email</th>
                                                    <th class="font-weight-bold">Gender</th>
                                                    <th class="font-weight-bold">Date of Birth</th>
                                                    <th class="font-weight-bold">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pageno = isset($_GET['pageno']) ? $_GET['pageno'] : 1;
                                                $no_of_records_per_page = 5;
                                                $offset = ($pageno - 1) * $no_of_records_per_page;

                                                $sql = "SELECT StuID, StudentName, StudentEmail, StudentClass, Gender, DOB 
                                                        FROM tblstudent 
                                                        WHERE StuID LIKE :sdata 
                                                        LIMIT :offset, :recordsPerPage";

                                                $query = $dbh->prepare($sql);
                                                $query->bindValue(':sdata', $sdata . '%', PDO::PARAM_STR);
                                                $query->bindValue(':offset', $offset, PDO::PARAM_INT);
                                                $query->bindValue(':recordsPerPage', $no_of_records_per_page, PDO::PARAM_INT);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                                if ($query->rowCount() > 0) {
                                                    $cnt = 1;
                                                    foreach ($results as $row) {
                                                ?>
                                                        <tr>
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td><?php echo htmlentities($row->StuID); ?></td>
                                                            <td><?php echo htmlentities($row->StudentClass); ?></td>
                                                            <td><?php echo htmlentities($row->StudentName); ?></td>
                                                            <td><?php echo htmlentities($row->StudentEmail); ?></td>
                                                            <td><?php echo htmlentities($row->Gender); ?></td>
                                                            <td><?php echo htmlentities($row->DOB); ?></td>
                                                            <td>
                                                                <div>
                                                                    <a href="edit-student-detail.php?editid=<?php echo htmlentities($row->StuID); ?>"><i class="icon-eye"></i></a>
                                                                    || <a href="manage-students.php?delid=<?php echo ($row->StuID); ?>" onclick="return confirm('Do you really want to Delete ?');"><i class="icon-trash"></i></a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                <?php
                                                        $cnt++;
                                                    }
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="8">No record found against this search</td>
                                                    </tr>
                                                <?php } ?>
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
                                    <?php endif; ?>
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
    <script src="./js/dashboard.js"></script>
</body>
</html>
<?php } ?>
