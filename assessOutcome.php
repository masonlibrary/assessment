<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Assess Outcomes Taught';
  include('includes/header.php');
  
  echo '<h3 class="pageTitle">'.$page_title.'</h3>';
 
  $currentSessions= array();
  
  
  
 $thisUser = $_SESSION['thisUser'];
 
 $currentSessions[0]= new InstructionSession();
 $currentSessions[0]->loadSession($_POST['assessID']);
 
// echo $thisSession->toString();
 
 echo $currentSessions[0]->getOutcomesToAssess();
 
 $_SESSION['currentSessions']=$currentSessions;

 include('includes/footer.php');
 
?>
