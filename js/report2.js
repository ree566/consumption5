var repositoryApp = angular.module("App", ["ui.bootstrap", "angles", "ezdialog"]);

repositoryApp.controller("report2", ["$scope", "$http", "$location", "ezdialog", function ($scope, $http, $location, dialog) {

    var timeEnd = new Date();
    var timeStart = new Date();
    timeStart = new Date(timeStart.setDate(timeStart.getDate() - 5));

    $scope.input = {
        timeStart: timeStart,
        timeEnd: timeEnd,
        search: {
            floor_id: -1
        }
    };

    $scope.data = {
        floors: window.floors
    };

    $scope.findReport = function () {

        var json = {
            command: "get-order-floor-history",
            floor: $scope.input.search.floor_id,
            time_start: getDate($scope.input.timeStart) + " 00:00:00",
            time_end: getDate($scope.input.timeEnd) + " 23:59:59"
        };

        if (json.floor == null) {
            json.floor = -1;
        }

        function success(json) {
            $scope.data.orderList = json.data;
        }

        function fail(json) {
            console.log(json);
            dialog.error("取得資料失敗！" + json.error);
        }

        myHttp($http, $scope, json, success, fail);
    };
}]).filter('makePositive', function () {
    return function (num) {
        return Math.abs(num);
    }
});