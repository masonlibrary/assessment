<?php

	require_once('control/connection.php');
	require_once('control/startSession.php');

	if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit("Not logged in"); }
	if ($_SESSION['roleID'] != 1) { header("HTTP/1.0 403 Forbidden"); die("Unauthorized"); }
	
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$id=$_GET['id'];
	} else {
		die('Non-numeric or nonexistant ID!');
	}
	
	$page_title = 'Manage outcome ' . $id;
	include('includes/header.php');
	
	if ($_POST) {
		if(!isset($_POST['outcomeHeading']) || $_POST['outcomeHeading'] == '') {
			$_POST['outcomeHeading']='(blank)';
		}
		
		if(isset($_POST['active']) && $_POST['active']) {
			$headingActive='yes';
		} else {
			$headingActive='no';
		}
		
		$stmt = mysqli_prepare($dbc, 'update outcomeheading set otchName=?, otchActive=? where otchID=?');
		mysqli_bind_param($stmt, "ssi", $_POST['outcomeHeading'], $headingActive, $_POST['headingID']);
//		echo 'update outcomeheading set otchName='.$_POST['outcomeHeading'].', otchActive='.$headingActive.' where otchID='.$_POST['headingID'];
		mysqli_stmt_execute($stmt) or die('Failed to update outcome headings: ' . mysqli_error($dbc));
		
		$stmt = mysqli_prepare($dbc, 'update outcomedetail set otcdName=? where otcdID=?');
		foreach ($_POST['editOutcomeDetailName'] as $i=>$name) {
			if (!$name) { $name = "(blank)"; }
			mysqli_bind_param($stmt, "si", $name, $i);
//			echo 'update outcomedetail set otcdName="'.$name.'" where otcdID='.$i.'<br/>';
			mysqli_stmt_execute($stmt) or die('Failed to execute query: ' . mysqli_error($dbc));
		}
		
		$stmt = mysqli_prepare($dbc, 'insert into outcomedetail (otcdotchID, otcdName) values (?, ?)');
		foreach ($_POST['newOutcomeDetailName'] as $name) {
			if(!$name) { continue; }
			mysqli_bind_param($stmt, "is", $id, $name);
//			echo 'insert into outcomedetail (otcdotchID, otcdName) values ('.$id.', '.$name.')<br/>';
			mysqli_stmt_execute($stmt) or die('Failed to execute query: ' . mysqli_error($dbc));
		}
		
		header('Location: outcomeList.php');
	}
	
	$heading = array();
	$detail = array();
	
	$stmt = mysqli_prepare($dbc, 'select otchID, otchName, otchActive from outcomeheading where otchID=?');
	mysqli_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt) or die("Error querying for outcome heading: " . mysqli_error($dbc));
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $heading['otchID'], $heading['otchName'], $heading['otchActive']);
	mysqli_stmt_fetch($stmt);
//	mysqli_stmt_free_result($stmt);
	
	echo '<h1>'.$page_title.'</h1>
		<h2>'.$heading['otchName'].'</h2>
		';
	
	echo '<form method="post">
		<div class="item" style="width:initial">
		<h3>Heading name</h3>
		<textarea name="outcomeHeading" style="width:100%">'.$heading['otchName'].'</textarea>
		<h3>Edit existing outcome details</h3>
		<table style="width:100%">';
	
	$stmt = mysqli_prepare($dbc, 'select otcdID, otcdotchID, otcdName from outcomedetail where otcdotchID=? order by otcdName');
	mysqli_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt) or die("Error querying for outcome detail: " . mysqli_error($dbc));
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $detail['otcdID'], $detail['otcdotchID'], $detail['otcdName']);
	while (mysqli_stmt_fetch($stmt)) {
		echo '<tr><td><textarea name="editOutcomeDetailName['.$detail['otcdID'].']" style="width:100%">'.htmlspecialchars($detail['otcdName']).'</textarea></td></tr>';
	}
	mysqli_stmt_free_result($stmt);
	
	echo '</table>
	 <h3>Add new outcome details</h3>
	 <table style="width:100%">';
	
	for ($i=0; $i<6; $i++) {
		echo '<tr><td><textarea name="newOutcomeDetailName[]" style="width:100%"></textarea></td></tr>';
	}

	echo '</table>
		<input type="hidden" name="headingID" value="'.$id.'"/>
		<input type="checkbox" id="active" name="active" '.($heading['otchActive']=="yes"?"checked":"").'/>
		<label for="active">Heading is active</label>
		<br/>
		<input type="submit"/>
		</form>';

	include("includes/footer.php");
	
?>
