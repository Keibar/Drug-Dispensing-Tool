<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'pharmacist');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
    header("Location: ../errors/403.php");
    exit();
}

// Database credentials
require_once('../database.php');

// Check if the pharmacyId is provided in the URL
if (!isset($_GET['pharmacyId']) || empty($_GET['pharmacyId']) || !is_numeric($_GET['pharmacyId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch pharmacy details from the database
$pharmacyId = $_GET['pharmacyId'];
$pharmacy = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM pharmacy WHERE pharmacyId = $pharmacyId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $pharmacy = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated pharmacy details from the form
    $name = $_POST['name'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];

    // Update pharmacy details in the database
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "UPDATE pharmacy SET 
                    name = '$name',
                    emailAddress = '$emailAddress',
                    phoneNumber = '$phoneNumber'
                WHERE pharmacyId = $pharmacyId";

        if ($conn->query($sql) === TRUE) {
            // Redirect to pharmacy profile page after successful update
            header("Location: ../profiles/pharmacy_profile.php?pharmacyId=$pharmacyId");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pharmacy Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Edit Pharmacy Details</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $pharmacy['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $pharmacy['emailAddress'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $pharmacy['phoneNumber'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
