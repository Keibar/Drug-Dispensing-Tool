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

// Fetch all administrators from the database
$administrators = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM administrator";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$administrators[] = $row;
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
    <title>Display Administrators</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>All Administrators</h1>
	<table class="table table-bordered table-striped">
	    <thead>
		<tr>
		    <th>Administrator ID</th>
		    <th>Email Address</th>
		    <th>Phone Number</th>
		    <th>Gender</th>
		</tr>
	    </thead>
	    <tbody>
		<?php foreach ($administrators as $administrator) : ?>
		    <tr>
			<td><?= $administrator['administratorId'] ?></td>
			<td><?= $administrator['emailAddress'] ?></td>
			<td><?= $administrator['phoneNumber'] ?></td>
			<td><?= $administrator['gender'] ?></td>
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
