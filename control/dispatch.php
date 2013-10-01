<?php

include('connectionVars.php');
include('../classes/InstructionSession.php');
require_once('startSession.php');



$gotoNext = '../'.$_GET['gotoNext'].'.php';
$_SESSION['returnTo']=$_GET['returnTo'].'.php';

//assessment/enterCurrentOutcomes.php?gotoNext=enterCurrentOutcomes&returnTo=enterSession >
        
        
        
        
        header("Location: $gotoNext"); 

?>
