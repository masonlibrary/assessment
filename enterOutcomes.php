<?php
	include('control/connectionVars.php');
	include('classes/InstructionSession.php');
	include('classes/User.php');
	require_once('control/startSession.php');

	// Insert the page header
	$page_title = 'Enter Outcomes Taught';
	include('includes/header.php');
	
	echo '<form id="outcomesTaughtForm" method="post" action="enterCurrentOutcomes.php">
		<div id="outcomesTaughtDiv">';

	$thisUser = $_SESSION['thisUser'];

	if ($thisUser->isLibrarian) {
		$_SESSION['currentLibrarianID'] = $thisUser->getLibrarianID();
		echo $thisUser->getNeedOutcomes($_SESSION['currentLibrarianID']);
	}
	
	echo '<br /><br /><input id="chooseCoursesOTCT" type="submit" name="submitOTCT" value="Add outcomes to selected sessions" disabled="disabled" /><br />
		</div></form><br />';

	include('includes/footer.php');
?>
