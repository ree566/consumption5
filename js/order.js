
var orderApp = angular.module("App", ["ui.bootstrap", "ezdialog"])
	.controller("OrderPage", function($scope, $http, ezdialog){
		var dialog = ezdialog;
	
		$scope.data = {
			itemList: window.itemList,
			orderList: window.orderList
		};
		
		$scope.input = {
			index: 0
		};
		
		$scope.checkKey = function(e) {
			if (e.keyCode == 38) {
				$scope.input.index += $scope.input.itemListFiltered.length - 1;
			} else if (e.keyCode == 40) {
				$scope.input.index += 1;
			} else {
				return;
			}
			$scope.input.index %= $scope.input.itemListFiltered.length;
			e.preventDefault();
		};
		
		$scope.$watch("input.itemListFiltered.length", function(l){
			if (l && l <= $scope.input.index) {
				$scope.input.index = l - 1;
			}
		});
	
		function updateOrder(){
			var json = {
				command: "get-order-team"
			};
			
			function success(json){
				$scope.data.orderList = json.data;
			}
			
			function fail(json){
				// console.log("update order fail", json);
				dialog.error("申請列表有更新，請重新整理！" + json.error);
			}
			
			myHttp($http, null, json, success, fail);
		}

		var ws = wsConnect({
            url: "ws://" + location.host + ":" + window.WS_PORT + window.WS_PATH,
			open: function(){
				var sessid = document.cookie.match(/PHPSESSID=([^;]*)/)[1];
				var json = {
					command: "login",
					sessid: sessid
				};
				
				json = JSON.stringify(json);
				
				this.send(json);
			},
			message: function(msg){
                console.log(msg);
                var receive = JSON.parse(msg);
                if (receive.hasOwnProperty('command') && receive.command == "update-order") {
                    console.log("update order...");
                    updateOrder();
                }
			}
		});

		function wsNewOrder(){
			var json = {
				command: "update-order"
			};

			json = JSON.stringify(json);
			
			ws.send(json);
		}
		
		$scope.submit = function(){
			var item = $scope.input.searchTerm && $scope.input.itemListFiltered[$scope.input.index] || null,
				number = $scope.input.number;
			
			if (!item) {
				dialog.error("找不到此項目");
				return;
			}
			
			var json = {
				command: "add-new-order",
				item_id: item.id,
				number: number
			};
			
			var success = function(json){
				$scope.input.number = null;
				$scope.data.orderList = json.data;
				wsNewOrder();
			};
			
			var fail = function(json){
				console.log(json);
				dialog.error("申請失敗！" + json.error);
			};
			
			dialog.confirm("確定送出申請？\n" + item.list_key + " " + item.name + " " + item.spec + " 總共 " + number + " " + item.dimension).ok(function(){
				this.close();
				myHttp($http, $scope, json, success, fail);
			});
		};
		
		$scope.notCheckout = function(order){
			return order.checkout_time == "0000-00-00 00:00:00";
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
