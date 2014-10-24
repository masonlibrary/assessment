<?php

	include('control/connectionVars.php');
	include_once('control/functions.php');
	include('classes/InstructionSession.php');
	include('classes/User.php');

	require_once('control/startSession.php');

	$page_title = 'My Sessions';
	include('includes/header.php');

	$thisUser = $_SESSION['thisUser'];

	echo '<h2>'.$page_title.'</h2>';
	echo '<a href="#" class="test">Test: Chart filtered sessions by prefix</a>';

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to the stupid database');

	if ($thisUser->isLibrarian) {
		echo '<div class="dataTables_filter">
				<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
				<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
			</div>
			<table id="mySessions"><thead id="mySessionsHead"><tr>
			<th>Course</th>
			<th>Title</th>
			<th>Faculty</th>
			<th>Fellow Present</th>
			<th>Session</th>
			<th>Semester</th>
			<th>Date</th>
			<th>Date2</th>
			<th>Outcomes</th>
			<th>Assessed</th>
			<th>Tools</th>
			</tr></thead><tbody>';
		$query = 'select
				s.sesdID as sessionID,
				cp.crspName as CoursePrefix,
				s.sesdCourseNumber as CourseNumber,
				s.sesdCourseSection as CourseSection,
				s.sesdCourseTitle as CourseTitle,
				s.sesdSessionSection as SessionNum,
				s.sesdFellowPresent as FellowPresent,
				s.sesdDate as Date,
				s.sesdFaculty as Faculty,
				s.sesdOutcomeDone as OutcomeDone,
				s.sesdAssessed as AssessedDone
			from sessiondesc s, courseprefix cp
			where s.sesdlibmID=? and s.sesdcrspID=cp.crspID
			order by CoursePrefix, CourseNumber, CourseSection';

		$stmt = mysqli_prepare($dbc, $query);
		mysqli_bind_param($stmt, 'i', $thisUser->getLibrarianID());
		mysqli_stmt_execute($stmt) or die('Failed to retrieve session info: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $sessionID, $coursePrefix, $courseNumber, $courseSection,
			$courseTitle, $sessionNumber, $fellowPresent, $date, $faculty, $outcomeDone, $AssessedDone);
		while (mysqli_stmt_fetch($stmt)) {
			echo "<tr class='mySessions' id='$sessionID'>
				<td class='coursePrefix'>$coursePrefix $courseNumber-$courseSection</td>
				<td class='courseTitle' >$courseTitle</td>
				<td>$faculty</td>
				<td>$fellowPresent</td>
				<td>$sessionNumber</td>
				<td>" . toSemester($date) . "</td>
				<td class='dateCell'>" . toUSDate($date) . "</td>
				<td class='sqlDateCel'>$date</td>
				<td>$outcomeDone</td>
				<td>$AssessedDone</td>
				<td><div id='d$sessionID' class='menu-div'><p>+</p></div></td>";
		}
		mysqli_stmt_free_result($stmt);

		echo '</tbody></table>';
	}

	echo '<div id="chartContainer" style="clear: both; margin-top: 40px;"> <div id="testChart"></div> </div>';

	$jsOutput .= 'var oTable = $("#mySessions").dataTable({
			"sDom": "T<\'clear\'>lrtip",
			"iDisplayLength": -1,
			"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
			"aoColumns": [
				null,
				null,
				null,
				null,
				null,
				null,
				{"iDataSort": 6},
				{"bVisible": false},
				null,
				null,
				null
			],
			"oTableTools": {
				"sSwfPath": "swf/copy_csv_xls_pdf.swf",
				"aButtons": [
					{
						"sExtends": "csv",
						"sButtonText": "Excel/CSV",
						"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
					},{
						"sExtends": "pdf",
						"sButtonText": "PDF",
						"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
					},{
						"sExtends": "print",
						"sButtonText": "Print",
						"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
					},{
						"sExtends": "copy",
						"sButtonText": "Copy",
						"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
					}
				]
			}
		});';

	echo '<div id="dialog" style="display:none;"><div class="vcenter">Loading...</div></div>';

	include('includes/reportsFooter.php');

?>
