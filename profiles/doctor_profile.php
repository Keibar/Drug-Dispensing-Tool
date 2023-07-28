<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
$allowedRoles = array('administrator', 'doctor', 'patient');
if (!isset($_SESSION['user']) || !in_array($_SESSION['user'], $allowedRoles)) {
	header("Location: ../errors/403.php");
	exit();
}

// Database credentials
require_once('../database.php');

// Check if the doctorId is provided in the URL
if (!isset($_GET['doctorId']) || empty($_GET['doctorId']) || !is_numeric($_GET['doctorId'])) {
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

// Fetch doctor details from the database
$doctorId = $_GET['doctorId'];
$doctor = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT * FROM doctor WHERE doctorId = $doctorId";
	$result = $conn->query($sql);
	if ($result->num_rows === 1) {
		$doctor = $result->fetch_assoc();
	} else {
		header("Location: ../errors/404.php");
		exit();
	}
	$conn->close();
}

// Function to display a select field for patients not assigned to the doctor
function displayUnassignedPatients($doctorId)
{
	$conn = new mysqli($GLOBALS['host'], $GLOBALS['username'], $GLOBALS['databasePassword'], $GLOBALS['databaseName']);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} else {
		$sql = "SELECT * FROM patient WHERE patientId NOT IN (SELECT patientId FROM patient_doctor WHERE doctorId = $doctorId)";
		$result = $conn->query($sql);
		echo '<select class="form-select" name="patientId" required>';
		echo '<option value="" selected disabled>Select a patient</option>';
		while ($row = $result->fetch_assoc()) {
			echo "<option value='{$row['patientId']}'>{$row['name']}</option>";
		}
		echo '</select>';
		$conn->close();
	}
}

// Check if the form to assign a patient to the doctor is submitted (for administrators and doctors only)
if ($_SESSION['user'] === 'administrator' || $_SESSION['user'] === 'doctor') {
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignPatient'])) {
		// Retrieve selected patientId from the form
		$patientId = $_POST['patientId'];

		// Insert the new assignment into the database
		$conn = new mysqli($host, $username, $databasePassword, $databaseName);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		} else {
			$sql = "INSERT INTO patient_doctor (patientId, doctorId, isPrimary)
				VALUES ($patientId, $doctorId, 0)";

			if ($conn->query($sql) === TRUE) {
				// Refresh the page after assigning the patient to update the assigned patients list
				header("Location: doctor_profile.php?doctorId=$doctorId");
				exit();
			} else {
				echo "Error assigning patient: " . $conn->error;
			}

			$conn->close();
		}
	}
}

// Fetch assigned patients for the doctor from the database
$assignedPatients = array();
$conn = new mysqli($host, $username, $databasePassword, $databaseName);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} else {
	$sql = "SELECT p.* FROM patient p
		INNER JOIN patient_doctor pd ON p.patientId = pd.patientId
		WHERE pd.doctorId = $doctorId";
	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$assignedPatients[] = $row;
	}
	$conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
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
	<h1>Doctor Profile</h1>
	<h4><?= $doctor['name'] ?></h4>
	<img src="../static/images/doctor.avif" class="img-thumbnail" alt="Doctor Thumbnail" style = "width: 250px; height: 250px;">

	<!-- Doctor Details -->
	<h3 class="mt-4">Doctor Details</h3>
	<p>Gender: <?= $doctor['gender'] ?></p>
	<p>Email: <?= $doctor['emailAddress'] ?></p>
	<p>Phone: <?= $doctor['phoneNumber'] ?></p>
	<p>SSN: <?= $doctor['SSN'] ?></p>

	<!-- Edit Profile Button -->
	<?php if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'doctor')) { ?>
	    <a href="../edit/edit_doctor.php?doctorId=<?= $doctorId ?>" class="btn btn-primary mt-2">Edit Profile</a>
	<?php } ?>

<?php
// Display the form to assign a patient (for administrators and doctors only)
if ($_SESSION['user'] === 'administrator' || ($_SESSION['user'] === 'doctor' && $_SESSION['userId'] === $doctorId)) {
?>
	    <h3 class="mt-4">Assign Patient</h3>
	    <form method="post">
<?php
	displayUnassignedPatients($doctorId);
?>
		<button type="submit" name="assignPatient" class="btn btn-primary mt-2">Assign Patient</button>
	    </form>
<?php
}

// Display the assigned patients for the doctor
if (!empty($assignedPatients)) {
?>
    <h3 class="mt-4">Assigned Patients</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($assignedPatients as $patient) {
        $patientId = $patient['patientId'];
        $patientName = $patient['name'];
        $patientEmail = $patient['emailAddress'];
        $patientPhone = $patient['phoneNumber'];
?>
            <tr>
                <td><?= $patientName ?></td>
                <td><?= $patientEmail ?></td>
                <td><?= $patientPhone ?></td>
                <td><a href="patient_profile.php?patientId=<?= $patientId ?>" class="btn btn-primary">View Profile</a></td>
            </tr>
<?php
    }
?>
        </tbody>
    </table>
<?php
} else {
    // Handle the case when there are no assigned patients
    echo "<p>No patients assigned to this doctor.</p>";
}
?>
    <?php echo $footer; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
