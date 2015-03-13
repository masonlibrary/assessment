<?php

	require_once 'control/startSession.php';
	require_once 'control/connection.php';

	$page_title = 'Request Session';
	require_once 'includes/header.php';

	if ($_POST) {

		// From http://www.php.net/manual/en/features.file-upload.errors.php#115746
		$errs = array(
			0 => 'There is no error, the file uploaded with success.',
			1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			3 => 'The uploaded file was only partially uploaded.',
			4 => 'No file was uploaded.',
			6 => 'Missing a temporary folder',
			7 => 'Failed to write file to disk.',
			8 => 'A PHP extension stopped the file upload.',
		);
		
		$fileids = array('assignment' => null, 'syllabus' => null);

		try {

			mysqli_autocommit($dbc, false);

			// Assignment doc upload handler
			if ($_FILES['assignment']['error'] == 0) {
				$f = fopen($_FILES['assignment']['tmp_name'], 'r');
				$data = fread($f, filesize($_FILES['assignment']['tmp_name']));
				fclose($f);
				$stmt = mysqli_prepare($dbc, 'insert into sessionreqs_files (filename, file) values (?, ?)');
				mysqli_stmt_bind_param($stmt, 'ss', $_FILES['assignment']['name'], $data);
				if(!mysqli_stmt_execute($stmt)) { throw new Exception('Failed to insert assignment file: ' . mysqli_error($dbc)); }
				$fileids['assignment'] = mysqli_insert_id($dbc);
			} else if ($_FILES['assignment']['error'] != 4) {
				throw new Exception('Error uploading assignment file: '.$errs[$_FILES['assignment']['error']]);
			}

			// Syllabus doc upload handler
			if ($_FILES['syllabus']['error'] == 0) {
				$f = fopen($_FILES['syllabus']['tmp_name'], 'r');
				$data = fread($f, filesize($_FILES['syllabus']['tmp_name']));
				fclose($f);
				$stmt = mysqli_prepare($dbc, 'insert into sessionreqs_files (filename, file) values (?, ?)');
				mysqli_stmt_bind_param($stmt, 'ss', $_FILES['syllabus']['name'], $data);
				if(!mysqli_stmt_execute($stmt)) { throw new Exception('Failed to insert syllabus file: ' . mysqli_error($dbc)); }
				$fileids['syllabus'] = mysqli_insert_id($dbc);
			} else if ($_FILES['syllabus']['error'] != 4) {
				throw new Exception('Error uploading syllabus file: '.$errs[$_FILES['syllabus']['error']]);
			}

			// Reformat dates for MySQL
			$date1 = DateTime::createFromFormat('m/d/Y', $_POST['date1'])->format('Y-m-d');
			$date2 = DateTime::createFromFormat('m/d/Y', $_POST['date2'])->format('Y-m-d');

			// Insert session request
			$stmt = mysqli_prepare($dbc, '
				insert into sessionreqs (requested, name, email, phone, department, coursename,
					courseprefixid, coursenumber, coursesection, meets,	numstudents, type,
					date1, date2, moredates, description, assignment_fileid, syllabus_fileid)
				values (now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
			mysqli_stmt_bind_param($stmt, 'sssssiiisiissssii', $_POST['name'],
				$_POST['email'], $_POST['phone'], $_POST['department'], $_POST['coursename'],
				$_POST['courseprefixid'],	$_POST['coursenumber'], $_POST['coursesection'],
				$_POST['meets'], $_POST['numstudents'], $_POST['type'], $date1, $date2,
				$_POST['moredates'], $_POST['description'], $fileids['assignment'],	$fileids['syllabus']);
			if(!mysqli_stmt_execute($stmt)) { throw new Exception('Failed to insert session request: ' . mysqli_error($dbc)); }

//			$result = mysqli_query($dbc, 'select LAST_INSERT_ID() as id');
//			if (!$result) { throw new Exception('Failed to get ID of inserted session request: ' . mysqli_error($dbc)); }
//			$row = mysqli_fetch_assoc($result);
//			$newid = $row['id'];
//			mysqli_free_result($result);

			// Get course prefix, name of session type
			// Hacky use of subqueries so we only make one request
			$row = array();
			$stmt = mysqli_prepare($dbc, 'select LAST_INSERT_ID() as id,
				                                   (select name from sessionreqs_types where id = ?) as type,
                                           (select crspName from courseprefix where crspID = ?) as prefix;');
			mysqli_stmt_bind_param($stmt, 'ii', $_POST['type'], $_POST['courseprefixid']);
			if (!mysqli_stmt_execute($stmt)) { throw new Exception('Failed to get last ID, course prefix, name of session type: ' . mysqli_error($dbc)); }
			mysqli_stmt_store_result($stmt);
			mysqli_stmt_bind_result($stmt, $row['id'], $row['typename'], $row['prefixname']);
			mysqli_stmt_fetch($stmt);

			$link = 'http://kscmasonlibrary.org/assessment/requestAssign.php?id='.$row['id'];
			$coursenum = $row['prefixname']."-".$_POST['coursenumber']."-".$_POST['coursesection'];

			// Build text summary of form for email
			$longtext =
				"A new session request has been added.\n".
				"Please visit $link to review the session.\n".
				"A summary of the data has been included below for your convenience.\n\n".
				"Name: "                  .$_POST['name']       ."\n".
				"Email: "                 .$_POST['email']      ."\n".
				"Phone: "                 .$_POST['phone']      ."\n".
				"Department: "            .$_POST['department'] ."\n".
				"Course number: "         .$coursenum           ."\n".
				"Course name: "           .$_POST['coursename'] ."\n".
				"Meets: "                 .$_POST['meets']      ."\n".
				"Number of students: "    .$_POST['numstudents']."\n".
				"Type of session: "       .$row['typename']     ."\n".
				"Date 1: "                .$date1               ."\n".
				"Date 2: "                .$date2               ."\n".
				"More dates: "            .$_POST['moredates']  ."\n".
				"Assignment description: ".$_POST['description']."\n";

			mysqli_stmt_free_result($stmt);

			// Get people to email (all admins)
			$result = mysqli_query($dbc, '
				select userID from users u
					left outer join librarianmap l on u.userID = l.libmuserID
					left outer join people p on l.libmppleID = p.ppleID
					left outer join userroles ur on ur.roleuserID = u.userID
				where ur.roleroleID = 1 and l.libmStatus = "active"');
			if (!$result) { throw new Exception('Failed to get notification email recipients:' . mysqli_error($dbc)); }
			while ($row = mysqli_fetch_assoc($result)) {
				notify($row['userID'], 'New information literacy session request ('.$coursenum.')', $longtext, $link);
			}
			mysqli_free_result($result);

			mysqli_commit($dbc);
			mysqli_autocommit($dbc, true);

			$_SESSION['dialogText'] .= 'Session request successfully added.';

		} catch (Exception $e) {

			mysqli_rollback($dbc);
			mysqli_autocommit($dbc, true);
			$_SESSION['dialogText'] .= 'Failed to add session request: '.$e->getMessage();

		}

		header('Location: requestSession.php');

	}

?>

<form method="post" enctype="multipart/form-data">

	<div>
		<label for="name">Your name</label>
		<div><input type="text" id="name" name="name" maxlength="80" required></div>
	</div>

	<div>
		<label for="email">Your KSC email address</label>
		<div><input type="email" id="email" name="email" maxlength="80" required></div>
	</div>

	<div>
		<label for="phone">Your phone number (campus number if possible)</label>
		<div><input type="tel" id="phone" name="phone" maxlength="80" required></div>
	</div>

	<div>
		<label for="department">Department</label>
		<div><input type="text" id="department" name="department" maxlength="80"></div>
	</div>

	<div>
		<label>Course prefix, number, and section</label>
		<div>
			<select id="courseprefixid" name="courseprefixid" required>
				<option selected disabled></option>
				<?php
					$result = mysqli_query($dbc, 'select crspID, crspName from courseprefix') or die('Failed get course prefixes:' . mysqli_error($dbc));
					while ($row = mysqli_fetch_assoc($result)) {
						echo '<option value="'.$row['crspID'].'">'.$row['crspName'].'</option>';
					}
					mysqli_free_result($result);
				?>
			</select>
			-
			<input type="text" id="coursenumber" name="coursenumber" size="3" maxlength="3" pattern="[0-9]{3}" title="Course number (three digits)" required>
			-
			<input type="text" id="coursesection" name="coursesection" size="2" maxlength="2" pattern="[0-9]{2}" title="Course section (two digits)" required>
		</div>
	</div>

	<div>
		<label for="coursename">Course name</label>
		<div><input type="text" id="coursename" name="coursename" maxlength="80" required></div>
	</div>

	<div>
		<label for="meets">Day(s) and time class meets</label>
		<div><input type="text" id="meets" name="meets" maxlength="80" required></div>
	</div>

	<div>
		<label for="numstudents">Number of students in the class</label>
		<div><input type="text" id="numstudents" name="numstudents" required></div>
	</div>

	<div>
		Type of session
		<?php

			$result = mysqli_query($dbc, 'select id, name from sessionreqs_types where active = "y"') or die('Failed to fetch session types:' . mysqli_error($dbc));
			while ($row = mysqli_fetch_assoc($result)) {
				echo '<div><label><input type="radio" name="type" value="'.$row['id'].'" required>'.$row['name'].'</input></label></div>';
			}
			mysqli_free_result($result);

		?>
	</div>

	<div>
		<p><strong>PLEASE NOTE:</strong></p>
		<p>Sessions are typically held NO MORE than 2 weeks before the assignment due date.</p>
		<p>Students MUST have been introduced to the assignment PRIOR to the session in the library.</p>
	</div>

	<div>
		<label for="date1">1st choice | Preferred date for session</label>
		<div><input type="text" id="date1" name="date1" maxlength="80" required></div>
	</div>

	<div>
		<label for="date2">2nd choice | Preferred date for session</label>
		<div><input type="text" id="date2" name="date2" maxlength="80" required></div>
	</div>

	<div>
		<label for="moredates">If you would like more than one session for your
			course, enter your preferred dates here:</label>
		<div><input type="text" id="moredates" name="moredates" maxlength="80"></div>
	</div>

	<div>
		<label for="description">Research sessions are provided in support of specific
			assignments. Briefly describe the assignment that the research session is
			to support. Please include the assignment due date. The librarian working
			with your class will need a copy of your syllabus and the assignment.
		</label>
		<div><textarea id="description" name="description" maxlength="400"></textarea></div>
	</div>

	<div>
		<label for="assignment">Please attach your assignment</label>
		<div><input type="file" id="assignment" name="assignment"></div>
	</div>

	<div>
		<label for="syllabus">Please attach your syllabus</label>
		<div><input type="file" id="syllabus" name="syllabus"></div>
	</div>

	<br/>

	<div>
		<div><input type="submit"></div>
	</div>

</form>

<?php

	$jsOutput .= '$("#numstudents").spinner({min: 0});';
	$jsOutput .= '$("#date1").datepicker();';
	$jsOutput .= '$("#date2").datepicker();';
	require_once 'includes/footer.php';

?>