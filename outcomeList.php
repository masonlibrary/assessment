<?php

	require_once('control/connection.php');
	require_once('control/startSession.php');

	if (!isset($_SESSION['userID'])) { header("Location: login.php"); exit("Not logged in"); }
	if ($_SESSION['roleID'] != 1) { header("HTTP/1.0 403 Forbidden"); die("Unauthorized"); }
	
	$page_title = 'Manage outcomes';
	include('includes/header.php');
	
	echo '<h1>'.$page_title.'</h1><table class="dataTable">';
	
	$evenodd = false;
	$result = mysqli_query($dbc, "select * from outcomeheading") or die('Error querying for outcome headings: ' . mysqli_error($dbc));
	while ($row = mysqli_fetch_assoc($result)) {
		echo '<tr class="'.($evenodd?"even":"odd").'"><td>'.$row['otchID'].'</td><td>'.$row['otchName'].'</td><td><a href="outcomeManage.php?id='.$row['otchID'].'">edit</a></td></tr>';
		$evenodd = !$evenodd;
	}
	
	echo '</table>';
	
//	echo '<script type="text/javascript">$(document).ready( function(){$("#outcomelist").dataTable();} );</script>';

	include("includes/footer.php");

?>
