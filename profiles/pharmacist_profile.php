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

// Check if the pharmacistId is provided in the URL
if (!isset($_GET['pharmacistId']) || empty($_GET['pharmacistId']) || !is_numeric($_GET['pharmacistId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Fetch pharmacist details from the database
$pharmacistId = $_GET['pharmacistId'];
$pharmacist = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM pharmacist WHERE pharmacistId = $pharmacistId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$pharmacist = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

// Fetch pharmacy details from the database
$pharmacyId = $pharmacist['pharmacyId'];
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

// Handle updating prescriptions' isDispensed status
if (isset($_POST['updatePrescription']) && isset($_POST['prescriptionId'])) {
	$prescriptionId = $_POST['prescriptionId'];
	$isDispensed = $_POST['isDispensed'] == 'true' ? 1 : 0;

	$conn = new mysqli($host, $username, $databasePassword, $databaseName);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$stmt = $conn->prepare("UPDATE prescription SET isDispensed = ? WHERE prescriptionId = ?");
		$stmt->bind_param("ii", $isDispensed, $prescriptionId);
		$stmt->execute();
		$stmt->close();
		$conn->close();

		header("Location: pharmacist_profile.php?pharmacistId=$pharmacistId");
	}
}

// Fetch latest 15 prescriptions associated with the pharmacist
$prescriptions = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT prescription.*, drug.scientificName, contract.pharmacyId, drug.tradeName, drug.form, pharmacy.name AS pharmacyName, patient_doctor.patientId, patient.name AS patientName
		FROM prescription
		INNER JOIN drug ON prescription.drugId = drug.drugId
		INNER JOIN patient_doctor ON prescription.patientDoctorId = patient_doctor.patientDoctorId
		INNER JOIN patient ON patient_doctor.patientId = patient.patientId
		INNER JOIN contract ON drug.contractId = contract.contractId
		INNER JOIN pharmacy ON contract.pharmacyId = pharmacy.pharmacyId
		WHERE contract.pharmacyId = $pharmacyId
		ORDER BY prescription.isDispensed DESC";
	$result = $conn->query($sql);
	if (!$result) {
		// Error handling for the query execution
		die("Error executing the query: " . $conn->error);
	} else {
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				$prescriptions[] = $row;
			}
		} else {
			echo "<p>No prescriptions found for this pharmacy.</p>";
		}

		$result->free();
	}
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Profile</title>
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
	<h1>Pharmacist Profile</h1>
	<h4><?= $pharmacist['name'] ?></h4>
	<p>Email: <?= $pharmacist['emailAddress'] ?></p>
	<p>Phone: <?= $pharmacist['phoneNumber'] ?></p>
	<p>Pharmacy: <a href="pharmacy_profile.php?pharmacyId=<?= $pharmacyId ?>"><?= $pharmacy['name'] ?></a></p>

	<!-- Edit Profile Button -->
	<?php if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'pharmacist')) { ?>
	    <a href="../edit/edit_pharmacist.php?pharmacistId=<?= $pharmacistId ?>" class="btn btn-primary mt-2">Edit Profile</a>
	<?php } ?>

<?php
// Display the latest 15 prescriptions
if (!empty($prescriptions)) {
?>
	    <h3 class="mt-4">Prescriptions</h3>
	    <table class="table table-bordered mt-2">
		<thead>
		    <tr>
			<th>Prescription ID</th>
			<th>Drug</th>
			<th>Dosage</th>
			<th>Price</th>
			<th>Frequency</th>
			<th>Pharmacy</th>
			<th>Patient</th>
			<th>Status</th>
			<th>Action</th>
		    </tr>
		</thead>
		<tbody>
<?php
	foreach ($prescriptions as $prescription) {
		$status = $prescription['isDispensed'] ? 'Dispensed' : 'Not Dispensed';
		echo "<tr>";
		echo "<td>{$prescription['prescriptionId']}</td>";
		echo "<td>{$prescription['scientificName']} ({$prescription['tradeName']}) - {$prescription['form']}</td>";
		echo "<td>{$prescription['dosage']}</td>";
		echo "<td>{$prescription['price']}</td>";
		echo "<td>{$prescription['frequency']}</td>";
		echo "<td><a href='pharmacy_profile.php?pharmacyId={$prescription['pharmacyId']}'>{$prescription['pharmacyName']}</a></td>";
		echo "<td><a href='patient_profile.php?patientId={$prescription['patientId']}'>{$prescription['patientName']}</a></td>";
		echo "<td>{$status}</td>";
		echo "<td>";
		if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'pharmacist' && $_SESSION['userId'] === $prescription['pharmacistId'])) {
			if (!$prescription['isDispensed']) {
				echo "<button class='btn btn-success' onclick='updatePrescription({$prescription['prescriptionId']}, true)'>Dispense</button>";
			} else {
				echo "<button class='btn btn-success' disabled>Dispensed</button>";
			}
		}
		echo "</td>";
		echo "</tr>";
	}
?>
		</tbody>
	    </table>
<?php
} else {
	echo "<p>No prescriptions available.</p>";
}
?>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
// Function to update prescription isDispensed status
function updatePrescription(prescriptionId, isDispensed) {
	if (confirm("Are you sure you want to mark this prescription as dispensed?")) {
		const formData = new FormData();
		formData.append('updatePrescription', true);
		formData.append('prescriptionId', prescriptionId);
		formData.append('isDispensed', isDispensed);

		fetch('pharmacist_profile.php?pharmacistId=<?= $pharmacistId ?>', {
		method: 'POST',
			body: formData
		})
			.then(response => response.json())
			.then(data => {
			if (data.success) {
				window.location.reload();
			} else {
				alert(data.message);
			}
		})
			.catch(error => console.error('Error:', error));
	}
}

// Function to redirect to prescriptions.php with pharmacyId parameter
function redirectToPrescriptions() {
	window.location.href = `prescriptions.php?pharmacyId=<?= $pharmacyId ?>`;
}
</script>
</body>

</html>
