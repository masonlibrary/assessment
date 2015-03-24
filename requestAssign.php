<?php

	require_once 'control/startSession.php';
	require_once 'control/connection.php';

	$page_title = 'Assign Session Request';
	require_once 'includes/header.php';

	$librarianid = null; // we'll need this later in the page, after we've made other queries

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
		$row['assignment_fileid'], $row['syllabus_fileid'], $row['status'], $librarianid);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_free_result($stmt);

	$stmt = mysqli_prepare($dbc, 'select (select name from sessionreqs_types where id = ?) as type,
                                       (select crspName from courseprefix where crspID = ?) as prefix;');
	mysqli_stmt_bind_param($stmt, 'ii', $row['type'], $row['courseprefixid']);
	mysqli_stmt_execute($stmt) or die('Failed to get session type, course prefix: ' . mysqli_error($dbc));
	mysqli_stmt_store_result($stmt);
	mysqli_stmt_bind_result($stmt, $row['typename'], $row['prefixname']);
	mysqli_stmt_fetch($stmt);
	mysqli_stmt_free_result($stmt);

	if($_POST) {

		if ($_SESSION['roleID'] != 1) { header('HTTP/1.0 403 Forbidden'); die('Must be admin to assign requests!'); }

		$librarianid = $_POST['libmID'];

		$stmt = mysqli_prepare($dbc, 'update sessionreqs set status="a", librarianid=? where id=?');
		mysqli_stmt_bind_param($stmt, 'ii', $_POST['libmID'], $_GET['id']);
		mysqli_stmt_execute($stmt) or die('Failed to assign librarian: ' . mysqli_error($dbc));

		$link = 'http://kscmasonlibrary.org/assessment/requestAccept.php?id='.$_GET['id'];
		$coursenum = $row['prefixname']."-".$row['coursenumber']."-".$row['coursesection'];

		$longtext =
			"You have been assigned to a new information literacy session.\n".
			"Please visit $link to accept the session.\n".
			"A summary of the data has been included below for your convenience.\n\n".
			"Name: "                  .$row['name']       ."\n".
			"Email: "                 .$row['email']      ."\n".
			"Phone: "                 .$row['phone']      ."\n".
			"Department: "            .$row['department'] ."\n".
			"Course number: "         .$coursenum         ."\n".
			"Course name: "           .$row['coursename'] ."\n".
			"Meets: "                 .$row['meets']      ."\n".
			"Number of students: "    .$row['numstudents']."\n".
			"Type of session: "       .$row['typename']   ."\n".
			"Date 1: "                .$row['date1']      ."\n".
			"Date 2: "                .$row['date2']      ."\n".
			"More dates: "            .$row['moredates']  ."\n".
			"Assignment description: ".$row['description']."\n";

		$email = '';
		$stmt = mysqli_prepare($dbc, 'select userID from users u
			left outer join librarianmap l on u.userID = l.libmuserID
			where l.libmID = ?;');
		mysqli_stmt_bind_param($stmt, 'i', $_POST['libmID']);
		mysqli_stmt_execute($stmt) or die('Failed to retrieve user ID: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $userid);
		mysqli_stmt_fetch($stmt);
		
		notify($userid, 'You have been assigned to an information literacy session ('.$coursenum.')', $longtext, $link);
		mysqli_stmt_free_result($stmt);

		$_SESSION['dialogText'] .= 'Session request successfully assigned.';
		header('Location: requestList.php');

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

	if ($_SESSION['roleID'] == 1) {
		echo '<form method="post">';
			echo '<h2>Assign to librarian</h2>';
			echo '<select id="libmID" name="libmID">';
			$result = mysqli_query($dbc, 'select l.libmID as ID, p.ppleFName as FName, p.ppleLName as LName, l.libmStatus as Status
				from people p, librarianmap l where p.ppleID=l.libmppleID') or die('Error querying for librarians: ' . mysqli_error($dbc));

			echo '<option selected disabled>Select a librarian</option>';
			while ($row = mysqli_fetch_assoc($result)) {

				if ($row['Status'] != 'active') { continue; }
				if ($librarianid == $row['ID']) {
					echo '<option id="libm'.$row['ID'].'" value="'.$row['ID'].'" selected>'.$row['FName'].' '.$row['LName'].'</option>';
				} else {
					echo '<option id="libm'.$row['ID'].'" value="'.$row['ID'].'">'.$row['FName'].' '.$row['LName'].'</option>';
				}
			}

			mysqli_free_result($result);
			echo '</select>';

			echo '<input type="submit">';
		echo '</form>';
	}

	require_once 'includes/footer.php';

?>
