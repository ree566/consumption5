
angular.module("App", ["ui.bootstrap", "ezdialog"])
	.controller("Main", ["$scope", "$http", "ezdialog", function($scope, $http, dialog){
	
		$scope.input = {
			oldPass: "",
			newPass: "",
			check: ""
		};
		
		$scope.data = {
			user: window.user
		};
		
		$scope.changePass = function(){
			if ($scope.input.newPass != $scope.input.check) {
				dialog.error("新密碼和確認欄位不相同");
				return;
			}
			
			var json = {
				command: "set-pass",
				new_pass: $scope.input.newPass,
				old_pass: $scope.input.oldPass
			};
			
			// console.log(json);
			
			var success = function(){
				dialog.show("密碼設定成功！請重新登入").close(function(){
					location.href = "logout.php";
				});
			};
			
			var fail = function(json){
				dialog.error("密碼設定失敗！" + json.error);
			};
			
			myHttp($http, $scope, json, success, fail);
		};
		
	}]);
