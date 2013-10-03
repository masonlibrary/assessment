<?php

include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Assess Outcomes Taught';
  include('includes/header.php');
  
   if ($_SESSION['dialogText']!=''){include('includes/dialogDiv.php');}
   
  if(isset($_SESSION['currentSessions']))
  {
          $currentSessions=$_SESSION['currentSessions'];
  }
  else
  {
      $currentSessions= array();
  }

  

?>

<div id="message" class="hidden"><p>Assessment successfully updated!</p></div>

		
        <?php 
        
            $thisUser = $_SESSION['thisUser'];
            
            if ($thisUser->isLibrarian)
                { 
                $_SESSION['currentLibrarianID']=$thisUser->getLibrarianID(); 
                $output=$thisUser->getNeedAssessed($_SESSION['currentLibrarianID']);
                
                $librarianID=$_SESSION['currentLibrarianID']; 
                echo '<div id="assessOutcomesDiv">';
                echo $output;
                echo '</div>';
                }
            
            /*
            if($_SESSION['roleName']=='admin' ){include('includes/selectLibrarian.php');}
            if($_SESSION['roleName']=='power' ){include('includes/selectLibrarianPre.php');}
            if($_SESSION['roleName']=='user' ){include('includes/selectLibrarianNo.php');}
            */
         ?>
            
      <?php //include('includes/selectSessionsNoOutcomes.php');?> 
    
      <?php   
     
      
      
//       echo "<br /><br />if you're interested, there are ".count($currentSessions)." other sessions still available for you to work with!<br /><br />"  ; 
//        
//                include("control/connection.php");
//                                        
//                                        
//                                           
//                $query = "select s.sesdcrspID as ID, c.crspName as Name, count(s.sesdcrspID) as Count ".
//                        "from sessionDesc s, courseprefix c ".
//                        "where sesdlibmID= $librarianID and ".
//                        "sesdOutcomeDone='no' and s.sesdcrspID=c.crspID group by s.sesdcrspID";
//                
//                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');
//                if(!$result){echo "this is an outrage: ".mysqli_error()."\n";}
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
        
        include('includes/footer.php');
?>