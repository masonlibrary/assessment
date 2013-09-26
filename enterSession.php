<?php


   require_once('control/startsession.php');
   require_once("control/connection.php");
// Insert the page header
  $page_title = 'Enter Session Data';
  include('includes/header.php');
  
  if ($_SESSION['dialogText']!=''){include('includes/dialogDiv.php');}
   
  ?>

   
<form id="assessmentForm" method="post" action="submitData.php">
		
        <?php 
            if($_SESSION['roleName']=='admin' ){include('includes/selectLibrarian.php');}
            if($_SESSION['roleName']=='power' ){include('includes/selectLibrarianPre.php');}
            if($_SESSION['roleName']=='user' ){include('includes/selectLibrarianNo.php');}
            
            ?>
        <?php include('includes/selectCourse.php');?> <!-- All the same except ILSession#  -->
        <?php include('includes/selectFaculty.php');?><!-- All the same -->
        <h1>test</h1>
        <?php include('includes/selectLocation.php');?><!-- possibly different --> 
        <h1>test</h1>
        <?php include('includes/selectDate.php');?>
        <?php include('includes/selectLength.php');?>
        <?php include('includes/selectNumber.php');?>
        
        
        <?php include('includes/selectResources.php');?>
        <?php include('includes/selectNote.php');?>
        <?php include('includes/selectSubmitButton.php');?>

        
</form>
<br />

<?php 
 
  include('includes/footer.php');
?>
