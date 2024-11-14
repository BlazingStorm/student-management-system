<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

// Check if the user is logged in
if (strlen($_SESSION['sturecmsaid']) == 0) {
    header('location:logout.php');
} else {
    // Fetch available classes for dropdown
    $classSql = "SELECT * FROM tblclass";
    $classQuery = $dbh->prepare($classSql);
    $classQuery->execute();
    $classes = $classQuery->fetchAll(PDO::FETCH_ASSOC);

    // Check if edit ID is set
    if (isset($_GET['editid'])) {
        $editid = $_GET['editid'];

        // Fetch student details
        $sql = "SELECT * FROM tblstudent WHERE StuID = :editid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':editid', $editid, PDO::PARAM_STR);
        $query->execute();

        if ($query->rowCount() > 0) {
            $student = $query->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<script>alert('No student found with that ID.');</script>";
            echo "<script>window.location.href = 'manage-students.php';</script>";
            exit;
        }
    }

    // Handle form submission to update student details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $editid = $_POST['editid'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $classID = $_POST['class']; // Class ID selected from dropdown
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $address = $_POST['address']; // Address field
        
        // Prepare and execute the update query
        $sql = "UPDATE tblstudent SET 
                StudentName = :name, 
                StudentEmail = :email, 
                StudentClass = :class, 
                Gender = :gender, 
                DOB = :dob, 
                Address = :address 
                WHERE StuID = :editid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name);
        $query->bindParam(':email', $email);
        $query->bindParam(':class', $classID); // Store Class ID
        $query->bindParam(':gender', $gender);
        $query->bindParam(':dob', $dob);
        $query->bindParam(':address', $address);
        $query->bindParam(':editid', $editid, PDO::PARAM_STR);
        $query->execute();

        echo "<script>alert('Student updated successfully!');</script>";
        echo "<script>window.location.href = 'manage-students.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Student Details</title>
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
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
                        <h3 class="page-title">Edit Student Details</h3>
                    </div>
                    <div class="row">
                        <div class="col-md-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <form method="post" action="edit-student-detail.php">
                                        <input type="hidden" name="editid" value="<?php echo htmlentities($student['StuID']); ?>">
                                        
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlentities($student['StudentName']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email:</label>
                                            <input type="email" class="form-control" name="email" value="<?php echo htmlentities($student['StudentEmail']); ?>" required>
                                        </div>

                                        <!-- Class Dropdown similar to add-student.php -->
                                        <div class="form-group">
                                            <label for="class">Class:</label>
                                            <select class="form-control" name="class" required>
                                                <option value="">Select Class</option>
                                                <?php foreach ($classes as $class) { ?>
                                                    <option value="<?php echo htmlentities($class['ID']); ?>" 
                                                        <?php if ($student['StudentClass'] == $class['ID']) echo 'selected'; ?>>
                                                        <?php echo htmlentities($class['ClassName']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gender">Gender:</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="Male" <?php if ($student['Gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                <option value="Female" <?php if ($student['Gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                <option value="Other" <?php if ($student['Gender'] == 'Other') echo 'selected'; ?>>Other</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="dob">Date of Birth:</label>
                                            <input type="date" class="form-control" name="dob" value="<?php echo htmlentities($student['DOB']); ?>" required>
                                        </div>

                                        <!-- Address Field -->
                                        <div class="form-group">
                                            <label for="address">Address:</label>
                                            <textarea class="form-control" name="address" rows="3" required><?php echo htmlentities($student['Address']); ?></textarea>
                                        </div>

                                        <input type="submit" class="btn btn-primary" value="Update">
                                        <a href="manage-students.php" class="btn btn-secondary">Cancel</a>
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
    <script src="js/off-canvas.js"></script>
    <script src="js/misc.js"></script>
</body>
</html>
