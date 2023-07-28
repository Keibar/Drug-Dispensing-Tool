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

// Check if the pharmacistId is provided in the URL
if (!isset($_GET['pharmacistId']) || empty($_GET['pharmacistId']) || !is_numeric($_GET['pharmacistId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch pharmacist details from the database
$pharmacistId = $_GET['pharmacistId'];
$pharmacist = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM pharmacist WHERE pharmacistId = $pharmacistId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $pharmacist = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated pharmacist details from the form
    $name = $_POST['name'];
    $pharmacyId = $_POST['pharmacyId'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];
    $SSN = $_POST['SSN'];

    // Update pharmacist details in the database
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "UPDATE pharmacist SET 
                    name = '$name',
                    pharmacyId = '$pharmacyId',
                    emailAddress = '$emailAddress',
                    phoneNumber = '$phoneNumber',
                    SSN = '$SSN'
                WHERE pharmacistId = $pharmacistId";

        if ($conn->query($sql) === TRUE) {
            // Redirect to pharmacist profile page after successful update
            header("Location: ../profiles/pharmacist_profile.php?pharmacistId=$pharmacistId");
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
    <title>Edit Pharmacist Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Edit Pharmacist Details</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $pharmacist['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="pharmacyId" class="form-label">Pharmacy</label>
                <select class="form-control" id="pharmacyId" name="pharmacyId" required>
                    <?php
                    // Fetch all pharmacies from the database
                    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    } else {
                        $sql = "SELECT pharmacyId, name FROM pharmacy";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = $pharmacist['pharmacyId'] === $row['pharmacyId'] ? 'selected' : '';
                            echo "<option value='{$row['pharmacyId']}' $selected>{$row['name']}</option>";
                        }
                        $conn->close();
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $pharmacist['emailAddress'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $pharmacist['phoneNumber'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="SSN" class="form-label">SSN</label>
                <input type="text" class="form-control" id="SSN" name="SSN" value="<?= $pharmacist['SSN'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
