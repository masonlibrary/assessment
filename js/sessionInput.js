//    function checkCompletion()
//    {
//        //move this (these) call(s) elsewhere
//        updateCourseLabels();
//
//
//        var mustHave = $('.mustHave')
//        var mustHaveCount = mustHave.length+1;
//        var mustHaveRemaining = 0
//        var mustHaveReportStr = '';
//        var remainingStr='';
//        var message='';
//		var retval;
//
//        $.each(mustHave, function()
//            {
//                if($(this).val()=="")
//                {
//                    //alert(mustHaveCount);
//                    if(!$(this).attr('readonly')=='readonly')
//                        { alert('!readonly');}
//                    mustHaveRemaining++;
//                    message= $(this).attr('title')
//                    mustHaveReportStr+='<br />'+message;
//
//                }
//                else{}
//            });
//
//	        if(!checkOutcomesCompletion()) {
//				mustHaveRemaining++;
//				retval = false;
//			}
//
//            if (mustHaveRemaining<=0)
//                {
//                    remainingStr='All required fields have been filled in.<br /><br />';
////                    $('#submitButton').removeAttr('disabled');
//                    $('#submitButtonDiv').addClass('complete')
//					retval = true;
//                }
//        else
//            {
////                $('#submitButton').attr('disabled', true);
//                $('#submitButtonDiv').removeClass('complete');
//                remainingStr+='There are '+mustHaveRemaining+' of '+mustHaveCount+' mandatory fields remaining.<br /><a href="#" id="recheck">recheck</a><br /><br />' ;
//				retval = false;
//            }
//        $('#completionStatus').empty().html('<p>'+remainingStr+mustHaveReportStr+'</p>');
//
//        checkDivCompletion('#librarianSelect');
//        checkDivCompletion('#courseSelect');
//        checkDivCompletion('#facultySelect');
//        checkDivCompletion('#locationSelect');
//        checkDivCompletion('#dateSelect');
//        checkDivCompletion('#lengthSelect');
//        checkDivCompletion('#numberSelect');
//        checkDivCompletion('#commentSelect');
//        pageUpdate();
//        return retval;
//    }
//
//    function checkDivCompletion(inID)
//    {
//        divMusts = $(inID+' .mustHave');
//        divMustsCount=divMusts.length;
//        divMustsRemaining=0;
//
//        $.each(divMusts, function()
//            {
//                if($(this).val()==""){divMustsRemaining++;}
//                else{}
//            });
//
//            if (divMustsRemaining>0) {$(inID).removeClass('complete');}
//            else {$(inID).removeClass('complete').addClass('complete');}
//        }
//
//    function checkOutcomesCompletion()
//    {
//        $('#outcomesSelect').removeClass('complete');
//
//            if ($('.outcomesBox').is(':checked'))
//            {
//                $('#outcomesSelect').removeClass('complete').addClass('complete');
//            }
//            else
//            {
//                $('#outcomesSelect').removeClass('complete');
//                $('#submitButtonDiv').removeClass('complete');
//				return false;
//
//
//            }
//		return true;
//    }
//
//    function updateCourseLabels()
//    {
//        courseSection='';
//        courseNumberText='';
//        courseNumberText+= $('#coursePrefixID option:selected').html();
//        courseNumberText+=' ';
//        courseNumberText+= $('#courseNumber').val();
//
//        //alert(courseNumberText);
//
//        $('.courseInfo').html(courseNumberText);
//        $('.courseSection').html('-'+$('#courseSection').val());
//    }
//
//    // updates for individual sections
//    function updateLibrarian()
//    {
//        librarianAppend='';
//            var librarianIDVal = $('#librarianID').val();
//            librarianAppend+= '<input class="copy mustHave" type="hidden" name="librarianID'+i+'" id="librarianID'+i+'" value="'+librarianIDVal+'" />';
//        $('#librarianSelect').append(librarianAppend);
//    }
//
//    function updateCoursePrefix()
//    {
//        var coursePrefixAppend='';
//        var coursePrefixSelectedVal = $('#coursePrefixID').val();
//        var coursePrefixSelectedText = $('#coursePrefixID option:selected').html();
//            coursePrefixAppend += '<input class="copy" id="coursePrefixID'+i+'" type="hidden" name="coursePrefixID'+i+'" value="'+coursePrefixSelectedVal+'" />'+
//                        '<input class="copy" id="coursePrefixText'+i+'" type="text" value="'+coursePrefixSelectedText+'" readonly="readonly" />';
//        $('#coursePrefixSelectContainer').append(coursePrefixAppend);
//
//    }
//
//    function updateCourse()
//    {
//        var courseNumberAppend='';
//        var courseSectionAppend='';
//        var courseTitleAppend='';
//        var sessionNumberAppend='';
//        var courseNumberVal = $('#courseNumber').val();
//        var courseTitleVal=$('#courseTitle').val();
//        var sessionNumberVal=$('#sessionNumber').val();
//
//				courseNumberAppend+= '<input id="courseNumber'+i+'" class="copy" type="text" name="courseNumber'+i+'" value="'+courseNumberVal+'" readonly="readonly" />';
//				courseSectionAppend+= '<input id="courseSection'+i+'" class="copy mustHave" title="You must enter a section number for each course." type="text" name="courseSection'+i+'" value=""  />';
//				courseTitleAppend+= '<input id="courseTitle'+i+'" class="copy " type="text" name="courseTitle'+i+'" value="'+courseTitleVal+'"  />';
//				sessionNumberAppend+= '<input id="sessionNumber'+i+'" class="copy" type="text" name="sessionNumber'+i+'" value="'+sessionNumberVal+'" readonly="readonly" />';
//
//        $('#courseNumberContainer').append(courseNumberAppend);
//        $('#courseSectionContainer').append(courseSectionAppend);
//        $('#courseTitleContainer').append(courseTitleAppend);
//        $('#sessionNumberContainer').append(sessionNumberAppend);
//
//    }
//
//    function updateFaculty()
//    {
//        $('.faculty.copy').remove();
//        var facultyAppend='';
//
//        var facultyVal = $('#faculty').val();
//        var facultySelectStr = $('#facultySelectContainer').html();
//        var facultyCopyStr = '<br class="faculty copy" />'+facultySelectStr.replace('xxx', 'copy');
//
//				if($('#sameFaculty').is(':checked'))
//								{
//										facultyAppend+='<input class="copy faculty" type="hidden" name="faculty'+i+'" id="faculty'+i+'" value="'+facultyVal+'" />';
//								}
//				else
//								{
//										nameReplaceVal='name="faculty'+i+'"';
//										idReplaceVal='id="faculty'+i+'"';
//										var replaceSection='courseSection'+i+' copy ';
//
//										facultyAppend+= facultyCopyStr.replace(/xxx/g, 'copy').replace(/id="faculty"/g, idReplaceVal).replace(/name="faculty"/g, nameReplaceVal).replace(/copy courseSection/g, replaceSection);
//								}
//        $('#facultySelectContainer').append(facultyAppend);
//        checkCompletion();
//    }
//
//    function updateLocation()
//    {
//        $('.location.copy').remove();
//
//        var locationAppend='';
//        var locationIDVal = $('#locationID').val();
//        var locationSelectStr = $('#locationSelectContainer').html();
//        var locationCopyStr = '<br class="copy location" />'+locationSelectStr.replace(/xxx/g, 'copy');
//
//				if($('#sameLocations').is(':checked'))
//				{
//						//alert('locationIDVal: '+locationIDVal);
//						locationAppend+='<input id="locationID'+i+'" class="location copy" type="hidden" name="locationID'+i+'" value="'+locationIDVal+'" />';
//				}
//				else
//				{
//						var replaceVal='locationID'+i;
//						var replaceSection='courseSection'+i+' copy ';
//						locationAppend+= locationCopyStr.replace(/locationID/g, replaceVal).replace(/copy courseSection/g, replaceSection);
//
//				}
//
//        $('#locationSelectContainer').append(locationAppend);
//        checkCompletion();
//    }
//
//    function updateDates()
//    {
//        $('.datepicker').datepicker('destroy');
//
//        $('.datepicker.copy').remove();
//        var dateAppend='';
//
//        var dateVal = $('#datePicker').val();
//        var dateSelectStr = $('#dateSelectContainer').html();
//        var dateCopyStr = '<br class="datepicker copy" />'+dateSelectStr.replace('xxx', 'copy');
//
//				if($('#sameDates').is(':checked'))
//								{
//										dateAppend+='<input class="copy datepicker" type="hidden" name="dateOfSession'+i+'" id="datePicker'+i+'" value="'+dateVal+'" />';
//								}
//				else
//								{
//										nameReplaceVal='dateOfSession'+i;
//										idReplaceVal='datePicker'+i;
//										var replaceSection='courseSection'+i+' copy ';
//
//										dateAppend+= dateCopyStr.replace(/xxx/g, 'copy').replace(/dateOfSession/g, nameReplaceVal).replace(/datePicker/g, idReplaceVal).replace(/copy courseSection/g, replaceSection);
//								}
//        $('#dateSelectContainer').append(dateAppend);
//        checkCompletion();
//    }
//    function updateLength()
//    {
//        $('.length.copy').remove();
//
//        var lengthAppend='';
//        var lengthIDVal = $('#lengthID').val();
//        var lengthSelectStr = $('#lengthSelectContainer').html();
//        var lengthCopyStr = '<br class="copy length" />'+lengthSelectStr.replace(/xxx/g, 'copy');
//
//				if($('#sameLengths').is(':checked'))
//				{
//						//alert('lengthIDVal: '+lengthIDVal);
//						lengthAppend+='<input id="lengthID'+i+'" class="length copy" type="hidden" name="lengthID'+i+'" value="'+lengthIDVal+'" />';
//				}
//				else
//				{
//						replaceVal='lengthID'+i;
//						var replaceSection='courseSection'+i+' copy ';
//						lengthAppend+= lengthCopyStr.replace(/lengthID/g, replaceVal).replace(/copy courseSection/g, replaceSection);
//				}
//
//        $('#lengthSelectContainer').append(lengthAppend);
//        checkCompletion();
//    }
//
//
//    function updateStudentCount()
//    {
//
//        var numberOfStudentsAppend='';
//
//        var numberSelectStr = $('#numberOfStudentsContainer').html();
//        var numberCopyStr = '<br class="copy" />'+numberSelectStr.replace(/xxx/g, 'copy');
//
//				var replaceVal='numberOfStudents'+i;
//				var replaceSection='courseSection'+i+' copy ';
//				numberOfStudentsAppend+=numberCopyStr.replace(/numberOfStudents/g, replaceVal).replace(/copy courseSection/g, replaceSection);
//        $('#numberOfStudentsContainer').append(numberOfStudentsAppend);
//
//    }
//
//    function updateResources()
//    {
//        $('.resourcesBox.copy').remove();
//        $('.resourcesbox.copy').remove();
//
//        var resourcesSelectStr=$('#resourcesSelectContainer').html().replace(/xxx/g, 'copy');
//        var resourcesIntroducedAppend='';
//        var replaceSection='courseSection'+i+' copy ';
//
//        if($('#sameResources').is(':checked'))
//        {
//            var selectedBoxes = $('input.resourcesBox');
//            $.each(selectedBoxes, function(){
//                if($(this).attr('checked'))
//                {
//                thisVal=$(this).val();
//
//                if (thisVal !='none')
//                    {
//                resourcesIntroducedAppend+='<input class="copy resourcesBox" type="hidden" name="resourcesIntroduced'+i+'[]"  value="'+thisVal+'" />';}
//
//                else {resourcesIntroducedAppend+='<input class="copy resourcesBox" type="hidden" name="resourcesIntroduced'+i+'"  value="'+thisVal+'" />';}
//                }
//
//            });
//
//        }
//        else
//        {
//            resourcesIntroducedAppend+= '<br class="copy resourcesbox resourcesbox'+i+'" /><hr class="resourcesbox copy" /> '+resourcesSelectStr.replace(/resourcesIntroduced/g, 'resourcesIntroduced'+i).replace(/copy courseSection/g, replaceSection) ;
//        }
//
//        $('#resourcesSelectContainer').append(resourcesIntroducedAppend);
//        checkCompletion();
//
//    }
//
//    function updateNotes()
//    {
//        $('.notebox.copy').remove();
//        var noteSelectAppend='';
//        var sessionNoteVal = $('#sessionNote').val();
//        var noteSelectStr =$('#noteSelectContainer').html();
//        var noteSelectCopy = noteSelectStr.replace(/xxx/g, "copy");
//
//				var replaceSection='courseSection'+i+' copy ';
//				if($('#sameNotes').is(':checked'))
//				{
//						noteSelectAppend+='<input id="sessionNote'+i+'" class="copy notebox" type="hidden" name="sessionNote'+i+'"  value="'+sessionNoteVal+'" />';
//				}
//
//				else
//				{
//						noteSelectAppend+= '<br class="copy" /> '+noteSelectCopy.replace(/id="sessionNote"/g, 'id="sessionNote'+i+'" ').replace(/name="sessionNote"/g, 'name="sessionNote'+i+'"').replace(/copy courseSection/g, replaceSection) ;
//				}
//        $('#noteSelectContainer').append(noteSelectAppend);
//        checkCompletion();
//    }

			// This function is unused (assessOutcome dropdowns default to 0)
			// see getOutcomesToAssess() in InstructionSession.php
//      function assessmentDropdown()
//      {
//            var mustHave = $('.assessmentDropDown')
//            var mustHaveCount = mustHave.length;
//            var mustHaveRemaining = 0
//
//
//            $.each(mustHave, function()
//                    {
//                        if($(this).val()=="")
//                        {
//                        mustHaveRemaining++;
//                        }
//                        else{}
//
//                    });
//               // alert('change!!!   /n mustHave='+mustHaveCount+'  mustHaveRemaining='+mustHaveRemaining);
//            if (mustHaveRemaining<=0){$('#assessSubmit').removeAttr('disabled');}
//            else{$('#assessSubmit').attr('disabled', true);}
//      }

//        function noneOrSome() {
//					if ($('.none').is(':checked')) {
//						$('.notNone').attr('checked', false);
//						$('.notNone').attr('disabled', 'disabled');
//					} else {
//						$('.notNone').removeAttr('disabled');
//					}
//
//					if ($('.notNone').is(':checked')) {
//						$('.none').attr('checked', false);
////						$('.none').attr('disabled', 'disabled');
//					} else {
//						$('.none').removeAttr('disabled');
//					}
//				}

$('input[type="number"]').on('change', function() { checkCompletion(); });

function checkCompletion() {
	var ret = true;
	$('input[type="number"]').each(function(){
		var $e = $(this);
		if($e.attr('required') && parseInt($e.val())) {
			$e.css({'border': '', 'background-color': ''});
//			return true;
		} else {
			$e.css({'border': '2px solid red', 'background-color': '#fcc'});
			$e.focus();
			ret = false;
		}
	});
	return ret;
}

$('input[type="submit"]').click(function($e){
	if(checkCompletion()) {
		return true;
	} else {
		$e.preventDefault();
		return false;
	};
});

// https://jqueryui.com/autocomplete/#combobox
(function($) {
	$.widget("custom.combobox", {
		_create: function () {
			this.wrapper = $("<span>")
				.addClass("custom-combobox")
				.insertAfter(this.element);

			this.element.hide();
			this._createAutocomplete();
			this._createShowAllButton();
		},
		_createAutocomplete: function () {
			var selected = this.element.children(":selected"),
				value = selected.val() ? selected.text() : "";

			this.input = $("<input>")
				.appendTo(this.wrapper)
				.val(value)
				.attr("title", "")
				.addClass("custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left")
				.autocomplete({
					delay: 0,
					minLength: 0,
					source: $.proxy(this, "_source")
				})
				.tooltip({
					tooltipClass: "ui-state-highlight"
				});

			// Added by Webster
			if(this.element.attr('required')) { this.input.attr('required', 'required'); }

			this._on(this.input, {
				autocompleteselect: function (event, ui) {
					ui.item.option.selected = true;
					this._trigger("select", event, {
						item: ui.item.option
					});
				},
				autocompletechange: "_removeIfInvalid"
			});
		},
		_createShowAllButton: function () {
			var input = this.input,
				wasOpen = false;

			$("<a>")
				.attr("tabIndex", -1)
//				.attr("title", "Show All Items")
//				.tooltip()
				.appendTo(this.wrapper)
				.button({
					icons: {
						primary: "ui-icon-triangle-1-s"
					},
					text: false
				})
				.removeClass("ui-corner-all")
				.addClass("custom-combobox-toggle ui-corner-right")
				.mousedown(function () {
					wasOpen = input.autocomplete("widget").is(":visible");
				})
				.click(function () {
					input.focus();

					// Close if already visible
					if (wasOpen) {
						return;
					}

					// Pass empty string as value to search for, displaying all results
					input.autocomplete("search", "");
				});
		},
		_source: function (request, response) {
			var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
			response(this.element.children("option").map(function () {
				var text = $(this).text();
				if (this.value && (!request.term || matcher.test(text)))
					return {
						label: text,
						value: text,
						option: this
					};
			}));
		},
		_removeIfInvalid: function (event, ui) {

			// Selected an item, nothing to do
			if (ui.item) {
				return;
			}

			// Search for a match (case-insensitive)
			var value = this.input.val(),
				valueLowerCase = value.toLowerCase(),
				valid = false;
			this.element.children("option").each(function () {
				if ($(this).text().toLowerCase() === valueLowerCase) {
					this.selected = valid = true;
					return false;
				}
			});

			// Found a match, nothing to do
			if (valid) {
				return;
			}

			// Remove invalid value
			this.input
				.val("")
				.attr("title", value + " didn't match any item")
				.tooltip("open");
			this.element.val("");
			this._delay(function () {
				this.input.tooltip("close").attr("title", "");
			}, 2500);
			this.input.autocomplete("instance").term = "";
		},
		_destroy: function () {
			this.wrapper.remove();
			this.element.show();
		}
	});
})(jQuery);





