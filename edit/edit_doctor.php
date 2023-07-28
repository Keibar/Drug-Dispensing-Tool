<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'doctor');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
    header("Location: ../errors/403.php");
    exit();
}

// Database credentials
require_once('../database.php');

// Check if the doctorId is provided in the URL
if (!isset($_GET['doctorId']) || empty($_GET['doctorId']) || !is_numeric($_GET['doctorId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch doctor details from the database
$doctorId = $_GET['doctorId'];
$doctor = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM doctor WHERE doctorId = $doctorId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $doctor = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated doctor details from the form
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];
    $SSN = $_POST['SSN'];

    // Update doctor details in the database
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "UPDATE doctor SET 
                    name = '$name',
                    gender = '$gender',
                    emailAddress = '$emailAddress',
                    phoneNumber = '$phoneNumber',
                    SSN = '$SSN'
                WHERE doctorId = $doctorId";

        if ($conn->query($sql) === TRUE) {
            // Redirect to doctor profile page after successful update
            header("Location: ../profiles/doctor_profile.php?doctorId=$doctorId");
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
    <title>Edit Doctor Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Edit Doctor Details</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $doctor['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="Male" <?= $doctor['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $doctor['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $doctor['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $doctor['emailAddress'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $doctor['phoneNumber'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="SSN" class="form-label">SSN</label>
                <input type="text" class="form-control" id="SSN" name="SSN" value="<?= $doctor['SSN'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
