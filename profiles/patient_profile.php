<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'patient', 'doctor');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
	header("Location: ../errors/403.php");
	exit();
}

// Database credentials
require_once('../database.php');

// Check if the patientId is provided in the URL
if (!isset($_GET['patientId']) || empty($_GET['patientId']) || !is_numeric($_GET['patientId'])) {
	header("Location: ../errors/404.php");
	exit();
}

// Function to display age using Moment.js
function displayAge($dateOfBirth)
{
	$dob = new DateTime($dateOfBirth);
	$now = new DateTime();
	$interval = $now->diff($dob);
	return $interval->format('%y years old');
}

// Fetch patient details from the database
$patientId = $_GET['patientId'];
$patient = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM patient WHERE patientId = $patientId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$patient = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

// Function to display a select field for doctors not assigned to the patient
function displayUnassignedDoctors($patientId)
{
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['databasePassword'], $GLOBALS['databaseName']);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$sql = "SELECT * FROM doctor WHERE doctorId NOT IN (SELECT doctorId FROM patient_doctor WHERE patientId = $patientId)";
		$result = $conn->query($sql);
		echo '<select class="form-select" name="doctorId" required>';
		echo '<option value="" selected disabled>Select a doctor</option>';
		while ($row = $result->fetch_assoc()) {
			echo "<option value='{$row['doctorId']}'>{$row['name']}</option>";
		}
		echo '</select>';
		$conn->close();
	}
}

// Check if the form to assign a doctor to the patient is submitted (for patients and administrators only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'patient') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignDoctor'])) {
		// Retrieve selected doctorId from the form
		$doctorId = $_POST['doctorId'];

		// Insert the new assignment into the database
		$conn = new mysqli($host, $username, $databasePassword, $databaseName);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} else {
			$sql = "INSERT INTO patient_doctor (patientId, doctorId, isPrimary)
				VALUES ($patientId, $doctorId, 0)";

			if ($conn->query($sql) === TRUE) {
				// Refresh the page after assigning the doctor to update the assigned doctors list
				header("Location: patient_profile.php?patientId=$patientId");
				exit();
			} else {
				echo "Error assigning doctor: " . $conn->error;
			}

			$conn->close();
		}
	}
}

// Check if the form to add a prescription is submitted (for doctors and administrators only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'doctor') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPrescription'])) {
		// Retrieve prescription details from the form
		$drugId = $_POST['drugId'];
		$dosage = $_POST['dosage'];
		$price = $_POST['price'];
		$frequency = $_POST['frequency'];

		// Insert the new prescription into the database
		$conn = new mysqli($host, $username, $databasePassword, $databaseName);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} else {
			$sql = "INSERT INTO prescription (drugId, dosage, patientDoctorId, price, frequency, isDispensed)
				VALUES ($drugId, '$dosage', $patientId, $price, '$frequency', 0)";

			if ($conn->query($sql) === TRUE) {
				// Refresh the page after adding the prescription to update the prescriptions table
				header("Location: patient_profile.php?patientId=$patientId");
				exit();
			} else {
				echo "Error adding prescription: " . $conn->error;
			}

			$conn->close();
		}
	}
}

// Fetch assigned doctors for the patient from the database
$assignedDoctors = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT d.* FROM doctor d
		INNER JOIN patient_doctor pd ON d.doctorId = pd.doctorId
		WHERE pd.patientId = $patientId";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$assignedDoctors[] = $row;
	}
	$conn->close();
}

// Fetch prescriptions for the patient from the database
$prescriptions = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT p.prescriptionId, p.dosage, p.price, p.frequency, p.isDispensed, d.scientificName, d.tradeName, d.form, pr.name AS pharmacyName, pr.pharmacyId, doc.doctorId, doc.name AS doctorName, pd.isPrimary, pd.patientDoctorId
		FROM prescription p
		INNER JOIN drug d ON p.drugId = d.drugId
		LEFT JOIN patient_doctor pd ON p.patientDoctorId = pd.patientDoctorId
		LEFT JOIN doctor doc ON pd.doctorId = doc.doctorId 
LEFT JOIN contract ON d.contractId = contract.contractId  
		LEFT JOIN pharmacy pr ON contract.pharmacyId = pr.pharmacyId
		WHERE pd.patientId = $patientId";
	$result = $conn->query($sql);
	if (!$result) {
        // Error handling for the query execution
        die("Error executing the query: " . $conn->error);
    } else {
        // Check if there are any records
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $prescriptions[] = $row;
            }
        } else {
            echo "No prescriptions found for this patient.";
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
    <title>Patient Profile</title>
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
	<h1>Patient Profile</h1>
	<h4><?= $patient['name'] ?></h4>
	<p><?= displayAge($patient['dateOfBirth']) ?></p>
	<img src="../static/images/patient.png" class="img-thumbnail" alt="Patient Thumbnail" style = "width: 300px; height; 300px;">

	<!-- Patient Details -->
	<h3 class="mt-4">Patient Details</h3>
	<p>Gender: <?= $patient['gender'] ?></p>
	<p>Email: <?= $patient['emailAddress'] ?></p>
	<p>Phone: <?= $patient['phoneNumber'] ?></p>
	<p>SSN: <?= $patient['SSN'] ?></p>
	<p>Date of Birth: <?= $patient['dateOfBirth'] ?></p>

	<!-- Edit Profile Button -->
	<?php if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'patient')) { ?>
	    <a href="../edit/edit_patient.php?patientId=<?= $patientId ?>" class="btn btn-primary mt-2">Edit Profile</a>
	<?php } ?>
<?php
// Display the form to assign a doctor (for patients and administrators only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'patient') {
?>
	    <h3 class="mt-4">Assign Doctor</h3>
	    <form method="post">
<?php
	displayUnassignedDoctors($patientId);
?>
		<button type="submit" name="assignDoctor" class="btn btn-primary mt-2">Assign Doctor</button>
	    </form>
<?php
}

// Display the assigned doctors for the patient
if (!empty($assignedDoctors)) {
?>
    <h3 class="mt-4">Assigned Doctors</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($assignedDoctors as $doctor) {
        $doctorName = $doctor['name'];
        $doctorEmail = $doctor['emailAddress'];
        $doctorPhone = $doctor['phoneNumber'];
?>
            <tr>
                <td><?= $doctorName ?></td>
                <td><?= $doctorEmail ?></td>
                <td><?= $doctorPhone ?></td>
            </tr>
<?php
    }
?>
        </tbody>
    </table>
<?php
} else {
    // Handle the case when there are no assigned doctors
    echo "<p>No doctors assigned to this patient.</p>";
}
?>

<?php
// Display the form to add a prescription (for doctors and administrators only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'doctor') {
	$conn = new mysqli($host, $username, $databasePassword, $databaseName);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$sql = "SELECT drugId, scientificName, tradeName, form FROM drug";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
?>
		    <h3 class="mt-4">Add Prescription</h3>
		    <form method="post">
			<div class="mb-3">
			    <label for="drugId" class="form-label">Select Drug</label>
			    <select class="form-select" name="drugId" required>
				<option value="" selected disabled>Select a drug</option>
<?php
			while ($row = $result->fetch_assoc()) {
				echo "<option value='{$row['drugId']}'>{$row['scientificName']} ({$row['tradeName']}) - {$row['form']}</option>";
			}
?>
			    </select>
			</div>
			<div class="mb-3">
			    <label for="dosage" class="form-label">Dosage</label>
			    <input type="text" class="form-control" id="dosage" name="dosage" required>
			</div>
			<div class="mb-3">
			    <label for="price" class="form-label">Price</label>
			    <input type="number" class="form-control" id="price" name="price" required step="0.01">
			</div>
			<div class="mb-3">
			    <label for="frequency" class="form-label">Frequency</label>
			    <input type="text" class="form-control" id="frequency" name="frequency" required>
			</div>
			<button type="submit" name="addPrescription" class="btn btn-primary">Add Prescription</button>
		    </form>
<?php
		} else {
			echo "<p>No drugs found in the database. Please add drugs before adding prescriptions.</p>";
		}
		$conn->close();
	}
}

// Display the prescriptions table
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
			<th>Doctor</th>
			<th>Status</th>
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
		echo "<td><a href='doctor_profile.php?doctorId={$prescription['doctorId']}'>{$prescription['doctorName']}</a></td>";
		echo "<td>{$status}</td>";
		echo "</tr>";
	}
?>
		</tbody>
	    </table>
<?php
}
?>
    </div>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
