<script>
<?php 
$list = json_en(get_floor_all());
echo "var floors = $list;";

$list = json_en(get_item_all());
echo "var items = $list;";

$list = json_en($SESS);
echo "var user = $list;";
?>
</script>

<div>
	<tabset class="tab-animation">
		<tab heading="管理樓層">
			<div ng-controller="floors">
				<h3>新增樓層</h3>
				<p>
					<button class="btn btn-primary" ng-click="add()">新增</button>
				</p>
				<h3>樓層清單</h3>
				<div class="table-responsive">
					<table class="table item-list table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>名稱</th>
								<th>#</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="item in resource.floors">
								<td>{{item.id}}</td>
								<td>{{item.name}}</td>
								<td>
									<button class="btn btn-primary" ng-click="edit(item)">修改</button>
									<button class="btn btn-danger" ng-click="delete(item)">刪除</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</tab>
		<tab heading="項目總覽">
			<div ng-controller="items">
				<h3>搜尋列表</h3>
				<button class="btn btn-primary" type="button" ng-click="download()">下載</button>
				<div ng-repeat="search in searchs">
					<h4>{{search.title}}</h4>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>樓層</th>
									<th>料號</th>
									<th>名稱</th>
									<th>規格</th>
									<th>供貨商</th>
									<th>安全水位</th>
									<th>庫存量</th>
									<th>單位</th>
									<th>價格</th>
								</tr>
							</thead>
							<tbody>
								<tr ng-repeat="item in resource.items | filter:search.item">
									<td>{{item.floor_name}}</td>
									<td>{{item.list_key}}</td>
									<td>{{item.name}}</td>
									<td>{{item.spec}}</td>
									<td>{{item.supplier}}</td>
									<td>{{item.low_floor}}</td>
									<td>{{item.count}}</td>
									<td>{{item.dimension}}</td>
									<td>{{item.price}}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<h3>搜尋</h3>
				<form autocomplete="off">
					<div class="row">
						<div class="form-group col-sm-5">
							<label for="name" class="sr-only control-label">名稱</label>
							<input type="text" class="form-control" placeholder="輸入名稱" ng-model="input.name" id="name">
						</div>
						<div class="form-group col-sm-5">
							<label for="supplier" class="sr-only control-label">供貨商</label>
							<input type="text" class="form-control" placeholder="輸入廠商" ng-model="input.supplier" id="supplier">
						</div>
						<div class="form-group col-sm-2">
							<button class="btn btn-primary" type="button" ng-click="cache()">加入列表</button>
						</div>
					</div>
				</form>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>樓層</th>
								<th>料號</th>
								<th>名稱</th>
								<th>規格</th>
								<th>供貨商</th>
								<th>安全水位</th>
								<th>庫存量</th>
								<th>單位</th>
								<th>價格</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="i in resource.items | filter:input">
								<td>{{i.floor_name}}</td>
								<td>{{i.list_key}}</td>
								<td>{{i.name}}</td>
								<td>{{i.spec}}</td>
								<td>{{i.supplier}}</td>
								<td>{{i.low_floor}}</td>
								<td>{{i.count}}</td>
								<td>{{i.dimension}}</td>
								<td>{{i.price}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</tab>
	</tabset>
</div>

<script type="text/ng-template" id="edit-floor.html">
	<div class="form-group">
		<label for="name">名稱</label>
		<input type="text" class="form-control" ng-model="param.name" id="name" required>
	</div>
</script>
