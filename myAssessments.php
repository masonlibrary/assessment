<?php
   
    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');
    
    // Insert the page header
  $page_title = 'My Assessments';
  include('includes/header.php');
  
  $thisUser=$_SESSION['thisUser'];
  
  ?>
  


    

        
    <?php
         if ($thisUser->isLibrarian)
                { 
                $_SESSION['currentLibrarianID']=$thisUser->getLibrarianID(); 
                $output=$thisUser->getMyAssessments($_SESSION['currentLibrarianID']);
                
              
                echo $output;
                }
   
  
  
     ?>
  
  
   
  
  <?php
  
  include('includes/reportsFooter.php');
  ?>
