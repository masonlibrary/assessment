<?php

	require_once('control/connection.php');
	require_once('control/startSession.php');

	$page_title = 'Manage resources';
	include('includes/header.php');

	if ($_POST) {

		var_dump($_POST);

		// Don't handle updating of resource names; maybe only let admin change?
//		$stmt = mysqli_prepare($dbc, 'update resourcepool set rsrpName=? where rsrpID=?');
//		foreach ($_POST['resources'] as $id=>$name) {
//			if (trim($name) == '') { $name = "(blank)"; }
//			mysqli_stmt_bind_param($stmt, 'si', $name, $id);
//			mysqli_stmt_execute($stmt) or die('Failed to update existing resources: ' . mysqli_error($dbc));
//		}

		// Update existing rows (only the activeness)
		$stmt = mysqli_prepare($dbc, 'update resourcepool set rsrpActive=? where rsrpID=?');
		for ($i=0; $i<$_POST['numResources']; $i++) {
			if (isset($_POST['active'][$i]) && $_POST['active'][$i] == 'on') { $active = 'yes'; } else { $active = 'no'; }
			mysqli_stmt_bind_param($stmt, 'si', $active, $i);
			mysqli_stmt_execute($stmt) or die('Failed to update existing resources: ' . mysqli_error($dbc));
		}

		// Insert new rows (name and activeness)
		$stmt = mysqli_prepare($dbc, 'insert into resourcepool (rsrpName, rsrpActive) values (?, ?)');
		foreach ($_POST['newResources'] as $id=>$name) {
			if (trim($name) == '') { continue; }
			if (isset($_POST['newActive'][$id]) && $_POST['newActive'][$id] == 'on') { $active = 'yes'; } else { $active = 'no'; };
			mysqli_stmt_bind_param($stmt, 'ss', $name, $active);
			mysqli_stmt_execute($stmt) or die('Failed to insert new resources: ' . mysqli_error($dbc));
		}

	}

	echo '<h1>'.$page_title.'</h1>
		<form method="post">
			<h2>Existing resources</h2>
			<table id="resourceList">
				<tr><th>#</th><th>Resource name</th><th>Active</th></tr>';

	$numResources = 0;
	$result = mysqli_query($dbc, 'select rsrpID, rsrpName, rsrpActive from resourcepool') or die('Error querying for users: ' . mysqli_error($dbc));
	while ($row = mysqli_fetch_assoc($result)) {
		echo '<tr>
				<td>'.$row['rsrpID'].'</td>
				<td><input type="text" disabled name="resources[]" size="40" value="'.$row['rsrpName'].'"/></td>
				<td><input type="checkbox" name="active['.$row['rsrpID'].']" '.($row['rsrpActive']=='yes'?'checked':'').'/></td>
			</tr>';
		$numResources++;
	}

	echo '</table>
		<h2>New resources</h2>
		<table id="newResourceList">
			<tr><th>Resource name</th><th>Active</th></tr>';

	for ($i=0; $i<5; $i++) {
		echo '<tr>
				<td><input type="text" name="newResources[]" size="40"/></td>
				<td><input type="checkbox" name="newActive['.$i.']" checked/></td>
			</tr>';
	}

	echo '</table>';
	echo '<input type="hidden" name="numResources" value="'.$numResources.'"/>';
	echo '<input type="submit"/></form>';

	//echo '<script type="text/javascript">$(document).ready( function(){$("#resourceList").dataTable();} );</script>';

	include('includes/footer.php');

?>
