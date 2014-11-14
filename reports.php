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
 
 <div id="highchart1" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
 <br /><br /><br />
 <div id="highchart2" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
 

 
<?php 
        
     include("control/connection.php");
                                        
                                        
                                           
                $query = "select * from allsessionsbylibrarian";
                
                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                $dataString="data=[";
                
                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $name= addslashes($row['name']);
                    $count = $row['count'];
                    $dataString.="['$name', $count],";
                }
                $dataString=trim($dataString,',');
                $dataString.="];";
        
                //next one....
                $query="select * from allsessionsbylength";
                $result = mysqli_query($dbc, $query) or die('This is an outrage- query issues.');
                if(!$result){echo "this is an outrage: ".mysqli_error($dbc)."\n";}

                $dataString1="data1=[";
                
                while ( $row = mysqli_fetch_assoc( $result) )
                {
                    $length= addslashes($row['length']);
                    $count = $row['count'];
                    $dataString1.="['$length', $count],";
                }
                $dataString1=trim($dataString1,',');
                $dataString1.="];";
                
                
                
                
                
        mysqli_free_result($result);
        mysqli_close($dbc);


   

//attempt to use HEREDOC
   $jsOutput = <<<EOD
$(function () {
var hChart;
var hChart1;
$(document).ready(function(){
$dataString
$dataString1
hChart= new Highcharts.Chart({
chart: {
renderTo: 'highchart1',
plotBackgrountColor: null,
plotBorderWidth: null,
plotShadow: false
},
credits:{
enabled: false
},
title:{
text: '# of session entries by librarian'
},
tooltip: {
formatter: function() {
return '<b>'+this.point.name+'</b>: '+this.point.y+' sessions';
}
},
plotOptions: {
pie: {
showInLegend: true,
allowPointSelect: true,
cursor: 'pointer',
dataLabels:{
enabled: true,
color: '#000000',
connectorColor: '#000000',
formatter: function(){
return '<b>'+ this.point.name +'</b>: '+ this.point.y +' sessions';
}
}
}
},
series: [{
type: 'pie',
name: 'Session by librarian',
data: data
}]
});
hChart1= new Highcharts.Chart({
chart: {
renderTo: 'highchart2',
plotBackgrountColor: null,
plotBorderWidth: null,
plotShadow: false
},
credits:{
enabled: false
},
title:{
text: '# of session entries by length'
},
tooltip: {
formatter: function() {
return '<b>'+this.point.name+'</b>: '+this.point.y+' sessions';
}
},
plotOptions: {
pie: {
showInLegend: true,
allowPointSelect: true,
cursor: 'pointer',
dataLabels:{
enabled: true,
color: '#000000',
connectorColor: '#000000',
formatter: function(){
return '<b>'+ this.point.name +'</b>: '+ this.point.y +' sessions';
}
}
}
},
series: [{
type: 'pie',
name: 'Session by length',
data: data1
}]
});
});
});
EOD;
    
   

  include('includes/footer.php');
?>



