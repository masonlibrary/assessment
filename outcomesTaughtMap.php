<?php

    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'Outcomes Taught Map';
  include('includes/header.php');

 // $thisUser=$_SESSION['thisUser'];

  ?>

<h2>Outcomes Map - Taught</h2>
<a href="outcomesAssessedMap.php">Go to Outcomes Assessed Map</a>

<?php
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');


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
                     'ov.students AS "Number of Students", ';



             foreach($otcHeadings as $IDValue)
                 {
                 $query.='(select if((count(0) > 0),"x","") from outcomesview x where ((x.sesdID = ov.sesdID) and (x.otchID = '.$IDValue.'))) AS "Outcome '.$IDValue.'", ';

                 }
             $query = substr($query, 0, -2);
             $query.=' ';
             $query.='from '.
                     'outcomesview ov '.
                     'where '.
                     'ov.prefix <>"ITW" '.
                     'and ov.prefix <>"IQL" '.
                     // fix the following group-by statement to also concatenate date
                     //otherwise duplicate course/sections accross semesters disappear.

                     'group by  concat(ov.prefix," ",ov.number,"-",ov.section) '.
                     'order by Date,concat(ov.prefix," ",ov.number,"-",ov.section)';




             // 9 columns.
             $output = '<br><table id="outcomesMap"><thead id="outcomesMapHead"><tr>'.



                    // *** for dataTables grouping addOn                  ***
                    // *** delete header and data lines if not used       ***
                    "<th>Semester</th>".
                    // ***                                                ***
                    // ***                                                ***

                     '<th>Course Number</th>'.
                     '<th>Course Faculty</th>'.
                     '<th>Session Held</th>'.
                     '<th># of Students</th>'.
                     '<th>Outcome 1</th>'.
                     '<th>Outcome 2</th>'.
                     '<th>Outcome 3</th>'.
                     '<th>Ouctome 5</th>'.
                     '</tr></thead><tbody>';

             $result = mysqli_query($dbc, $query) or die('This is an outrage-in outcomesMap.    '.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n $query";}


                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $semester= toSemester($row['Date']);
                    $courseNumber=$row['CourseNumber'];
                    $courseFaculty=$row['Course Faculty'];
                    $sessionDate=  toUSDate($row['Date']);
                    $numberOfStudents=$row['Number of Students'];
                    $outcome1=$row['Outcome 1'];
                    $outcome2=$row['Outcome 2'];
                    $outcome3=$row['Outcome 3'];
                    $outcome5=$row['Outcome 5'];


                    $output.="<tr class='outcomesMap'>".


                        // *** for dataTables grouping addOn                 ***
                        // *** delete header and data line if not used       ***
                        "<td class='outcomesMap'>$semester</td>".
                        // ***                                               ***

                        "<td class='outcomesMap'>$courseNumber</td>".
                        "<td class='outcomesMap'>$courseFaculty</td>".
                        "<td class='outcomesMap'>$sessionDate</td>".
                        "<td class='outcomesMap'>$numberOfStudents</td>".
                        "<td class='outcomesMap'>$outcome1</td>".
                        "<td class='outcomesMap'>$outcome2</td>".
                        "<td class='outcomesMap'>$outcome3</td>".
                        "<td class='outcomesMap'>$outcome5</td></tr>";

                }

                $output.='</tbody></table>';
                echo $output;
?>




 <?php

  include('includes/reportsFooter.php');
  ?>
