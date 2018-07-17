var repositoryApp = angular.module("App", ["ui.bootstrap", "ezdialog", "ngAnimate"]);
repositoryApp.controller("RepositoryPage", ["$scope", "$http", "ezdialog", "$filter", function ($scope, $http, dialog, $filter) {

    function updateOrder() {
        var json = {
            command: "get-order-new"
        };

        function success(json) {
            $scope.data.orderList = json.data;
        }

        function fail(json) {
            dialog.error("申請列表有更新，請重新整理！" + json.error);
        }

        myHttp($http, null, json, success, fail);
    }

    /*
    * Test
    * */
    var ws = wsConnect({
        url: "ws://" + location.host + window.WS_PATH,
        open: function () {
            // console.log("onopen");
            var sessid = document.cookie.match(/PHPSESSID=([^;]*)/)[1];
            var json = {
                command: "login",
                sessid: sessid
            };

            json = JSON.stringify(json);

            this.send(json);
        },
        message: function (msg) {
            if (msg == "update-order") {
                updateOrder();
            }
        }

    });

    function wsCheckOut() {
        var json = {
            command: "update-order"
        };

        json = JSON.stringify(json);

        ws.send(json);
    }

    $scope.data = {
        itemList: window.itemList,
        orderList: window.orderList,
        suppliers: window.suppliers
    };
    $scope.input = {};
    $scope.stage = {
        show: false,
        itemList: {}
    };

    $scope.percent = function (item) {
        return (item.count - item.low_floor) / item.low_floor;
    };

    $scope.addCount = function (item) {
        var param = {
            number: 0,
            item: item
        };

        dialog.confirm({
            title: "進貨「" + item.name + "」",
            template: "add-count.html",
            param: param,
            size: 'md'
        }).ok(function () {
            var d = this;

            dialog.confirm("確定要進貨「" + item.name + "」共 " + param.number + item.dimension + "？").close(function (ret) {
                if (!ret) {
                    return;
                }

                var json = {
                    command: "add-item-count",
                    item_id: item.id,
                    count: param.number
                };

                function success(json) {
                    $scope.data.itemList = json.data;
                    d.close();
                }

                function fail(json) {
                    console.log(json);
                    dialog.error("儲存失敗！" + json.error);
                }

                myHttp($http, null, json, success, fail);
            });
        });
    };

    $scope.showSupplier = function (name) {
        var supplier = $scope.inSuppliers(name);
        if (!supplier) {
            // dialog.error("找不到此廠商「" + name + "」");
            return;
        }

        dialog.show({
            template: "show-supplier.html",
            title: T("廠商資訊「{}」", name),
            param: supplier,
            size: "md"
        });
    };

    $scope.inSuppliers = function (name) {
        var i, l = $scope.data.suppliers;
        for (i = 0; i < l.length; i++) {
            if (name == l[i].name) {
                return l[i];
            }
        }
        return null;
    };

    $scope.addItem = function () {
        var item = {
            comment: null,
            count: null,
            dimension: null,
            list_key: null,
            low_floor: null,
            moq: null,
            name: null,
            price: null,
            spec: null,
            supplier: null
        };

        dialog.confirm({
            title: "新增耗材",
            param: item,
            size: "md",
            template: "edit-item.html"
        }).ok(function () {
            var d = this, i, l = $scope.data.itemList;

            for (i = 0; i < l.length; i++) {
                if (l[i] && l[i].list_key == item.list_key) {
                    dialog.error("料號重覆！");
                    return;
                }
            }

            var json = {
                command: "add-item",
                item: item
            };

            var success = function (json) {
                d.close();
                $scope.data.itemList = json.data;
            };

            var fail = function (json) {
                console.log(json);
                dialog.error("新增失敗！" + json.error);
            };

            myHttp($http, null, json, success, fail);
        });
    };

    $scope.editItem = function (item) {
        item = angular.copy(item);

        dialog.confirm({
            title: "修改",
            param: item,
            size: "md",
            template: "edit-item.html"
        }).ok(function () {
            var d = this, i, l = $scope.data.itemList;

            for (i = 0; i < l.length; i++) {
                if (l[i].id != item.id && l[i] && l[i].list_key == item.list_key) {
                    dialog.error("料號重覆！");
                    return;
                }
            }

            var json = {
                command: "set-item",
                item: item
            };

            function success(json) {
                d.close();
                $scope.data.itemList = json.data;
            }

            function fail(json) {
                console.log(json);
                dialog.error("儲存失敗！" + json.error);
            }

            myHttp($http, null, json, success, fail);
        });
    };

    $scope.deleteItem = function (item) {
        dialog.confirm("確定要刪除「" + item.name + "」？").ok(function () {
            this.close();

            var json = {
                command: "delete-item",
                item_id: item.id
            };

            function success(json) {
                $scope.data.itemList = json.data;
            }

            function fail(json) {
                console.log(json);
                dialog.error("刪除失敗！" + json.error);
            }

            myHttp($http, null, json, success, fail);
        });
    };

    $scope.isCheck = function (o) {
        return !o.checkout;
    };

    $scope.checkout = function (order) {
        var item = $filter("filter")($scope.data.itemList, {id: order.item_id}, true)[0];

        dialog.confirm({
            title: "領料申請 from " + order.team_name,
            template: "checkout-order.html",
            param: {
                order: order,
                item: item
            },
            size: "lg"
        }).ok(function () {
            if (order.number > item.count) {
                dialog.error("核可失敗！庫存不足");
                return;
            }
            var d = this;

            dialog.confirm("確定要核可「" + item.name + " 共 " + order.number + item.dimension + "」？").ok(function () {
                var d2 = this;

                var json = {
                    command: "checkout-order",
                    order_id: order.id
                };

                var success = function (json) {
                    d2.close();
                    d.close();
                    $scope.data.itemList = json.data.items;
                    $scope.data.orderList = json.data.orders;
                    wsCheckOut();
                };

                var fail = function (json) {
                    console.log(json);
                    dialog.error("發生錯誤！" + json.error);
                };

                myHttp($http, $scope, json, success, fail);
            });
        });
    };

    $scope.reject = function (order) {
        var item = $filter("filter")($scope.data.itemList, {id: order.item_id}, true)[0];

        dialog.confirm("確定要拒絕「" + item.name + " " + order.number + item.dimension + "」？").close(function (ret) {
            if (!ret) {
                return;
            }

            myHttp(
                $http,
                $scope,
                {
                    command: "reject-order",
                    order_id: order.id
                },
                function (json) {
                    $scope.data.orderList = json.data;
                    wsCheckOut();
                },
                function (json) {
                    console.log(json);
                    dialog.error("發生錯誤！" + json.error);
                }
            );
        });
    };

    $scope.addSupplier = function () {
        var supplier = {
                name: null,
                contact: null,
                tel: null,
                phone: null,
                fax: null,
                address: null,
                email: null,
                receipt_number: null,
                comment: null
            },
            suppliers = $scope.data.suppliers,
            i;

        dialog.confirm({
            title: "新增廠商",
            template: "edit-supplier.html",
            size: "md",
            param: supplier
        }).ok(function () {
            var d = this;

            for (i = 0; i < suppliers; i++) {
                if (suppliers[i].name == supplier.name) {
                    dialog.error("新增失敗！廠商名稱已存在");
                    return;
                }
            }

            var json = {
                command: "add-supplier",
                supplier: supplier
            };

            function success(json) {
                d.close();
                $scope.data.suppliers = json.data;
            }

            function fail(json) {
                console.log(json);
                dialog.error("新增失敗！" + json.error);
            }

            myHttp($http, null, json, success, fail);
        });
    };

    $scope.editSupplier = function (supplier) {
        supplier = angular.copy(supplier);

        dialog.confirm({
            title: "修改",
            template: "edit-supplier.html",
            param: supplier,
            size: "md"
        }).ok(function () {
            var d = this;

            var json = {
                command: "set-supplier",
                supplier: supplier
            };

            function success(json) {
                d.close();
                $scope.data.suppliers = json.data;
            }

            function fail(json) {
                dialog.error("儲存失敗！" + json.error);
            }

            myHttp($http, null, json, success, fail);
        });
    };

    $scope.removeSupplier = function (supplier) {
        dialog.confirm("確定要刪除「" + supplier.name + "」？").ok(function () {
            var d = this;

            var json = {
                command: "remove-supplier",
                supplier_id: supplier.id
            };

            function success(json) {
                d.close();
                $scope.data.suppliers = json.data;
            }

            function fail(json) {
                dialog.error("刪除失敗！" + json.error);
            }

            myHttp($http, null, json, success, fail);
        });
    };
}]);
