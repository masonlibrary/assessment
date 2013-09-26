<?php

include('control/connectionVars.php');
include('classes/InstructionSession.php');
include('classes/User.php');
require_once('control/startSession.php');

// Insert the page header
  $page_title = 'Reports';
  include('includes/header.php');
  ?>

<h1>Reports</h1>
<p>coming some fine day</p>
<br />
<br />


 

 
<?php 
        
     include("control/connection.php");
                                        
                                        
           
               
                $output="";
                //next one....
                $query="select * from allsessionsbylength";
                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');
                if(!$result){echo "this is an outrage: ".mysql_error()."\n";}


                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $output.="<tr><th>";
                    $output.= addslashes($row['length']);
                    $output.="</th><td>";
                    $output.= $row['count'];
                    $output.="</td></tr>";
            
                }
                
                
                
        mysqli_free_result($result);
        mysqli_close($dbc);


  
   ?>

 <div>
     <table id="sessionsByLength">
         <thead>
         <th></th><th>Count</th>
        </thead>
         <?php echo $output ?>
     </table>
     
     <div id="highchart2" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
 </div>

<?PHP
   
$jsOutput='';
  include('includes/reportsFooter.php');
?>



