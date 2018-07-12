
angular.module("App", ["ui.bootstrap", "ezdialog", "ngAnimate"])
	.factory("resource", function(){
		var init = {
			floors: window.floors,
			user: window.user,
			items: window.items
		};
		
		return init;
	})
	.controller("floors", function($scope, ezdialog, resource, $http){
		
		$scope.resource = resource;
		
		$scope.add = function(){
			var floor = {
				name: null
			};
			
			ezdialog.confirm({
				title: "新增樓層",
				template: "edit-floor.html",
				param: floor
			}).ok(function(){
				var dialog = this;
					
				myHttp(
					$http, 
					null,
					{
						command: "add-floor",
						floor: floor
					},
					function(json){
						dialog.close();
						resource.floors = json.data;
					},
					function(json){
						console.log(json);
						ezdialog.error("新增失敗！" + json.error);
					}
				);
			});
		};
		
		$scope.edit = function(floor){
			floor = angular.copy(floor);
			ezdialog.confirm({
				title: "修改",
				template: "edit-floor.html",
				param: floor
			}).ok(function(){
				var dialog = this;
					
				myHttp(
					$http, 
					null,
					{
						command: "set-floor",
						floor: floor
					},
					function(json){
						dialog.close();
						resource.floors = json.data;
					},
					function(json){
						console.log(json);
						ezdialog.error("儲存失敗！" + json.error);
					}
				);
			});
		};
		
		$scope.delete = function(floor){
			ezdialog.confirm("確定要刪除「" + floor.name + "」？").close(function(ret){
				if (!ret) {
					return;
				}
				
				myHttp(
					$http, 
					null,
					{
						command: "delete-floor",
						floor_id: floor.id
					},
					function(json){
						resource.floors = json.data;
					},
					function(json){
						console.log(json);
						ezdialog.error("刪除失敗！" + json.error);
					}
				);
			});
		};
	})
	.controller("items", function($scope, resource, $filter, ezdialog){
		$scope.resource = resource;
		
		$scope.input = {};
		
		$scope.searchs = [];
		
		$scope.cache = function(){
			var title = ($scope.input.name || "") + ($scope.input.supplier || "");
			if (!title) {
				return;
			}
			
			$scope.searchs.push({
				title: title,
				item: angular.copy($scope.input)
			});
		};
		
		$scope.download = function(){
			var searchs = $scope.searchs, i, lines = [], l, j, data, link;
			
			if (!searchs.length) {
				searchs = [resource.items];
				lines.push("樓層,料號,名稱,規格,供貨商,安全水位,庫存量,單位,價格");
				l = resource.items;
				for (j = 0; j < l.length; j++) {
					lines.push(T('="{}",="{}",="{}",="{}",="{}",{},{},="{}",{}', l[j].floor_name, l[j].list_key, l[j].name, l[j].spec, l[j].supplier,l[j].low_floor, l[j].count, l[j].dimension, l[j].price));
				}
				lines.push("");
			} else {
				for (i = 0; i < searchs.length; i++) {
					lines.push("搜尋︰" + searchs[i].title);
					lines.push("樓層,料號,名稱,規格,供貨商,安全水位,庫存量,單位,價格");
					l = $filter("filter")(resource.items, searchs[i].item);
					for (j = 0; j < l.length; j++) {
						lines.push(T('="{}",="{}",="{}",="{}",="{}",{},{},="{}",{}', l[j].floor_name, l[j].list_key, l[j].name, l[j].spec, l[j].supplier,l[j].low_floor, l[j].count, l[j].dimension, l[j].price));
					}
					lines.push("");
				}
			}
			
			data = lines.join("\n");
			
			link = document.createElement("a");
			link.href = "data:text/csv;charset=utf-8," + encodeURI("\ufeff" + data);
			link.download = "items.csv";
			link.title = "items.csv";
			link.textContent = "items.csv";
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		};
	});
