

function pageStart(){ 
                        //uncheck any radio/checkbox inputs
			$('input[type="radio"]').prop('checked', false);
			$('input[type="checkbox"]').prop('checked', false);
                        pageUpdate();
                        if ($('#slider').hasClass('open') && window.location.pathname!='/assessment/index.php')
                        {
                            $('#tabzilla-contents').slideUp(400);
                            $('body').animate(
                            {"margin-top": ['-=193px', 'swing']},
                                "400");
                            $('#slider').removeClass('open').addClass('closed')
                        }
                        
                    
                       $('#dialog').dialog({ 
                                            modal: true,
                                            minWidth: 400,
                                            maxWidth: 700,
                                            maxHeight: 200, 
                                            position: [500,350]});
                        
                    
                            var numberOfCopies =$('select#numberOfCopies').selectmenu({
                                width: 50,
                                menuWidth: 400
				});    
                            numberOfCopies.selectmenu("disable");
                        
			}


function pageUpdate() {
    
                $(':input').addClass('ui-widget')
                $('.courseInfo').addClass('ui-widget')
                $('.courseSection').addClass('ui-widget')
                $('.selectBox').addClass('ui-widget')
    
    
        
}
//TODO: set up messaging variable in footer include so there is a per-page message of either *nothing* or a string to display