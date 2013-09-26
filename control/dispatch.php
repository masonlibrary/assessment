<?php

include('connectionVars.php');
include('../classesInstructionSession.php');
require_once('startSession.php');



$gotoNext = '../'.$_GET['gotoNext'].'.php';
$_SESSION['returnTo']=$_GET['returnTo'].'.php';

//assessment/enterCurrentOutcomes.php?gotoNext=enterCurrentOutcomes&returnTo=enterSession >
        
        
        
        
        header("Location: $gotoNext"); 

?>
