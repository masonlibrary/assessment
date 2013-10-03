<?php

    include('control/connectionVars.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');   
    // Insert the page header
  $page_title = 'Dana test';
  include('includes/header.php');
  
 // $thisUser=$_SESSION['thisUser'];
  
  ?>
  
<h2>Test</h2>

<?php
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');
            
   
            
              
            $query ="select * from iloutcomesmapview";
            
           
            
            $result = mysqli_query($dbc, $query) or die('dang it to heck!- query issues.'.mysqli_error().$query);
        if(!$result){echo "this is an outrage: ".mysqli_error().$query."\n";}
                     
               print_r($result);
               echo "<br /><br />";
               
               
                $xxx = $result->field_count;
                $yyy = $result->num_rows;
                
                 $output='';
                 $output.="<br />Query: $query";
                 $output.="<br />Field count: $xxx <br />Number of rows: $yyy";
                 $output.="<br /><br />";
                 
                $i=1;
                while($info=mysqli_fetch_field($result))
                    {
                    $output.="<h2> Column $i</h2>";
                    $output.="<br >Field name: $info->name";
                    $output.="<br >Source table: $info->table";
                    $i++;
                    $output.="<br />";
                    $output.="----------";
                    $output.="<br /><br />";
                    
                    }
                
                
                
                
              
                
            
                echo $output;


?>


  
  <?php
  
  include('includes/reportsFooter.php');
  ?>
<?php

