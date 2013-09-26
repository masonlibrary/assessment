<?php

    include('control/connectionVars.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');    
    
    
  
 // $thisUser=$_SESSION['thisUser'];
  
  
    $inID = $_POST['inID'];
  
  
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the goshdarned database');
            
             $query ="delete from outcomesassessed where otcaotctID in (select otctID from outcomestaught where otctsesdID = $inID)";
                mysqli_query($dbc, $query) or die('This is an outrage- deleteSession.php:    '.$query);
                $output='Assessments deleted <br />';
               
            $query = "delete from outcomestaught where otctsesdID = $inID";
             mysqli_query($dbc, $query) or die('This is an outrage- deleteSession.php:    '.$query);
             $output.='Outcomes deleted <br />';
             
             
             $query = "delete from resourcesintroduced where rsrisesdID = $inID";
                mysqli_query($dbc, $query) or die('This is an outrage- deleteSession.php:    '.$query);
                $output.='Resources introduced deleted <br />';
             
             
            $query = "delete from sessionnotes where sesnsesdID = $inID";
                mysqli_query($dbc, $query) or die('This is an outrage- deleteSession.php:    '.$query);
                $output.='Session notes deleted <br />';
               
               
             
            $query = "delete from sessiondesc where sesdID = $inID";
                mysqli_query($dbc, $query) or die('This is an outrage- deleteSession.php:    '.$query);
                $output.='Session deleted';
               
               
                $_SESSION['dialogText']=$output;
                $_SESSION['dialogTitle']="Result";
                
                header('Location: mySessions.php');
             
             
             
             
  ?>