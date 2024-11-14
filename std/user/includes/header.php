<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('dbconnection.php');

$uid = $_SESSION['sturecmsuid'] ?? null;
if ($uid) {
    $sql = "SELECT * FROM tblstudent WHERE StuID = :uid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
}
?>

<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex align-items-center">
        <a class="navbar-brand brand-logo" href="dashboard.php">
            <strong style="color: white;">KCE</strong>
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center flex-grow-1">
        <?php if ($results && $query->rowCount() > 0): 
            foreach ($results as $row): ?>
                <h5 class="mb-0 font-weight-medium d-none d-lg-flex">
                    <?php echo htmlentities($row->StudentName); ?> Welcome to dashboard!
                </h5>
                <ul class="navbar-nav navbar-nav-right ml-auto">
                    <li class="nav-item dropdown d-none d-xl-inline-flex user-dropdown">
                        <a class="nav-link dropdown-toggle" id="UserDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                            <img class="img-xs rounded-circle ml-2" src="images/faces/face8.jpg" alt="Profile image"> 
                            <span class="font-weight-normal"><?php echo htmlentities($row->StudentName); ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                            <div class="dropdown-header text-center">
                                <img class="img-md rounded-circle" src="images/faces/face8.jpg" alt="Profile image">
                                <p class="mb-1 mt-3"><?php echo htmlentities($row->StudentName); ?></p>
                                <p class="font-weight-light text-muted mb-0"><?php echo htmlentities($row->StudentEmail); ?></p>
                            </div>
                            <a class="dropdown-item" href="student-profile.php"><i class="dropdown-item-icon icon-user text-primary"></i> My Profile</a>
                            <a class="dropdown-item" href="change-password.php"><i class="dropdown-item-icon icon-energy text-primary"></i> Setting</a>
                            <a class="dropdown-item" href="logout.php"><i class="dropdown-item-icon icon-power text-primary"></i>Sign Out</a>
                        </div>
                    </li>
                </ul>
            <?php endforeach; endif; ?>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>
</nav>
