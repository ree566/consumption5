/**
	chart.js helper
*/

"use strict";

function getColor(i, len){
	return chroma.hsl(Math.floor(360 * (i + 1) / len), 0.47, 0.6);
}

function triggerResize(){
	var event;
	event = document.createEvent("Event");
	event.initEvent("resize", false, false);
	window.dispatchEvent(event);
}
	
function removeLegend(){
	var s = document.querySelectorAll(".line-legend+.line-legend, .pie-legend+.pie-legend, .bar-legend+.bar-legend"), i;
	for (i = 0; i < s.length; i++) {
		s[i].parentNode.removeChild(s[i]);
	}
}
	
function createLineChartData(options){
	var timeLine = [],
		itemSet = {},
		l = options.list,
		pTime = options.xAxis,
		pCost = options.yAxis,
		pIdent = options.groupBy,
		i, j,
		color,
		chartData;
		
	l.sort(function(a, b){
		return a[pTime].localeCompare(b[pTime]);
	});
		
	for (i = 0; i < l.length; i++) {
		// time set
		var date = l[i][pTime];
		if (!timeLine.length || timeLine[timeLine.length - 1] != date) {
			timeLine.push(date);
		}
		
		// itemSet
		var name = l[i][pIdent];
		// console.log(name, i);
		if (!itemSet[name]) {
			itemSet[name] = {
				label: name,
				fillColor: "transparent",
				pointStrokeColor: "#fff",
				pointHighlightFill: "#fff",
				data: [],
				date: {}
			};
		}
		
		// itemSet-dateSet(number)
		if (!itemSet[name].date[date]) {
			itemSet[name].date[date] = 0;
		}
		itemSet[name].date[date] += l[i][pCost];
	}
	
	// chart data
	chartData = {
		labels: timeLine,
		datasets: []
	};

	for (i in itemSet) {
		chartData.datasets.push(itemSet[i]);
	}

	for (i = 0; i < timeLine.length; i++) {
		for (j = 0; j < chartData.datasets.length; j++) {
			chartData.datasets[j].data.push(
				chartData.datasets[j].date[timeLine[i]] || 0
			);
		}
	}
	
	var len = chartData.datasets.length;
	for (i = 0; i < len; i++) {
		color = getColor(i, len);
		chartData.datasets[i].pointColor = color.css();
		chartData.datasets[i].pointHighlightStroke = color.css();
		chartData.datasets[i].strokeColor = color.css();
		chartData.datasets[i].fillColor = color.alpha(0.2).css();
	}

	return chartData;
}
	
function createBarChartData(options){
	var timeLine = [],
		itemSet = {},
		l = options.list,
		pTime = options.xAxis,
		pCost = options.yAxis,
		pIdent = options.groupBy,
		i, j,
		color,
		chartData;
		
	l.sort(function(a, b){
		return a[pTime].localeCompare(b[pTime]);
	});
		
	for (i = 0; i < l.length; i++) {
		// time set
		var date = l[i][pTime];
		if (!timeLine.length || timeLine[timeLine.length - 1] != date) {
			timeLine.push(date);
		}
		
		// itemSet
		var name = l[i][pIdent];
		// console.log(name, i);
		if (!itemSet[name]) {
			itemSet[name] = {
				label: name,
				data: [],
				date: {}
			};
		}
		
		// itemSet-dateSet(number)
		if (!itemSet[name].date[date]) {
			itemSet[name].date[date] = 0;
		}
		itemSet[name].date[date] += l[i][pCost];
	}
	
	// chart data
	chartData = {
		labels: timeLine,
		datasets: []
	};

	for (i in itemSet) {
		chartData.datasets.push(itemSet[i]);
	}

	for (i = 0; i < timeLine.length; i++) {
		for (j = 0; j < chartData.datasets.length; j++) {
			chartData.datasets[j].data.push(
				chartData.datasets[j].date[timeLine[i]] || 0
			);
		}
	}
	
	var len = chartData.datasets.length;
	for (i = 0; i < len; i++) {
		color = getColor(i, len);
		// chartData.datasets[i].fillColor = color.alpha(0.5).css();
		// chartData.datasets[i].strokeColor = color.alpha(0.8).css();
		// chartData.datasets[i].highlightFill = color.alpha(0.75).css();
		// chartData.datasets[i].highlightStroke = color.alpha(1).css();
		chartData.datasets[i].fillColor = color.alpha(1).css();
		chartData.datasets[i].strokeColor = color.alpha(1).css();
		chartData.datasets[i].highlightFill = color.alpha(0.75).css();
		chartData.datasets[i].highlightStroke = color.alpha(0.8).css();
	}

	return chartData;
}
	
function createPieChartData(options){
	var l = options.list,
		groupBy = options.groupBy,
		yAxis = options.yAxis,
		chartData = [],
		itemSet = {},
		i,
		name,
		color;
		
	for (i = 0; i < l.length; i++) {
		name = l[i][groupBy];
		if (!itemSet[name]) {
			itemSet[name] = {
				value: 0,
				label: name
			};
		}
		
		itemSet[name].value += l[i][yAxis];
	}
	
	for (i in itemSet) {
		chartData.push(itemSet[i]);
	}
	
	var len = chartData.length;
	for (i = 0; i < len; i++) {
		color = getColor(i, len);
		chartData[i].color = color.css();
		chartData[i].highlight = color.alpha(0.8).css();
	}

	return chartData;
}

function filterProp(l, p, t, s){
	var l2 = [], i;
	if (s === undefined) {
		for (i = 0; i < l.length; i++) {
			if (l[i][p] != t) {
				continue;
			}
			l2.push(l[i]);
		}
	} else {
		if (typeof t == "string") {
			for (i = 0; i < l.length; i++) {
				if (l[i][p].localeCompare(t) < 0) {
					continue;
				}
				
				if (l[i][p].localeCompare(s) > 0) {
					continue;
				}
				
				l2.push(l[i]);
			}
		} else if (typeof t == "number") {
			for (i = 0; i < l.length; i++) {
				if (l[i][p] < t) {
					continue;
				}
				
				if (l[i][p] > s) {
					continue;
				}
				
				l2.push(l[i]);
			}
		} else {
			throw "can't compare value";
		}
	}
	
	return l2;
}
