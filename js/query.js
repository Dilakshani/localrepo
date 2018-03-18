
$(document).ready(function(e) {
    $('#menu_icon').click(function(){
        if($("#content_details").hasClass('drop_menu'))
		{
        $("#content_details").addClass('drop_menu1').removeClass('drop_menu');
    }
		else{
			$("#content_details").addClass('drop_menu').removeClass('drop_menu1');
			}
	
	
	});
	
});
