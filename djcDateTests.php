<?php

    include('control/connectionVars.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    include('control/functions.php');
    require_once('control/startSession.php');
    
    // Insert the page header
  $page_title = 'djc Date Tests';
  include('includes/header.php');
?>
<h2>Date tests</h2>


<?php
    
     $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');
            
   

            $query ='select '.
                        's.sesdID as sessionID, '.
                        's.sesdDate as Date '.
                        'from '.
                        'sessiondesc s; ';
                     
      
             // 9 columns. 
             $output = '<table id="dateTest"><thead id="dateTestHead"><tr>'.
                     
                   
                    "<th>ID</th>".
                     '<th>MySQL Date</th>'.
                     '<th>US Date</th>'.
                     '<th>Week#</th>'.
                     '<th>Semester</th>'.
                     '<th>Fiscal Year</th>'.
                     '<th>AcademicYear</th>'.
                     '<th>SemesterQuery</th>'.
                     '<th>FY Query</th>'.
                     '<th>AY Query</th>'.
                     '</tr></thead><tbody>';
             
             $result = mysqli_query($dbc, $query) or die('This is an outrage-in testingDates.    '.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n $query";}
                
               
                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $id=$row['sessionID']; 
                    $date=$row['Date'];
                    $USDate= toUSDate($date);
                    $WeekNumber = toWeekNumber($date);
                    $Semester= toSemester($date);
                    $FiscalYear= toFiscalYear($date);
                    $AcademicYear=toAcademicYear($date);
                    $SemesterQuery= inSemester($Semester);
                    $FYQuery = inFiscalYear($FiscalYear);
                    $AYQuery=  inAcademicYear($AcademicYear);
                    
                   
                    $output.="<tr class='outcomesMapa'>".
                            
                       
                        "<td class='outcomesMapa'>$id</td>".
                        "<td class='outcomesMapa'>$date</td>".
                        "<td class='outcomesMapa'>$USDate</td>".
                        "<td class='outcomesMapa'>$WeekNumber</td>".
                        "<td class='outcomesMapa'>$Semester</td>".
                        "<td class='outcomesMapa'>$FiscalYear</td>".
                        "<td class='outcomesMapa'>$AcademicYear</td>".
                        "<td class='outcomesMapa'>$SemesterQuery</td>".
                        "<td class='outcomesMapa'>$FYQuery</td>".
                        "<td class='outcomesMapa'>$AYQuery</td></tr>";
                       
                    
                }
                
                $output.='</tbody></table>';
                echo $output;



?>


 <?php
  
  include('includes/reportsFooter.php');
  ?>