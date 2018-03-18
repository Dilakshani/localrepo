$(document).ready(function(){
	$.ajax({
		url: "http://localhost/apnic/data.php",
		method: "GET",
		success: function(data) {
			console.log(data);
                        var cc = [];
                        var value = [];

			for(var i in data) {
                                cc.push(data[i].cc);
                                value.push(data[i].value);
			}

			var chartdata = {
				labels: cc,
				datasets : [
					{
						label: 'Count ',
						backgroundColor: 'rgba(0,191,255, 0.75)',
						borderColor: 'rgba(0,191,255, 0.75)',
						hoverBackgroundColor: 'rgba(0,191,255, 1)',
						hoverBorderColor: 'rgba(0,191,255, 1)',
						data: value
					}
				]
			};

			var ctx = $("#mycanvas");

			var barGraph = new Chart(ctx, {
				type: 'bar',
				data: chartdata
			});
		},
		error: function(data) {
			console.log(data);
		}
	});
});
