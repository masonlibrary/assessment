<?php

//    include('control/connectionVars.php');
	include('control/connection.php');
	include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'Outcomes Assessed Map';
  include('includes/header.php');

 // $thisUser=$_SESSION['thisUser'];

	(isset($_GET['semester']) && $_GET['semester'] != "") ? $semester = $_GET['semester'] : $semester = "any";
	(isset($_GET['year']) && $_GET['year'] != "") ? $year = $_GET['year'] : $year = "any";

	echo "<h2>Outcomes Map - Assessed ($semester semester, AY $year)</h2>";
	echo "<a href='outcomesTaughtMap.php?semester=$semester&year=$year'>Go to Outcomes Taught Map</a>";

//	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
//                    or die('Error connecting to the stupid database');


            //get all active outcome headings.

             $query ='select otchID as "IDValue" from outcomeheading where otchActive="yes" order by otchID asc';
             $result = mysqli_query($dbc, $query) or die('This is an outrage-in outcomesMap.    '.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n $query";}

                $otcHeadings = array();
                $x=0;
             while ( $row = mysqli_fetch_assoc( $result) )
                 {
                 $otcHeadings[$x] =$row['IDValue'];
                 $x++;
                 }


             //query to build report
             $query ='';
             $query = 'select '.
                     'ov.Date as "Date", '.
                     'concat(ov.prefix," ",ov.number,"-",ov.section) AS "CourseNumber", '.
                     'ov.faculty AS "Course Faculty", '.
										 'ov.fellowpresent AS "Fellow Present", '.
                     'ov.students AS "Number of Students", ';



             foreach($otcHeadings as $IDValue) {
				 // @FIXME parameterize
				$query.='(select if((count(0) > 0),"x","") from outcomesview x where ((x.sesdID = ov.sesdID) and (x.otchID = '.$IDValue.'))) AS "Outcome '.$IDValue.'", ';
			 }
             $query = substr($query, 0, -2); // Take off final comma, space
             $query.=' ';
             $query.='from '.
                     'outcomesview ov '.
                     'where '.
                     'ov.prefix <>"ITW" '.
                     'and ov.prefix <>"IQL" '.
										 'and ov.assessed = "yes"'.
										 'and' . inSemester($semester, 'Date').
										 'and' . inAcademicYear($year, 'Date').
										 // fix the following group-by statement to also concatenate date
										 // otherwise duplicate course/sections accross semesters disappear.
										 'group by concat(ov.prefix," ",ov.number,"-",ov.section) '.
                     'order by ov.Date,concat(ov.prefix," ",ov.number,"-",ov.section)';




						// 9 columns.
						$output = '<div class="dataTables_filter">
								<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
								<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
							</div>
							<table id="outcomesMap"><thead id="outcomesMapHead"><tr>' .



                    // *** for dataTables grouping addOn                  ***
                    // *** delete header and data lines if not used       ***
                    "<th>Semester</th>".
                    // ***                                                ***
                    // ***                                                ***

                     '<th>Course Number</th>'.
                     '<th>Course Faculty</th>'.
                     '<th>Fellow Present</th>'.
                     '<th>Session Held</th>'.
                     '<th># of Students</th>'.
                     '<th>Outcome 1</th>'.
                     '<th>Outcome 2</th>'.
                     '<th>Outcome 3</th>'.
                     '<th>Outcome 5</th>'.
                     '</tr></thead><tbody>';

				$row = array();
				$stmt = mysqli_prepare($dbc, $query);
				mysqli_stmt_execute($stmt) or die('Failed to retrieve outcomes taught: ' . mysqli_error($dbc));
				mysqli_stmt_store_result($stmt);
				mysqli_stmt_bind_result($stmt, $row['Date'], $row['CourseNum'], $row['CourseFaculty'], $row['FellowPresent'],
					$row['NumStudents'], $row['Outcome1'], $row['Outcome2'], $row['Outcome3'], $row['Outcome5'], $row['Outcome6']);

				while (mysqli_stmt_fetch($stmt)) {
				$output.="<tr class='outcomesMap'>" .
					// *** for dataTables grouping addOn                 ***
					// *** delete header and data line if not used       ***
					"<td class='outcomesMap'>".toSemester($row['Date'])."</td>".
					// ***                                               ***
					"<td class='outcomesMap'>".$row['CourseNum']."</td>".
					"<td class='outcomesMap'>".$row['CourseFaculty']."</td>".
					"<td class='outcomesMap'>".$row['FellowPresent']."</td>".
					"<td class='outcomesMap'>".toUSDate($row['Date'])."</td>".
					"<td class='outcomesMap'>".$row['NumStudents']."</td>".
					"<td class='outcomesMap'>".$row['Outcome1']."</td>".
					"<td class='outcomesMap'>".$row['Outcome2']."</td>".
					"<td class='outcomesMap'>".$row['Outcome3']."</td>".
					"<td class='outcomesMap'>".$row['Outcome5']."</td></tr>";
				}
                $output.='</tbody></table>';
                echo $output;
	$jsOutput .= '
		var oTable = $("#outcomesMap").dataTable({
			"sDom":"T<\'clear\'>lrtip",
			"bPaginate": false,
			"oTableTools":{ "sSwfPath":"swf/copy_csv_xls_pdf.swf" }
		});';

  include('includes/footer.php');
  ?>
