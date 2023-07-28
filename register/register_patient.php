<?php
// Start session
session_start();


// Database credentials
require_once('../database.php');

// Define variables to hold form input values and errors
$name = $emailAddress = $phoneNumber = $gender = $SSN = $dateOfBirth = $password = $confirmPassword = '';
$errors = array();

// Handle form submission
if (isset($_POST['register'])) {
    // Retrieve form data
    $name = $_POST['name'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNumber = $_POST['phoneNumber'];
    $gender = $_POST['gender'];
    $SSN = $_POST['SSN'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate form data
    if (empty($name)) {
        $errors['name'] = 'Name is required.';
    }

    if (empty($emailAddress)) {
        $errors['emailAddress'] = 'Email Address is required.';
    } elseif (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $errors['emailAddress'] = 'Invalid email format.';
    }

    if (empty($phoneNumber)) {
        $errors['phoneNumber'] = 'Phone Number is required.';
    }

    // Additional validation for SSN, dateOfBirth, and other fields can be added as needed.

    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match.';
    }

    // If there are no errors, proceed with registration
    if (count($errors) === 0) {
        // Hash the password for security
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert patient data into the database
        $conn = new mysqli($host, $username, $databasePassword, $databaseName);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } else {
            $stmt = $conn->prepare("INSERT INTO patient (name, emailAddress, phoneNumber, gender, SSN, dateOfBirth, passwordHash) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $name, $emailAddress, $phoneNumber, $gender, $SSN, $dateOfBirth, $passwordHash);

            if ($stmt->execute()) {
                // Redirect to patient profile page after successful registration
                $patientId = $stmt->insert_id;
                $stmt->close();
                $conn->close();
                header("Location: ../register/register_patient.php");
                exit();
            } else {
                $errors['db_error'] = 'Registration failed. Please try again later.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Patient</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Register Patient</h1>
        <?php if (isset($errors['db_error'])) { ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= $errors['db_error'] ?>
            </div>
        <?php } ?>
        <form action="../register/register_patient.php" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" required>
                <?php if (isset($errors['name'])) { ?>
                    <div class="invalid-feedback"><?= $errors['name'] ?></div>
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address:</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?= $emailAddress ?>" required>
                <?php if (isset($errors['emailAddress'])) { ?>
                    <div class="invalid-feedback"><?= $errors['emailAddress'] ?></div>
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="phoneNumber" class="form-label">Phone Number:</label>
                <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?= $phoneNumber ?>" required>
                <?php if (isset($errors['phoneNumber'])) { ?>
                    <div class="invalid-feedback"><?= $errors['phoneNumber'] ?></div>
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender:</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="" disabled>Select Gender</option>
                    <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="SSN" class="form-label">SSN (Social Security Number):</label>
                <input type="text" class="form-control" id="SSN" name="SSN" value="<?= $SSN ?>" required>
                <?php // Additional validation for SSN can be added as needed. ?>
            </div>
            <div class="mb-3">
                <label for="dateOfBirth" class="form-label">Date of Birth:</label>
                <input type="date" class="form-control" id="dateOfBirth" name="dateOfBirth" value="<?= $dateOfBirth ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <?php if (isset($errors['password'])) { ?>
                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                <?php } ?>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                <?php if (isset($errors['confirmPassword'])) { ?>
                    <div class="invalid-feedback"><?= $errors['confirmPassword'] ?></div>
                <?php } ?>
            </div>
            <button type="submit" class="btn btn-success" name="register">Register</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
