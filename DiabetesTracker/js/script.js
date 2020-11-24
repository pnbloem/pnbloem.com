//Bind functions to events when page is loaded.
var startRecs = 0;
var numRecs = 10;

$(function() {
	$('#moreRowsButton').click(function(){
		getMeasurements(startRecs, 5);
	});
	$("#chart").mouseout(function(){
		$("#tooltip").remove();
		previousPoint = null;
	});
	$('#newtime').change(function(){
		guessType(parseInt($('#newtime').val().substring(11, 13)));
	});
	plotMeasurements();
});

function getMeasurements(start, countLimit){
	//countLimit == 0 means get all results
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getMeasurements',
			start: start,
			limit: countLimit
		},
		datatype: 'json',
		success: function(data){
			startRecs += countLimit;
			var result = JSON.parse(data);
			for(meas in result["rows"]){
				addMeasurementToList(result["rows"][meas], 1, result["loggedIn"]);
			}
		}
	});	
}
function getAllTimeAverage(){
	//Get all-time average
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getAverage',
			num_days: 0,
                        userid: 1
		},
		datatype: 'json',
		success: function(data){
			var response = JSON.parse(data);
			if(response['status'] == 'Success'){
				$('#averageLevel').text("Average: " + response['average'] + " mg/dL");
			} else {
				$('#averageLevel').text("Error retrieving average.");
			}
		}
	});	
}
function getThreeMonthAverage(){
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getAverage',
			num_days: 90,
                        userid: 1
		},
		datatype: 'json',
		success: function(data){
			var response = JSON.parse(data);
			if(response['status'] == 'Success'){
				$('#threeMonthAvg').text("3 Month Average: "+response['average'] + " mg/dL");
			} else {
				$('#threeMonthAvg').text("Error retrieving average.");
			}
		}
	});
}
function getOneMonthAverage(){
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getAverage',
			num_days: 30,
                        userid: 1
		},
		datatype: 'json',
		success: function(data){
			var response = JSON.parse(data);
			if(response['status'] == 'Success'){
				$('#oneMonthAvg').text("1 Month Average: "+response['average'] + " mg/dL");
			} else {
				$('#oneMonthAvg').text("Error retrieving average.");
			}
		}
	});
}
function getOneWeekAverage(){
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getAverage',
			num_days: 7,
                        userid: 1
		},
		datatype: 'json',
		success: function(data){
			var response = JSON.parse(data);
			if(response['status'] == 'Success'){
				$('#oneWeekAvg').text("1 Week Average: "+response['average'] + " mg/dL");
			} else {
				$('#oneWeekAvg').text("Error retrieving average.");
			}
		}
	});
}

//m is the array with measurement data. Position is 0 for beginning of list, 1 for end of list.
function addMeasurementToList(m, position, loggedIn){
	var measurement = "<measurement-item ";
	measurement += "id='" + m['MeasID'] + "' ";
	measurement += "measID='" + m['MeasID'] + "' ";
	measurement += "type='" + m['CheckCategory'] + "' ";
	measurement += "time='" + m['Timestamp'] + "' ";
	measurement += "level='" + m['GlucoseLevel'] + "' ";
	measurement += "insulinType='" + m['InsulinType'] + "' ";
	measurement += "insulinAmt='" + m['InsulinAmt'] + "' ";
	measurement += "loggedIn='" + loggedIn + "' ";
	measurement += "userid='" + m['UserID'] + "' ";
	measurement += "</measurement-item>";
	if(position === 0){
		$("#measurements-list").prepend(measurement);
	} else {
		$("#measurements-list").append(measurement);
	}
}
function refreshItems(){
	getAllTimeAverage();
	getThreeMonthAverage();
	getOneMonthAverage();
	getOneWeekAverage();
	plotMeasurements();
}

function colorRow(m){
	//Color the row based on whether it's in the good range or not.
	var levelCell = $('#'+m['MeasID']).find('.glucoselevel');
	levelCell.removeClass('bad closeLow closeHigh good');
	if(m['CheckCategory'] == 'Before Meal'){
		if(m['GlucoseLevel'] < 60 || m['GlucoseLevel'] > 140){
			levelCell.addClass('bad');
		} else if(m['GlucoseLevel'] < 80){
			levelCell.addClass('closeLow');
		} else if(m['GlucoseLevel'] > 120){
			levelCell.addClass('closeHigh');
		} else {
			levelCell.addClass('good');
		}
	} else if(m['CheckCategory'] == 'Before Bed'){
		if(m['GlucoseLevel'] < 80 || m['GlucoseLevel'] > 160){
			levelCell.addClass('bad');
		} else if(m['GlucoseLevel'] < 100){
			levelCell.addClass('closeLow');
		} else if(m['GlucoseLevel'] > 140){
			levelCell.addClass('closeHigh');
		} else {
			levelCell.addClass('good');
		}
	}
}
function plotMeasurements(){
	$.ajax({
		type: "POST",
		url: "api/api.php",
		data: {
			method: 'getMeasurements',
			start: 0,
			limit: 1500
		},
		datatype: 'json',
		success: function(data){
			var points = [];
			var resp = JSON.parse(data)["rows"];
			for(meas in resp){
				var pt = resp[meas];
				var plotPt = []
				var utc = Date.UTC(pt['Timestamp'].substr(0,4), 
								   parseInt(pt['Timestamp'].substr(5,2)-1),
								   pt['Timestamp'].substr(8,2),
								   pt['Timestamp'].substr(11,2),
								   pt['Timestamp'].substr(14,2),
								   pt['Timestamp'].substr(17,2));
				plotPt.push(utc);
				plotPt.push(pt['GlucoseLevel']);
				points.push(plotPt);
			}
			
			/* 
				Get timestamp of four weeks ago. This is used
				to chart only the last four weeks, allowing for all measurements
				to be viewed using the summary chart below.
			*/
			var now = new Date();
			var minusFourWeeks = now - 2419200000; 
			
			var options = {
				series:{
					points: { show: true },
					bars: { show:true, lineWidth:2}
				},
				grid: { hoverable: true, clickable: true },
				colors: ["#12375C"],
				xaxis: {mode:"time", max: now, min: minusFourWeeks }
			};
			var plot = $.plot($('#chart'), [ points ], options);
			var overview = $.plot($("#overview"), [ points ], {
				series: {
					lines: { show: true, lineWidth: 1 },
					shadowSize: 0
				},
				xaxis: { ticks: [], mode: "time" },
				yaxis: { ticks: [], min: 0, autoscaleMargin: 0.1 },
				colors: ["#12375C"],
				selection: { mode: "x", color:"#AAAAAA" }
			});
			$("#chart").bind("plotselected", function (event, ranges) {
				// do the zooming
				plot = $.plot($("#chart"), [ points ],
							  $.extend(true, {}, options, {
								  xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
							  }));
		
				// don't fire event on the overview to prevent eternal loop
				overview.setSelection(ranges, true);
			});
			
			$("#overview").bind("plotselected", function (event, ranges) {
				plot.setSelection(ranges);
			});
			previousPoint = null;
			$("#chart").bind("plothover", function(event, pos, item){
				if(item){
					if(previousPoint != item.dataIndex){
						previousPoint = item.dataIndex;							
						$("#tooltip").remove();
						var x = item.datapoint[0].toFixed(2),
							y = item.datapoint[1].toFixed(2);
						
						showToolTip(item.pageX, item.pageY, y.substr(0,y.length - 3));
					}
				} else {
					//$("#tooltip").remove();
					//previousPoint = null;
				}
			});
		}
	});	
}
function showToolTip(x, y, contents){
	$('<div id="tooltip">' + contents + '</div>').css({
		position: 'absolute',
		display: 'none',
		top: y-30,
		left: x-5,
		border:'1px solid black',
		"background-color": "#12375C",
		color: 'white',
		padding:'3px',
		'font-weight': 'bold',
		cursor: 'default'
	}).appendTo("body").fadeIn(200);
}
