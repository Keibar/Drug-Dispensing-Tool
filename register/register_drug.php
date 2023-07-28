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

// Check if the contractId is provided in the URL
if (!isset($_GET['contractId']) || empty($_GET['contractId']) || !is_numeric($_GET['contractId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Retrieve drug details from the form
	$contractId = $_GET['contractId'];
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
			// Redirect back to the contract profile page after successful drug addition
			header("Location: ../profiles/contract_profile.php?contractId=$contractId");
			exit();
		} else {
			echo "Error inserting record: " . $conn->error;
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
    <title>Register Drug</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Register Drug</h1>
	<form method="post">
	    <div class="mb-3">
		<label for="scientificName" class="form-label">Scientific Name</label>
		<input type="text" class="form-control" id="scientificName" name="scientificName" required>
	    </div>
	    <div class="mb-3">
		<label for="tradeName" class="form-label">Trade Name</label>
		<input type="text" class="form-control" id="tradeName" name="tradeName" required>
	    </div>
	    <div class="mb-3">
		<label for="form" class="form-label">Form</label>
		<input type="text" class="form-control" id="form" name="form" required>
	    </div>
	    <button type="submit" class="btn btn-success">Add Drug</button>
	</form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
