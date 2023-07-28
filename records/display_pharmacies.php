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

// Fetch all pharmacies from the database
$pharmacies = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM pharmacy";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$pharmacies[] = $row;
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
    <title>Display Pharmacies</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>All Pharmacies</h1>
	<table class="table table-bordered table-striped">
	    <thead>
		<tr>
		    <th>Pharmacy ID</th>
		    <th>Name</th>
		    <th>Email Address</th>
		    <th>Phone Number</th>
		</tr>
	    </thead>
	    <tbody>
		<?php foreach ($pharmacies as $pharmacy) : ?>
		    <tr>
			<td><?= $pharmacy['pharmacyId'] ?></td>
			<td><a href="../profiles/pharmacy_profile.php?pharmacyId=<?= $pharmacy['pharmacyId'] ?>"><?= $pharmacy['name'] ?></a></td>
			<td><?= $pharmacy['emailAddress'] ?></td>
			<td><?= $pharmacy['phoneNumber'] ?></td>
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
