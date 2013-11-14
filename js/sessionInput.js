    var sessionCopies=0;

    //TODO: write code preventing same section for multiple entry of course
    function removeCopies()
                    {
                        $('.copy').remove();
                        checkCompletion();
                        return true;
                    }

    function checkCompletion()
    {
        //move this (these) call(s) elsewhere
        updateCourseLabels();


        var mustHave = $('.mustHave')
        var mustHaveCount = mustHave.length+1;
        var mustHaveRemaining = 0
        var mustHaveReportStr = '';
        var remainingStr='';
        var message='';
		var retval;

        $.each(mustHave, function()
            {
                if($(this).val()=="")
                {
                    //alert(mustHaveCount);
                    if(!$(this).attr('readonly')=='readonly')
                        { alert('!readonly');}
                    mustHaveRemaining++;
                    message= $(this).attr('title')
                    mustHaveReportStr+='<br />'+message;

                }
                else{}
            });

	        if(!checkResourcesCompletion()) {
				mustHaveRemaining++;
				retval = false;
			}

            if (mustHaveRemaining<=0)
                {
                    remainingStr='All required fields have been filled in.<br /><br />';
//                    $('#submitButton').removeAttr('disabled');
                    $('#submitButtonDiv').addClass('complete')
					retval = true;
                }
        else
            {
//                $('#submitButton').attr('disabled', true);
                $('#submitButtonDiv').removeClass('complete');
                remainingStr+='There are '+mustHaveRemaining+' of '+mustHaveCount+' mandatory fields remaining.<br /><a href="#" id="recheck">recheck</a><br /><br />' ;
				retval = false;
            }
        $('#completionStatus').empty().html('<p>'+remainingStr+mustHaveReportStr+'</p>');

        checkDivCompletion('#librarianSelect');
        checkDivCompletion('#courseSelect');
        checkDivCompletion('#facultySelect');
        checkDivCompletion('#locationSelect');
        checkDivCompletion('#dateSelect');
        checkDivCompletion('#lengthSelect');
        checkDivCompletion('#numberSelect');
        checkDivCompletion('#commentSelect');
        pageUpdate();
        return retval;
    }

    function checkDivCompletion(inID)
    {
        divMusts = $(inID+' .mustHave');
        divMustsCount=divMusts.length;
        divMustsRemaining=0;

        $.each(divMusts, function()
            {
                if($(this).val()==""){divMustsRemaining++;}
                else{}
            });

            if (divMustsRemaining>0) {$(inID).removeClass('complete');}
            else {$(inID).removeClass('complete').addClass('complete');}
        }

    function checkResourcesCompletion()
    {
        $('#resourcesSelect').removeClass('complete');

        if ($('#makeCopies').is(':checked'))
        {

            if($('#sameResources').is(':checked'))
            {
                //alert('copies with same resources');
                if ($('.resourcesBox').is(':checked')){$('#resourcesSelect').removeClass('complete').addClass('complete');}
            }
            else
            {
                //alert('copies with different resources')
                $('#resourcesSelect').removeClass('complete').addClass('complete');

                if( !$('.resourcesBox.xxx').is(':checked') ){ $('#resourcesSelect').removeClass('complete');}

                for(x=1; x<=sessionCopies; x++)
                    {
                    nameStr='.resourcesBox[name*="'+x+'"]';
                    if (!$(nameStr).is(':checked')) {$('#resourcesSelect').removeClass('complete');}
                    else{}
                    }
				return false;
            }
        }
        else
        {
            //alert('single copy');
            if ($('.resourcesBox').is(':checked'))
            {
                $('#resourcesSelect').removeClass('complete').addClass('complete');
            }
            else
            {
                $('#resourcesSelect').removeClass('complete');

                //TODO: Fix this quick fix. Not ideal. see bug in docReady.js for details.
				// Probably fixed -Webster

//                $('#submitButton').attr('disabled', true);
                $('#submitButtonDiv').removeClass('complete');
				return false;


            }
        }
		return true;
    }

    function updateCourseLabels()
    {
        courseSection='';
        courseNumberText='';
        courseNumberText+= $('#coursePrefixID option:selected').html();
        courseNumberText+=' ';
        courseNumberText+= $('#courseNumber').val();

        //alert(courseNumberText);

        $('.courseInfo').html(courseNumberText);
        $('.courseSection').html('-'+$('#courseSection').val());

        if ($('#makeCopies').attr('checked')=='checked')
        {
            for(x=1;x<=sessionCopies;x++)
            {
                sectionString = 'courseSection'+x;
                sectionValue='-';
                sectionValue+= $('#'+sectionString).val();
                // alert("x: "+x +"\nsectionString: "+sectionString+"\nsectionValue: "+sectionValue);

                $('.'+sectionString).html(sectionValue);
            }
        }


    }

    function updateCopies(inID)
    {
        if ($('#makeCopies').attr('checked')=='checked' )
            {
                for(x=1;x<=sessionCopies;x++)
                    {
                        $('#'+inID+x).val($('#'+inID).val()) ;
                    }
            }
            checkCompletion();
            return ;
    }

    // updates for individual sections
    function updateLibrarian()
    {
        librarianAppend='';
            var librarianIDVal = $('#librarianID').val();
        for(i=1;i<=sessionCopies;i++)
            {
            librarianAppend+= '<input class="copy mustHave" type="hidden" name="librarianID'+i+'" id="librarianID'+i+'" value="'+librarianIDVal+'" />';
            }
        $('#librarianSelect').append(librarianAppend);
    }

    function updateCoursePrefix()
    {
        var coursePrefixAppend='';
        var coursePrefixSelectedVal = $('#coursePrefixID').val();
        var coursePrefixSelectedText = $('#coursePrefixID option:selected').html();
        for(i=1; i<=sessionCopies; i++)
            {
            coursePrefixAppend += '<input class="copy" id="coursePrefixID'+i+'" type="hidden" name="coursePrefixID'+i+'" value="'+coursePrefixSelectedVal+'" />'+
                        '<input class="copy" id="coursePrefixText'+i+'" type="text" value="'+coursePrefixSelectedText+'" readonly="readonly" />';
            }

        $('#coursePrefixSelectContainer').append(coursePrefixAppend);

    }

    function updateCourse()
    {
        var courseNumberAppend='';
        var courseSectionAppend='';
        var courseTitleAppend='';
        var sessionNumberAppend='';
        var courseNumberVal = $('#courseNumber').val();
        var courseTitleVal=$('#courseTitle').val();
        var sessionNumberVal=$('#sessionNumber').val();

        for(i=1; i<=sessionCopies; i++)
            {
            courseNumberAppend+= '<input id="courseNumber'+i+'" class="copy" type="text" name="courseNumber'+i+'" value="'+courseNumberVal+'" readonly="readonly" />';
            courseSectionAppend+= '<input id="courseSection'+i+'" class="copy mustHave" title="You must enter a section number for each course." type="text" name="courseSection'+i+'" value=""  />';
            courseTitleAppend+= '<input id="courseTitle'+i+'" class="copy " type="text" name="courseTitle'+i+'" value="'+courseTitleVal+'"  />';
            sessionNumberAppend+= '<input id="sessionNumber'+i+'" class="copy" type="text" name="sessionNumber'+i+'" value="'+sessionNumberVal+'" readonly="readonly" />';
            }

        $('#courseNumberContainer').append(courseNumberAppend);
        $('#courseSectionContainer').append(courseSectionAppend);
        $('#courseTitleContainer').append(courseTitleAppend);
        $('#sessionNumberContainer').append(sessionNumberAppend);

    }

    function updateFaculty()
    {
        $('.faculty.copy').remove();
        var facultyAppend='';

        var facultyVal = $('#faculty').val();
        var facultySelectStr = $('#facultySelectContainer').html();
        var facultyCopyStr = '<br class="faculty copy" />'+facultySelectStr.replace('xxx', 'copy');

        for(i=1; i<=sessionCopies; i++)
        {
            if($('#sameFaculty').is(':checked'))
                    {
                        facultyAppend+='<input class="copy faculty" type="hidden" name="faculty'+i+'" id="faculty'+i+'" value="'+facultyVal+'" />';
                    }
            else
                    {
                        nameReplaceVal='name="faculty'+i+'"';
                        idReplaceVal='id="faculty'+i+'"';
                        var replaceSection='courseSection'+i+' copy ';

                        facultyAppend+= facultyCopyStr.replace(/xxx/g, 'copy').replace(/id="faculty"/g, idReplaceVal).replace(/name="faculty"/g, nameReplaceVal).replace(/copy courseSection/g, replaceSection);
                    }
        }
        $('#facultySelectContainer').append(facultyAppend);
        checkCompletion();
    }

    function updateLocation()
    {
        $('.location.copy').remove();

        var locationAppend='';
        var locationIDVal = $('#locationID').val();
        var locationSelectStr = $('#locationSelectContainer').html();
        var locationCopyStr = '<br class="copy location" />'+locationSelectStr.replace(/xxx/g, 'copy');

            for(i=1; i<=sessionCopies; i++)
        {
            if($('#sameLocations').is(':checked'))
            {
                //alert('locationIDVal: '+locationIDVal);
                locationAppend+='<input id="locationID'+i+'" class="location copy" type="hidden" name="locationID'+i+'" value="'+locationIDVal+'" />';
            }
            else
            {
                var replaceVal='locationID'+i;
                var replaceSection='courseSection'+i+' copy ';
                locationAppend+= locationCopyStr.replace(/locationID/g, replaceVal).replace(/copy courseSection/g, replaceSection);

            }
        }

        $('#locationSelectContainer').append(locationAppend);
        checkCompletion();
    }

    function updateDates()
    {
        $('.datepicker').datepicker('destroy');

        $('.datepicker.copy').remove();
        var dateAppend='';

        var dateVal = $('#datePicker').val();
        var dateSelectStr = $('#dateSelectContainer').html();
        var dateCopyStr = '<br class="datepicker copy" />'+dateSelectStr.replace('xxx', 'copy');

        for(i=1; i<=sessionCopies; i++)
        {
            if($('#sameDates').is(':checked'))
                    {
                        dateAppend+='<input class="copy datepicker" type="hidden" name="dateOfSession'+i+'" id="datePicker'+i+'" value="'+dateVal+'" />';
                    }
            else
                    {
                        nameReplaceVal='dateOfSession'+i;
                        idReplaceVal='datePicker'+i;
                        var replaceSection='courseSection'+i+' copy ';

                        dateAppend+= dateCopyStr.replace(/xxx/g, 'copy').replace(/dateOfSession/g, nameReplaceVal).replace(/datePicker/g, idReplaceVal).replace(/copy courseSection/g, replaceSection);
                    }
        }
        $('#dateSelectContainer').append(dateAppend);
        checkCompletion();
    }
    function updateLength()
    {
        $('.length.copy').remove();

        var lengthAppend='';
        var lengthIDVal = $('#lengthID').val();
        var lengthSelectStr = $('#lengthSelectContainer').html();
        var lengthCopyStr = '<br class="copy length" />'+lengthSelectStr.replace(/xxx/g, 'copy');

            for(i=1; i<=sessionCopies; i++)
        {
            if($('#sameLengths').is(':checked'))
            {
                //alert('lengthIDVal: '+lengthIDVal);
                lengthAppend+='<input id="lengthID'+i+'" class="length copy" type="hidden" name="lengthID'+i+'" value="'+lengthIDVal+'" />';
            }
            else
            {
                replaceVal='lengthID'+i;
                var replaceSection='courseSection'+i+' copy ';
                lengthAppend+= lengthCopyStr.replace(/lengthID/g, replaceVal).replace(/copy courseSection/g, replaceSection);
            }
        }

        $('#lengthSelectContainer').append(lengthAppend);
        checkCompletion();
    }


    function updateStudentCount()
    {

        var numberOfStudentsAppend='';

        var numberSelectStr = $('#numberOfStudentsContainer').html();
        var numberCopyStr = '<br class="copy" />'+numberSelectStr.replace(/xxx/g, 'copy');

        for(i=1;i<=sessionCopies;i++)
            {
            var replaceVal='numberOfStudents'+i;
            var replaceSection='courseSection'+i+' copy ';
            numberOfStudentsAppend+=numberCopyStr.replace(/numberOfStudents/g, replaceVal).replace(/copy courseSection/g, replaceSection);
            }
        $('#numberOfStudentsContainer').append(numberOfStudentsAppend);

    }

    function updateResources()
    {
        $('.resourcesBox.copy').remove();
        $('.resourcesbox.copy').remove();

        var resourcesSelectStr=$('#resourcesSelectContainer').html().replace(/xxx/g, 'copy');
        var resourcesIntroducedAppend='';
        for(i=1; i<=sessionCopies; i++)
        {
        var replaceSection='courseSection'+i+' copy ';

        if($('#sameResources').is(':checked'))
        {
            var selectedBoxes = $('input.resourcesBox');
            $.each(selectedBoxes, function(){
                if($(this).attr('checked'))
                {
                thisVal=$(this).val();

                if (thisVal !='none')
                    {
                resourcesIntroducedAppend+='<input class="copy resourcesBox" type="hidden" name="resourcesIntroduced'+i+'[]"  value="'+thisVal+'" />';}

                else {resourcesIntroducedAppend+='<input class="copy resourcesBox" type="hidden" name="resourcesIntroduced'+i+'"  value="'+thisVal+'" />';}
                }

            });

        }
        else
        {
            resourcesIntroducedAppend+= '<br class="copy resourcesbox resourcesbox'+i+'" /><hr class="resourcesbox copy" /> '+resourcesSelectStr.replace(/resourcesIntroduced/g, 'resourcesIntroduced'+i).replace(/copy courseSection/g, replaceSection) ;
        }
        }

        $('#resourcesSelectContainer').append(resourcesIntroducedAppend);
        checkCompletion();

    }

    function updateNotes()
    {
        $('.notebox.copy').remove();
        var noteSelectAppend='';
        var sessionNoteVal = $('#sessionNote').val();
        var noteSelectStr =$('#noteSelectContainer').html();
        var noteSelectCopy = noteSelectStr.replace(/xxx/g, "copy");

        for(i=1; i<=sessionCopies; i++)
        {
            var replaceSection='courseSection'+i+' copy ';
            if($('#sameNotes').is(':checked'))
            {
                noteSelectAppend+='<input id="sessionNote'+i+'" class="copy notebox" type="hidden" name="sessionNote'+i+'"  value="'+sessionNoteVal+'" />';
            }

            else
            {
                noteSelectAppend+= '<br class="copy" /> '+noteSelectCopy.replace(/id="sessionNote"/g, 'id="sessionNote'+i+'" ').replace(/name="sessionNote"/g, 'name="sessionNote'+i+'"').replace(/copy courseSection/g, replaceSection) ;
            }
        }
        $('#noteSelectContainer').append(noteSelectAppend);
        checkCompletion();
    }

    //utility function calls all 'update-section' funcitons'
    function makeCopies()
        {
            //start fresh!
            removeCopies();

            //update each section.
            updateLibrarian();
            updateCoursePrefix();
            updateCourse();
            updateFaculty();
            updateLocation();
            updateDates();
            updateLength();
            updateStudentCount();
            updateResources();
            updateNotes();

            checkCompletion();
        }


      function assessmentDropdown()
      {
            var mustHave = $('.assessmentDropDown')
            var mustHaveCount = mustHave.length;
            var mustHaveRemaining = 0


            $.each(mustHave, function()
                    {
                        if($(this).val()=="")
                        {
                        mustHaveRemaining++;
                        }
                        else{}

                    });
               // alert('change!!!   /n mustHave='+mustHaveCount+'  mustHaveRemaining='+mustHaveRemaining);
            if (mustHaveRemaining<=0){$('#assessSubmit').removeAttr('disabled');}
            else{$('#assessSubmit').attr('disabled', true);}
      }
        function noneOrSome() {

                    var noneName=$(this).attr('name');
                    var selector='.resourcesBox[name="'+noneName+'[]"]';
                    if ($(this).is(':checked'))
                        {
                        //remove all other options of the same name

                        $(selector).attr('checked', false).attr('disabled', true);

                         }
                    else
                        {
                            //add all other options
                            $(selector).removeAttr('disabled');
                        }

                    }



 (function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var input,
					self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "",
					wrapper = this.wrapper = $( "<span>" )
						.addClass( "ui-combobox" )
						.insertAfter( select );

				input = $( "<input>" )
					.appendTo( wrapper )
					.val( value )
					.addClass( "ui-state-default ui-combobox-input" )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {


							if ( !ui.item ) {

								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}

                                                       //alert('change');
                                                    //TODO: look at this ugly fix and better-fix
                                                    if ( window.location.pathname=='/assessment/enterSession.php')
                                                    {

                                                        makeCopies();}

                                                    if (window.location.pathname=='/assessment/assessOutcome.php')
                                                        {

                                                          assessmentDropdown();
                                                        }


						} //end Change detect;
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				$( "<a>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.appendTo( wrapper )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-combobox-toggle" )
					.click(function() {

						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.wrapper.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );






