<?php

	require_once 'control/connection.php';
	require_once 'control/startSession.php';
	
	$stmt = mysqli_prepare($dbc, 'update users set userLastActive = now() where userID = ?');
	mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
	if(mysqli_stmt_execute($stmt)) {
		echo 'Updated active time of uid ' . $_SESSION['userID'];
	} else {
		header('HTTP/1.0 500 Internal Server Error');
		echo 'Failed to update active time of uid '. $_SESSION['userID'] . ': ' . mysqli_error($dbc);
	}

?>
