<?php 
   
    // Start the session

    include('control/connectionVars.php');
    include('classes/User.php');
    require_once('control/startSession.php');  

  

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['userID'])) 
        {header("Location: login.php"); exit();}
        
   // Insert the page header
  $page_title = 'IL Summary Reports';
  
  include('includes/header.php');    
        
        
	if( @$_SESSION['userID'] ){ 
              
            
               if (!isset($_SESSION['thisUser']))
                   { //echo "<h3>Newly created User object</h3>";
                   $_SESSION['thisUser']= new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);}
             }
        ?>
<h2 id="introduction">Reports</h2>
<p>Please note: these reports are samples.  <br>
    TODO: add drop-down menus to allow user to select summary data by Semester or Year.
    <br>
    
</p><br>
<div id="adminReportsMenu" >
	<p>
        <ul>
            <li><a href="outcomesTaughtMap.php">Outcomes Map - Taught</a></li>
            <li><a href="outcomesAssessedMap.php">Outcomes Map - Assessed</a></li>
            <li><a href="assessmentSummary.php">Assessment Summary</a></li>
            <li><a href="aySessionSummary.php">Academic Year Session Summary</a></li>
            <li><a href="allSessionsByLibrarian.php">All Sessions By Librarian (test)</a></li>
            
        </ul>
            
            
        <br>
        <br>
        </p>
        
            
       
</div>
<?php

  
	
	include("includes/footer.php");
?>