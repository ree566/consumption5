<style>
    .order-in {
        color: green;
    }

    .order-out {
        color: red;
    }
</style>

<script>
    <?php
    $l = json_en(get_floor_all());
    echo "var floors = $l;";
    ?>
</script>

<div ng-controller="report2">

    <form class="search" role="form" name="form" ng-submit="findReport()">
        <div class="row">
            <div class="table">
                <div class="form-group col-sm-3">
                    <label for="item-name" class="control-label">樓層</label>
                    <select ng-model="input.search.floor_id"
                            ng-options="floor.id as floor.name for floor in data.floors" class="form-control">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="form-group col-sm-3" ng-class="{'has-error': form.timeStart.$invalid}">
                    <label for="time-start" class="control-label">起始日期</label>
                    <input type="text" class="form-control" id="time-start" name="timeStart"
                           datepicker-popup="yyyy-MM-dd" ng-model="input.timeStart" is-open="input.editTimeStart"
                           ng-focus="input.editTimeStart=true" close-on-date-selection="false" show-button-bar="false"
                           max-date="input.timeEnd">
                </div>
                <div class="form-group col-sm-3" ng-class="{'has-error': form.timeEnd.$invalid}">
                    <label for="time-end" class="control-label">結束日期</label>
                    <input type="text" class="form-control" id="time-end" name="timeEnd" datepicker-popup="yyyy-MM-dd"
                           ng-model="input.timeEnd" is-open="input.editTimeEnd" ng-focus="input.editTimeEnd=true"
                           close-on-date-selection="false" show-button-bar="false" min-date="input.timeStart">
                </div>
                <div class="form-gruop col-sm-3 text-bottom">
                    <button class="btn btn-primary btn-block" ng-disabled="form.$invalid || data.loading">搜尋</button>
                </div>
            </div>
        </div>
    </form>

    <hr/>

    <div class="row">
        <div class="orders">
            <table id="table1" class="table table-striped">
                <tr>
                    <th>id</th>
                    <th>uid</th>
                    <th>uName</th>
                    <th>tName</th>
                    <th>iName</th>
                    <th>num</th>
                    <th>otime</th>
                    <th>ctime</th>
                    <th>fName</th>
                </tr>
                <tr ng-repeat="order in data.orderList = (data.orderList | orderBy:'id')">
                    <td>{{order.id}}</td>
                    <td>{{order.user_id}}</td>
                    <td>{{order.user_name}}</td>
                    <td>{{order.team_name}}</td>
                    <td>{{order.item_name}}</td>
                    <td ng-class="{'order-out': order.number > 0, 'order-in': order.number < 0}">
                        <span ng-class="{'glyphicon glyphicon-chevron-down': order.number > 0, 'glyphicon glyphicon-chevron-up': order.number < 0}"></span>
                        {{(order.number | makePositive) + order["dimension"]}}
                    </td>
                    <td>{{order.order_time}}</td>
                    <td>{{order.checkout_time}}</td>
                    <td>{{order.floor_name}}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div ng-controller="excelController">
    <button class="btn btn-link" ng-click="exportToExcel('#table1')">
        <span class="glyphicon glyphicon-share"></span> Export to Excel
    </button>
</div>