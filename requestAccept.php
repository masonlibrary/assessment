<?php

	require_once 'classes/InstructionSession.php';
	require_once 'classes/User.php';
	require_once 'control/startSession.php';
	require_once 'control/connection.php';

	$page_title = 'Accept Session Request';
	require_once 'includes/header.php';

	$row = array();
	$stmt = mysqli_prepare($dbc, 'select requested, name, email, phone, department,
		courseprefixid, coursenumber, coursesection, coursename, meets, numstudents,
		type, date1, date2, moredates, description, assignment_fileid, syllabus_fileid,
		status, librarianid
		from sessionreqs where id = ?');
	mysqli_stmt_bind_param($stmt, 'i', $_GET['id']);
	mysqli_stmt_execute($stmt) or die('Failed to get session request: ' . mysqli_error($dbc));
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $row['requested'], $row['name'], $row['email'],
		$row['phone'], $row['department'], $row['courseprefixid'], $row['coursenumber'],
		$row['coursesection'], $row['coursename'], $row['meets'],	$row['numstudents'],
		$row['type'], $row['date1'], $row['date2'],	$row['moredates'], $row['description'],
		$row['assignment_fileid'], $row['syllabus_fileid'], $row['status'], $row['librarianid']);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_free_result($stmt);

	if ($row['librarianid'] != $_SESSION['thisUser']->librarianID) { die('This request is not yours to accept!'); }
	if ($row['status'] != 'a') { die('This request is not ready to be accepted!'); }

	$stmt = mysqli_prepare($dbc, 'select (select name from sessionreqs_types where id = ?) as typename,
                                       (select crspName from courseprefix where crspID = ?) as prefixname;');
	mysqli_stmt_bind_param($stmt, 'ii', $row['type'], $row['courseprefixid']);
	mysqli_stmt_execute($stmt) or die('Failed to get session type, course prefix: ' . mysqli_error($dbc));
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $row['typename'], $row['prefixname']);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_free_result($stmt);

	if($_POST) {

		$accepteddate = DateTime::createFromFormat('m/d/Y', $_POST['date'])->format('Y-m-d');

		// create session
		$session = new InstructionSession($_SESSION['userName']);
		$session->setLibrarianID($row['librarianid']);
//		if (isset($inPost['fellowPresent']) && $inPost['fellowPresent'] == 'on') {
//			$session->setFellowPresent('yes');
//		} else {
//			$session->setFellowPresent('no');
//		}
		$session->setDateOfSession($accepteddate);
//		$session->setLengthOfSessionID(null);
		$session->setNumberOfStudents($row['numstudents']);
		$session->setCoursePrefixID($row['courseprefixid']);
		$session->setCourseNumber($row['coursenumber']);
		$session->setCourseSection($row['coursesection']);
		$session->setCourseTitle($row['coursename']);
//		$session->setSessionNumber();
		$session->setFaculty($row['name']);
//		$session->setLocationID($inPost['locationID']);
//		$session->setSessionNote($inPost['sessionNote']);
//		$session->setResourcesIntroducedID($inPost['resourcesIntroduced']);
		$newid = $session->insertSession();

		try {

			mysqli_autocommit($dbc, false);

			// update request
			$stmt = mysqli_prepare($dbc, 'update sessionreqs set status="x", sessionid=?, accepted=now() where id=?');
			mysqli_stmt_bind_param($stmt, 'ii', $newid, $_GET['id']);
			if (!mysqli_stmt_execute($stmt)) { throw new Exception('Failed to accept session: ' . mysqli_error($dbc)); }

			mysqli_commit($dbc);
			mysqli_autocommit($dbc, true);

			$_SESSION['dialogText'] .= 'Session accepted.';
			header('Location: index.php');

		} catch (Exception $e) {

			mysqli_rollback($dbc);
			mysqli_autocommit($dbc, true);
			$_SESSION['dialogText'] .= $e->getMessage();

		}

	}

	echo '<table>';
		echo '<tr><th>Requested on</th><td>'.$row['requested'].'</td></tr>';
		echo '<tr><th>Requested by</th><td>'.$row['name'].'</td></tr>';
		echo '<tr><th>Email</th><td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td></tr>';
		echo '<tr><th>Phone</th><td>'.$row['phone'].'</td></tr>';
		echo '<tr><th>Department</th><td>'.$row['department'].'</td></tr>';
		echo '<tr><th>Course number</th><td>'.$row['prefixname']."-".$row['coursenumber']."-".$row['coursesection'].'</td></tr>';
		echo '<tr><th>Course name</th><td>'.$row['coursename'].'</td></tr>';
		echo '<tr><th>Meets on</th><td>'.$row['meets'].'</td></tr>';
		echo '<tr><th>Number of students</th><td>'.$row['numstudents'].'</td></tr>';
		echo '<tr><th>Type of session</th><td>'.$row['typename'].'</td></tr>';
		echo '<tr><th>Date (1st choice)</th><td>'.$row['date1'].'</td></tr>';
		echo '<tr><th>Date (2nd choice)</th><td>'.$row['date2'].'</td></tr>';
		echo '<tr><th>Additional dates</th><td>'.$row['moredates'].'</td></tr>';
		echo '<tr><th>Assignment description</th><td>'.$row['description'].'</td></tr>';
		echo '<tr><th>Assignment</th><td>';
			if ($row['assignment_fileid']) {
				echo '<a href="requestFile.php?id='.$row['assignment_fileid'].'">Download</a>';
			} else {
				echo 'Not provided';
			}
		echo '</tr>';
		echo '<tr><th>Syllabus</th><td>';
			if ($row['syllabus_fileid']) {
				echo '<a href="requestFile.php?id='.$row['syllabus_fileid'].'">Download</a>';
			} else {
				echo 'Not provided';
			}
		echo '</tr>';
	echo '</table>';

	echo '<form method="post">';
		echo '<h2>Choose date and accept</h2>';
		echo '<input type="text" id="date" name="date">';
		echo '<input type="submit" value="Accept">';
	echo '</form>';

	$jsOutput .= '$("#date").datepicker()';

	require_once 'includes/footer.php';

?>
