<?php

include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Assess Outcomes Taught';
  include('includes/header.php');
  
  
  echo '<h2>'.$page_title.'</h2>';
       /*TODO: this is an annoying workaround that prevents the focus to filter input from breaking (can't click or enter */
        echo'<span>&nbsp;</span>';
?>

<div id="message" class="hidden"><p>Assessment successfully updated!</p></div>

		
        <?php 
        
            $thisUser = $_SESSION['thisUser'];
            
            if ($thisUser->isLibrarian)
                { 
                $_SESSION['currentLibrarianID']=$thisUser->getLibrarianID(); 
								
                echo '<div id="assessOutcomesDiv">';
                echo $thisUser->getNeedAssessed($_SESSION['currentLibrarianID']);
                echo '</div>';
                }
            
         ?>
            
<br />

<?php 
        
        include('includes/footer.php');
?>