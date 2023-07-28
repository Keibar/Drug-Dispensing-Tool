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

// Check if the supervisorId is provided in the URL
if (!isset($_GET['supervisorId']) || empty($_GET['supervisorId']) || !is_numeric($_GET['supervisorId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Fetch supervisor details from the database
$supervisorId = $_GET['supervisorId'];
$supervisor = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT s.*, p.name as pharmaceuticalName FROM supervisor s
		INNER JOIN pharmaceutical p ON s.pharmaceuticalId = p.pharmaceuticalId
		WHERE s.supervisorId = $supervisorId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$supervisor = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

// Handle initiating a contract
if (isset($_POST['initiateContract'])) {
	$pharmaceuticalId = $_POST['pharmaceuticalId'];
	$pharmacyId = $_POST['pharmacyId'];
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];

	$conn = new mysqli($host, $username, $databasePassword, $databaseName);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$stmt = $conn->prepare("INSERT INTO contract (pharmacyId, pharmaceuticalId, startDate, endDate) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iiss", $pharmacyId, $pharmaceuticalId, $startDate, $endDate);
		$stmt->execute();
		$stmt->close();
		$conn->close();
	}
}

// Fetch all contracts associated with the supervisor's pharmaceutical
$contracts = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT contract.*, pharmacy.name AS pharmacyName, pharmaceutical.name AS pharmaceuticalName
		FROM contract
		INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
		INNER JOIN pharmaceutical ON contract.pharmaceuticalId = pharmaceutical.pharmaceuticalId
		WHERE contract.pharmaceuticalId = {$supervisor['pharmaceuticalId']}
		ORDER BY contract.startDate DESC";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$contracts[] = $row;
	}
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervisor Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
	<h1>Supervisor Profile</h1>
	<div class="row mt-4">
	    <div class="col-md-4">
		<div class="card">
		    <img src="../static/images/supervisor.jpeg" class="card-img-top" alt="Supervisor Thumbnail" style = "width: 100%; height: 400px;">
		    <div class="card-body">
			<h5 class="card-title"><?= $supervisor['name'] ?></h5>
			<p class="card-text">Email: <?= $supervisor['emailAddress'] ?></p>
			<p class="card-text">Pharmaceutical: <a href="pharmaceutical_profile.php?pharmaceuticalId=<?= $supervisor['pharmaceuticalId'] ?>"><?= $supervisor['pharmaceuticalName'] ?></a></p>
			<?php if ($_SESSION['user'] === 'administrator') { ?>
			    <a href="../edit/edit_supervisor.php?supervisorId=<?= $supervisorId ?>" class="btn btn-primary">Edit Profile</a>
			<?php } ?>
		    </div>
		</div>
	    </div>
	    <div class="col-md-8">
		<h3>Initiate Contract</h3>
		<form action="" method="post">
		    <div class="mb-3">
			<label for="pharmacyId" class="form-label">Select Pharmacy:</label>
			<select class="form-select" name="pharmacyId" id="pharmacyId">
			    <option value="" disabled selected>Select Pharmacy</option>
<?php
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM pharmacy";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		echo "<option value='{$row['pharmacyId']}'>{$row['name']}</option>";
	}
	$conn->close();
}
?>
			</select>
		    </div>
		    <div class="mb-3">
			<label for="startDate" class="form-label">Start Date:</label>
			<input type="date" class="form-control" id="startDate" name="startDate" required>
		    </div>
		    <div class="mb-3">
			<label for="endDate" class="form-label">End Date:</label>
			<input type="date" class="form-control" id="endDate" name="endDate" required>
		    </div>
		    <input type="hidden" name="pharmaceuticalId" value="<?= $supervisor['pharmaceuticalId'] ?>">
		    <button type="submit" class="btn btn-primary" name="initiateContract">Initiate Contract</button>
		</form>
	    </div>
	</div>
	<div class="mt-5">
	    <h3>Contracts</h3>
	    <?php if (!empty($contracts)) { ?>
		<table class="table table-bordered mt-2">
		    <thead>
			<tr>
			    <th>Contract ID</th>
			    <th>Pharmacy</th>
			    <th>Start Date</th>
			    <th>End Date</th>
			    <th>Period</th>
			</tr>
		    </thead>
		    <tbody>
<?php
foreach ($contracts as $contract) {
	$startDate = date('D d M, Y', strtotime($contract['startDate']));
	$endDate = date('D d M, Y', strtotime($contract['endDate']));
	$period = date_diff(date_create($contract['startDate']), date_create($contract['endDate']))->format('%a days');
	echo "<tr>";
	echo "<td><a href='../edit/contract_profile.php?contractId={$contract['contractId']}'>{$contract['contractId']}</a></td>";
	echo "<td><a href='../profiles/pharmacy_profile.php?pharmacyId={$contract['pharmacyId']}'>{$contract['pharmacyName']}</a></td>";
	echo "<td>{$startDate}</td>";
	echo "<td>{$endDate}</td>";
	echo "<td>{$period}</td>";
	echo "</tr>";
}
?>
		    </tbody>
		</table>
<?php } else {
echo "<p>No contracts available.</p>";
} ?>
	</div>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
