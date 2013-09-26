<?php 
   
    // Start the session

    include('control/connectionVars.php');
    include('classes/User.php');
    require_once('control/startSession.php');   

  

  // Make sure the user is logged in before going any further.
  if (!isset($_SESSION['userID'])) 
        {header("Location: login.php"); exit();}
        
   // Insert the page header
  $page_title = 'Home';
  
  include('includes/header.php');    
        
        
	if( @$_SESSION['userID'] ){ 
                //echo"<h3>Nice work. You're logged in now.</h3>";
               //echo"you have role of ". $_SESSION['roleName']; 
            
               if (!isset($_SESSION['thisUser']))
                   { //echo "<h3>Newly created User object</h3>";
                   $_SESSION['thisUser']= new User($_SESSION['userID'], $_SESSION['userName'], $_SESSION['roleName']);}
              //  else{ echo"<h3>User Object already set</h3>";}
             //  echo 'userSession object???'.$_SESSION['thisUser']->toString().' delete this line when finished debugging!';
                }
        ?>
<h2 id="introduction">Welcome!</h2>

<div id="welcome" class="ui-widget" >
	<p>Welcome the the InfoLit tracking tool. <br />Please use the menu at the top of the page. <br /><br />
        The above menu can be opened and closed by clicking on the tab labeled &quot;Menu&quot;.</p>
</div>
<?php

  
	
	include("includes/footer.php");
?>