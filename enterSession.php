<?php
	require_once('control/startSession.php');
	require_once("control/connection.php");
	include('classes/InstructionSession.php');
	
	//djc added 2014
	include('classes/User.php');
	
	// Make sure the user is logged in before going any further.
	if (!isset($_SESSION['userID']))
		{header("Location: login.php"); exit();}

	// Insert the page header
	$page_title = 'Enter Session Data';
	include('includes/header.php');

	if ($_SESSION['dialogText'] != '') { include('includes/dialogDiv.php'); }


//	if (isset($_GET["sesdID"])) {
//		$query ='select '.
//			's.sesdID as sessionID, '.
//			'cp.crspName as CoursePrefix, '.
//			's.sesdCourseNumber as CourseNumber, '.
//			's.sesdCourseSection as CourseSection, '.
//			's.sesdCourseTitle as CourseTitle, '.
//			's.sesdSessionSection as SessionNum, '.
//			's.sesdDate as Date, '.
//			's.sesdFaculty as Faculty, '.
//			's.sesdOutcomeDone as OutcomeDone, '.
//			's.sesdAssessed as AssessedDone '.
//			'from '.
//			'sessiondesc s, '.
//			'courseprefix cp '.
//			'where '.
//			's.sesdID = ' . $_GET["sesdID"]; //@FIXME needs permissions validation
//		echo $query;
//		$result = mysqli_query($dbc, $query) or die("error in field prepopulation query: " . mysqli_error($dbc));
//
//		print_r($result);
//	}

	$currentSession = new InstructionSession();
	if (isset($_GET["sesdID"])) {
		$currentSession->loadSession($_GET["sesdID"]);
	}
?>

<form id="assessmentForm" method="post" action="submitData.php">
	<div  id="librarianSelect" class="item ui-corner-all">
		<h2 id="librarianHeader">Librarian</h2>
		<div class="selectBox">
			<div class="floatLeft">
				<?php
					// WEBSTER: This code doesn't work for non-admin librarians. Please fix asap. Thanks -Dana
					// I've made a quick change to things that hopefully doesn't mess up other stuff.
					$thisUser = new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);
					$_SESSION['librarianID'] = $thisUser->getLibrarianID();
																		
					echo '<select id="librarianID" name="librarianID" class="mustHave" title="You must select a librarian."' . ($_SESSION['roleName'] == 'user' ? 'disabled="disabled"' : '') . '>';
					$reportLibID = $currentSession->getLibrarianID();
					echo '<option value="" ' . ((!$reportLibID && $_SESSION['roleName'] == 'admin') ? 'selected="selected"' :  '') . '> &nbsp; &nbsp;Please select:</option>';

					//WEBSTER: I've also modified this query to select the librarian ID rather than the personID.
					//double check that this isn't a mistake on my part. I believe sessions should 
					$query = "select l.libmID as ID, p.ppleFName as FName, p.ppleLName as LName, l.libmStatus as Status " .
						"from people p, librarianmap l where p.ppleID=l.libmppleID;";
					$result = mysqli_query($dbc, $query) or die('Error querying for librarians: ' . mysqli_error($dbc));

					while ($row = mysqli_fetch_assoc($result)) {
						if ($row['Status'] != 'active') { continue; }
						$id = $row['ID'];
						$librarianName = $row['FName'] . ' ' . $row['LName'];
						if (($reportLibID == $id) || (!$reportLibID && $_SESSION['librarianID'] == $id)) {
							echo '<option id="libm' . $id . '" value="' . $id . '" selected="selected">' . $librarianName . '</option>';
						} else {
							echo '<option id="libm' . $id . '" value="' . $id . '">' . $librarianName . '</option>';
						}
					}

					mysqli_free_result($result);
				?>
				</select>
			</div>
		</div>
	</div>

	<?php // include('includes/selectCourse.php'); ?> <!-- All the same except ILSession#  -->
	<div id="courseSelect" class="item">
		<h2 id="courseSelectHeader">Course ID-Selection </h2>
<!--		<div id="makeCopiesDiv"><input type="checkbox" name="makeCopies" id="makeCopies" /> <span class="askCopy">Create</span>
			<div id="numberOfCopiesDiv">
				<select id="numberOfCopies" class="hidden" name="numberOfCopies">
					<option value="1" selected="selected">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
			</div> <span class="askCopy"> additional section&lpar;s&rpar;: </span>

		</div>-->
		<div class="coursePrefixColumn">
			<h4>Course Prefix</h4>
			<div id="selectBox">
				<div id="coursePrefixSelectContainer" class="floatLeft">
					<select id="coursePrefixID" name="coursePrefixID" class="mustHave" title="You must select a course prefix.">
						<option value="" >&nbsp;</option>
						<?php
//							include("control/connection.php");
							$query = "select crspID as ID, crspName as Name from courseprefix";
							$result = mysqli_query($dbc, $query) or die('Mr. Christian!- query issues.' . mysqli_error($dbc));
							if (!$result) { echo "this is an outrage: " . mysqli_error($dbc) . "\n"; }

							$crspID = $currentSession->getCoursePrefixID();
							while ($row = mysqli_fetch_assoc($result)) {
								$id = $row['ID'];
								$Name = trim($row['Name']);
								if ($crspID == $id) {
									echo '<option id="crsp' . $id . '" value="' . $id . '" selected="selected" >' . $Name . '</option>';
								} else {
									echo '<option id="crsp' . $id . '" value="' . $id . '">' . $Name . '</option>';
								}

							}

							mysqli_free_result($result);
//	                        mysqli_close($dbc);
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="courseNumberColumn">
			<h4>Number</h4> <div id="courseNumberContainer"><input id="courseNumber" name="courseNumber" type="text" size="15" value="<?php echo $currentSession->getCourseNumber(); ?>" class="mustHave" title="You must have a course number." /></div>
		</div>
		<div class="courseSectionColumn">
			<h4>Section</h4> <div id="courseSectionContainer"><input id="courseSection" name="courseSection" type="text" size="10" value="<?php echo $currentSession->getCourseSection(); ?>" class="mustHave" title="You must provide a section number." /></div>
		</div>
		<div class="courseTitleColumn">
			<h4>Title</h4> <div id="courseTitleContainer"><input id="courseTitle" name="courseTitle" type="text" size="15" value="<?php echo $currentSession->getCourseTitle(); ?>" class="mustHave" title="You must have a course title." /></div>
		</div>
		<div class="courseSessionColumn">
			<h4>Session #</h4>
			<div id="sessionNumberContainer">
				<select name="sessionNumber" id="sessionNumber" class="mustHave" title="You must select a session number." >
					<?php $sessionNumber = $currentSession->getSessionNumber(); ?>
					<option value="I" <?php if($sessionNumber === "I") echo 'selected="selected"'; ?> >I</option>
					<option value="II" <?php if($sessionNumber === "II") echo 'selected="selected"'; ?> >II</option>
					<option value="III" <?php if($sessionNumber === "III") echo 'selected="selected"'; ?> >III</option>
					<option value="Visit" <?php if($sessionNumber === "Visit") echo 'selected="selected"'; ?> >Visit</option>
					<option value="Other" <?php if($sessionNumber === "Other") echo 'selected="selected"'; ?> >Other</option>
					' ?>
				</select>
			</div>
		</div>
		<br />
	</div>

	<?php // include('includes/selectFaculty.php'); ?><!-- All the same -->
	<div id="facultySelect" class="item">
		<h2>Faculty name <span id="facultyComment" class="commentDiv" ></span></h2> <!-- classroom faculty -->
		<div class="copyOptions hidden">
			Same for all sections:<input type="checkbox"  name="sameFaculty" id="sameFaculty" checked="checked" />
		</div>
		<div id="facultySelectContainer" >
			<span class="courseInfo xxx faculty"></span><span class="xxx courseSection faculty"></span>
			<input id="faculty" class="mustHave xxx faculty" name="faculty" type="text" value="<?php echo $currentSession->getFaculty(); ?>" title="You must enter the faculty name." />
		</div>
	</div>

	<!--<h1>test</h1>-->
	<?php // include('includes/selectLocation.php'); ?><!-- possibly different -->

	<?php
		/* select locaName as location from location */
	?>

	<div id="locationSelect" class="item ui-corner-all">
		<h2>Location <span id="locationComment" class="commentDiv" ></span></h2> <!-- classroom -->
		<div  class="copyOptions hidden">
			Same for all sections:<input type="checkbox" checked="checked" name="sameLocations" id="sameLocations" />
		</div>
		<div class="selectBox">
			<div id="locationSelectContainer" class="floatLeft">
				<span class="courseInfo xxx location"></span><span class="xxx courseSection location"></span>
				<select id="locationID" name="locationID" class="xxx location mustHave" title="You must select a location." >

					<option class="xxx location" value=""> &nbsp; &nbsp;Please select:</option>

					<?php
						echo "<p>before include</p>";
//						include("control/connection.php");
						echo "<p>after include</p>";
						$query = "select locaID as ID, locaname as Name from location";
						echo "<p>$query</p>";
						$result = mysqli_query($dbc, $query) or die('crappy crustacean!- query issues.' . mysqli_error($dbc));
						if (!$result) { echo "this is an outrage: " . mysqli_error($dbc) . "\n"; }

						$location = $currentSession->getLocation();
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['ID'];
							$Name = $row['Name'];
							if ($location == $id) {
								echo '<option class="xxx location" value="' . $id . '" selected="selected">' . $Name . '</option>';
							} else {
								echo '<option class="xxx location" value="' . $id . '" >' . $Name . '</option>';
							}
						}

						echo "<h3>$query</h3>";
						mysqli_free_result($result);
//						mysqli_close($dbc);
					?>
				</select>
			</div>
		</div>

	</div>

	<!--<h1>test</h1>-->
	<?php // include('includes/selectDate.php'); ?>
	<div id="dateSelect" class="item">
		<h2>Date of session <span id="dateTimeComment" class="commentDiv"></span></h2>
		<div class="copyOptions hidden">
			Same for all sections:<input type="checkbox"  name="sameDates" id="sameDates" checked="checked" />
		</div>
		<div id="dateSelectContainer" class="floatLeft">
			<span class="xxx courseInfo datepicker"></span><span class="xxx courseSection datepicker"></span>
			<?php $date = $currentSession->getDateOfSession(); ?>
			<input type="text" id="datePicker" class="xxx datepicker mustHave" name="dateOfSession" value="<?php if($date) {echo date("m/d/y", strtotime($date)); } ?>" title="You must enter the date of the session." />
		<!--  <input type="text" name="timeOfSession" id="timepicker" /> -->

		</div>
	</div>

	<?php // include('includes/selectLength.php'); ?>

	<div id="lengthSelect" class="item ui-corner-all">
		<h2>Session Length <span id="lengthComment" class="commentDiv"></span></h2>
		<div class="copyOptions hidden">
			Same for all sections:<input type="checkbox" checked="checked" name="sameLengths" id="sameLengths" />
		</div>
		<div class="selectBox">
			<div id="lengthSelectContainer" class="floatLeft">
				<span class="courseInfo xxx length"></span><span class="xxx courseSection length"></span>
				<select id="lengthID" name="lengthID" class="mustHave xxx length" title="You must select a session length." >
					<option value="" selected="selected"> &nbsp; &nbsp;Please select:</option>
					<?php
//						include("control/connection.php");
						$query = "select seslID as ID, seslName as Name from sesslength";
						$result = mysqli_query($dbc, $query) or die('Victoria! I know your secret!- query issues.' . mysqli_error($dbc));
						if (!$result) { echo "this is an outrage: " . mysqli_error($dbc) . "\n"; }

						$length = $currentSession->getLengthOfSessionID();
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['ID'];
							$Name = $row['Name'];
							if ($length == $id) {
								echo '<option id="sesl' . $id . '" value="' . $id . '" selected="selected">' . $Name . '</option>';
							} else {
								echo '<option id="sesl' . $id . '" value="' . $id . '" >' . $Name . '</option>';
							}
						}

						mysqli_free_result($result);
//						mysqli_close($dbc);
					?>
				</select>
			</div>
		</div>
	</div>

	<?php // include('includes/selectNumber.php'); ?>
	<div id="numberSelect" class="item ui-corner-all">
		<h2>Number of students<span id="numberStudentsComment" class="commentDiv"></span></h2>
		<div id="numberOfStudentsContainer">
			<span class="courseInfo xxx student"></span><span class="xxx courseSection student"></span>
			<input id="numberOfStudents" class="mustHave xxx student" title="You must enter the number of students in session."
				   name="numberOfStudents" type="text" value="<?php $numStudents = $currentSession->getNumberOfStudents(); if($numStudents) echo $numStudents; ?>"/>
		</div>
	</div>
	<?php // TODO make select number of students into a dropdown. ?>

	<?php // include('includes/selectResources.php'); ?>
	<div id="resourcesSelect" class="item ui-corner-all">
		<h2>Resources introduced <span id="resourcesComment" class="commentDiv"></span></h2>
		<div class="copyOptions hidden">
			Same for all sections:<input type="checkbox" checked="checked" name="sameResources" id="sameResources" />
		</div>
		<div class="selectBox">
			<div id="resourcesSelectContainer" class="floatLeft">
				<span class="courseInfo xxx resourcesBox"></span><span class="xxx courseSection resourcesBox"></span><br />
				<?php
//					include("control/connection.php");
					//$query = "select rsrpID as ID, rsrpName as Name from resourcepool order by Name asc";
					//$result = mysqli_query($dbc, $query) or die('Shite!- query issues.' . mysqli_error($dbc));
					if(isset($_GET['sesdID'])) {
//						$query = "select rp.rsrpID as ID, rp.rsrpName as Name,
//							(select if((count(rsrisesdID) > 0), 'checked', '') from resourcesintroduced ri
//							where (ri.rsrirsrpID=rp.rsrpID) and (rsrisesdID=" . $_GET['sesdID'] . ")) as 'checked'
//							from resourcesintroduced ri, resourcepool rp
//							where rsrisesdID=" . $_GET['sesdID'] . " group by ID order by Name asc;";
						$query = "select * from resourcepool rp
							left outer join resourcesintroduced ri
							on (ri.rsrirsrpID = rp.rsrpID and ri.rsrisesdID = " . $_GET['sesdID'] . ");";
						$result = mysqli_query($dbc, $query) or die('Error querying for resource checkboxes: ' . mysqli_error($dbc));

						echo '<input class="xxx resourcesBox none mustHaveBox" title="You must choose at least 1 resource (or specify NONE) per session" type="checkbox" name="resourcesIntroduced" value="none"  /><span class="xxx resourcesbox">None</span><br class="xxx resourcesbox" />';
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['rsrpID'];
							$Name = $row['rsrpName'];
							$row['rsrirsrpID'] ? $checked = "checked" : $checked = "";
							echo '<input class="xxx resourcesBox mustHaveBox notNone" title="You must choose at least 1 resource per session" type="checkbox" ' . $checked . ' name="resourcesIntroduced[]" value="' . $id . '"  /><span class="xxx resourcesbox">' . $Name . '</span><br class="xxx resourcesbox" />';
						}
					} else {
						$query = "select rsrpID as ID, rsrpName as Name from resourcepool order by Name asc";
						$result = mysqli_query($dbc, $query) or die('Error querying for resource checkboxes: ' . mysqli_error($dbc));

						echo '<input class="xxx resourcesBox none mustHaveBox" title="You must choose at least 1 resource (or specify NONE) per session" type="checkbox" name="resourcesIntroduced" value="none"  /><span class="xxx resourcesbox">None</span><br class="xxx resourcesbox" />';
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['ID'];
							$Name = $row['Name'];
							echo '<input class="xxx resourcesBox mustHaveBox notNone" title="You must choose at least 1 resource per session" type="checkbox" name="resourcesIntroduced[]" value="' . $id . '"  /><span class="xxx resourcesbox">' . $Name . '</span><br class="xxx resourcesbox" />';
						}
					}


					mysqli_free_result($result);
//					mysqli_close($dbc); // seems to go a little faster if we don't explicitly close the connection -Webster
				?>

			</div>
		</div>
	</div>
	<?php // include('includes/selectNote.php'); ?>
	<div id="commentSelect" class="item">
		<h2>Comments/Notes</h2>
		<div class="copyOptions hidden">
			Same for all sections:<input type="checkbox" checked="checked" name="sameNotes" id="sameNotes" />
		</div>
		<div id="noteSelectContainer">
			<span class="courseInfo xxx notebox"></span><span class="xxx courseSection notebox"></span><br />
			<textarea class="xxx notebox optional" rows="4" cols="60" id="sessionNote" name="sessionNote" title="Notes are optional"><?php echo $currentSession->getSessionNote() ?></textarea>
		</div>
	</div>
	<?php // include('includes/selectSubmitButton.php'); ?>

	<div  id="submitButtonDiv" class="item ui-corner-all ">
		<h2>Complete form: </h2>
		<div class="selectBox">
			<div id="completionStatus"></div>
			<div class="floatLeft">
				<input type="hidden" name="action" value="<?php if(isset($_GET['action'])){echo $_GET['action'];} ?>" />
				<input type="hidden" name="sesdID" value="<?php if(isset($_GET['sesdID'])){echo $_GET['sesdID'];} ?>" />
				<input id="submitButton" type="submit" onclick="javascript:if(!checkCompletion()){return false;}else{$('#librarianID').removeAttr('disabled');return true;}" value="Enter Session" name="submit" />
			</div>
		</div>
	</div>


</form>
<br />

<?php include('includes/footer.php'); ?>
