<?php

require_once('config.php');

// Database Connection
$con = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if (mysqli_connect_errno()) {
    die("Unable connect to database");
}

$username = htmlentities(trim($_GET['username']));
$code     = htmlentities(trim($_GET['code']));

$query = "SELECT email_id, verified FROM users WHERE username=?";
if ($stmt = mysqli_prepare($con, $query)) {

	mysqli_stmt_bind_param($stmt, "s", $username);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);

	if ( mysqli_stmt_num_rows($stmt) > 0 ) {

		mysqli_stmt_bind_result($stmt, $db_email_id, $db_verified);

		while (mysqli_stmt_fetch($stmt)) {
			if ($db_verified == '1') {
				die("Account already verified.");
			}

			$ver_code = Md5('4et4$55765' . $db_email_id . 'd94ereg');

			if ($ver_code == $code) {
				// Code okay - let's say the user is verified
				$query = "UPDATE users SET verified='1' WHERE username=?";
				$stmt = mysqli_prepare($con, $query);

				mysqli_stmt_bind_param($stmt, "s", $username);
				mysqli_stmt_execute($stmt);

				if (mysqli_stmt_errno($stmt)) {
					$error = "Something went wrong.";
				} else {
					header("Location: login.php?login");
					exit();
				}

			} else {
				echo $ver_code;
				die("Invalid verification code.");
			}
		}
	} else {
		die("Username not found.");
	}
	mysqli_stmt_close($stmt);
} else {
	die('Things went terribly wrong.');
}
