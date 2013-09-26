<?php
   
    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');

	require_once('control/startSession.php');
    
    
    // Insert the page header
  $page_title = 'My Sessions';
  include('includes/header.php');
  if ($_SESSION['dialogText']!=''){include('includes/dialogDiv.php');}
  
  $thisUser=$_SESSION['thisUser'];
  
  ?>
  

    
<a href="#" class="test">Test: Chart filtered sessions by prefix</a> 
        
    <?php
         if ($thisUser->isLibrarian)
                { 
                $_SESSION['currentLibrarianID']=$thisUser->getLibrarianID(); 
                $output=$thisUser->getMySessions($_SESSION['currentLibrarianID']);
                
                
                echo $output;
                }
   
  
  
     ?>
  
  
    
  <div id="chartContainer" style="clear: both; margin-top: 40px;"> <div id="testChart"></div> </div>
  

  
  <?php
  
  include('includes/reportsFooter.php');
  ?>
