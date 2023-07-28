<?php
// Start session
session_start();

// Check if the user is authenticated and an administrator
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'administrator') {
    header("Location: ../errors/403.php");
    exit();
}

// Database credentials
require_once('../database.php');

// Check if the administratorId is provided in the URL
if (!isset($_GET['administratorId']) || empty($_GET['administratorId']) || !is_numeric($_GET['administratorId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch administrator details from the database
$administratorId = $_GET['administratorId'];
$administrator = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT * FROM administrator WHERE administratorId = $administratorId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $administrator = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve updated administrator details from the form
    $name = $_POST['name'];
    $pharmaceuticalId = $_POST['pharmaceuticalId'];
    $emailAddress = $_POST['emailAddress'];
    $SSN = $_POST['SSN'];

    // Update administrator details in the database
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "UPDATE administrator SET 
                    name = '$name',
                    pharmaceuticalId = '$pharmaceuticalId',
                    emailAddress = '$emailAddress',
                    SSN = '$SSN'
                WHERE administratorId = $administratorId";

        if ($conn->query($sql) === TRUE) {
            // Redirect to administrator profile page after successful update
            header("Location: ../profiles/administrator_profile.php?administratorId=$administratorId");
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
    <title>Edit Administrator Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Edit Administrator Details</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $administrator['name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="pharmaceuticalId" class="form-label">Pharmaceutical</label>
                <select class="form-control" id="pharmaceuticalId" name="pharmaceuticalId" required>
                    <?php
                    // Fetch all pharmaceuticals from the database
                    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    } else {
                        $sql = "SELECT pharmaceuticalId, name FROM pharmaceutical";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            $selected = $administrator['pharmaceuticalId'] === $row['pharmaceuticalId'] ? 'selected' : '';
                            echo "<option value='{$row['pharmaceuticalId']}' $selected>{$row['name']}</option>";
                        }
                        $conn->close();
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $administrator['emailAddress'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="SSN" class="form-label">SSN</label>
                <input type="text" class="form-control" id="SSN" name="SSN" value="<?= $administrator['SSN'] ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
