<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'supervisor', 'pharmacist');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
    header("Location: ../errors/403.php");
    exit();
}

// Database credentials
require_once('../database.php');

// Check if the contractId is provided in the URL
if (!isset($_GET['contractId']) || empty($_GET['contractId']) || !is_numeric($_GET['contractId'])) {
    header("Location: ../errors/404.php");
    exit();
}

// Fetch contract details from the database
$contractId = $_GET['contractId'];
$contract = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT c.*, p.name AS pharmacyName, ph.name AS pharmaceuticalName
            FROM contract c
            INNER JOIN pharmacy p ON c.pharmacyId = p.pharmacyId
            INNER JOIN pharmaceutical ph ON c.pharmaceuticalId = ph.pharmaceuticalId
            WHERE c.contractId = $contractId";

    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $contract = $result->fetch_assoc();
    } else {
        header("Location: ../errors/404.php");
        exit();
    }
    $conn->close();
}

// Check if the form to add a drug is submitted (for administrators and supervisors only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'supervisor') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addDrug'])) {
        // Retrieve drug details from the form
        $scientificName = $_POST['scientificName'];
        $tradeName = $_POST['tradeName'];
        $form = $_POST['form'];

        // Insert the new drug into the database
        $conn = new mysqli($host, $username, $databasePassword, $databaseName);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            $sql = "INSERT INTO drug (scientificName, tradeName, contractId, form)
                    VALUES ('$scientificName', '$tradeName', $contractId, '$form')";

            if ($conn->query($sql) === TRUE) {
                // Refresh the page after adding the drug to update the table
                header("Location: ../profiles/contract_profile.php?contractId=$contractId");
                exit();
            } else {
                echo "Error inserting record: " . $conn->error;
            }

            $conn->close();
        }
    }
}

// Function to display the drug table with pagination
function displayDrugTable($contractId, $currentPage, $resultsPerPage)
{
    // Calculate the offset for pagination
    $offset = ($currentPage - 1) * $resultsPerPage;

    // Fetch drugs associated with the contract from the database
    $conn = new mysqli($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['databasePassword'], $GLOBALS['databaseName']);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $sql = "SELECT * FROM drug WHERE contractId = $contractId LIMIT $offset, $resultsPerPage";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<table class="table table-bordered mt-4">';
            echo '<thead><tr><th>Drug ID</th><th>Scientific Name</th><th>Trade Name</th><th>Form</th></tr></thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['drugId']}</td><td>{$row['scientificName']}</td><td>{$row['tradeName']}</td><td>{$row['form']}</td></tr>";
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No drugs associated with this contract.</p>';
        }
        $conn->close();
    }
}

// Pagination settings
$resultsPerPage = 10;
$totalResults = 0;
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Get the total number of drugs associated with the contract
    $sql = "SELECT COUNT(*) AS total FROM drug WHERE contractId = $contractId";
    $result = $conn->query($sql);
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $totalResults = $row['total'];
    }
    $conn->close();
}

// Calculate the total number of pages for pagination
$totalPages = ceil($totalResults / $resultsPerPage);

// Get the current page from the URL or default to the first page
$currentPage = 1;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $currentPage = $_GET['page'];
    if ($currentPage < 1) {
        $currentPage = 1;
    } elseif ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract Details</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4">Contract Details</h1>
            <p class="lead">Contract ID: <?= $contract['contractId'] ?></p>
            <hr class="my-4">
            <p>Pharmacy: <a href="../profiles/pharmacy_profile.php?pharmacyId=<?= $contract['pharmacyId'] ?>"><?= $contract['pharmacyName'] ?></a></p>
            <p>Pharmaceutical: <a href="../profiles/pharmaceutical_profile.php?pharmaceuticalId=<?= $contract['pharmaceuticalId'] ?>"><?= $contract['pharmaceuticalName'] ?></a></p>
            <p>Start Date: <?= $contract['startDate'] ?></p>
            <p>End Date: <?= $contract['endDate'] ?></p>
        </div>

        <?php
        // Display the "Add Drug" button for administrators and supervisors
        if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'supervisor') {
            echo '<a href="../register/register_drug.php?contractId=' . $contractId . '" class="btn btn-primary btn-lg btn-block mb-4">Add Drug</a>';
        }

        // Display the drug table with pagination
        displayDrugTable($contractId, $currentPage, $resultsPerPage);
        ?>

        <nav aria-label="Drug Pagination">
            <ul class="pagination justify-content-center mt-4">
                <?php
                // Display pagination links
                for ($page = 1; $page <= $totalPages; $page++) {
                    $activeClass = $page === $currentPage ? 'active' : '';
                    echo "<li class='page-item $activeClass'><a class='page-link' href='contract_profile.php?contractId=$contractId&page=$page'>$page</a></li>";
                }
                ?>
            </ul>
        </nav>
    </div>
    <?php echo $footer; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
