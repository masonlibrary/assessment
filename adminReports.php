<?php
	// Start the session
	include('control/connectionVars.php');
	include('classes/User.php');
	require_once('control/startSession.php');

	// Insert the page header
	$page_title = 'IL Summary Reports';
	include('includes/header.php');
?>

<h2 id="introduction">Reports</h2>
<p>Please note: these reports are samples.</p>
<div id="adminReportsMenu">
	<ul id="linklist">
		<li><a href='outcomesTaughtMap.php'>Outcomes Map - Taught</a></li>
		<li><a href='outcomesAssessedMap.php'>Outcomes Map - Assessed</a></li>
		<li><a href='allSessionsByLibrarian.php'>All Sessions By Librarian (test)</a></li>
		<li><a href='assessmentSummary.php'>Assessment Summary</a></li>
		<li><a href='aySessionSummary.php'>Academic Year Session Summary</a></li>
	</ul>
</div>

<?php
	include("includes/footer.php");
?>
