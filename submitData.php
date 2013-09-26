<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php'); 
 
 // TODO destroy old POST data so that re-submit can't occur. 
// Insert the page header
 // $page_title = 'Home';
 // include('includes/header.php');
  

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['userID'])) 
        {header("Location: login.php"); exit();}
        
       
//read all form data from POST and put into session variables. 
//create new InstructionSession and populate with POST data (do both at same time?)
$userName=$_SESSION['userName'];
$InstructionSession= new InstructionSession($userName);

$output='';

//$output.= 
$InstructionSession->doPost($_POST);
//$output.= '<p>'.
$InstructionSession->insertSession();
$output.= "<br />";
$output.="<span class='immediateOutcomeEntry'><a id='immediateOutcomeEntry'class='ui-corner-all' href='/assessment/control/dispatch.php?gotoNext=enterCurrentOutcomes&returnTo=enterSession' >Enter Outcomes Taught for these sessions</a></span><br /><br />";
$output.= "<h3>Original Session</h3>".$InstructionSession->toString()."<hr /><br /><br />";


$currentSessions = array();
//first number in this array is the number of session copies held by this array.
    $currentSessions[0]=$InstructionSession;
   
    
if ( isset($_POST['makeCopies']) && $_POST['makeCopies']=='on'  )
    { 
    $numberOfCopies = intval($_POST['numberOfCopies']);
    
    for ($x=1; $x<=$numberOfCopies; $x++)
        {
         $currentSessions[$x] = new InstructionSession($userName);
         //$output.= 
         $currentSessions[$x]->doPost($_POST, strval($x));
            $output.= "<h4>Copy # $x  </h4>";
            $currentSessions[$x]->insertSession();
//            $output.= "<br />";
//            $output.= "user name: $userName <br />";
//            $output.= "user id: ".$_SESSION['userID']."<br />";
//            $output.= "<h1>Copy #$x; toString</h1>";
            $output.= $currentSessions[$x]->toString();
            $output.= "<hr /><br /><br />";
        }
    
    }
else {$output.= "No copies. <br />";}

 
    
    

//echo "if you're interested, there are ".count($currentSessions)." sessions still available for you to work with!";

$_SESSION['currentSessions']=$currentSessions;
$_SESSION['currentSessionCount']=count($currentSessions);


$_SESSION['dialogText']=$output;
$_SESSION['dialogTitle']="Session Result";

$returnTo='';
if (isset($_SESSION['returnTo']) and $_SESSION['returnTo'] !=''  )
    { $returnTo=$_SESSION['returnTo'];  
      unset($_SESSION['returnTo']);
    }
else { $returnTo = 'enterSession.php';}


header("Location: $returnTo"); 
exit();
//include('includes/footer.php');
?>


