<?php

//	require_once 'control/startSession.php';
	require_once 'control/connection.php';

	$row = array();
	$stmt = mysqli_prepare($dbc, 'select filename, file, length(file) as length from sessionreqs_files where id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $_GET['id']);
	if(!mysqli_stmt_execute($stmt)) {
		header('HTTP/1.0 500 Internal Server Error');
		die('Failed to retrieve file: ' . mysqli_error($dbc));
	}
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $row['filename'], $row['file'], $row['length']);
	mysqli_stmt_fetch($stmt);
	header('Content-length: '.$row['length']);
	header('Content-Disposition: attachment; filename='.$row['filename']);
	echo $row['file'];
	mysqli_stmt_free_result($stmt);

?>
