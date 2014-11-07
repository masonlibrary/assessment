var testChart;
var chartData;
var aySSChart;
var aySSChartData;
var sySSChartData1;

var asInitVals = new Array();
$(function(){

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
														$('.fixedHeader').animate({top:"-=212"}, "400")
//                            $('body').animate(
//                            {"margin-top": ['-=193px', 'swing']},
//                                "400");
                            $('#slider').removeClass('open').addClass('closed')
                        }
                     else
                         {
                             $('#tabzilla-contents').slideDown(400);
														 $('.fixedHeader').animate({top:"+=212"}, "400")
//                             $('body').animate(
//                                {"margin-top":['+=193px', 'swing']},
//                                '400');
                             $('#slider').removeClass('closed').addClass('open');
                         }
                });

				$( document ).delegate( "a.menu-link", "click", function(e) {
				e.stopPropagation();
//				e.preventDefault();
//				alert('pop'+this.id);
//				return false;
			});

			try {
				var fh = new $.fn.dataTable.FixedHeader(oTable, {"offsetTop":-2});
			} catch (e) {
				console.log('Exception: '+e);
				console.log('(If the page has no table, oTable not being defined is normal)');
			}

			$("#messagebox-close").click(function(){
				$('.fixedHeader').hide();
				$("#messagebox").hide("blind", {}, 400, function(){
					fh.fnUpdate();
					$('.fixedHeader').show();
				});
			});

			$.fn.dataTableExt.afnFiltering.push(
				function(oSettings, aData, iDataIndex) {
					var searchstr = $("#dataTables_filter").val().toLowerCase(); // The string to search for
					var invert = $("#dataTables_invert").prop("checked"); // Whether or not to invert the result
					var hasMatch = false; // Whether or not the row matches the string
					for (var i=0; i<aData.length; i++) { // Loop through each data-element in the row-array
						if (aData[i].toLowerCase().indexOf(searchstr) >= 0) {
							hasMatch = true; // If a data-element in the row-array matches, we want to display this row
							break; // So stop processing the row (for performance)
						}
					}
					return (invert ? !hasMatch : hasMatch); // If inverted, return negated result, else return regular result (effective boolean XOR)
				}
			);

			$("#dataTables_filter").keyup( function() { oTable.fnDraw(); } );
			$("#dataTables_invert").change( function() { oTable.fnDraw(); } );

			$("tr.mySessions").click( function(event){

						// Credit: http://stackoverflow.com/a/3550649/217374
						if($(event.target).is('a')) return;

						//event.preventDefault();
						var rowID = $(this).attr('id');
						var menuFinder='#d'+rowID;

							if ($(this).hasClass("isOpen") ) {
								$(menuFinder+' div.removable-menu').remove();
								$(menuFinder).animate({
									height: "19px",
									"padding-top":0,
									"border-radius":"30px",
									width: "19px"
									}, 200, function() {
									// Animation complete.
									$('#'+this.id+' p').html('+');
									});
								$(this).removeClass("isOpen");

							} else {
								$(menuFinder).animate({
									height: "90px",
									width: "50px",
									"padding-top": "5px",
									"border-radius":0
									}, 200, function() {
									// Animation complete.
										$('#'+this.id+' p').html('-');
										$(this).append('\
											<div class="removable-menu" > \
												<a class="menu-link" onclick="rowdialog('+rowID+', \'view\')">View</a> \
												<a class="menu-link" onclick="rowdialog('+rowID+', \'edit\')">Edit</a> \
												<a class="menu-link" onclick="rowdialog('+rowID+', \'duplicate\')">Duplicate</a> \
												<a class="menu-link" onclick="rowdialog('+rowID+', \'delete\')">Delete</a> \
											</div>');

								});
								$(this).addClass("isOpen");
							}
							return ;
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
                $('.resourcesBox').live('click', noneOrSome);


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
                        checkCompletion();
			});

                $('#coursePrefixID').change(function(){
                        checkCompletion();
                });

                $('#courseNumber').change(function(){
                     checkCompletion();
                });

                $('#courseSection').change(function(){
                    checkCompletion();
                });

                $('#courseTitle').change(function(){
                    checkDivCompletion('#courseSelect');
                    checkCompletion();
                });

                $('#sessionNumber').change(function(){
                    checkCompletion();
                });

                $('#faculty').change(function(){
			var faculty= $('#faculty').val();

                         checkCompletion();
			});


		$('#locationID').change(function(){

                          checkCompletion();
			});


		$('#datePicker').change(function(){

			var datePick = $('#datePicker').val();

                           if($('#sameDates').is(':checked')){updateDates();}
                           checkCompletion();
			});


		$('#lengthID').change(function(){
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
				   // Changed it so that the button is always live, but won't go unless checkCompletion() returns true; should fix this. -Webster
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

								$('#assessmentForm').live('submit', function(e){
									if (checkCompletion()) {
										$('#librarianID').removeAttr('disabled');
										return true;
									} else {
										e.preventDefault();
										$('#librarianID').attr('disabled', 'disabled');
										return false;
									}
								})

});

// Returns a multidimensional array from a table with id "tableid"
// between column numbers mincol and maxcol
function tableToArray(tableid, mincol, maxcol) {
	var data = Array();
	$('#'+tableid+' tbody tr').each(function(i, v){
		data[i] = Array();
		$(this).children('td').slice(mincol, maxcol).each(function(ii, vv){
			var str = $(this).text();
			var num = parseInt(str);
			// Load into array as string or integer
			// parseInt() returned NaN if this isn't an integer
			if (isNaN(num)) {
				data[i][ii] = str;
			} else {
				data[i][ii] = num;
			}
		});
	});
	return data;
}

function rowdialog(row, action) {

	$('#dialog').html('Loading...');
	$("#dialog").dialog({
		width: 800,
		height: 600
	});

	var req;

	if (action === 'delete') {
		req = $.get("deleteSession.php?lite&sesdID="+row);
	} else {
		req = $.get("enterSession.php?lite&sesdID="+row+"&action="+action);
	}

	req.done(function(data){
		$('#dialog').html(data);
		$('input').change( function(){checkCompletion(); });
		$('.ui-widget-overlay').click(function(){ $('#dialog').dialog('close'); });
	});

}
