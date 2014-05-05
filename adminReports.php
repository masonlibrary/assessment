<?php
	// Start the session
	include('control/connectionVars.php');
	include('classes/User.php');
	require_once('control/startSession.php');

	// Insert the page header
	$page_title = 'IL Summary Reports';
	include('includes/header.php');
?>



<script type="text/javascript">
	function formFunction() {
		semester = $('#semester').val();
		year = $('#year').val();
		$('#linklist').html(" \
			<li><a href='outcomesTaughtMap.php?semester="+semester+"&year="+year+"'>Outcomes Map - Taught</a></li> \
			<li><a href='outcomesAssessedMap.php?semester="+semester+"&year="+year+"'>Outcomes Map - Assessed</a></li> \
			<li><a href='allSessionsByLibrarian.php?semester="+semester+"&year="+year+"'>All Sessions By Librarian (test)</a></li> \
			<li><a href='assessmentSummary.php?semester="+semester+"&year="+year+"'>Assessment Summary</a></li> \
			<li><a href='aySessionSummary.php?semester="+semester+"&year="+year+"'>Academic Year Session Summary</a></li>");
	}
	$(document).ready(function(){
		$('#semester').change(function(){formFunction()})
		$('#year').change(function(){formFunction()})
		formFunction();
	});
</script>

<h2 id="introduction">Reports</h2>
<p>Please note: these reports are samples.</p>
<br>
<div id="adminReportsMenu" >
	<form id="adminReportsForm" method="get" action="">
		<label for="semester">Semester</label>
		<select name="semester" id="semester">
			<option value="any">Any</option>
			<option value="spring">Spring</option>
			<option value="summer">Summer</option>
			<option value="fall">Fall</option>
		</select>
		<label for="year">Academic year ending in</label>
		<select name="year" id="year">
			<option value="any">Any</option>
			<?php
				// Should probably query the DB for range of years we have data for
				for($i=date("Y"); $i>=2012; $i--) { echo "<option value='$i'>$i</option>"; }
			?>
		</select>
		<br><br/>
		<ul id="linklist">
			<?php // This should immediately be overwritten by formFunction() -Webster ?>
			<li><a href='outcomesTaughtMap.php'>Outcomes Map - Taught</a></li>
			<li><a href='outcomesAssessedMap.php'>Outcomes Map - Assessed</a></li>
			<li><a href='allSessionsByLibrarian.php'>All Sessions By Librarian (test)</a></li>
			<li><a href='assessmentSummary.php'>Assessment Summary</a></li>
			<li><a href='aySessionSummary.php'>Academic Year Session Summary</a></li>
		</ul>
	</form>
	<br/>
</div>

<?php
	include("includes/footer.php");
?>
