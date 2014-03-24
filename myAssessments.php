<?php

	include('control/connectionVars.php');
	include_once('control/functions.php');
	include('classes/InstructionSession.php');
	include('classes/User.php');
	require_once('control/startSession.php');

	$page_title = 'My Assessments';
	include('includes/header.php');

	$thisUser = $_SESSION['thisUser'];
	
	echo '<h2>'.$page_title.'</h2>';

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Error connecting to the stupid database');

	if ($thisUser->isLibrarian) {
		$query = 'select
				s.sesdID as SessionID,
				cp.crspName as CoursePrefix,
				s.sesdCourseNumber as CourseNumber,
				s.sesdCourseSection as CourseSection,
				s.sesdSessionSection as SessionSection,
				s.sesdDate as Date,
				ot.otctID as OutcomeTaughtID,
				CONCAT(od.otcdotchID, od.otcdName) as OutcomeName,
				oa.otcaID as OutcomeID,
				oa.otcaMet as Met,
				oa.otcaPartial as Partial,
				oa.otcaNotMet as NotMet,
				oa.otcaNotAssessed as NotAssessed
			from
				sessiondesc s,
				courseprefix cp,
				outcomestaught ot,
				outcomedetail od,
				outcomesassessed oa
			where
				s.sesdlibmID = ?
				and s.sesdAssessed = "yes"
				and s.sesdcrspID = cp.crspID
				and ot.otctsesdID = s.sesdID
				and ot.otctotcdID = od.otcdID
				and oa.otcaotctID = ot.otctID
			order by
				CoursePrefix,
				CourseNumber,
				CourseSection,
				outcomeName';

		// 8 columns.
		echo '<div class="dataTables_filter">
				<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
				<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
			</div>
			<table id="myAssessments"><thead id="myAssessmentsHead"><tr>' .
			// *** for dataTables grouping addOn                  ***
			'<th>Course</th>' .
			// ***                                                ***
			'<th>Semester</th>' .
			// '<th>Course</th>'.
			'<th>Outcome</th>' .
			'<th>Met</th>' .
			'<th>Partially Met</th>' .
			'<th>Not Met</th>' .
			'<th>Assessed</th>' .
			'</tr></thead><tbody>';

		$row = array();
		$stmt = mysqli_prepare($dbc, $query);
		mysqli_bind_param($stmt, 'i', $thisUser->getLibrarianID());
		mysqli_stmt_execute($stmt) or die('Failed to retrieve assessments: ' . mysqli_error($dbc));
		mysqli_stmt_store_result($stmt);
		mysqli_stmt_bind_result($stmt, $row['SessionID'], $row['CoursePrefix'], $row['CourseNumber'], $row['CourseSection'], $row['SessionSection'], $row['Date'], $row['OutcomeTaughtID'], $row['OutcomeName'], $row['OutcomeID'], $row['Met'], $row['Partial'], $row['NotMet'], $row['NotAssessed']);

		while (mysqli_stmt_fetch($stmt)) {
			$sessionID = $row['SessionID'];
			$coursePrefix = $row['CoursePrefix'];
			$courseNumber = $row['CourseNumber'];
			$courseSection = $row['CourseSection'];
			$sessionSection = $row['SessionSection'];

			$date = $row['Date'];
			$sessionDate = toUSDate($date);
			$semester = toSemester($date);

			$outcomeTaughtID = $row['OutcomeTaughtID'];
			$outcomeName = $row['OutcomeName'];
			$outcomeID = $row['OutcomeID'];
			$met = $row['Met'];
			$partial = $row['Partial'];
			$notMet = $row['NotMet'];
			$notAssessed = $row['NotAssessed'];

			if ($notAssessed == '1') {
				$notAssessed = "No";
			} else {
				$notAssessed = "Yes";
			}

			echo "<tr class='myAssessments'>" .
				// *** for dataTables grouping addOn                 ***
				"<td class='myAssessments otcdID$outcomeID coursePrefix'>$coursePrefix $courseNumber-$courseSection $sessionSection $semester</td>" .
				// ***                                               ***
				"<td class='myAssessments otcdID$outcomeID semester'>$semester</td>" .
				// "<td class='myAssessments otcdID$outcomeID coursePrefix'>$coursePrefix $courseNumber-$courseSection $sessionSection</td>".
				"<td class='myAssessments otcdID$outcomeID outcomeName'>$outcomeName</td>" .
				"<td class='myAssessments otcdID$outcomeID met'>$met</td>" .
				"<td class='myAssessments otcdID$outcomeID partial'>$partial</td>" .
				"<td class='myAssessments otcdID$outcomeID notMet'>$notMet</td>" .
				"<td class='myAssessments otcdID$outcomeID notAssessed'>$notAssessed</td></tr>";
		}

		echo '</tbody></table>';

		mysqli_stmt_free_result($stmt);

	}

	$jsOutput .= 'var oTable = $("#myAssessments").dataTable({
			"sDom": "T<\'clear\'>lrtip",
			"bPaginate": false,
			"oTableTools": { "sSwfPath":"swf/copy_csv_xls_pdf.swf" }
		});';

	include('includes/footer.php');

?>
