
<?php
// Start the session
session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
	<? include "header.php"; ?>
	<title>BackOffice</title>
</head>
<body>
	<h1 class="text-center mb-2">Logout</h1>
	<div class="text-center mb-3 text-muted col-12 fst-italic fs-12"><a href="login.php">See you...</a></div>

	<div class="container">
 	</div> <!-- container -->

	<? include "footer.php" ?>
</body>
</html>
