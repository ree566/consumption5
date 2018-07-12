
var repositoryApp = angular.module("App", ["ui.bootstrap", "angles", "ezdialog"]);

repositoryApp.controller("mainPage", ["$scope", "$http", "ezdialog", function($scope, $http, dialog){
	
	$scope.input = {
		timeStart: new Date(),
		timeEnd: new Date(),
		search: {}
	};
	
	$scope.input.timeStart.setDate(1);
	
	$scope.chart = {
		select: {
			type: "bar",
			time: "date",
			groupBy: "item_name"
		},
		selectOptions: {
			type: [
				{
					name: "長條圖",
					value: "bar"
				}, 
				{
					name: "線圖",
					value: "line"
				}
			],
			time: [
				{
					name: "計日",
					value: "date"
				},
				{
					name: "計月",
					value: "month"
				}
			],
			groupBy: [
				{
					name: "項目",
					value: "item_name"
				},
				{
					name: "部門",
					value: "team_name"
				}
			]
		},
		type: 0,
		title: "這個月的消耗",
		barData: {
			labels: [],
			datasets: []
		},
		lineData1: {
			labels: [],
			datasets: []	
		},
		lineData2: {
			labels: [],
			datasets: []	
		},
		pieData: {
			data: []
		},
		lineOptions: {
			legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"dist\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><span class=\"chart-label\"><%if(datasets[i].label){%><%=datasets[i].label%><%}%></span></li><%}%></ul>",
			tooltipTemplate: "<%= value %>",
			multiTooltipTemplate: "<%=datasetLabel%> <%= value %>"
		},
		barOptions: {
			legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span class=\"dist\" style=\"background-color:<%=datasets[i].strokeColor%>\"></span><span class=\"chart-label\"><%if(datasets[i].label){%><%=datasets[i].label%><%}%></span></li><%}%></ul>",
			tooltipTemplate: "<%= value %>",
			multiTooltipTemplate: "<%=datasetLabel%> <%= value %>"
		},
		pieOptions: {
			legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span class=\"dist\" style=\"background-color:<%=segments[i].fillColor%>\"></span><span class=\"chart-label\"><%if(segments[i].label){%><%=segments[i].label%><%}%></span></li><%}%></ul>"
		}
	};
	
	$scope.data = {
		orderList: window.orderList,
		itemList: window.itemList,
		teamList: window.teamList,
		floors: window.floors,
		filteredList: [],
		monthlySummary: window.monthlySummary
	};
	
	$scope.allowNull = function (expected, actual) {
        if (actual === null) {
            return true;
        } else {
            // angular's default (non-strict) internal comparator
            var text = ('' + actual).toLowerCase();
            return ('' + expected).toLowerCase().indexOf(text) > -1;
        }
    };
	
	function propDate(l){
		var i;
		for (i = 0; i < l.length; i++) {
			l[i].date = getDate(l[i].checkout_time);
		}
		return l;
	}
	
	function propMonth(l){
		var i;
		for (i = 0; i < l.length; i++) {
			l[i].month = getMonth(l[i].checkout_time);
		}
		return l;
	}

	$scope.$watch("data.filteredList", updateChart, true);
	$scope.$watch("chart.select", updateChart, true);
	
	function updateChart(){
		setTimeout(removeLegend, 0);
		fillChartData();
	}
	
	$scope.isPositive = function(order){
		return order.number > 0;
	};
	
	$scope.noReject = function(order){
		return !order.reject;
	};
	
	function fillChartData(){
		var l = $scope.data.filteredList, arg = $scope.chart.select;
		
		if (arg.time == "date") {
			l = propDate(l);
		} else {
			l = propMonth(l);
		}
		
		$scope.chart.lineData = createLineChartData({
			list: l,
			xAxis: arg.time,
			yAxis: "number",
			groupBy: arg.groupBy
		});
		
		$scope.chart.barData = createBarChartData({
			list: l,
			xAxis: arg.time,
			yAxis: "number",
			groupBy: arg.groupBy
		});
		
		$scope.chart.pieData = createPieChartData({
			list: l,
			yAxis: "number",
			groupBy: arg.groupBy
		});
	}
	
	$scope.triggerResize = triggerResize;
	
	$scope.updateOrder = function(){
		var json = {
			command: "get-order-time",
			time_start: getDate($scope.input.timeStart) + " 00:00:00",
			time_end: getDate($scope.input.timeEnd) + " 23:59:59"
		};
		
		function success(json){
			$scope.data.orderList = json.data;
		}
		
		function fail(json){
			console.log(json);
			dialog.error("取得資料失敗！" + json.error);
		}
		
		myHttp($http, $scope, json, success, fail);
	};
	
}]).filter("positiveNumber", function(){
	return function(l){
		var arr = [], i;
		for (i = 0; i < l.length; i++) {
			if (l[i].number > 0) {
				arr.push(l[i]);
			}
		}
		return arr;
	};
}).filter("orderStatus", function(){
	return function(s){
		if (s.reject) {
			return "拒絕";
		}
		if (s.checkout_time == "0000-00-00 00:00:00") {
			return "未核可";
		}
		return s.checkout_time;
	};
});
