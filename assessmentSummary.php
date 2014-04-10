<?php

    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'Assessment Summary';
  include('includes/header.php');

	(isset($_GET['semester']) && $_GET['semester'] != "") ? $semester = $_GET['semester'] : $semester = "any";
	(isset($_GET['year']) && $_GET['year'] != "") ? $year = $_GET['year'] : $year = "any";

	if ($year == 'any') {
		$reportRange = 'AY 2012-' . date('Y');
	}	else {
		$reportRange = 'AY ' . ($year-1) . '-' . ($year);
	}
 // $thisUser=$_SESSION['thisUser'];

  ?>

<h2>Assessment Summary for <?php echo $reportRange; ?></h2>

<br />

<?php
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');



            /* ******************************************************************************
             * This portion is for set up:
             *  Determines all existing headings and outcomes in a '1' and '1a' format
             * each are stored in numerically indexed arrays.
             * Later those array values will be used as keys/indices to
             * other arrays ($Met, $partial, $NotMet containing assessment data.
             *
             * For instance: 1a will be the key in $Met[]. If 23 studenets
             * met the outcome '1a' then:
             *
             *               $Met['1a'] = 23;
             *
             ********************************************************************************* */

            $query='select * from outcomeabbrview order by headingID;';


            $result = mysqli_query($dbc, $query) or die('dang it to heck!- query issues.'.mysqli_error($dbc).$query);
        if(!$result){echo "this is an outrage: ".mysqli_error($dbc).$query."\n";}

            // set up variables
                $uniqueOutcomes = array();
                $uniqueHeadings = array();
                $Met = array();
                $Partial = array();
                $NotMet = array();
                $TotalsMet = array();
                $TotalsPartial = array();
                $TotalsNotMet = array();

                $outcomeIndex = 0;
                $headingIndex = -1;
                $currentOutcomeHeading='first';
                $newHeading = '';

               while ( $row = mysqli_fetch_assoc( $result) )
                   {
                        $uniqueOutcomes[$outcomeIndex] = $row['abbr'];
                        $newHeading = (string) $row['headingID'];
                        $outcomeName = $row['OutcomeDetail'];

                        //Keep track of each outcome heading
                        if ($newHeading != $currentOutcomeHeading)
                            {
                                $headingIndex++;
                                $uniqueHeadings[$headingIndex]=$newHeading;
                                $TotalsMet[$uniqueHeadings[$headingIndex]]=0;
                                $TotalsPartial[$uniqueHeadings[$headingIndex]]=0;
                                $TotalsNotMet[$uniqueHeadings[$headingIndex]]=0;
                                $currentOutcomeHeading=$newHeading;
                            }


                        $Met[$uniqueOutcomes[$outcomeIndex]]=0;
                        $Partial[$uniqueOutcomes[$outcomeIndex]]=0;
                        $NotMet[$uniqueOutcomes[$outcomeIndex]]=0;


                        $outcomeIndex++;

                   }

                   /*
                    * Loop through outcomesassessedview and

                    */

                   $MetResults = 0;
                   $PartialResults = 0;
                   $NotMetResults = 0;
                   $rowTotal = 0;
                   $lastHeadingID ='reset';
                    $output='';
										$output.='<div class="dataTables_filter">
												<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
												<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
											</div>';
                    $output.='<table id="assessmentSummary">';
                    $output.='<thead><tr><th>Heading</th><th>Outcome</th><th>Met</th><th>Partially Met</th><th>Not Met</th><th>Total</th></tr></thead>';
                    $output.='<tbody>';
                   foreach( $uniqueOutcomes as $value)
                       {
                       // $value = the 1a, 1b, 1c etc...  $key is the numeric index
                       $currentABBR = $value;


                       $currentHeadingID = substr($currentABBR, 0, 1);

                        if ($lastHeadingID =='reset'){$lastHeadingID = $currentHeadingID; }


                        if ($lastHeadingID != $currentHeadingID)
                            {
                                $totalTotal = 0;

                                $totalTotal = intval($TotalsMet[$lastHeadingID]) +intval($TotalsPartial[$lastHeadingID])+intval($TotalsNotMet[$lastHeadingID]);
                                // do TOTALS Row $TotalsMet[$lastHeadingID], $TotalsPartial[$lastHeadingID], $Totals
                                $output.='<tr class="summary">'.
                                        '<td>Outcome '.$lastHeadingID.'</td>'.
                                        '<td>Total</td>'.
                                        '<td>'.$TotalsMet[$lastHeadingID].'</td>'.
                                        '<td>'.$TotalsPartial[$lastHeadingID].'</td>'.
                                        '<td>'.$TotalsNotMet[$lastHeadingID].'</td>'.
                                        '<td>'.$totalTotal.'</td>'.
                                        '</tr>';
                                // set $lastHeadingID = $currentHeadingID
                                    $lastHeadingID = $currentHeadingID;


                            }

                       $query ="select sum(Met) as Met, sum(Partial) as Partial, sum(NotMet) as NotMet ".
                               "from outcomesassessedview where date ".inAcademicYear($reportRange)." and abbr = '$currentABBR'";

                        $result = mysqli_query($dbc, $query) or die('dang it to heck!- query issues.'.mysqli_error($dbc).$query);
                        if(!$result){echo "this is an outrage: ".mysqli_error($dbc).$query."\n";}
                        while ( $row = mysqli_fetch_assoc( $result) )
                            {


                              $MetResults = intval ($row['Met']) ;
                              $PartialResults = intval ($row['Partial']) ;
                              $NotMetResultsMetResults = intval ($row['NotMet']) ;

                              $Met[$currentABBR] += $MetResults;
                              $Partial[$currentABBR] += $PartialResults;
                              $NotMet[$currentABBR] += $NotMetResults;

                              $rowTotal = $MetResults+$PartialResults+$NotMetResults;

                              $TotalsMet[$currentHeadingID]+=$MetResults;
                              $TotalsPartial[$currentHeadingID]+=$PartialResults;
                              $TotalsNotMet[$currentHeadingID]+=$NotMetResults;



                            }
                        // do  Row , $Totals
                                $output.='<tr>'.
                                        '<td>Outcome '.$lastHeadingID.'</td>'.
                                        '<td>'.$currentABBR.'</td>'.
                                        '<td>'.$MetResults.'</td>'.
                                        '<td>'.$PartialResults.'</td>'.
                                        '<td>'.$NotMetResults.'</td>'.
                                        '<td>'.$rowTotal.'</td>'.
                                        '</tr>';

                       }

                       // do last TOTALS Row $TotalsMet[$lastHeadingID], $TotalsPartial[$lastHeadingID], $Totals
                       $totalTotal = 0;

                        $totalTotal = intval($TotalsMet[$lastHeadingID]) +intval($TotalsPartial[$lastHeadingID])+intval($TotalsNotMet[$lastHeadingID]);
                        $output.='<tr class="summary">'.
                                '<td>Outcome '.$lastHeadingID.'</td>'.
                                '<td>Total</td>'.
                                '<td>'.$TotalsMet[$lastHeadingID].'</td>'.
                                '<td>'.$TotalsPartial[$lastHeadingID].'</td>'.
                                '<td>'.$TotalsNotMet[$lastHeadingID].'</td>'.
                                '<td>'.$totalTotal.'</td>'.
                                '</tr>';

                        //wrap up table
                       $output.='</tbody></table>';









                echo $output;

$jsOutput .= '
	var oTable = $("#assessmentSummary").dataTable({
		"sDom": "T<\'clear\'>lrtip",
		"bPaginate": false,
		"oTableTools": {
			"sSwfPath":"swf/copy_csv_xls_pdf.swf",
			"aButtons":[
				{
					"sExtends": "csv",
					"sButtonText": "Excel/CSV",
					"mColumns": [0, 1, 2, 3, 4, 5]
				}, {
					"sExtends": "pdf",
					"sButtonText": "PDF",
					"mColumns": [   1, 2, 3, 4, 5]
				}, {
					"sExtends": "print",
					"sButtonText": "Print",
					"mColumns": [0, 1, 2, 3, 4, 5]
				}, {
					"sExtends": "copy",
					"sButtonText": "Copy",
					"mColumns": [0, 1, 2, 3, 4, 5]
				}
			]
		}
	}).rowGrouping({
		bExpandableGrouping: true /*,
		asExpandedGroups: []        */
	});';

  include('includes/reportsFooter.php');
  ?>
