<?php
// Start session
session_start();

// Database credentials
require_once('../database.php');

// Handle login form submission
if (isset($_POST['login'])) {
    $selectedTable = $_POST['userTable'];
    $emailAddress = $_POST['emailAddress'];
    $password = $_POST['password'];

    // Check which table to query based on selected user role
    $table = '';
    switch ($selectedTable) {
        case 'administrator':
            $table = 'administrator';
            break;
        case 'patient':
            $table = 'patient';
            break;
        case 'doctor':
            $table = 'doctor';
            break;
        case 'pharmacist':
            $table = 'pharmacist';
            break;
        case 'supervisor':
            $table = 'supervisor';
            break;
    }

    // Query the database to check if the user exists and validate the password
    $conn = new mysqli($host, $username, $databasePassword, $databaseName);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        $stmt = $conn->prepare("SELECT {$table}Id, passwordHash FROM {$table} WHERE emailAddress = ?");
        $stmt->bind_param("s", $emailAddress);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $passwordHash);
            $stmt->fetch();

            if (password_verify($password, $passwordHash)) {
                // Password is correct, set session variables and redirect to appropriate page
                $_SESSION['user'] = $selectedTable;
                $_SESSION[$selectedTable . 'Id'] = $userId;
                switch ($selectedTable) {
                    case 'administrator':
                        header("Location: ../profiles/administrator_profile.php?administratorId={$userId}");
                        break;
                    case 'patient':
                        header("Location: ../profiles/patient_profile.php?patientId={$userId}");
                        break;
                    case 'doctor':
                        header("Location: ../profiles/doctor_profile.php?doctorId={$userId}");
                        break;
                    case 'pharmacist':
                        header("Location: ../profiles/pharmacist_profile.php?pharmacistId={$userId}");
                        break;
                    case 'supervisor':
                        header("Location: ../profiles/supervisor_profile.php?supervisorId={$userId}");
                        break;
                }
                exit();
            } else {
                $error = "Invalid credentials. Please try again.";
            }
        } else {
            $error = "Invalid credentials. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
<?php 
include_once '../commons.php';
echo $header;
?>
    <div class="container mt-5">
        <h1>Login</h1>
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?= $error ?>
            </div>
        <?php } ?>
        <form action="" method="post">
            <div class="mb-3">
                <label for="userTable" class="form-label">Select User Role:</label>
                <select class="form-select" name="userTable" id="userTable" required>
                    <option value="" disabled selected>Select User Role</option>
                    <option value="administrator">Administrator</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="pharmacist">Pharmacist</option>
                    <option value="supervisor">Supervisor</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="emailAddress" class="form-label">Email Address:</label>
                <input type="email" class="form-control" id="emailAddress" name="emailAddress" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" name="login">Login</button>
        </form>
    </div>
    <?php echo $footer; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
