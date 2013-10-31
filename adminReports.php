<?php

    // Start the session

    include('control/connectionVars.php');
    include('classes/User.php');
    require_once('control/startSession.php');



  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['userID']))
        {header("Location: login.php"); exit();}

   // Insert the page header
  $page_title = 'IL Summary Reports';

  include('includes/header.php');


	if( @$_SESSION['userID'] ){


               if (!isset($_SESSION['thisUser']))
                   { //echo "<h3>Newly created User object</h3>";
                   $_SESSION['thisUser']= new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);}
             }
        ?>

<script type="text/javascript">
	function formFunction(action) { $('#adminReportsForm').attr('action', action).submit(); }
</script>

<h2 id="introduction">Reports</h2>
<br>
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
			<?php
				// Should probably query the DB for range of years we have data for,
				// but I'm not sure that'd be effective (nor which table I should query).
				// So, this'll do, I guess. -Webster
				for($i=date("Y"); $i>=2012; $i--) { echo "<option value='$i'>$i</option>"; }
			?>
		</select>
		<br><br/>
		<ul>
			<!--
			<li><a href="outcomesTaughtMap.php">Outcomes Map - Taught</a></li>
			<li><a href="outcomesAssessedMap.php">Outcomes Map - Assessed</a></li>
			<li><a href="assessmentSummary.php">Assessment Summary</a></li>
			<li><a href="aySessionSummary.php">Academic Year Session Summary</a></li>
			<li><a href="allSessionsByLibrarian.php">All Sessions By Librarian (test)</a></li>
			-->
			<?php // FIXME should turn these back into regular links (use onblur on select boxes?) -Webster ?>
			<li><a href="javascript:formFunction('outcomesTaughtMap.php')">Outcomes Map - Taught</a></li>
			<li><a href="javascript:formFunction('outcomesAssessedMap.php')">Outcomes Map - Assessed</a></li>
			<li><a href="javascript:formFunction('allSessionsByLibrarian.php')">All Sessions By Librarian (test)</a></li>
			<li><a href="javascript:formFunction('assessmentSummary.php')">Assessment Summary</a></li>
			<li><a href="javascript:formFunction('aySessionSummary.php')">Academic Year Session Summary</a></li>
		</ul>
	</form>
	<br/>
</div>
<?php



	include("includes/footer.php");
?>