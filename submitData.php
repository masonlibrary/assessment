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
//
// We don't actually handle the duplicate action anywhere as of right now.
// Only handle the "edit" action for updating rather than inserting. We should
// be able to safely default to inserting for all non-edit cases. -Webster
if(isset($_POST['action']) && $_POST['action'] == "edit") {
	if(isset($_POST['sesdID']) && $_POST['sesdID']) {
		$InstructionSession->updateSession($_POST['sesdID']);
	} else {
		die("Tried to perform an edit action with no session ID!");
	}
} else {
	$InstructionSession->insertSession();
}
$output.= "<br />";
$output.="<span class='immediateOutcomeEntry'><a id='immediateOutcomeEntry'class='ui-corner-all' href='enterCurrentOutcomes.php?session=".$InstructionSession->getSessionID()."'>Enter Outcomes Taught for these sessions</a></span><br /><br />";
$output.= "<h3>Original Session</h3>".$InstructionSession->toString();

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


