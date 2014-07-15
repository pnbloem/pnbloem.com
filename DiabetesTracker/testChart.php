<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>

.bar {
  fill: steelblue;
}

.bar:hover {
  fill: brown;
}

.axis {
  font: 10px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

</style>
<body>
<script src="../js/jquery-2.1.0.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>
var chartdata = {}
function plotMeasurements(){
	$.ajax({
		type: "POST",
		url: "http://pnbloem.com/DiabetesTracker/api/api.php",
		data: {
			method: 'getMeasurements',
			start: 0,
			limit: 1500
		},
		datatype: 'json',
		success: function(data){	
			data = JSON.parse(data).rows;
			chartdata = data;
			var margin = {top: 20, right: 20, bottom: 30, left: 40},
				width = 960 - margin.left - margin.right,
				height = 500 - margin.top - margin.bottom;
			
			var x = d3.scale.ordinal()
			    .rangeRoundBands([0, width], .1);
			
			var y = d3.scale.linear()
			    .range([height, 0]);
			
			var xAxis = d3.svg.axis()
			    .scale(x)
			    .orient("bottom");
			
			var yAxis = d3.svg.axis()
			    .scale(y)
			    .orient("left")
			    .ticks(10);
			
			var svg = d3.select("body").append("svg")
			    .attr("width", width + margin.left + margin.right)
			    .attr("height", height + margin.top + margin.bottom)
			  .append("g")
			    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			  x.domain(data.map(function(d) { return d.Timestamp; }));
			  y.domain([0, d3.max(data, function(d) { return parseInt(d.GlucoseLevel); })]);
			
			  svg.append("g")
			      .attr("class", "x axis")
			      .attr("transform", "translate(0," + height + ")")
			      .call(xAxis);
			
			  svg.append("g")
			      .attr("class", "y axis")
			      .call(yAxis)
			    .append("text")
			      .attr("transform", "rotate(-90)")
			      .attr("y", 6)
			      .attr("dy", ".71em")
			      .style("text-anchor", "end")
			      .text("GlucoseLevel");
			
			  svg.selectAll(".bar")
			      .data(data)
			    .enter().append("rect")
			      .attr("class", "bar")
			      .attr("x", function(d) { return x(d.Timestamp); })
			      .attr("width", x.rangeBand())
			      .attr("y", function(d) { return y(parseInt(d.GlucoseLevel)); })
			      .attr("height", function(d) { return height - y(parseInt(d.GlucoseLevel)); });
				
		}
	});	
}

function type(d) {
  d.frequency = +d.frequency;
  return d;
}
plotMeasurements();

</script>
</head>
<body>
	Test
</body>
</html>