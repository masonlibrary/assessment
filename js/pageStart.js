

function pageStart(){
                        //uncheck any radio/checkbox inputs
			$('input[type="radio"]').prop('checked', false);
			//$('input[type="checkbox"]').prop('checked', false);	// disabled to allow pre-population -Webster
                        pageUpdate();
                        if ($('#slider').hasClass('open') && window.location.pathname!='/assessment/index.php')
                        {
//                            $('#tabzilla-contents').slideUp(400);
//                            $('#tabzilla-contents').hide();
//                            $('body').animate(
//                            {"margin-top": ['-=193px', 'swing']},
//                                "400");
                            $('#slider').removeClass('open').addClass('closed')
                        }


//                       $('#dialog').dialog({
//                                            modal: true,
//                                            minWidth: 400,
//                                            maxWidth: 700,
//                                            maxHeight: 200,
//                                            position: [500,350]});
			}


function pageUpdate() {

                $(':input').addClass('ui-widget')
                $('.courseInfo').addClass('ui-widget')
                $('.courseSection').addClass('ui-widget')
                $('.selectBox').addClass('ui-widget')



}
