var testChart;
var chartData;
var aySSChart;
var aySSChartData;
var sySSChartData1;

var asInitVals = new Array();
$(function(){
    
//                global (ugh TODO figure out better way. 
                var numberOfCopies =$('select#numberOfCopies').selectmenu({
                                width: 50,
                                menuWidth: 400
				});
                                
		jQuery.event.add(window, "load", pageStart);
                // **************************************
                //slide down menu code. Remove if unused.
                // **************************************
                // open body-top-margin=320   closed: body-top-margin=127
                $('#tabzilla').click(function(e){
                    e.preventDefault();
                    
                    if ($('#slider').hasClass('open'))
                        {
                            $('#tabzilla-contents').slideUp(400);
                            $('body').animate(
                            {"margin-top": ['-=193px', 'swing']},
                                "400");
                            $('#slider').removeClass('open').addClass('closed')
                        }
                     else
                         {
                             $('#tabzilla-contents').slideDown(400);
                             $('body').animate(
                                {"margin-top":['+=193px', 'swing']},
                                '400');
                             $('#slider').removeClass('closed').addClass('open');
                         }
                });
                
                
                
               var oTable = $('#mySessions').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bStateSave":true,
                    "iDisplayLength": -1,
                    "aLengthMenu":[[25, 50, 100, -1], [25, 50, 100, "All"]],
                    "aoColumns" : [
                        null,
                        null,
                        null,
                        null,
                        null,
                        {"iDataSort":6},
                        {"bVisible":false},
                        
                        null,
                        null,
                        null
                    ],
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf",
                        "aButtons":[
                            {
                                "sExtends": "csv",
                                "sButtonText": "csv/Excel",
                                "mColumns": [ 0, 1, 2,3,4,5,7,8 ]
                            },
                            {
                                "sExtends": "pdf",
                                "sButtonText": "PDF",
                                "mColumns": [ 0, 1, 2,3,4,5,7,8 ]
                            },
                            {
                                "sExtends": "print",
                                "sButtonText": "Print",
                                "mColumns": [ 0, 1, 2 ,3 ,4 ,5 ,7 , 8 ]
                            },
                            {
                                "sExtends": "copy",
                                "sButtonText": "Copy",
                                "mColumns": [ 0, 1, 2,3,4,5,7,8 ]
                            },
                        ]
                    }
                    
                });
                
               

                // new version to attempt to use row grouping plugin 
                var pTable = $('#myAssessments').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bStateSave":true,
                   "bLengthChange": false,
                   "bPaginate": false, 
                   
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }).rowGrouping({
                    bExpandableGrouping: true, 
                    asExpandedGroups: []        
                    });
                
                
                     // new version to attempt to use row grouping plugin 
                var qTable = $('#outcomesMap').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bStateSave":true,
                   "bLengthChange": false,
                   "bPaginate": false, 
                   
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }).rowGrouping({ 
                    bExpandableGrouping: true/*, 
                    asExpandedGroups: []*/      
                    });
                var rTable = $('#outcomesMapa').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bStateSave":true,
                   "bLengthChange": false,
                   "bPaginate": false, 
                   
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }).rowGrouping({
                    bExpandableGrouping: true /*, 
                    asExpandedGroups: []        */
                    });
               
               
               var sTable = $('#aySS').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bFilter": false,
                    "bPaginate": false,
                    aaSorting: [],
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }); 
               
                 var tTable = $('#assessmentSummary').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bFilter" :false,
                    "bSort":false,
                    "bStateSave":true,
                   "bLengthChange": false,
                   "bPaginate": false, 
                   
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }).rowGrouping({
                    bExpandableGrouping: true /*, 
                    asExpandedGroups: []        */
                    });
                    
                    
                    
               var uTable = $('#allSessions').dataTable({
                    "sDom":'T<"clear">lfrtip',
                    "bFilter" :true,
                    "bSort":false,
                    "bStateSave":true,
                   "bLengthChange": false,
                   "bPaginate": false, 
                   "bProcessing" : true,
                    "oTableTools":{
                        "sSwfPath":"swf/copy_csv_xls_pdf.swf"
                    }
                    
                }).rowGrouping({
                    bExpandableGrouping: true /*, 
                    asExpandedGroups: []        */
                    });
                    
                    
               // **********************************************************
               /*THIS WORKS FOR OBTAINING FILTERED DATA FROM dataTables*/
               // **********************************************************
               
               $('.chartUpperLower').click(function(){
                  
                 
                      aySSchartData=[];
                 
                     aySSchartData.push(['Upper Level', +$('.upperLevelCount').html()]);
                     aySSchartData.push(['Lower Level', +$('.lowerLevelCount').html()]);
                     
                    // alert(aySSchartData);
                     
                      aySSChart= new Highcharts.Chart({
                            chart: {
                                renderTo: 'upperLowerChart',
                                plotBackgrountColor: null,
                                plotBorderWidth: null,
                                plotShadow: false
                            },
                            credits:{
                                enabled: false
                                },
                            title:{
                                text: 'Session by Upper/Lower Level <br />'+$('.fyValue').html()
                                },
                            tooltip: {
                                formatter: function() {
                                    return '<b>'+this.point.name+'</b>: '+ this.point.y+' sessions';
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
                                            return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage,2 )+'%';
                                            }
                                        }
                                    }
                                },
                            series: [{
                            type: 'pie',
                            name: 'Session by Level',
                            data: aySSchartData
                            }]
                            });
                            });
                       


            $('.chartITWetc').click(function(){
                  
                 
                      aySSchartData1=[];
                      
                      totalCount = parseInt($('.totalSessionsCount').html(), 10);
                      ITWCount = parseInt($('.ITWCount').html(), 10);
                      IQLCount = parseInt($('.IQLCount').html(), 10);
                      HLSCCount = parseInt($('.HLSCCount').html(), 10);
                      IHCOMM171Count = parseInt($('.IHCOMM171Count').html(), 10);
                      
                      totalMinusOthersCount = totalCount-ITWCount-IQLCount-HLSCCount-IHCOMM171Count;
                 
                 
                     aySSchartData1.push(['All Others', totalMinusOthersCount]);
                     aySSchartData1.push(['ITW', ITWCount]);
                     aySSchartData1.push(['IQL', IQLCount]);
                     aySSchartData1.push(['HLSC', HLSCCount]);
                     aySSchartData1.push(['IHCOMM171', IHCOMM171Count]);
                     
                     //alert(aySSchartData1);
                     
                      aySSChart= new Highcharts.Chart({
                            chart: {
                                renderTo: 'ITWetcChart',
                                plotBackgrountColor: null,
                                plotBorderWidth: null,
                                plotShadow: false
                            },
                            credits:{
                                enabled: false
                                },
                            title:{
                                text: 'Session by specific course of '+totalCount+' sessions '+$('.fyValue').html()
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
                                            return '<b>'+ this.point.name +'</b>: '+Highcharts.numberFormat(this.percentage,2 )+'%'; 
                                            }
                                        }
                                    }
                                },
                            series: [{
                            type: 'pie',
                            name: 'Session by Level',
                            data: aySSchartData1
                            }]
                            });
                            });
                   








                $('.test').click(function(){
                  var data = oTable._('td.coursePrefix', {"filter":"applied"});
                  
                  var associativeData = [];
                  
                  var filterString ='<br />'+ $('div.dataTables_filter input').val(); 
                  
                  for(var x=1; x< data.length; x++)
                      {
                          interimVar=data[x].split(" ");
                          prefix = interimVar[0];
                          if (associativeData[prefix] == null) {associativeData[prefix]=1;}
                          else {associativeData[prefix]++;}
                          
                      }
                      
                      var output='';
                     
                      
                      chartData=[];
                      
                      for(var key in associativeData)
                          {output+= key + ' : '+ associativeData[key] +'\n';

                          chartData.push([key, associativeData[key]]);
                      
                        }



                  //alert(output);
                  
                  
                  testChart= new Highcharts.Chart({
                            chart: {
                                renderTo: 'testChart',
                                plotBackgrountColor: null,
                                plotBorderWidth: null,
                                plotShadow: false
                            },
                            credits:{
                                enabled: false
                                },
                            title:{
                                text: 'Sessions by prefix'+filterString
                                },
                            tooltip: {
                                formatter: function() {
                                    return '<b>'+this.point.name+'</b>: '+ point.y+' sessions';
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
                                            return '<b>'+ this.point.name +'</b>: '+ Highcharts.numberFormat(this.percentage,2 )+'%';
                                            }
                                        }
                                    }
                                },
                            series: [{
                            type: 'pie',
                            name: 'Session by prefix',
                            data: chartData
                            }]
                            });
                            });
                           
                 
                  
             
               // ***********************************************************
                
                
                $( "#coursePrefixID" ).combobox();
                $('.assessmentDropDown').combobox();
                $('select#sessionNumber').selectmenu({width: 65});
                
                //$('#locationID').selectmenu();
                //$('#lengthID').selectmenu();
             
                $('#outcomesTaughtDiv').tabs();
                $('#assessOutcomesDiv').tabs();
                 
                //if user selects 'none' (no resources introduced) then disable resources checkbox
                $('.none').live('click', noneOrSome);
                
                
                $('.areYouSure').click(function(e){
                       return window.confirm(this.title || 'Delete this record?');
                       e.preventDefault();
                });
                
                
                $('#courseIdent').click(function(){
                    
                    $('#courseSummary').toggle('slow');
                });
                
                
                
                $('.didNotAssess').click( function()
                {
                    var noAssessNum='';
                    var noAssessValue='';
                    var noAssessString='';
                    var thisID=$(this).attr('id');
                    
                    if ( $(this).is(':checked')) {
                        noAssessNum = $(this).val();
                        noAssessValue= noAssessNum+' 1';
                        noAssessString='#notAssessed'+noAssessNum;
                        
                        $(noAssessString).val(noAssessValue);
                        
                       
                        $('select.'+thisID).val('0').removeClass('assessmentDropDown');
                        //alert('In yes it is checked'+ $(noAssessString).val());
                        
                        }
                    else{
                        
                        noAssessNum = $(this).val();
                        noAssessValue= noAssessNum+' 0';
                        noAssessString='#notAssessed'+noAssessNum;

                        $(noAssessString).val(noAssessValue);

                        $('select.'+thisID).val('').addClass('assessmentDropDown');
                        //alert('in no it is not checked'+ $(noAssessString).val());
                        
                    }
                        assessmentDropdown();

                });


                
                
                $('.outcomesBox').change(function(){
                    
                    if($('input.outcomesBox').is(':checked')){$('#OTCTsubmit').removeAttr('disabled');}
                    else{$('#OTCTsubmit').attr('disabled', true);}
                    
                });
                
                $('.checkAll').change(function(){
                    prefixName=$(this).attr('class').split(' ')[1];
                   differential = '.'+ prefixName;
                   spanID='span#span'+prefixName;
                   
                   if ($(this).is(':checked')) 
                   {
                       $(differential).attr('checked', true);
                       $(spanID).addClass('hasChecked');
                       $('input.outcomesNeeded').not(differential).attr('disabled', true);
                       $('input.checkAll').not(differential).attr('disabled', true); 
                   }
                    else
                    {
                       $(differential).attr('checked', false);
                       $(spanID).removeClass('hasChecked');
                       $('input.outcomesNeeded').removeAttr("disabled");
                       $('input.checkAll').removeAttr('disabled'); 
                    }
                    
                   if ($('.outcomesNeeded').is(':checked')) {$('#chooseCoursesOTCT').removeAttr('disabled');}
                   else {$('#chooseCoursesOTCT').attr('disabled', true);}   
                });
               
                $('input.outcomesNeeded').change(function(){
                    
                   //get 2nd class
                   prefixName=$(this).attr('class').split(' ')[1];
                   differential = '.'+ prefixName;
                   spanID='span#span'+prefixName;
                   if($(differential).is(':checked')){
                      $('input.outcomesNeeded').not(differential).attr('disabled', true);
                      $('input.checkAll').not(differential).attr('disabled', true);
                      $(spanID).addClass('hasChecked');
                   }
                   else {
                       $('input.outcomesNeeded').removeAttr("disabled");
                       $('input.checkAll').removeAttr('disabled');
                       $(spanID).removeClass('hasChecked');
                   }
                   
                   if ($('.outcomesNeeded').is(':checked')) {$('#chooseCoursesOTCT').removeAttr('disabled');}
                   else {$('#chooseCoursesOTCT').attr('disabled', true);} 
                   
                });
                  
                  
                  
                $('#makeCopies').change(function(){
                        
                       if( $('#makeCopies').is(':checked') )
                            { 
                                $('#numberOfCopies').removeClass('hidden');
                                numberOfCopies.selectmenu("enable");
                                sessionCopies = parseInt($('#numberOfCopies').val(), 10);
                                $('.copyOptions').removeClass('hidden');
                                $('.copyOptions input').attr('checked', true);
                                makeCopies();
                            }
                       else
                            {
                                numberOfCopies.selectmenu("disable");
                                $('#numberOfCopies').addClass('hidden');
                                sessionCopies=0;
                                $('.copyOptions').addClass('hidden');
                                removeCopies();
                            }
                      checkCompletion();
                });
                
                $('#numberOfCopies').change(function(){
                    removeCopies();
                    sessionCopies= parseInt($('#numberOfCopies').val(), 10);
                    makeCopies();
                }); 
                
                $('#sameNotes').change(function(){updateNotes();})
                $('#sameResources').change(function(){updateResources();});
                $('#sameLocations').change(function(){updateLocation();});
                
                //think about how to handle this without destroying everything else
                $('#sameDates').change(function(){
                   $('.datePicker').datepicker('destroy');
                   updateDates(); 
                });
                
                $('#sameLengths').change(function(){updateLength();});
                $('#sameFaculty').change(function(){updateFaculty();});
                
                /* ******************** */
                /*  Detect Changes      */
                /* *******************  */
                
                $('#librarianID').change(function(){
                    
                        updateCopies($(this).attr('id'));
                        checkCompletion();
			});
                        
                $('#coursePrefixID').change(function(){
                        
                        if ($('#makeCopies').attr('checked')=='checked' )
                        {
                            for(x=1; x<=sessionCopies; x++)
                                {
                                    $('#coursePrefixID'+x).val( $('#coursePrefixID').val() );
                                    $('#coursePrefixText'+x).val($('#coursePrefixID option:selected').html());
                                }
                            
                        }
                        else {}
                        checkCompletion();
                });
                
                $('#courseNumber').change(function(){
                     updateCopies($(this).attr('id'));
                     checkCompletion();
                });
                
                $('#courseSection').change(function(){
                    checkCompletion();
                });
                
                $('#courseTitle').change(function(){
                    updateCopies($(this).attr('id'));
                    checkDivCompletion('#courseSelect');
                    checkCompletion();
                });
                
                $('#sessionNumber').change(function(){
                    updateCopies($(this).attr('id'));
                    checkCompletion();
                });
                
                $('#faculty').change(function(){
			var faculty= $('#faculty').val();
			
                        updateCopies($(this).attr('id'));
                         checkCompletion();
			});  
                
                
		$('#locationID').change(function(){
                        updateCopies($(this).attr('id'));
                        
                          checkCompletion();
			});	
		
		
		$('#datePicker').change(function(){
			
			var datePick = $('#datePicker').val();
                        
                           if($('#sameDates').is(':checked')){updateDates();}
                           checkCompletion();
			});
			
                
		$('#lengthID').change(function(){
                        updateCopies($(this).attr('id'));
                        //updateLength();
                        checkCompletion();
			});
                        
		$('#numberOfStudents').change(function(){
			var numberStudents= $('#numberOfStudents').val();
			
			if ( isNaN(numberStudents))
                            { 
				alert(numberStudents +' is not a number.');
                                $('#numberOfStudents').val('');
                            }
                         checkCompletion();
			});
                   
                   
                   //TODO: Fix this BUG! 
                   // can click a resource, complete form, enable submit then unclick a resource and submit still enabled. 
                  $('.resourcesBox.xxx').change(function(){
                       if($('#sameResources').is(':checked')){updateResources();}
                      checkCompletion();
                  });
                  
                  $('.resourcesBox.copy').live('click', function() {
                    checkCompletion();
                    });
                 
                  
          
                /* ********************* */
                // Activate datepicker   //
                /* **********************/
                $('.datepicker').live('click', function() {
                   // alert('click!');
                    $(this).datepicker('destroy').datepicker({showOn:'focus', changeMonth: true, changeYear: true}).focus();
                    });

               
               
               $('h4.outcomeHeading').click(function(){
                  
                   var id= $(this).attr('id');
                   var targetDiv = 'div.'+id;
                    
                    if ($(targetDiv).hasClass('hidden')) {
                            
                    }
                        $(targetDiv).toggle('fast', function(){

                            if($(targetDiv).is(':visible')){  $('#'+id).addClass('opened'); $('#'+id+' span').html('&nbsp;-&nbsp;');}
                                else { $('#'+id).removeClass('opened');$('#'+id+' span').html('&nbsp;+&nbsp;');}
                        });
                  
                   
                   
               })
                    
                $('.mustHave.copy').live('click', function(){
                    checkCompletion();
                })
                    
                $('#recheck').live('click', function(e) {
                    e.preventDefault();
                    checkCompletion();
                    });
                    
                    
                    
             
});