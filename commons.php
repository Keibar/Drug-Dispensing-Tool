<?php
// Define common variables
$header = <<<EOD
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
	<a class="navbar-brand" href="../basic/home.php">Drug-Dispenser</a>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
	    <ul class="navbar-nav">
		<li class="nav-item">
		    <a class="nav-link" href="../basic/home.php">Home</a>
		</li>

EOD;

if (isset($_SESSION['user'])) {
	$userDisplay = ucfirst($_SESSION['user']);
	$userType = strtolower($userDisplay);
	$userId = $_SESSION[$_SESSION['user'] . 'Id'];
	$header .= <<<EOD
		<li class="nav-item">
		    <a class="nav-link" href="../authentication/logout.php">
			<i class="fas fa-sign-out-alt"></i> Logout
		    </a>
		</li>
		<li class="nav-item">
		    <a class="nav-link" href="../profiles/{$userType}_profile.php?{$userType}Id={$userId}">
			<i class="fas fa-user"></i> $userDisplay
		    </a>
		</li>
EOD;
} else {
	$header .= <<<EOD
		<li class="nav-item">
		    <a class="nav-link" href="../authentication/login.php">
			<i class="fas fa-sign-in-alt"></i> Login
		    </a>
		</li>
		<li class="nav-item">
		    <a class="nav-link" href="../register/register_patient.php">
			<i class="fas fa-user-plus"></i> Sign Up
		    </a>
		</li>
EOD;
}

$header .= <<<EOD
	    </ul>
	</div>
    </div>
</nav>
EOD;

// Define footer
$date = date('Y');
$footer = <<<EOD
<footer class="mt-5 py-3 bg-dark text-light text-center">
    <div class="container">
	&copy; Drug Dispensing Tool - $date
    </div>
</footer>
EOD;
?>
