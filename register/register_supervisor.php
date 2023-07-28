<?php
// Start session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user'])) {
	header("Location: ../authentication/login.php");
	exit();
}

// Check if the user is an administrator
if ($_SESSION['user'] !== 'administrator') {
	header("Location: ../errors/403.php");
	exit();
}

// Database credentials
require_once('../database.php');

// Query all available pharmaceuticals for the select field
$pharmaceuticals = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT pharmaceuticalId, name FROM pharmaceutical";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$pharmaceuticals[] = $row;
		}
	}
	$conn->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	// Database connection
	$conn = new mysqli($host, $username, $databasePassword, $databaseName);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// Prepare and bind statements to prevent SQL injection
	$stmt = $conn->prepare("INSERT INTO supervisor (name, pharmaceuticalId, emailAddress, SSN, passwordHash) VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sisss", $name, $pharmaceuticalId, $emailAddress, $SSN, $passwordHash);

	// Get the form data
	$name = $_POST["name"];
	$pharmaceuticalId = $_POST["pharmaceuticalId"];
	$emailAddress = $_POST["emailAddress"];
	$SSN = $_POST["SSN"];
	$passwordHash = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash the password

	// Execute the statement
	if ($stmt->execute()) {
		// Registration successful
		echo "<script>alert('Supervisor registered successfully!');</script>";
	} else {
		// Registration failed
		echo "<script>alert('Registration failed. Please try again later.');</script>";
	}

	$stmt->close();
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Supervisor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Register Supervisor</h1>
	<form method="post">
	    <div class="mb-3">
		<label for="name" class="form-label">Name</label>
		<input type="text" class="form-control" id="name" name="name" required>
	    </div>
	    <div class="mb-3">
		<label for="pharmaceuticalId" class="form-label">Pharmaceutical</label>
		<select class="form-control" id="pharmaceuticalId" name="pharmaceuticalId" required>
		    <?php foreach ($pharmaceuticals as $pharmaceutical) : ?>
			<option value="<?= $pharmaceutical['pharmaceuticalId'] ?>"><?= $pharmaceutical['name'] ?></option>
		    <?php endforeach; ?>
		</select>
	    </div>
	    <div class="mb-3">
		<label for="emailAddress" class="form-label">Email Address</label>
		<input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
	    </div>
	    <div class="mb-3">
		<label for="SSN" class="form-label">SSN</label>
		<input type="text" class="form-control" id="SSN" name="SSN" required>
	    </div>
	    <div class="mb-3">
		<label for="password" class="form-label">Password</label>
		<input type="password" class="form-control" id="password" name="password" required>
	    </div>
	    <button type="submit" class="btn btn-success">Register</button>
	</form>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
