<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Submit Assessment';
  include('includes/header.php');
  
echo"<h1>Submit assessments</h1>";
 
 $assessedCount=$_POST['assessedCount'];
 echo $assessedCount;
 
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
     
		$a=explode(" ",$met[$x]);
		$b=explode(" ",$partial[$x]);
		$c=explode(" ",$notmet[$x]);
		$d=explode(" ", $didNotAssess[$x]);
		
		// This code dies on a blank entry in an array. Here, we force it to 0
		// (no students in this category) to ensure it doesn't. -Webster
		if(!$a[0]) { $a[0] = $outcomesTaughtIDS[$x]; $a[1] = 0; }
		if(!$b[0]) { $b[0] = $outcomesTaughtIDS[$x]; $b[1] = 0; }
		if(!$c[0]) { $c[0] = $outcomesTaughtIDS[$x]; $c[1] = 0; }
		if(!$d[0]) { $d[0] = $outcomesTaughtIDS[$x]; $d[1] = 0; }
		
       $MET[$a[0]]=$a[1];
       $PARTIAL[$b[0]]=$b[1];
       $NOTMET[$c[0]]=$c[1];
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
     
		 $currentSession = new InstructionSession();
		 $currentSession->loadSession($_POST['sessionID']);
     $output = $currentSession->setAndInsertOutcomesAssessed($assessed);
		 echo $output;
    
     header('Location: assessOutcomes.php');
     
     
?>
