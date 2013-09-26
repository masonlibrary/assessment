<?php


include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

$currentSessions=$_SESSION['currentSessions'];

$sessionCount = count($currentSessions);
$outcomesTaught = $_POST['outcomesTaught'];

for ($x=0; $x< $sessionCount; $x++)
    {
        $output=$currentSessions[$x]->setAndInsertOutcomesTaught($outcomesTaught);
        echo $output;
    }

    $_SESSION['dialogText']="Success!";
    $_SESSION['dialogTitle']='Result';
    
    $returnTo='';
    if (isset($_SESSION['returnTo']) and $_SESSION['returnTo'] !=''  )
       { $returnTo=$_SESSION['returnTo'];  
         unset($_SESSION['returnTo']);
       }
    else { $returnTo = 'enterOutcomes.php';}


    header("Location: $returnTo"); 
   // header('Location: enterOutcomes.php');
    
    
?>
