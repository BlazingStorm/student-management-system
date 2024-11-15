<?php
session_start();
include('includes/dbconnection.php');

// Check if student is logged in
if (!isset($_SESSION['sturecmsuid'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['sturecmsuid'];

// Fetch the student's details (optional: can be used for display)
$sql = "SELECT * FROM tblstudent WHERE StuID = :uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $uid, PDO::PARAM_STR);
$query->execute();
$student = $query->fetch(PDO::FETCH_OBJ);

// Fetch remaining balance for the selected year (if any)
$remainingBalance = null;
$yearSelected = null;
$paymentWarning = '';

// Handle form submission for fee payment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];
    $tuition_fee = $_POST['tuition_fee'];
    $hostel_fee = $_POST['hostel_fee'];
    $total_fee = $tuition_fee + $hostel_fee;

    // Fetch the most recent remaining balance for the selected year
    $sql = "SELECT RemainingBalance FROM tblfees WHERE StuID = :uid AND Year = :year ORDER BY PaymentDate DESC LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':year', $year, PDO::PARAM_STR);
    $query->execute();
    $feeDetails = $query->fetch(PDO::FETCH_OBJ);

    if ($feeDetails) {
        // Subtract the total fee from the existing remaining balance
        $remainingBalance = $feeDetails->RemainingBalance - $total_fee;
    } else {
        // If no record exists, assume initial balance of ₹225,000
        $remainingBalance = 225000 - $total_fee;
    }

    // Check if the remaining balance is negative and prevent overpayment
    if ($remainingBalance < 0) {
        $paymentWarning = "Warning: The total fees exceed the remaining balance by ₹" . abs($remainingBalance) . ". Please adjust the amount to avoid overpayment.";
    } else {
        // Update the balance in tblfees
        $sql = "INSERT INTO tblfees (StuID, FeeAmount, Year, FeeStatus, RemainingBalance, PaymentDate) 
                VALUES (:StuID, :FeeAmount, :Year, 'Paid', :RemainingBalance, NOW())";
        $query = $dbh->prepare($sql);
        $query->bindParam(':StuID', $uid, PDO::PARAM_STR);
        $query->bindParam(':FeeAmount', $total_fee, PDO::PARAM_STR);
        $query->bindParam(':Year', $year, PDO::PARAM_STR);
        $query->bindParam(':RemainingBalance', $remainingBalance, PDO::PARAM_STR);
        $query->execute();

        echo "<script>alert('Fees for $year have been paid successfully!');</script>";

        // Reload the page to reset the form
        header('Location: fees.php');
        exit();
    }
}

// Handle year selection change to fetch remaining balance
if (isset($_GET['year'])) {
    $yearSelected = $_GET['year'];

    // Fetch the most recent remaining balance for the selected year
    $sql = "SELECT RemainingBalance FROM tblfees WHERE StuID = :uid AND Year = :year ORDER BY PaymentDate DESC LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->bindParam(':year', $yearSelected, PDO::PARAM_STR);
    $query->execute();
    $feeDetails = $query->fetch(PDO::FETCH_OBJ);

    // If no payment record exists, assume the default balance of ₹225,000
    if ($feeDetails) {
        $remainingBalance = $feeDetails->RemainingBalance;
    } else {
        $remainingBalance = 225000; // Default balance if no previous record
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Fees</title>
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
                    <div class="row">
                        <div class="col-lg-12 stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Pay Tuition and Hostel Fees</h4>

                                    <!-- Display remaining balance based on selected year -->
                                    <?php if ($remainingBalance !== null): ?>
                                        <p><strong>Remaining Balance for <?php echo htmlentities($yearSelected); ?>: ₹<?php echo number_format($remainingBalance, 2); ?></strong></p>
                                    <?php endif; ?>

                                    <!-- Display warning if overpayment is attempted -->
                                    <?php if ($paymentWarning !== ''): ?>
                                        <div class="alert alert-warning">
                                            <?php echo $paymentWarning; ?>
                                        </div>
                                    <?php endif; ?>

                                    <form method="POST" action="fees.php">
                                        <div class="form-group">
                                            <label for="year">Select Year</label>
                                            <select class="form-control" id="year" name="year" required onchange="window.location.href='fees.php?year=' + this.value;">
                                                <option value="">Select Year</option>
                                                <option value="1st Year" <?php if ($yearSelected == '1st Year') echo 'selected'; ?>>1st Year</option>
                                                <option value="2nd Year" <?php if ($yearSelected == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                                                <option value="3rd Year" <?php if ($yearSelected == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                                                <option value="4th Year" <?php if ($yearSelected == '4th Year') echo 'selected'; ?>>4th Year</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tuition_fee">Tuition Fee</label>
                                            <input type="number" class="form-control" id="tuition_fee" name="tuition_fee" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="hostel_fee">Hostel Fee</label>
                                            <input type="number" class="form-control" id="hostel_fee" name="hostel_fee" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Pay Fees</button>
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
