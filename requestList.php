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
		echo '<th>Department</th>';
		echo '<th>Course</th>';
		echo '<th>Meets</th>';
		echo '<th>Assignment</th>';
		echo '<th>Syllabus</th>';
		echo '<th>Requested</th>';
		echo '<th></th>';
	echo '</tr></thead><tbody>';
	$result = mysqli_query($dbc, 'select name, department, course, meets,
		assignment_fileid, syllabus_fileid, requested
		from sessionreqs order by id desc') or die('Failed to execute query:' . mysqli_error($dbc));
	while ($row = mysqli_fetch_assoc($result)) {
		echo '<tr>';
			echo '<td>'.$row['name'].'</td>';
			echo '<td>'.$row['department'].'</td>';
			echo '<td>'.$row['course'].'</td>';
			echo '<td>'.$row['meets'].'</td>';
			if ($row['assignment_fileid']) {
				echo '<td><a class="symbol" href="requestFile?id='.$row['assignment_fileid'].'">&#128196;</a></td>';
			} else {
				echo '<td></td>';
			}
			if ($row['syllabus_fileid']) {
				echo '<td><a class="symbol" href="requestFile?id='.$row['syllabus_fileid']  .'">&#128196;</a></td>';
			} else {
				echo '<td></td>';
			}
			echo '<td>'.$row['requested'].'</td>';
			echo '<td>View &raquo;</td>';
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
