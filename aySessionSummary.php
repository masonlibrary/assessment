<?php

    include('control/connectionVars.php');
    include_once('control/functions.php');
    include('classes/InstructionSession.php');
    include('classes/User.php');
    require_once('control/startSession.php');

    // Insert the page header
  $page_title = 'Academic Year Session Summary';
  include('includes/header.php');

 // $thisUser=$_SESSION['thisUser'];

	(isset($_GET['semester']) && $_GET['semester'] != "") ? $semester = $_GET['semester'] : $semester = "any";
	(isset($_GET['year']) && $_GET['year'] != "") ? $year = $_GET['year'] : $year = "any";

	if ($year == 'any') {
		$desiredAY = 'AY 2012-' . date('Y');
	}	else {
		$desiredAY = 'AY ' . ($year-1) . '-' . ($year);
	}
	
	$AYQueryString = inAcademicYear($desiredAY);

?>

<h2>Academic Year - Session Summary for <?php echo $desiredAY ?></h2>

<?php
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                    or die('Error connecting to the stupid database');

            //$query ="call aySessionSummary('FY 2012-2013')";

            $query='select * from ('.
                    'select '.
                    '(select count(sesdID) from lowerlevel where sesdDate '.$AYQueryString.') as Count, '.
                    '"Lower Level" as Description, '.
                    'round((select count(sesdID) from lowerlevel where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    'select '.
                    '(select count(sesdID) from upperlevel where sesdDate '.$AYQueryString.') as Count, '.
                    '"Upper Level" as Description, '.
                    'round((select count(sesdID) from upperlevel where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    'select '.
                    '(select count(sesdID) from itw where sesdDate '.$AYQueryString.') as Count, '.
                    '"ITW" as Description, '.
                    'round((select count(sesdID) from itw where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    ' select '.
                    '(select count(sesdID) from iql where sesdDate '.$AYQueryString.') as Count, '.
                    '"IQL" as Description, '.
                    'round((select count(sesdID) from iql where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    'select '.
                    '(select count(sesdID) from hlsc where sesdDate '.$AYQueryString.') as Count, '.
                    '"HLSC" as Description, '.
                    ' round((select count(sesdID) from hlsc where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    'select '.
                    '(select count(sesdID) from ihcomm171 where sesdDate '.$AYQueryString.') as Count, '.
                    '"IHCOMM171" as Description, '.
                    'round((select count(sesdID) from ihcomm171 where sesdDate '.$AYQueryString.')/(select count(*) from sessiondesc where sesdDate '.$AYQueryString.')*100, 2) as percent '.
                    'from sessiondesc group by Description '.
                    'UNION '.
                    'select count(*) as Count, '.
                    '"Total sessions for" as Description, '.
                    '"'.$desiredAY.'" as percent '.
                    ' from sessiondesc where sesdDate '.$AYQueryString.' '.

                    ') as tt';







             // 9 columns.
             $output = '<div class="dataTables_filter">
								<label for="dataTables_filter">Filter</label><input type="text" id="dataTables_filter" class="ui-widget" />
								<label for="dataTables_invert">Invert</label><input type="checkbox" id="dataTables_invert" />
							</div>
							<table id="aySS"><thead id="aySSHead"><tr>
							<th>Count</th>
							<th>Description</th>
							<th>Percent</th></thead>';

             $result = mysqli_query($dbc, $query) or die('This is an outrage-in aySS.    '.$query);
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n $query";}


                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $count=$row['Count'];
                    $description=$row['Description'];
                    $percent=$row['percent'];

                    $output.="<tr class='aySS'>";
                    switch($description)
                        {
                        case 'Upper Level':
                            $output.='<td class="aySS byLevel byLevelCount count upperLevelCount">'.$count.'</td>'.
                                '<td class="aySS byLevel byLevelDescription upperLevel level">'.$description.'</td>'.
                                '<td class ="aySS byLevel percent upperLevelPercent">'.$percent.'</td>';
                        break;

                       case 'Lower Level':
                            $output.='<td class="aySS byLevel byLevelCount count lowerLevelCount">'.$count.'</td>'.
                               '<td class="aySS byLevel byLevelDesciption upperLevel level">'.$description.'</td>'.
                               '<td class="aySS byLevel percent lowerLevelPercent">'.$percent.'</td>';
                        break;

                      case 'ITW':
                            $output.='<td class="aySS byPrefix byPrefixCount count ITWCount">'.$count.'</td>'.
                               '<td class="aySS byPrefix byPrefixDesciption description ITWdescription">'.$description.'</td>'.
                               '<td class="aySS byPrefix percent ITWpercent">'.$percent.'</td>';
                        break;

                    case 'IQL':
                            $output.='<td class="aySS byPrefix byPrefixCount count IQLCount">'.$count.'</td>'.
                               '<td class="aySS byPrefix byPrefixDesciption description IQLdescription">'.$description.'</td>'.
                               '<td class="aySS percent byPrefix IQLpercent">'.$percent.'</td>';
                        break;

                    case 'HLSC':
                            $output.='<td class="aySS byPrefix byPrefixCount count HLSCCount">'.$count.'</td>'.
                               '<td class="aySS byPrefix byPrefixDesciption description HLSCdescription">'.$description.'</td>'.
                               '<td class="aySS percent byPrefix HLSCpercent">'.$percent.'</td>';
                        break;

                    case 'IHCOMM171':
                            $output.='<td class="aySS byPrefix byPrefixCount count IHCOMM171Count">'.$count.'</td>'.
                               '<td class="aySS byPrefix byPrefixDesciption description IHCOMM171description">'.$description.'</td>'.
                               '<td class="aySS percent byPrefix IHCOMM171percent">'.$percent.'</td>';
                        break;


                    case 'Total sessions for':
                            $output.='<td class="aySS byTotal totalCount count totalSessionsCount">'.$count.'</td>'.
                               '<td class="aySS byTotal byTotalDesciption totalSessions ">'.$description.'</td>'.
                               '<td class="aySS byLevel fyValue">'.$percent.'</td>';
                        break;
                        }

                        $output.='</tr>';



                }

                $output.='</tbody></table>';
                echo $output;


?>
<br /> <br />
<a href="#" class="chartUpperLower">Chart Upper/Lower level sessions</a> <br />
<br />
<a href="#" class="chartITWetc">Chart ITW/IQL/HLSC/IHCOMM171</a> <br />

        <div id="chartContainer" style="clear: both; margin-top: 40px;">
            <div id="upperLowerChart"></div>
            <div id="ITWetcChart"></div>
        </div>

<?php
$jsOutput .= '
	var oTable = $("#aySS").dataTable({
		"sDom": "T<\'clear\'>lrtip",
		"bSort": false,
		"bPaginate": false,
		"oTableTools": { "sSwfPath":"swf/copy_csv_xls_pdf.swf" }
	});';

  include('includes/reportsFooter.php');
?>
