<script>
    <?php
    $itemList = json_en(get_item_all());
    echo "var itemList = $itemList;";

    $orderList = json_en(get_order_month());
    echo "var orderList = $orderList;";

    $teamList = json_en(get_team_all());
    echo "var teamList = $teamList;";

    $l = json_en(get_floor_all());
    echo "var floors = $l;";
    ?>
</script>

<script src="js/chart-helper.js"></script>

<div ng-controller="mainPage">
    <form role="form">
        <h3>圖表</h3>
        <div class="row">
            <div class="col-sm-4 form-group">
                <label for="chart-type" class="control-label">圖表類型</label>
                <select ng-model="chart.select.type" ng-options="i.value as i.name for i in chart.selectOptions.type"
                        class="form-control" id="chart-type" ng-change="triggerResize()"></select>
            </div>
            <div class="col-sm-4 form-group">
                <label for="chart-time" class="control-label">日期</label>
                <select ng-model="chart.select.time" ng-options="i.value as i.name for i in chart.selectOptions.time"
                        class="form-control" id="chart-time"
                        ng-change="search()"></select>
            </div>
            <div class="col-sm-4 form-group">
                <label for="chart-group" class="control-label">Group By</label>
                <select ng-model="chart.select.groupBy"
                        ng-options="i.value as i.name for i in chart.selectOptions.groupBy" class="form-control"
                        id="chart-group" ng-change="search()"></select>
            </div>
        </div>
    </form>


    <div class="chart">
        <div class="row" collapse="input.itemName && input.teamName && false">
            <div class="col-sm-4 vcenter">
                <div>
                    <canvas piechart responsive="true" options="chart.pieOptions" data="chart.pieData" legend="true"
                            height="150" width="300"></canvas>
                </div>
            </div>
            <div class="col-sm-8 vcenter">
                <div collapse="chart.select.type!='bar'">
                    <div>
                        <canvas barchart responsive="true" options="chart.barOptions" data="chart.barData" legend="true"
                                height="150" width="300"></canvas>
                    </div>
                </div>
                <div collapse="chart.select.type!='line'">
                    <div>
                        <canvas linechart responsive="true" options="chart.lineOptions" data="chart.lineData"
                                legend="true" height="150" width="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form class="search" role="form" name="form" ng-submit="updateOrder()">
        <h3>查詢日期</h3>
        <div class="row">
            <div class="table">
                <div class="form-group col-sm-4" ng-class="{'has-error': form.timeStart.$invalid}">
                    <label for="time-start" class="control-label">起始日期</label>
                    <input type="text" class="form-control" id="time-start" name="timeStart"
                           datepicker-popup="yyyy-MM-dd" ng-model="input.timeStart" is-open="input.editTimeStart"
                           ng-focus="input.editTimeStart=true" close-on-date-selection="false" show-button-bar="false"
                           max-date="input.timeEnd">
                </div>
                <div class="form-group col-sm-4" ng-class="{'has-error': form.timeEnd.$invalid}">
                    <label for="time-end" class="control-label">結束日期</label>
                    <input type="text" class="form-control" id="time-end" name="timeEnd" datepicker-popup="yyyy-MM-dd"
                           ng-model="input.timeEnd" is-open="input.editTimeEnd" ng-focus="input.editTimeEnd=true"
                           close-on-date-selection="false" show-button-bar="false" min-date="input.timeStart">
                </div>
                <div class="form-gruop col-sm-4 text-bottom">
                    <button class="btn btn-primary btn-block" ng-disabled="form.$invalid || data.loading">搜尋</button>
                </div>
            </div>
        </div>
    </form>
    <h3>過濾</h3>
    <div class="row">
        <div class="form-group col-sm-4">
            <label for="item-name" class="control-label">樓層</label>
            <select ng-model="input.search.floor_id" ng-options="floor.id as floor.name for floor in data.floors"
                    class="form-control">
                <option value="">All</option>
            </select>
        </div>
        <div class="form-group col-sm-4">
            <label for="item-name" class="control-label">物品</label>
            <input type="text" class="form-control" id="item-name" placeholder="輸入物品" name="itemName"
                   ng-model="input.search.item_name"
                   typeahead="item.name for item in data.itemList | filter:{floor_id: input.search.floor_id}:allowNull | filter:$viewValue">
        </div>
        <div class="form-group col-sm-4">
            <label for="team-name" class="control-label">部門</label>
            <input type="text" class="form-control" id="team-name" placeholder="輸入部門" name="teamName"
                   ng-model="input.search.team_name"
                   typeahead="team.name for team in data.teamList | filter:{floor_id: input.search.floor_id}:allowNull | filter:$viewValue">
        </div>
    </div>
    <div class="orders">
        <h3>申請詳細</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>樓層</th>
                    <th>項目</th>
                    <th>申請單位</th>
                    <th>申請人</th>
                    <th class="text-right">數量</th>
                    <th>申請時間</th>
                    <th>核可時間</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="order in data.filteredList = (data.orderList | filter:input.search:allowNull | filter:isPositive | filter:noReject | orderBy:'-checkout_time')">
                    <td>{{order.floor_name}}</td>
                    <td>{{order.item_name}}</td>
                    <td>{{order.team_name}}</td>
                    <td>{{order.user_name}}</td>
                    <td class="text-right">{{order.number}}</td>
                    <td>{{order.order_time}}</td>
                    <td>{{order | orderStatus}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

