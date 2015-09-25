<?php
	require_once('control/startSession.php');
	require_once("control/connection.php");
	include('classes/InstructionSession.php');
	
	//djc added 2014
	include('classes/User.php');

	if (!isset($_GET['lite'])) {
		$page_title = 'Enter Session Data';
		include('includes/header.php');
	} else {
		$jsOutput = '';
	}

	$currentSession = new InstructionSession();
	if (isset($_GET["sesdID"])) {
		$currentSession->loadSession($_GET["sesdID"]);
	}

	if (isset($_GET['action'])){
		$action = $_GET['action'];
	} else {
		if (isset($_GET['sesdID'])) {
			$action = 'view';
		} else {
			$action = 'insert';
		}
	}
?>

<form id="assessmentForm" method="post" action="submitData.php">
	<?php /****** Librarian dropdown ******/ ?>
	<div  id="librarianSelect" class="item ui-corner-all">
		<h2 id="librarianHeader">Librarian</h2>
		<div class="selectBox">
			<div class="floatLeft">
				<?php
					$thisUser = new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);
					$_SESSION['librarianID'] = $thisUser->getLibrarianID();
					
                                      
                                        
					echo '<select id="librarianID" name="librarianID" required title="You must select a librarian."' . ($_SESSION['roleName'] == 'user' ? 'disabled="disabled"' : '') . '>';
                                       
                                        
					$reportLibID = $currentSession->getLibrarianID();
                                        
					echo '<option value="" ' . ((!$reportLibID && $_SESSION['roleName'] == 'admin') ? 'selected="selected"' :  '') . '> &nbsp; &nbsp;Please select:</option>';

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
				<br>
				<?php
                                    //add hidden input to compensate for cases where select is disabled
                                         if ($_SESSION['roleName']=='user'){
                                             echo "<input type='hidden' name='librarianID' value='".$_SESSION['librarianID']."' />";
                                         }
					echo '<input type="checkbox" id="fellowPresent" name="fellowPresent"'.(($currentSession->getFellowPresent()=='yes')?' checked':'').'>
						<label for="fellowPresent">Fellow was present</label>';
				?>
			</div>
		</div>
	</div>

	<?php /****** Course information block ******/ ?>
	<div id="courseSelect" class="item">
		<h2 id="courseSelectHeader">Course ID-Selection </h2>
		<div class="coursePrefixColumn">
			<h4>Course Prefix</h4>
			<div id="selectBox">
				<div id="coursePrefixSelectContainer" class="floatLeft">
					<select id="coursePrefixID" name="coursePrefixID" required title="You must select a course prefix.">
						<option value="" >&nbsp;</option>
						<?php
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
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="courseNumberColumn">
			<h4>Number</h4> <div id="courseNumberContainer"><input id="courseNumber" name="courseNumber" required type="number" size="15" value="<?php echo $currentSession->getCourseNumber(); ?>" tyitle="You must have a course number." /></div>
		</div>
		<div class="courseSectionColumn">
			<h4>Section</h4> <div id="courseSectionContainer"><input id="courseSection" name="courseSection" required type="number" size="10" value="<?php if ($action!='duplicate'){echo $currentSession->getCourseSection();} ?>" title="You must provide a section number." /></div>
		</div>
		<div class="courseTitleColumn">
			<h4>Title</h4> <div id="courseTitleContainer"><input id="courseTitle" name="courseTitle" required type="text" size="15" value="<?php echo $currentSession->getCourseTitle(); ?>" title="You must have a course title." /></div>
		</div>
		<div class="courseSessionColumn">
			<h4>Session #</h4>
			<div id="sessionNumberContainer">
				<select name="sessionNumber" id="sessionNumber" required title="You must select a session number." >
					<?php $sessionNumber = $currentSession->getSessionNumber(); ?>
					<option value="I" <?php if($sessionNumber === "I") echo 'selected="selected"'; ?> >I</option>
					<option value="II" <?php if($sessionNumber === "II") echo 'selected="selected"'; ?> >II</option>
					<option value="III" <?php if($sessionNumber === "III") echo 'selected="selected"'; ?> >III</option>
					<option value="Visit" <?php if($sessionNumber === "Visit") echo 'selected="selected"'; ?> >Visit</option>
					<option value="Other" <?php if($sessionNumber === "Other") echo 'selected="selected"'; ?> >Other</option>
				</select>
			</div>
		</div>
		<br />
	</div>

	<?php /****** Faculty text box ******/ ?>
	<div id="facultySelect" class="item">
		<h2>Faculty name <span id="facultyComment" class="commentDiv" ></span></h2> <!-- classroom faculty -->
		<div id="facultySelectContainer" >
			<span class="courseInfo faculty"></span><span class="courseSection faculty"></span>
			<input id="faculty" class="faculty" required name="faculty" type="text" value="<?php echo $currentSession->getFaculty(); ?>" title="You must enter the faculty name." />
		</div>
	</div>

	<?php /****** Location dropdown ******/ ?>
	<div id="locationSelect" class="item ui-corner-all">
		<h2>Location <span id="locationComment" class="commentDiv" ></span></h2> <!-- classroom -->
		<div class="selectBox">
			<div id="locationSelectContainer" class="floatLeft">
				<span class="courseInfo location"></span><span class="courseSection location"></span>
				<select id="locationID" name="locationID" class="location" required title="You must select a location." >

					<option class="location" value=""> &nbsp; &nbsp;Please select:</option>

					<?php
						$query = "select locaID as ID, locaname as Name from location";
						$result = mysqli_query($dbc, $query) or die('crappy crustacean!- query issues.' . mysqli_error($dbc));
						if (!$result) { echo "this is an outrage: " . mysqli_error($dbc) . "\n"; }

						$location = $currentSession->getLocation();
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['ID'];
							$Name = $row['Name'];
							if ($location == $id) {
								echo '<option class="location" value="' . $id . '" selected="selected">' . $Name . '</option>';
							} else {
								echo '<option class="location" value="' . $id . '" >' . $Name . '</option>';
							}
						}

						mysqli_free_result($result);
					?>
				</select>
			</div>
		</div>
	</div>

	<?php /****** Date picker ******/ ?>
	<div id="dateSelect" class="item">
		<h2>Date of session <span id="dateTimeComment" class="commentDiv"></span></h2>
		<div id="dateSelectContainer" class="floatLeft">
			<span class="courseInfo datepicker"></span><span class="courseSection datepicker"></span>
			<?php $date = $currentSession->getDateOfSession(); ?>
			<input type="text" id="datePicker" class="datepicker" required name="dateOfSession" value="<?php if($date) {echo date("m/d/y", strtotime($date)); } ?>" title="You must enter the date of the session." />
		<!--  <input type="text" name="timeOfSession" id="timepicker" /> -->

		</div>
	</div>

	<?php /****** Session length dropdown ******/ ?>
	<div id="lengthSelect" class="item ui-corner-all">
		<h2>Session Length <span id="lengthComment" class="commentDiv"></span></h2>
		<div class="selectBox">
			<div id="lengthSelectContainer" class="floatLeft">
				<span class="courseInfolength"></span><span class="courseSection length"></span>
				<select id="lengthID" name="lengthID" required title="You must select a session length." >
					<option value="" selected="selected"> &nbsp; &nbsp;Please select:</option>
					<?php
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
					?>
				</select>
			</div>
		</div>
	</div>

	<?php /****** Number of students text box ******/ ?>
	<div id="numberSelect" class="item ui-corner-all">
		<h2>Number of students<span id="numberStudentsComment" class="commentDiv"></span></h2>
		<div id="numberOfStudentsContainer">
			<span class="courseInfostudent"></span><span class="courseSection student"></span>
			<input id="numberOfStudents" required title="You must enter the number of students in session."
				   name="numberOfStudents" type="number" value="<?php echo $currentSession->getNumberOfStudents(); ?>"/>
		</div>
	</div>

	<?php /****** Resources checkboxes ******/ ?>
	<?php /*
	<div id="resourcesSelect" class="item ui-corner-all">
		<h2>Resources introduced <span id="resourcesComment" class="commentDiv"></span></h2>
		<div class="selectBox">
			<div id="resourcesSelectContainer" class="floatLeft">
				<span class="courseInforesourcesBox"></span><span class="courseSection resourcesBox"></span><br />
				<?php
					if(isset($_GET['sesdID'])) {
						$query = "select rsrpID, rsrpName, rsrirsrpID from resourcepool rp
							left outer join resourcesintroduced ri
							on (ri.rsrirsrpID = rp.rsrpID and ri.rsrisesdID = ?)
							where rp.rsrpActive='yes'";
						$stmt = mysqli_prepare($dbc, $query);
						mysqli_stmt_bind_param($stmt, "i", $_GET['sesdID']);
						mysqli_stmt_execute($stmt) or die('Error querying for resource checkboxes: ' . mysqli_error($dbc));
						mysqli_stmt_store_result($stmt);
						mysqli_stmt_bind_result($stmt, $id, $Name, $resourceID);

						$isNone='none'; // Add 'none' class for first row
						while (mysqli_stmt_fetch($stmt)) {
							$resourceID ? $checked = "checked" : $checked = "";
							echo '<input class="resourcesBox '.$isNone.'" title="You must choose at least 1 resource (or \'None\') per session" type="checkbox" ' . $checked . ' name="resourcesIntroduced[]" value="' . $id . '"  /><span class="resourcesbox">' . $Name . '</span><br class="resourcesbox" />';
							$isNone='notNone'; // Add notNone class for subsequent rows
						}
						mysqli_stmt_free_result($stmt);
					} else {
						$query = "select rsrpID as ID, rsrpName as Name from resourcepool where rsrpActive='yes'";
						$result = mysqli_query($dbc, $query) or die('Error querying for resource checkboxes: ' . mysqli_error($dbc));

						$isNone='none'; // Add 'none' class for first row
						while ($row = mysqli_fetch_assoc($result)) {
							$id = $row['ID'];
							$Name = $row['Name'];
							echo '<input class="resourcesBox '.$isNone.'" title="You must choose at least 1 resource (or \'None\') per session" type="checkbox" name="resourcesIntroduced[]" value="' . $id . '"  /><span class="resourcesbox">' . $Name . '</span><br class="resourcesbox" />';
							$isNone='notNone'; // Add notNone class for subsequent rows
					}
						mysqli_free_result($result);
					}
				?>

			</div>
		</div>
	</div>
	*/ ?>

	<?php /****** Outcomes checkboxes ******/ ?>
	<div id="outcomesSelect" class="item ui-corner-all">
		<h2>Outcomes taught</h2>
		<div id="outcomesSelectContainer">
			<?php

				// Don't allow changing outcomes if they've been assessed
				if ($currentSession->getAssessed() == 'yes') {
					$disabled = 'disabled';
					echo '<span style="color: #78f;">These outcomes have already been assessed. Editing is disabled.</span>';
				} else {
					$disabled = '';
				}

				$lastheading = 0;
				$row = array();
				$stmt = mysqli_prepare($dbc, '
					select oh.otchID, oh.otchName, od.otcdID, od.otcdName, ot.otctsesdID
					from outcomedetail od
					left join outcomeheading oh on oh.otchID = od.otcdotchID
					left join outcomestaught ot on ot.otctotcdID = od.otcdID and ot.otctsesdID = ?
					order by oh.otchID, od.otcdName
				');
				mysqli_stmt_bind_param($stmt, 'i', $_GET['sesdID']);
				mysqli_stmt_execute($stmt) or die('Failed to get outcomes: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $row['otchID'], $row['otchName'], $row['otcdID'], $row['otcdName'], $row['checked']);
				while (mysqli_stmt_fetch($stmt)) {
					if ($row['otchID'] != $lastheading) {
						echo '<br><h3>'.$row['otchID'].". ".$row['otchName'].'</h3>';
						$lastheading = $row['otchID'];
					}
					if ($row['checked']) { $checked = 'checked'; } else { $checked = ''; }
					echo '<label><input type="checkbox" name="outcomesTaught[]" class="outcomesBox" value="'.$row['otcdID'].'" '.$checked.' '.$disabled.'>'.$row['otcdName'].'</label><br>';
				}
				mysqli_stmt_free_result($stmt);
			?>
		</div>
	</div>
	
	<?php /****** Notes field ******/ ?>
	<div id="commentSelect" class="item">
		<h2>Comments/Notes</h2>
		<div id="noteSelectContainer">
			<span class="courseInfonotebox"></span><span class="courseSection notebox"></span><br />
			<textarea class="notebox optional" rows="4" cols="60" id="sessionNote" name="sessionNote" title="Notes are optional"><?php echo $currentSession->getSessionNote() ?></textarea>
		</div>
	</div>

	<?php /****** Submit button ******/ ?>
	<?php

		if (isset($_GET['sesdID'])) {
			$session = $_GET['sesdID'];
		} else {
			if ($action != 'insert') die('Error: Trying to perform a non-insert action without session ID!');
			$session = null;
		}
		
		switch ($action) {
			case 'insert':
			case 'duplicate':
				$submitText='Add session';
				break;
			case 'edit':
				$submitText='Update session';
				break;
			case 'view':
			default:
				$submitText='';
				break;
		}
		
		if ($submitText) {
			echo '<div  id="submitButtonDiv" class="item ui-corner-all ">
				<h2>Complete form: </h2>
				<div class="selectBox">
					<div id="completionStatus"></div>
						<div class="floatLeft">
							<input type="hidden" name="action" value="'.$action.'"/>
							<input type="hidden" name="sesdID" value="'.$session.'"/>
							<input id="submitButton" type="submit" name="submit" value="'.$submitText.'"/>
					</div>
				</div>
			</div>';
		} // else, no button (view/unknown action)

	?>

</form>
<br />

<?php
	$jsOutput .= '//$(document).ready( function(){noneOrSome(); checkCompletion();} );';
	$jsOutput .= '$("#numberOfStudents").spinner();';
	if (!isset($_GET['lite'])) { include('includes/footer.php'); }
?>
