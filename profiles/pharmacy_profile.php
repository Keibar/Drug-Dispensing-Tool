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

// Fetch pharmacists assigned to the pharmacy from the database
$assignedPharmacists = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM pharmacist WHERE pharmacyId = $pharmacyId";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$assignedPharmacists[] = $row;
	}
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Pharmacy Profile</h1>
	<h4><?= $pharmacy['name'] ?></h4>

	<!-- Pharmacy Details -->
	<h3 class="mt-4">Pharmacy Details</h3>
	<p>Email: <?= $pharmacy['emailAddress'] ?></p>
	<p>Phone: <?= $pharmacy['phoneNumber'] ?></p>

	<!-- Edit Profile Button -->
	<?php if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'pharmacist')) { ?>
	    <a href="../edit/edit_pharmacy.php?pharmacyId=<?= $pharmacyId ?>" class="btn btn-primary mt-2">Edit Pharmacy Details</a>
	<?php } ?>

<?php
// Display the pharmacists assigned to the pharmacy
if (!empty($assignedPharmacists)) {
?>
	    <h3 class="mt-4">Assigned Pharmacists</h3>
	    <table class="table table-bordered mt-2">
		<thead>
		    <tr>
			<th>Pharmacist ID</th>
			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
		    </tr>
		</thead>
		<tbody>
<?php
	foreach ($assignedPharmacists as $pharmacist) {
		echo "<tr>";
		echo "<td>{$pharmacist['pharmacistId']}</td>";
		echo "<td><a href = '../profiles/pharmacist_profile.php?pharmacistId={$pharmacist['pharmacistId']}'>{$pharmacist['name']}</td>";
		echo "<td>{$pharmacist['emailAddress']}</td>";
		echo "<td>{$pharmacist['phoneNumber']}</td>";
		echo "</tr>";
	}
?>
		</tbody>
	    </table>
<?php
} else {
	echo "<p>No pharmacists assigned to this pharmacy.</p>";
}
?>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
