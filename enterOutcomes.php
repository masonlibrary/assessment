<?php

include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Enter Outcomes Taught';
  include('includes/header.php');
  
  if(isset($_SESSION['currentSessions']))
  {
          $currentSessions=$_SESSION['currentSessions'];
  }
  else
  {
      $currentSessions= array();
  }

  

?>
<form id="outcomesTaughtForm" method="post" action="enterCurrentOutcomes.php">
<div id="outcomesTaughtDiv">
   

		
        <?php 
        
            $thisUser = $_SESSION['thisUser'];
            
            if ($thisUser->isLibrarian)
                { 
                $_SESSION['currentLibrarianID']=$thisUser->getLibrarianID(); 
                $output=$thisUser->getNeedOutcomes($_SESSION['currentLibrarianID']);
                
                $librarianID=$_SESSION['currentLibrarianID']; 
                echo $output;
                }
            
            /*
            if($_SESSION['roleName']=='admin' ){include('includes/selectLibrarian.php');}
            if($_SESSION['roleName']=='power' ){include('includes/selectLibrarianPre.php');}
            if($_SESSION['roleName']=='user' ){include('includes/selectLibrarianNo.php');}
            */
         ?>
            
      <?php //include('includes/selectSessionsNoOutcomes.php');?> 
    
      <?php   
     
      
       echo '<br /><br /><input id="chooseCoursesOTCT" type="submit" name="submitOTCT" value="Add outcomes to selected sessions" disabled="disabled" /><br />';
      /* echo "<br /><br />if you're interested, there are ".count($currentSessions)." other sessions still available for you to work with!<br /><br />"  ; */
       
       ?>
    </div></form>
    
        <?php 
//                include("control/connection.php");
//                                        
//                                        
//                                           
//                $query = "select s.sesdcrspID as ID, c.crspName as Name, count(s.sesdcrspID) as Count ".
//                        "from sessiondesc s, courseprefix c ".
//                        "where sesdlibmID= $librarianID and ".
//                        "sesdOutcomeDone='no' and s.sesdcrspID=c.crspID group by s.sesdcrspID";
//                
//                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');
//                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}
//
//                
//                
//                while ( $row = mysqli_fetch_assoc( $result) )
//                {
//                    $id= $row['ID'];
//                    $prefixName = $row['Name'];
//                    $count = $row['Count'];
//                    echo '<span class="yyy">There are '.$count.' '.$prefixName.' courses that require outcomes taught.<br />';
//                }
//
//
//        mysqli_free_result($result);
//        mysqli_close($dbc);
//             
//             
//          echo "<br /><br />";   
             
             
      ?>
    
        


<br />

<?php 
       if ($_SESSION['dialogText']!=''){include('includes/dialogDiv.php');}
        include('includes/footer.php');
?>