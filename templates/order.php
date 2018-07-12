<script>
<?php 
$itemList = json_en(get_item_floor($SESS["floor_id"]));
echo "var itemList = $itemList;";

$orderList = json_en(get_order_team($SESS["team_id"]));
echo "var orderList = $orderList;";
?>
</script>

<div ng-controller="OrderPage">
	<form class="clearfix" role="form" method="post" name="form" ng-submit="submit()" autocomplete="off">
		<h3>耗材需求</h3>
		<div class="row">
			<div class="form-group col-sm-7 col-lg-9">
				<label for="search-term" class="sr-only">料號或名稱</label>
				<input ng-model="input.searchTerm" ng-keydown="checkKey($event)" type="text" class="form-control" id="search-term" placeholder="輸入料號或名稱" name="searchTerm" required>
			</div>
			<div class="form-group col-sm-3 col-lg-2">
				<label for="order-number" class="sr-only">數量</label>
				<div class="input-group">
					<input ng-model="input.number" type="number" class="form-control text-right" id="order-number" placeholder="數量" required min="1" name="number" required>
					<div class="input-group-addon">
						{{input.searchTerm && input.itemListFiltered[input.index].dimension || "單位"}}
					</div>
				</div>
				
			</div>
			<div class="form-group col-sm-2 col-lg-1">
				<button class="btn btn-primary" type="submit" ng-disabled="form.$invalid || !input.itemListFiltered.length || data.loading">發送</button>
			</div>
		</div>
		<div class="table-responsive" collapse="!input.searchTerm">
			<table class="table">
				<thead>
					<tr>
						<th>料號</th>
						<th>名稱</th>
						<th>規格</th>
						<th>單位</th>
						<th>供應商</th>
						<th>價格</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-click="input.index = $index" ng-class="{'bg-success': $index == input.index}" ng-repeat="item in input.searchTerm && (input.itemListFiltered = (data.itemList | filter:input.searchTerm | orderBy:'list_key'))" class="selection">
						<td>{{item.list_key}}</td>
						<td>{{item.name}}</td>
						<td>{{item.spec}}</td>
						<td>{{item.dimension}}</td>
						<td>{{item.supplier}}</td>
						<td>{{item.price}}</td>
					</tr>
					<tr ng-if="!input.itemListFiltered.length">
						<td>無資料</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>

	<div class="orders" collapse="!data.orderList.length">
		<h3>領出紀錄</h3>
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>料號</th>
						<th>名稱</th>
						<th>規格</th>
						<th>供應商</th>
						<th>價格</th>
						<th>數量</th>
						<th>申請人</th>
						<th>申請時間</th>
						<th>核可時間</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="order in data.orderList | orderBy:'order_time':true">
						<td>{{order.list_key}}</td>
						<td>{{order.item_name}}</td>
						<td>{{order.spec}}</td>
						<td>{{order.supplier}}</td>
						<td>{{order.price}}</td>
						<td>{{order.number}}</td>
						<td>{{order.user_name}}</td>
						<td>{{order.order_time}}</td>
						<td ng-class="{'text-success': notCheckout(order), 'text-danger': order.reject}">{{order | orderStatus}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
