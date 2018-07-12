
angular.module("App", ["ui.bootstrap", "ezdialog", "ngAnimate"])
	.controller("Main", ["$scope", "$http", "ezdialog", "$filter", function($scope, $http, dialog, $filter){
	
		var filter = $filter("filter");
	
		$scope.input = {};
		
		$scope.data = {
			users: window.users,
			teams: window.teams,
			floors: window.floors,
			user: window.user
		};
		
		$scope.stage = {};
		
		$scope.addUser = function(){
			var user = {
				id: null,
				permission: 2,
				name: null,
				team_id: $scope.data.user.team_id
			};
		
			dialog.confirm({
				title: "新增使用者",
				template: "edit-user.html",
				param: {
					user: user,
					teams: $scope.data.teams,
					floors: $scope.data.floors,
					my: $scope.data.user
				}
			}).ok(function(){
				var d = this, i, l = $scope.data.users;
				
				for (i = 0; i < l.length; i++) {
					if (l[i].id == user.id) {
						dialog.error("工號重覆！");
						return;
					}
				}
				
				var json = {
					command: "add-user",
					user: user
				};
				
				function success(json){
					d.close();
					$scope.data.users = json.data;
				}
				
				function fail(json){
					console.log(json);
					dialog.error("新增失敗！" + json.error);
				}
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.editUser = function(user){
			if (user.permission >= $scope.data.user.permission) {
				dialog.error("權限不足");
				return;
			}
		
			user = angular.copy(user);
			
			dialog.confirm({
				title: "編輯",
				template: "edit-user.html",
				param: {
					user: user,
					floors: $scope.data.floors,
					teams: $scope.data.teams,
					my: $scope.data.user,
					edit: true
				}
			}).ok(function(){
				var d = this;
				
				var json = {
					command: "set-user",
					user: user
				};
				
				function success(json){
					d.close();
					$scope.data.users = json.data;
				}
				
				function fail(json){
					console.log(json);
					dialog.error("儲存失敗！" + json.error);
				}
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.deleteUser = function(user){
			if (user.permission >= $scope.data.user.permission) {
				dialog.error("權限不足");
				return;
			}
			
			dialog.confirm("確定要刪除「" + user.name + "」？").ok(function(){
				var d = this;
				
				var json = {
					command: "delete-user",
					user_id: user.id
				};
				
				function success(json){
					d.close();
					$scope.data.users = json.data;
				}
				
				function fail(json){
					console.log(json);
					dialog.error("刪除失敗！" + json.error);
				}
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.addTeam = function(){
			var team = {
				floor_id: $scope.data.user.floor_id,
				name: null
			};
			
			dialog.confirm({
				title: "新增區塊",
				template: "edit-team.html",
				param: {
					team: team,
					floors: $scope.data.floors,
					my: $scope.data.user
				}
			}).ok(function(){
				var d = this;
				var json = {
					command: "add-team",
					team: team
				};
				
				var success = function(json){
					d.close();
					$scope.data.teams = json.data;
				};
				
				var fail = function(json){
					console.log(json);
					dialog.error("新增部門失敗！" + json.error);
				};
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.editTeam = function(team){
			team = angular.copy(team);
			
			dialog.confirm({
				title: "修改",
				template: "edit-team.html",
				param: {
					team: team,
					floors: $scope.data.floors,
					my: $scope.data.user
				}
			}).ok(function(){
				var json = {
						command: "set-team",
						team: team
					},
					d = this;
				
				var success = function(json){
					d.close();
					$scope.data.teams = json.data;
				};
				
				var fail = function(json){
					console.log(json);
					dialog.error("新增部門失敗！" + json.error);
				};
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.deleteTeam = function(team){
			dialog.confirm("確定要刪除「" + team.name + "」？").ok(function(){
				var json = {
						command: "delete-team",
						team_id: team.id
					},
					d = this;
				
				var success = function(json){
					d.close();
					$scope.data.teams = json.data;
				};
				
				var fail = function(json){
					console.log(json);
					dialog.error("刪除部門失敗！" + json.error);
				};
				
				myHttp($http, null, json, success, fail);
			});
		};
		
		$scope.importUser = function(){
			var text = $scope.input.importText,
				i, 
				u, 
				users = [], 
				my = $scope.data.user, 
				user, teams;
			
			text = text.split(/\r?\n/);
			for (i = 0; i < text.length; i++) {
				if (!text[i]) {
					continue;
				}
				u = text[i].split(/\s+/);
				
				if (!u[1]) {
					dialog.error("格式錯誤︰\n" + text[i]);
					return;
				}
				
				if (!(user = filter($scope.data.users, {id: u[0]}, true)[0])) {
					// add
					user = {
						id: u[0],
						name: u[1],
						team_id: my.team_id,
						permission: 2
					};
				} else if (my.permission < 5 && user.floor_id != my.floor_id) {
					dialog.error("工號重覆︰" + user.floor_name + u[0] + "\n" + text[i]);
					return;
				} else if (user.permission >= my.permission) {
					dialog.error("權限不足︰" + user.permission + "\n" + text[i]);
					return;
				} else {
					// edit
					user = angular.copy(user);
				}
				
				user.name = u[1];
				
				teams = $scope.data.teams;
				if (u[2]) {
					teams = filter(teams, {"name": u[2]}, true);
					if (!teams.length) {
						dialog.error("無此區塊︰" + u[2] + "\n" + text[i]);
						return;
					}
				}
				
				if (u[3]) {
					if (u[3] >= my.permission) {
						dialog.error("權限不足︰" + u[3] + "\n" + text[i]);
						return;
					}
					user.permission = u[3] * 1;
				}
				
				if (u[4]) {
					if (my.permission < 5 && u[4] != my.floor_name) {
						dialog.error("樓層不同︰" + u[4] + "\n" + text[i]);
						return;
					}
				
					teams = filter(teams, {"floor_name": u[4]}, true);
					if (!teams.length) {
						dialog.error("無此區塊︰" + u[4] + u[2] + "\n" + text[i]);
						return;
					}
					user.team_id = teams[0].id;
				} else if (u[2]) {
					teams = filter(teams, {"floor_id": my.floor_id}, true);
					if (!teams.length) {
						dialog.error("無此區塊︰" + my.floor_name + u[2] + "\n" + text[i]);
						return;
					}
					user.team_id = teams[0].id;
				}
				
				users.push(user);
			}
			
			console.log(users);
			
			var json = {
				command: "set-users",
				users: users
			};
			
			var success = function(json){
				dialog.show("匯入成功！");
				$scope.data.users = json.data;
				$scope.input.importText = "";
			};
			
			var fail = function(json){
				console.log(json);
				dialog.error("匯入失敗！" + json.error);
			};
			
			myHttp($http, null, json, success, fail);
		};
		
	}]);
