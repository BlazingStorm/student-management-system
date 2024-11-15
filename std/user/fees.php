<?php
session_start();
include('includes/dbconnection.php');

// Check if student is logged in
if (!isset($_SESSION['sturecmsuid'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['sturecmsuid'];

// Fetch the student's details and fees
$sql = "SELECT * FROM tblstudent WHERE StuID = :uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$student = $query->fetch(PDO::FETCH_OBJ);

// Check if the student is found
if (!$student) {
    echo "Student not found.";
    exit();
}

// Handle form submission for updating fee payments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated fees and payment details
    $tuitionFeePaid = $_POST['PaidTuitionFees'];
    $hostelFeePaid = $_POST['PaidHostelFees'];
    $paymentDate = date('Y-m-d H:i:s');

    // Calculate remaining fees
    $remainingTuitionFees = $student->TuitionFees - $tuitionFeePaid;
    $remainingHostelFees = $student->HostelFees - $hostelFeePaid;

    // Update the fees data in the database
    $sql = "UPDATE tblstudent SET 
                PaidTuitionFees = :PaidTuitionFees,
                PaidHostelFees = :PaidHostelFees,
                PaidDate = :PaidDate,
                RemainingTuitionFees = :RemainingTuitionFees,
                RemainingHostelFees = :RemainingHostelFees
            WHERE StuID = :StuID";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':PaidTuitionFees', $tuitionFeePaid, PDO::PARAM_STR);
    $query->bindParam(':PaidHostelFees', $hostelFeePaid, PDO::PARAM_STR);
    $query->bindParam(':PaidDate', $paymentDate, PDO::PARAM_STR);
    $query->bindParam(':RemainingTuitionFees', $remainingTuitionFees, PDO::PARAM_STR);
    $query->bindParam(':RemainingHostelFees', $remainingHostelFees, PDO::PARAM_STR);
    $query->bindParam(':StuID', $uid, PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Payment updated successfully!');</script>";
}

// Fetch the updated student data with fees
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
    <title>Fees Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 20px;
            border-radius: 10px 10px 0 0;
        }
        .table th, .table td {
            text-align: center;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .form-group label {
            font-weight: bold;
        }
        .form-group input {
            margin-top: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .icon {
            color: #28a745;
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
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
                                <div class="card-header">
                                    <h4 class="card-title">Student Fees Details</h4>
                                </div>
                                <div class="card-body">
                                    <h5 class="mb-4">Welcome, <?php echo htmlentities($student->StudentName); ?>!</h5>

                                    <!-- Display student's fee details -->
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Detail</th>
                                                <th>Amount (₹)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tuition Fee</td>
                                                <td><?php echo number_format($student->TuitionFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Hostel Fee</td>
                                                <td><?php echo number_format($student->HostelFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total Fees</td>
                                                <td><?php echo number_format($student->TuitionFees + $student->HostelFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Paid Tuition Fee</td>
                                                <td><?php echo number_format($student->PaidTuitionFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Paid Hostel Fee</td>
                                                <td><?php echo number_format($student->PaidHostelFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Remaining Tuition Fee</td>
                                                <td><?php echo number_format($student->TuitionFees - $student->PaidTuitionFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Remaining Hostel Fee</td>
                                                <td><?php echo number_format($student->HostelFees - $student->PaidHostelFees, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Remaining Total Fee</td>
                                                <td><?php echo number_format(($student->TuitionFees + $student->HostelFees) - ($student->PaidTuitionFees + $student->PaidHostelFees), 2); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <br><br>
                                    <!-- Form to make payment -->
                                    <form method="POST" action="fees.php">
                                        <div class="form-group">
                                            <label for="PaidTuitionFees">Paid Tuition Fees</label>
                                            <input type="number" class="form-control" id="PaidTuitionFees" name="PaidTuitionFees" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="PaidHostelFees">Paid Hostel Fees</label>
                                            <input type="number" class="form-control" id="PaidHostelFees" name="PaidHostelFees" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Update Payment</button>
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
