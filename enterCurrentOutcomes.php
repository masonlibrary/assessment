<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Enter Outcomes Taught';
  include('includes/header.php');
  
  
  if (isset($_POST['submitOTCT'])) { 
    $currentSessions= array();
    
    $outcomesNeeded = $_POST['outcomesNeeded'];
    
   // echo "<h1>".count($outcomesNeeded)."</h1>";
    $count=count($outcomesNeeded);
    
    for($x=0; $x<$count; $x++)
        {
            $currentSessions[$x] = new InstructionSession();
            $currentSessions[$x]->loadSession($outcomesNeeded[$x]);
            
           // echo $currentSessions[$x]->toString();
        
        
        }
    $_SESSION['currentSessions']=$currentSessions;
}
  
  
  
  
  
  
  $currentSessions=$_SESSION['currentSessions'];
  $coursePrefix = $currentSessions[0]->getCoursePrefix();
  $coursePrefixID = $currentSessions[0]->getCoursePrefixID();
  $courseNumber = $currentSessions[0]->getCourseNumber();
  $courseTitle = $currentSessions[0]->getCourseTitle();
  $sessionCount = count($currentSessions);
  
  $courseHeaderString = '';
  $submitButtonString = '';
  
  if ($sessionCount > 1) 
      { 
        $courseHeaderString = 'You are selecting outcomes taught for <span class="sessionCountSpan">'.$sessionCount.'</span> sections of this course';
        $submitButtonString = 'Associate outcomes with these sessions';
        }
  else
      {
      $courseHeaderString = 'You are selecting outcomes taught for <span class="sessionCountSpan">'.$sessionCount.'</span> section of this course';
      $submitButtonString = 'Associate outcomes with this session';
      }
?>

		
        <?php 
           echo "<h2>$coursePrefix $courseNumber: $courseTitle</h2><p>$courseHeaderString</p>";
            
         ?>
         
     <form id="outcomesTaughtForm" method="post" action="submitOutcomes.php">       
         
         
      <?php //include('includes/selectSessionsNoOutcomes.php');
         include("control/connection.php");
           $query = "select ".
                "otpm.otcmotchID as headingID, ".
                "oh.otchName as headingName, ".
                "otpm.otcmsubhName as subheadingName, ".
                "od.otcdID as outcomeID, ".
                "od.otcdName as outcomeName ".
                "from ".
                "outcometoprefixmap otpm, ".
                "outcomeheading oh, ".
                "outcomedetail od ".
                "where ".
                "otpm.otcmcrspID=$coursePrefixID ".
                "and otpm.otcmotchID=oh.otchID ".
                "and oh.otchID=od.otcdotchID ".
                " order by otpm.otcmotchID"; 
                
             $currentOutcomeHeading='first';
             
            
             
            $result = mysqli_query($dbc, $query) or die('dammit- query issues. <br /><h4>'.$query.'</h4');
                    if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                    
                    echo '<div class="outcomesBox">';
                    
                          //$letters='abcdefghijklmnopqrstuvwxyz';
                          //$countByLetterIndex=0;   
                          
                    while ( $row = mysqli_fetch_assoc( $result) )
                    {
                        $headingID= $row['headingID'];
                        $headingName=$row['headingName'];
                        $subheadingName=$row['subheadingName'];
                        $outcomeID = $row['outcomeID'];
                        $outcomeName = $row['outcomeName'];
                        
                        if ($headingName!=$currentOutcomeHeading)
                            {
                               if($currentOutcomeHeading!='first'){echo '</div>'; /*close previous heading div*/}
                                
                                $currentOutcomeHeading = $headingName;
                             //   $countByLetterIndex=0;
                                echo '<h4 class="xxx outcomesBox outcomeHeading outcomeDiv" id="outcomeDiv'.$headingID.'"><span class="explode">&nbsp;+&nbsp;</span>'.$headingID.'. '.$headingName.'</h4>';
                                if ($subheadingName ==''){echo '<h5 class="outcomesBox outcomeSubheading">'.$subheadingName.'</h5>';}
                                else {echo '<h5 class="outcomesBox outcomeSubheading">'.$coursePrefix.': '.$subheadingName.'</h5>';}
                                echo '<div class="hidden outcomeHeadingDiv outcomeDiv'.$headingID.'">';
                            }
                        
                       echo '<input class="xxx outcomesBox mustHaveBox" title="Outcomes" type="checkbox" name="outcomesTaught[]" value="'.$outcomeID.'" /> <span class="xxx outcomesBox">'.$headingID.$outcomeName.'</span><br class="xxx outcomesBox" /><br />';
                               // $countByLetterIndex++;
                    }
                    echo '</div>'; /*close final heading div*/
               echo'<br /><br /> <input id="OTCTsubmit" type="submit" value="'.$submitButtonString.'" name="submit" disabled="disabled" />';
              echo '</div>';     
            mysqli_free_result($result);
            mysqli_close($dbc);
      
           
      ?> 
    
      
    
        
</form>
<br />

<?php   
     
   
        include('includes/footer.php');
             
      ?>