<?php
require_once('../database.php');

// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
if (!isset($_SESSION['user']) || $_SESSION['user'] !== 'administrator') {
	header("Location: ../errors/403.php");
	exit();
}

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Administrator Profile</h1>
	<div class="card mt-4">
	    <div class="card-body">
		<p class="card-text">Gender: <?= $administrator['gender'] ?></p>
		<p class="card-text">PhoneNumber: <?= $administrator['phoneNumber'] ?></p>
		<p class="card-text">Email: <?= $administrator['emailAddress'] ?></p>
		<a href="../edit/edit_administrator.php?administratorId=<?= $administratorId ?>" class="btn btn-success">Edit Profile</a>
	    </div>
	</div>

	<div class="mt-5">
	    <h3>Registration Links</h3>
	    <div class="list-group mt-2">
		<a href="../register/register_administrator.php" class="list-group-item list-group-item-action">Register Administrator</a>
		<a href="../register/register_patient.php" class="list-group-item list-group-item-action">Register Patient</a>
		<a href="../register/register_doctor.php" class="list-group-item list-group-item-action">Register Doctor</a>
		<a href="../register/register_supervisor.php" class="list-group-item list-group-item-action">Register Supervisor</a>
		<a href="../register/register_pharmacy.php" class="list-group-item list-group-item-action">Register Pharmacy</a>
		<a href="../register/register_pharmaceutical.php" class="list-group-item list-group-item-action">Register Pharmaceutical</a>
		<a href="../register/register_pharmacist.php" class="list-group-item list-group-item-action">Register Pharmacist</a>
	    </div>
	</div>

	<div class="mt-5" style = "padding-bottom: 30px;">
	    <h3>Records Links</h3>
	    <div class="list-group mt-2">
		<a href="../records/display_administrators.php" class="list-group-item list-group-item-action">Administrators Records</a>
		<a href="../records/display_patients.php" class="list-group-item list-group-item-action">Patients Records</a>
		<a href="../records/display_doctors.php" class="list-group-item list-group-item-action">Doctors Records</a>
		<a href="../records/display_supervisors.php" class="list-group-item list-group-item-action">Supervisors Records</a>
		<a href="../records/display_pharmacies.php" class="list-group-item list-group-item-action">Pharmacies Records</a>
		<a href="../records/display_pharmaceuticals.php" class="list-group-item list-group-item-action">Pharmaceuticals Records</a>
		<a href="../records/display_pharmacists.php" class="list-group-item list-group-item-action">Pharmacists Records</a>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
