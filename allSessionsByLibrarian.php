<?php

    include('control/connectionVars.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    include('control/functions.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'All Sessions - by Librarian';
  include('includes/header.php');

 // $thisUser=$_SESSION['thisUser'];

  ?>

<h2>All Sessions - by Librarian</h2>


<?php
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');



            $query ='select '.
                        's.sesdID as sessionID, '.
                        'p.ppleFName as FirstName, '.
                        'p.ppleLName as LastName, '.
                        'cp.crspName as CoursePrefix, '.
                        's.sesdCourseNumber as CourseNumber, '.
                        's.sesdCourseSection as CourseSection, '.
                        's.sesdCourseTitle as CourseTitle, '.
                        's.sesdDate as Date, '.
                        's.sesdFaculty as Faculty, '.
                        'sl.seslName as SessionLength, '.
                        'sl.seslMinutes as SessionMinutes, '.
                        's.sesdNumStudents as StudentCount, '.
                        's.sesdOutcomeDone as OutcomeDone, '.
                        's.sesdAssessed as AssessedDone '.
                        'from '.
                        'sessiondesc s, '.
                        'sesslength sl, '.
                        'courseprefix cp, '.
                        'librarianmap lm, '.
                        'people p '.
                        'where '.
                        's.sesdcrspID = cp.crspID '.
                        'and lm.libmppleID=p.ppleID '.
                        'and s.sesdlibmID = lm.libmID '.
                        'and s.sesdseslID = sl.seslID ';

						if (isset($_GET['semester']) && $_GET['semester'] != "any") {
							switch ($_GET['semester']) {
								case "spring":
									$query .= "and MONTH(s.sesdDate) <= 4 ";
									break;
								case "fall":
									$query .= "and MONTH(s.sesdDate) >= 8 ";
									break;
								case "summer":
									$query .= "and MONTH(s.sesdDate) > 4 AND MONTH(s.sesdDate) < 8 ";
									break;
							}
						}

						if (isset($_GET['year']) && $_GET['year'] != "") {
							// FIXME user input in a query
							$query .= "and YEAR(s.sesdDate) = " . $_GET['year'] . " ";
						}

						$query .= 'order by '.
                        'LastName, '.
                        'CoursePrefix, '.
                        'CourseNumber, '.
                        'CourseSection;';


             // 9 columns.
             $output = '<table id="allSessions"><thead id="allSessionsHead"><tr>'.

                    // *** for dataTables grouping addOn                  ***
                    // *** delete header and data lines if not used       ***
                    "<th>Librarian</th>".
                    // ***                                                ***

                     '<th>Course Number</th>'.
                     '<th>Course Faculty</th>'.
                     '<th>Session Held</th>'.
                     '<th>Semester</th>'.
                     '<th>Week</th>'.
                     '<th>Students</th>'.
                     '<th>Minutes</th>'.
                     '<th>Outcomes?</th>'.
                     '<th>Assessed?</th>'.
                     '</tr></thead><tbody>';

             $result = mysqli_query($dbc, $query) or die('Error in allSessionsByLibrarian: ' . mysqli_error($dbc));

                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $librarian=$row['LastName'].", ".$row['FirstName'];
                    $courseNumber=$row['CoursePrefix'].$row['CourseNumber'].'-'.$row['CourseSection'];
                    $courseFaculty=$row['Faculty'];
                    $sessionDate=$row['Date'];
                    $semester= toSemester($sessionDate);
                    $weekNumber=  toWeekNumber($sessionDate);
                    $numberOfStudents=$row['StudentCount'];
                    $sessionMinutes=$row['SessionMinutes'];
                    $outcomesDone=$row['OutcomeDone'];
                    $assessedDone=$row['AssessedDone'];



                    $output.="<tr class='allSessions'>".


                        // *** for dataTables grouping addOn                 ***
                        // *** delete header and data line if not used       ***
                        "<td class='allSessions'>$librarian</td>".
                        // ***                                               ***

                        "<td class='allSessions'>$courseNumber</td>".
                        "<td class='allSessions'>$courseFaculty</td>".
                        "<td class='allSessions'>$sessionDate</td>".
                        "<td class='allSessions'>$semester</td>".
                        "<td class='allSessions'>$weekNumber</td>".
                        "<td class='allSessions'>$numberOfStudents</td>".
                        "<td class='allSessions'>$sessionMinutes</td>".
                        "<td class='allSessions'>$outcomesDone</td>".
                        "<td class='allSessions'>$assessedDone</td>".
                        "</tr>";

                }

                $output.='</tbody></table>';
                echo $output;


?>



  <?php

  include('includes/reportsFooter.php');
  ?>
