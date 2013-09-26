<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Submit Assessment';
  include('includes/header.php');
  
echo"<h1>Submit assessments</h1>";
  $currentSessions=$_SESSION['currentSessions'];
  
  
 $thisUser = $_SESSION['thisUser'];
 
 
 $assessedCount=$_POST['assessedCount'];
 echo $assessedCount;
 echo $currentSessions[0]->toString();
 
 $met=$_POST['Met'];
 $partial=$_POST['Partial'];
 $notmet=$_POST['NotMet'];
 $didNotAssess=$_POST['otctDidNotAssess'];
 $outcomesTaughtIDS=$_POST['otctIDS'];
 

 $MET=array();
 $PARTIAL=array();
 $NOTMET=array();
 $NOTASSESSED=array();
 $assessed=array();
echo "<br />";

//break apart the otctID and count data from the POST['met, partial, notmet'] arrays. 
 for ($x=0;$x<$assessedCount;$x++)
     {
     
      
       $a= explode(" ",$met[$x]);
       $MET[$a[0]]=$a[1];
       
       $b=explode(" ",$partial[$x]);
       $PARTIAL[$b[0]]=$b[1];
       
       $c=explode(" ",$notmet[$x]);
       $NOTMET[$c[0]]=$c[1];  
      
       $d=explode(" ", $didNotAssess[$x]);
       $NOTASSESSED[$d[0]]=$d[1];
     }
     
   //reorganize this data into a new array $assessed containing this data: ([row#], [otctID], [MET count], [Partial count], [NotMet count])  
   for ($x=0; $x<$assessedCount; $x++)
    {
     $assessed[$x]=array("otctID"=>$outcomesTaughtIDS[$x], "NotAssessed"=>$NOTASSESSED[$outcomesTaughtIDS[$x]], "Met"=>$MET[$outcomesTaughtIDS[$x]], "Partial"=>$PARTIAL[$outcomesTaughtIDS[$x]], "NotMet"=>$NOTMET[$outcomesTaughtIDS[$x]]);       
    }
  
     foreach($assessed as $row)
         {
         echo "ID:".$row['otctID']."  Met:".$row['Met']."  Partial:".$row['Partial']."  NotMet:".$row['NotMet']."<br />";
         }
     
     $output= $currentSessions[0]->setAndInsertOutcomesAssessed($assessed);
    
     header('Location: assessOutcomes.php');
     
     
     
?>
