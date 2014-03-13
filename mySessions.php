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

		$jsOutput .= 'var oTable = $("#mySessions").dataTable({
				"sDom": "T<\'clear\'>lfrtip",
				"iDisplayLength": -1,
				"aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
				"aoColumns": [
					null,
					null,
					null,
					null,
					null,
					{"iDataSort": 6},
					{"bVisible": false},
					null,
					null,
					null
				],
				"oTableTools": {
					"sSwfPath": "swf/copy_csv_xls_pdf.swf",
					"aButtons": [
						{
							"sExtends": "csv",
							"sButtonText": "Excel/CSV",
							"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
						},{
							"sExtends": "pdf",
							"sButtonText": "PDF",
							"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
						},{
							"sExtends": "print",
							"sButtonText": "Print",
							"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
						},{
							"sExtends": "copy",
							"sButtonText": "Copy",
							"mColumns": [ 0, 1, 2, 3, 4, 5, 7, 8 ]
						}
					]
				}
			});';

		include('includes/reportsFooter.php');
  ?>
