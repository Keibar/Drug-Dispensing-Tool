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

// Fetch all contracts from the database along with associated pharmacy and pharmaceutical names
$contracts = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT c.*, p.name AS pharmacyName, ph.name AS pharmaceuticalName 
            FROM contract c
            LEFT JOIN pharmacy p ON c.pharmacyId = p.pharmacyId
            LEFT JOIN pharmaceutical ph ON c.pharmaceuticalId = ph.pharmaceuticalId
            ORDER BY c.contractId";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $contracts[] = $row;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Contracts</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>All Contracts</h1>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Contract ID</th>
                    <th>Date Created</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Pharmacy</th>
                    <th>Pharmaceutical</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $contract) : ?>
                    <tr>
                        <td><?= $contract['contractId'] ?></td>
                        <td><?= $contract['dateCreated'] ?></td>
                        <td><?= $contract['startDate'] ?></td>
                        <td><?= $contract['endDate'] ?></td>
                        <td>
                            <?php if ($contract['pharmacyName']) : ?>
                                <a href="pharmacy_profile.php?pharmacyId=<?= $contract['pharmacyId'] ?>"><?= $contract['pharmacyName'] ?></a>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($contract['pharmaceuticalName']) : ?>
                                <a href="../profiles/pharmaceutical_profile.php?pharmaceuticalId=<?= $contract['pharmaceuticalId'] ?>"><?= $contract['pharmaceuticalName'] ?></a>
                            <?php else : ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
