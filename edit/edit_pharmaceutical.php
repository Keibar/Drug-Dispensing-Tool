<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'supervisor');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
    header("Location: ../errors/403.php");
    exit();
}

// Database credentials
require_once('../database.php');

// Check if the pharmaceuticalId is provided in the URL
if (!isset($_GET['pharmaceuticalId']) || empty($_GET['pharmaceuticalId']) || !is_numeric($_GET['pharmaceuticalId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch pharmaceutical details from the database
$pharmaceuticalId = $_GET['pharmaceuticalId'];
$pharmaceutical = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM pharmaceutical WHERE pharmaceuticalId = $pharmaceuticalId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $pharmaceutical = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated pharmaceutical details from the form
    $name = $_POST['name'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];

    // Update pharmaceutical details in the database
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "UPDATE pharmaceutical SET 
                    name = '$name',
                    emailAddress = '$emailAddress',
                    phoneNumber = '$phoneNumber'
                WHERE pharmaceuticalId = $pharmaceuticalId";

        if ($conn->query($sql) === TRUE) {
            // Redirect to pharmaceutical profile page after successful update
            header("Location: ../profiles/pharmaceutical_profile.php?pharmaceuticalId=$pharmaceuticalId");
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
    <title>Edit Pharmaceutical Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Edit Pharmaceutical Details</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $pharmaceutical['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $pharmaceutical['emailAddress'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $pharmaceutical['phoneNumber'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
