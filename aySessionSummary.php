<?php

    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'Academic Year Session Summary';
  include('includes/header.php');

	(isset($_GET['semester']) && $_GET['semester'] != "") ? $semester = $_GET['semester'] : $semester = "any";
	(isset($_GET['year']) && $_GET['year'] != "") ? $year = $_GET['year'] : $year = "any";

echo '<h2>Academic Year - Session Summary for <span id="daterange">'.$semester.' semester, AY '.$year.'</span></h2>';

            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Failed to connect to the database: '.mysqli_error($dbc));

						echo '<h3>Sessions by level</h3>';

						// This will be reused in several queries
						$dateFragment = ' where '.inSemester($semester, 'sesdDate').' and '.inAcademicYear($year, 'sesdDate');

						$query = 'select (select count(sesdID) from lowerlevel '.$dateFragment.') as lcount, (select count(sesdID) from upperlevel '.$dateFragment.') as ucount';
						$result = mysqli_query($dbc, $query) or die('Failed to query database: ' . mysqli_error($dbc));
						$row = mysqli_fetch_assoc($result);
						$totalsessions = $row['lcount'] + $row['ucount'];
						echo '<table id="ulsession">
								<thead>
									<tr>
										<th>Description</th>
										<th>Count</th>
										<th>Percent</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Lower level</td>
										<td id="llcount">'.$row['lcount'].'</td>
										<td>'.round($row['lcount']/$totalsessions*100, 2).'</td>
									</tr>
									<tr>
										<td>Upper level</td>
										<td id="ulcount">'.$row['ucount'].'</td>
										<td>'.round($row['ucount']/$totalsessions*100, 2).'</td>
									</tr>
								</tbody>
							</table>';

						echo '<h3>Sessions by course</h3>';
						$query = 'select (select count(sesdID) from itw '.$dateFragment.') as count, "ITW" as description
							  union select (select count(sesdID) from iql '.$dateFragment.'), "IQL"
							  union select (select count(sesdID) from hlsc '.$dateFragment.'), "HLSC"
							  union select (select count(sesdID) from ihcomm171 '.$dateFragment.'), "IHCOMM-171"';
						$result = mysqli_query($dbc, $query) or die('Failed to query database: ' . mysqli_error($dbc));

						$stotal = 0;

						echo '<table id="subjectsession">
								<thead>
									<tr>
										<th>Description</th>
										<th>Count</th>
										<th>Percent</th>
									</tr>
								</thead>
								<tbody>';

						while ($row = mysqli_fetch_assoc($result)) {
							$stotal += $row['count'];
							echo '<tr>
									<td>'.$row['description'].'</td>
									<td>'.$row['count'].'</td>
									<td>'.round($row['count']/$totalsessions*100, 2).'</td>
								</tr>';
						}

						echo '<tr>
								<td>Other</td>
								<td>'.($totalsessions-$stotal).'</td>
								<td>'.round(($totalsessions-$stotal)/$totalsessions*100, 2).'</td>
							</tr>';

						echo '</tbody>
							</table>';

?>

        <div id="chartContainer" style="clear: both; margin-top: 40px;">
            <div id="upperLowerChart"></div>
            <div id="ITWetcChart"></div>
        </div>

<?php
$jsOutput .= '
	var oTable = $("#ulsession").dataTable({
		"sDom": "T<\'clear\'>lrtp",
		"bSort": false,
		"bPaginate": false,
		"oTableTools": { "sSwfPath":"swf/copy_csv_xls_pdf.swf" }
	});
	var oTable = $("#subjectsession").dataTable({
		"sDom": "T<\'clear\'>lrtp",
		"bSort": false,
		"bPaginate": false,
		"oTableTools": { "sSwfPath":"swf/copy_csv_xls_pdf.swf" }
	});

	var uldata = tableToArray("ulsession", 0, 2);
	ulchart = new Highcharts.Chart({
		title: { text: "Sessions by level ("+$("#daterange").text()+")" },
		chart: { renderTo: "upperLowerChart" },
		series: [{
			type: "pie",
			data: uldata,
			animation: false
		}]
	});

	var sdata = tableToArray("subjectsession", 0, 2);
	schart = new Highcharts.Chart({
		title: { text: "Sessions by course ("+$("#daterange").text()+")" },
		chart: { renderTo: "ITWetcChart" },
		series: [{
			type: "pie",
			data: sdata,
			animation: false
		}]
	});
	';

  include('includes/footer.php');
?>
