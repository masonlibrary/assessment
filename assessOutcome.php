<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Assess Outcomes Taught';
  include('includes/header.php');
  
  echo '<h3 class="pageTitle">'.$page_title.'</h3>';
 
 $currentSession= new InstructionSession();
 $currentSession->loadSession($_POST['assessID']);
 
 echo $currentSession->getOutcomesToAssess();

 $jsOutput .= 'assessmentDropdown();';
 include('includes/footer.php');
 
?>
