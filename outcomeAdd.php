<?php

	require_once('control/connection.php');
	require_once('control/startSession.php');

	if ($_SESSION['roleID'] != 1) { header("HTTP/1.0 403 Forbidden"); die("Unauthorized"); }
	
	$page_title = 'Add outcome';
	include('includes/header.php');
	
	if($_POST) {
		
		if(isset($_POST['active']) && $_POST['active']) {
			$headingActive='yes';
		} else {
			$headingActive='no';
		}
		
		$stmt = mysqli_prepare($dbc, 'insert into outcomeheading (otchName, otchActive) values (?, ?)');
		mysqli_bind_param($stmt, "ss", $_POST['outcomeName'], $headingActive);
//		echo 'insert into outcomeheading (otchName) values ("'.$_POST['outcomeName'].'")<br/>';
		mysqli_stmt_execute($stmt) or die('Failed to insert outcome heading: ' . mysqli_error($dbc));
		
		// Fetch out the last ID to use for the following detail insertions
		$result = mysqli_query($dbc, 'select LAST_INSERT_ID() as otchID');
		$row = mysqli_fetch_assoc($result);
		$otchID = $row['otchID'];
		
		$stmt = mysqli_prepare($dbc, 'insert into outcomedetail (otcdotchID, otcdName) values (?, ?)');
		foreach ($_POST['outcomeDetails'] as $detail) {
			if(!$detail) { continue; }
			mysqli_bind_param($stmt, "is", $otchID, $detail);
//			echo 'insert into outcomedetail (otcdotchID, otcdName) values ('.$otchID.', "'.$detail.'")<br/>';
			mysqli_stmt_execute($stmt) or die('Failed to insert outcome detail: ' . mysqli_error($dbc));
		}
		
		header('Location: outcomeList.php');
		
	}
	
	echo '<h1>'.$page_title.'</h1>
		<form method="post">
			<div class="item" style="width:initial">
				<h2>Outcome heading</h2>
				<input type="text" name="outcomeName"/>		
				<h2>Outcome details</h2>
				<table style="width:100%">';
	for ($i=0; $i<6; $i++) {
		echo '<tr><td><textarea name="outcomeDetails[]" style="width:100%"></textarea></td></tr>';
	}
	echo '</table>
				<input type="checkbox" id="active" name="active" checked="checked"/>
				<label for="active">Heading is active</label>
				<br/>
				<input type="submit"/>
			</div>
		</form>';
	
	include("includes/footer.php");

?>
