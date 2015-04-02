<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Assess Outcomes Taught';
  include('includes/header.php');
  
  echo '<h3 class="pageTitle">'.$page_title.'</h3>';

	$id = 0;
	if (isset($_POST['assessID'])) {
		$id = $_POST['assessID'];
	} else if (isset($_GET['session'])) {
		$id = $_GET['session'];
	} else {
		die('no id!');
	}
 
 $currentSession= new InstructionSession();
 $currentSession->loadSession($id);
 
 echo $currentSession->getOutcomesToAssess();

 $jsOutput .= 'assessmentDropdown();';
 include('includes/footer.php');
 
?>
