<?php

	require_once ('control/connection.php');
//	include('classes/InstructionSession.php');
//	include('classes/User.php');
	require_once('control/startSession.php');

	if ($_POST) {

	if (isset($_POST['inID']) && is_numeric($_POST['inID'])) {
		$inID = $_POST['inID'];
	} else {
		die('Non-numeric or nonexistant session ID!');
	}

	$output = 'Deleting session '.$inID.'...<br/>';

	try {
		mysqli_autocommit($dbc, false);

		$stmt = mysqli_prepare($dbc, 'delete from outcomesassessed where otcaotctID in (select otctID from outcomestaught where otctsesdID=?)');
		mysqli_bind_param($stmt, 'i', $inID);
		if (mysqli_stmt_execute($stmt)) {
			$output .= 'Outcomes assessed prepared for deletion<br/>';
		} else {
			throw new Exception('Failed to prepare outcomes assessed for deletion: ' . $stmt->error);
		}

		$stmt = mysqli_prepare($dbc, 'delete from outcomestaught where otctsesdID=?');
		mysqli_bind_param($stmt, 'i', $inID);
		if (mysqli_stmt_execute($stmt)) {
			$output .= 'Outcomes taught prepared for deletion<br/>';
		} else {
			throw new Exception('Failed to prepare outcomes taught for deletion: ' . $stmt->error);
		}

		$stmt = mysqli_prepare($dbc, 'delete from resourcesintroduced where rsrisesdID=?');
		mysqli_bind_param($stmt, 'i', $inID);
		if (mysqli_stmt_execute($stmt)) {
			$output .= 'Resources introduced prepared for deletion<br/>';
		} else {
			throw new Exception('Failed to prepare resources introduced for deletion: ' . $stmt->error);
		}

		$stmt = mysqli_prepare($dbc, 'delete from sessionnotes where sesnsesdID=?');
		mysqli_bind_param($stmt, 'i', $inID);
		if (mysqli_stmt_execute($stmt)) {
			$output .= 'Session notes prepared for deletion<br/>';
		} else {
			throw new Exception('Failed to prepare session notes for deletion: ' . $stmt->error);
		}

		$stmt = mysqli_prepare($dbc, 'delete from sessiondesc where sesdID=?');
		mysqli_bind_param($stmt, 'i', $inID);
		if (mysqli_stmt_execute($stmt)) {
			$output .= 'Session description prepared for deletion<br/>';
		} else {
			throw new Exception('Failed to prepare session description for deletion: ' . $stmt->error);
		}

		if (mysqli_commit($dbc)) {
			$output .= 'Successfully deleted.<br/>';
		} else {
			throw new Exception('Failed to delete session and associated data: ' . mysqli_error($dbc));
		}

		mysqli_autocommit($dbc, true);
	} catch (Exception $e) {
		mysqli_rollback($dbc);
		mysqli_autocommit($dbc, true);
		die('Error: ' . $e->getMessage());
	}

	$_SESSION['dialogText'] = $output;
	$_SESSION['dialogTitle'] = "Result";

	echo $output;
	header('Location: mySessions.php');
	exit();

	}

	$sesdID = htmlspecialchars($_GET['sesdID']);
	echo '
		<form method="post" action="deleteSession.php">
			Really delete session '.$sesdID.'?
			<input type="hidden" value="'.$sesdID.'" name="inID">
			<input type="submit">
		</form>';

?>