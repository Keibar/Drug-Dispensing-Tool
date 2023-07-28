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

// Number of patients per page
$patientsPerPage = 10;

// Calculate total number of patients
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT COUNT(*) AS total FROM patient";
	$result = $conn->query($sql);
	$totalPatients = $result->fetch_assoc()['total'];
	$conn->close();
}

// Calculate total number of pages
$totalPages = ceil($totalPatients / $patientsPerPage);

// Get the current page from the URL, default to 1 if not set or invalid
$currentpage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Ensure current page is within bounds
if ($currentpage < 1) {
	$currentpage = 1;
} elseif ($currentpage > $totalPages) {
	$currentpage = $totalPages;
}

// Calculate the offset for the LIMIT clause in the SQL query
$offset = ($currentpage - 1) * $patientsPerPage;

// Fetch patients for the current page
$patients = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM patient LIMIT $offset, $patientsPerPage";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$patients[] = $row;
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
    <title>Display Patients</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>All Patients</h1>
	<table class="table table-bordered table-striped">
	    <thead>
		<tr>
		    <th>ID</th>
		    <th>Name</th>
		    <th>Gender</th>
		    <th>Email Address</th>
		    <th>Phone Number</th>
		    <th>SSN</th>
		</tr>
	    </thead>
	    <tbody>
		<?php foreach ($patients as $patient) : ?>
		    <tr>
			<td><?= $patient['patientId'] ?></td>
			<td><a href="../profiles/patient_profile.php?patientId=<?= $patient['patientId'] ?>"><?= $patient['name'] ?></a></td>
			<td><?= $patient['gender'] ?></td>
			<td><?= $patient['emailAddress'] ?></td>
			<td><a href="tel:<?= $patient['phoneNumber'] ?>"><?= $patient['phoneNumber'] ?></a></td>
			<td><?= $patient['SSN'] ?></td>
		    </tr>
		<?php endforeach; ?>
	    </tbody>
	</table>

	<!-- Pagination -->
	<nav aria-label="Page navigation">
	    <ul class="pagination justify-content-center">
		<?php if ($currentpage > 1) : ?>
		    <li class="page-item"><a class="page-link" href="?page=1">First</a></li>
		    <li class="page-item"><a class="page-link" href="?page=<?= $currentpage - 1 ?>">Previous</a></li>
		<?php endif; ?>

		<?php for ($i = max(1, $currentpage - 2); $i <= min($currentpage + 2, $totalPages); $i++) : ?>
		    <li class="page-item <?php if ($i === $currentpage) echo 'active'; ?>">
			<a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
		    </li>
		<?php endfor; ?>

		<?php if ($currentpage < $totalPages) : ?>
		    <li class="page-item"><a class="page-link" href="?page=<?= $currentpage + 1 ?>">Next</a></li>
		    <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>">Last</a></li>
		<?php endif; ?>
	    </ul>
	</nav>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
