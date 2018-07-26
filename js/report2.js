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

        var sD = moment(json.time_start);
        var eD = moment(json.time_end);

        if(Math.abs(sD.diff(eD, 'days')) > 30){
            alert("日期區間請設定在一個月內");
            return false;
        }

        if (json.floor == null) {
            json.floor = -1;
        }

        function success(json) {
            $scope.data.orderList = json.data;

            // Datatable init here because init before button click can't export excel.
            $('#table1').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'print'
                ],
                data: json.data,
                "columns": [
                    {"data": "id"},
                    {"data": "user_id"},
                    {"data": "user_name"},
                    {"data": "team_name"},
                    {"data": "item_name"},
                    {"data": "number", width: "100px"},
                    {"data": "order_time"},
                    {"data": "checkout_time"},
                    {"data": "floor_name"}
                ],
                "columnDefs": [
                    {
                        "type": "html",
                        "targets": [5],
                        'render': function (data, type, full, meta) {
                            return "<h5 class='" + (data > 0 ? "order-out" : "order-in") + "'>" +
                                "<span class='glyphicon glyphicon-chevron-" + (data > 0 ? "down" : "up") + "'>" +
                                (Math.abs(data) + full["dimension"]) +
                                "</span>" +
                                "</h5>";
                        }
                    }
                ],
                destroy: true,
                resize: false,
                paging: false,
                searching: false,
                processing: true
            });
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