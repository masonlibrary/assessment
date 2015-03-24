<?php

	require_once 'control/startSession.php';
	require_once 'control/connection.php';

	$page_title = 'Session Requests';
	require_once 'includes/header.php';

	echo '<div class="dataTables_filter">
			<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
			<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
		</div>';
	echo '<table id="requestList"><thead><tr>';
		echo '<th>Name</th>';
		echo '<th>Course number</th>';
		echo '<th>Course name</th>';
		echo '<th>Meets</th>';
		echo '<th>Assignment</th>';
		echo '<th>Syllabus</th>';
		echo '<th>Requested</th>';
		echo '<th>Status</th>';
		echo '<th></th>';
	echo '</tr></thead><tbody>';
	$result = mysqli_query($dbc, 'select id, name, cp.crspName, coursenumber, coursesection,
		coursename, meets, assignment_fileid, syllabus_fileid, requested, status
		from sessionreqs sr left join courseprefix cp on sr.courseprefixid = cp.crspID order by id desc') or die('Failed to execute query:' . mysqli_error($dbc));
	while ($row = mysqli_fetch_assoc($result)) {
		echo '<tr>';
			echo '<td>'.$row['name'].'</td>';
			echo '<td>'.$row['crspName'].'-'.$row['coursenumber'].'-'.$row['coursesection'].'</td>';
			echo '<td>'.$row['coursename'].'</td>';
			echo '<td>'.$row['meets'].'</td>';
			if ($row['assignment_fileid']) {
				echo '<td><a class="symbol" title="Download file" href="requestFile.php?id='.$row['assignment_fileid'].'">&#128196;</a></td>';
			} else {
				echo '<td></td>';
			}
			if ($row['syllabus_fileid']) {
				echo '<td><a class="symbol" title="Download file" href="requestFile.php?id='.$row['syllabus_fileid']  .'">&#128196;</a></td>';
			} else {
				echo '<td></td>';
			}
			echo '<td>'.$row['requested'].'</td>';
			switch($row['status']) {
				case 'u': echo '<td>Unassigned</td>'; break;
				case 'a': echo '<td>Assigned</td>'; break;
				case 'x': echo '<td>Accepted</td>'; break;
				case 'c': echo '<td>Canceled</td>'; break;
			}
			echo '<td><a href="requestAssign.php?id='.$row['id'].'">View&nbsp;&raquo;</a></td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	mysqli_free_result($result);

	$jsOutput = 'var oTable = $("#requestList").dataTable({
			"sDom": "lrtip",
			"aaSorting": [[6, "desc"]],
		});';

	require_once 'includes/footer.php';

?>
