<?php
session_start();
include('includes/dbconnection.php');

// Check if student is logged in
if (!isset($_SESSION['sturecmsuid'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['sturecmsuid']; // Get the logged-in student's ID

// Fetch personal notices specific to the student from tblnotice
$sql = "SELECT * FROM tblnotice WHERE StuID = :stuID ORDER BY CreationDate DESC";
$query = $dbh->prepare($sql);
$query->bindParam(':stuID', $uid, PDO::PARAM_STR);
$query->execute();
$personalNotices = $query->fetchAll(PDO::FETCH_OBJ);

// Fetch public notices (which are visible to all students) from tblpublicnotice
$sqlPublic = "SELECT * FROM tblpublicnotice ORDER BY CreationDate DESC";
$queryPublic = $dbh->prepare($sqlPublic);
$queryPublic->execute();
$publicNotices = $queryPublic->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Notices</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
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
                    <h4 class="card-title">Your Personal Notices</h4>

                    <!-- Personal Notices -->
                    <?php if (count($personalNotices) > 0): ?>
                        <?php foreach ($personalNotices as $notice): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h5><?php echo htmlentities($notice->NoticeTitle); ?></h5>
                                    <p><?php echo nl2br(htmlentities($notice->NoticeMsg)); ?></p>
                                    <p><small>Posted on: <?php echo htmlentities($notice->CreationDate); ?></small></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No personal notices available at the moment.</p>
                    <?php endif; ?>

                    <h4 class="card-title">Public Notices</h4>

                    <!-- Public Notices -->
                    <?php if (count($publicNotices) > 0): ?>
                        <?php foreach ($publicNotices as $notice): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h5><?php echo htmlentities($notice->NoticeTitle); ?></h5>
                                    <p><?php echo nl2br(htmlentities($notice->NoticeMessage)); ?></p>
                                    <p><small>Posted on: <?php echo htmlentities($notice->CreationDate); ?></small></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No public notices available at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>

</html>
