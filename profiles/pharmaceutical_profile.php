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

// Check if the pharmaceuticalId is provided in the URL
if (!isset($_GET['pharmaceuticalId']) || empty($_GET['pharmaceuticalId']) || !is_numeric($_GET['pharmaceuticalId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Fetch pharmaceutical details from the database
$pharmaceuticalId = $_GET['pharmaceuticalId'];
$pharmaceutical = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM pharmaceutical WHERE pharmaceuticalId = $pharmaceuticalId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$pharmaceutical = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

// Fetch all supervisors associated with the pharmaceutical
$supervisors = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM supervisor WHERE pharmaceuticalId = {$pharmaceutical['pharmaceuticalId']}";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$supervisors[] = $row;
	}
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmaceutical Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Pharmaceutical Profile</h1>
	<div class="row mt-4">
	    <div class="col-md-4">
		<div class="card">
		    <div class="card-body">
			<h5 class="card-title"><?= $pharmaceutical['name'] ?></h5>
			<p class="card-text">Email: <?= $pharmaceutical['emailAddress'] ?></p>
			<?php if ($_SESSION['user'] === 'administrator') { ?>
			    <a href="../edit/edit_pharmaceutical.php?pharmaceuticalId=<?= $pharmaceuticalId ?>" class="btn btn-primary">Edit Profile</a>
			<?php } ?>
		    </div>
		</div>
	    </div>
	    <div class="col-md-8">
		<h3>Supervisors</h3>
		<?php if (!empty($supervisors)) { ?>
		    <table class="table table-bordered mt-2">
			<thead>
			    <tr>
				<th>ID</th>
				<th>Name</th>
				<th>Email</th>
				<th>Actions</th>
			    </tr>
			</thead>
			<tbody>
<?php
foreach ($supervisors as $supervisor) {
	echo "<tr>";
	echo "<td>{$supervisor['supervisorId']}</td>";
	echo "<td>{$supervisor['name']}</td>";
	echo "<td>{$supervisor['emailAddress']}</td>";
	echo "<td>";
	echo "<a href='supervisor_profile.php?supervisorId={$supervisor['supervisorId']}' class='btn btn-primary btn-sm'>View</a>";
	if ($_SESSION['user'] === 'administrator') {
		echo " <a href='../edit/edit_supervisor.php?supervisorId={$supervisor['supervisorId']}' class='btn btn-secondary btn-sm'>Edit</a>";
	}
	echo "</td>";
	echo "</tr>";
}
?>
			</tbody>
		    </table>
<?php } else {
echo "<p>No supervisors available.</p>";
} ?>
	    </div>
	</div>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
