<?php
// Start session
session_start();

// Check if the user is authenticated and allowed to access the page
if (isset($_SESSION['user'])) {
    switch ($_SESSION['user']) {
        case 'administrator':
            $profileLink = 'administrator_profile.php?administratorId=' . $_SESSION['administratorId'];
            break;
        case 'patient':
            $profileLink = 'patient_profile.php?patientId=' . $_SESSION['patientId'];
            break;
        case 'doctor':
            $profileLink = 'doctor_profile.php?doctorId=' . $_SESSION['doctorId'];
            break;
        case 'pharmacist':
            $profileLink = 'pharmacist_profile.php?pharmacistId=' . $_SESSION['pharmacistId'];
            break;
        case 'supervisor':
            $profileLink = 'supervisor_profile.php?supervisorId=' . $_SESSION['supervisorId'];
            break;
        default:
            $profileLink = '';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>403 - Access Denied</h1>
        <p>You are not permitted to access this page.</p>
        <?php if (isset($profileLink) && !empty($profileLink)) { ?>
            <p>Click <a href="../profiles/<?= $profileLink ?>">here</a> to go back to your profile page.</p>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>
