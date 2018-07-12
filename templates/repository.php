<script>
<?php 
$itemList = json_en(get_item_floor($SESS["floor_id"]));
echo "var itemList = $itemList;";

$orderList = json_en(get_order_floor_new($SESS["floor_id"]));
echo "var orderList = $orderList;";

$l = json_en(get_supplier_all());
echo "var suppliers = $l;";
?>
</script>

<div ng-controller="RepositoryPage">
	<tabset class="tab-animation">
		<tab heading="管理儲存庫">
			<div class="new-orders" collapse="!(data.orderList | filter:isCheck).length">
				<h3>未核可申請</h3>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>物品名稱</th>
							<th>供應商</th>
							<th>價格</th>
							<th>數量</th>
							<th>申請單位</th>
							<th>申請時間</th>
							<th>動作</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="order in data.orderList | filter:isCheck">
							<td>{{order.item_name}}</td>
							<td>{{order.supplier}}</td>
							<td>{{order.price}}</td>
							<td>{{order.number}}</td>
							<td>{{order.team_name}}</td>
							<td>{{order.order_time}}</td>
							<td>
								<button class="btn btn-primary" ng-click="checkout(order)" ng-disabled="data.loading">核可</button>
								<button class="btn btn-danger" ng-click="reject(order)" ng-disabled="data.loading">拒絕</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="item-list">
				<h3>庫存資訊</h3>
				<div class="row form-group">
					<div class="col-sm-10"><input type="text" class="form-control" placeholder="搜尋" ng-model="input.itemName" ng-disabled="stage.show"></div>
					<div class="col-sm-2"><button class="btn btn-primary" ng-click="input.itemName = null" ng-disabled="stage.show">Clear</button></div>
				</div>
				<div class="table-responsive">
					<table class="table item-list table-striped">
						<thead>
							<th>料號</th>
							<th>物品名稱</th>
							<th>規格</th>
							<th>供應商</th>
							<th class="text-right">價格</th>
							<th class="text-right">單位</th>
							<th class="text-right">MOQ</th>
							<th class="text-right">庫存數量</th>
							<th class="text-right" class="text-right">安全庫存</th>
							<th class="text-right">剩餘比例</th>
							<th class="text-right">備註</th>
							<th>#</th>
						</thead>
						<tbody>
							<tr ng-repeat="item in data.itemList | orderBy:percent | filter:input.itemName" ng-class="{'danger': item.count < item.low_floor, 'warning': item.count/item.low_floor < 1.2}">
								<td>{{item.list_key}}</td>
								<td>{{item.name}}</td>
								<td>{{item.spec}}</td>
								<td>
									<span ng-click="showSupplier(item.supplier)" ng-class="{'btn-link': inSuppliers(item.supplier)}">{{item.supplier}}</span>
								</td>
								<td class="text-right">{{item.price}}</td>
								<td class="text-right">{{item.dimension}}</td>
								<td class="text-right">{{item.moq}}</td>
								<td class="text-right">{{item.count}}</td>
								<td class="text-right">{{item.low_floor}}</td>
								<td class="text-right">{{((item.count - item.low_floor)/item.low_floor*100).toFixed(0)}}%</td>
								<td class="text-right">{{item.comment}}</td>
								<td><button class="btn btn-danger" ng-click="addCount(item)">進貨</button></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</tab>
		<tab heading="耗材清單">
			<h3>新增項目</h3>
			<button class="btn btn-primary" ng-click="addItem()">新增</button>
			
			<h3>搜尋</h3>
			<div class="form-group">
				<label for="search-item" class="sr-only">搜尋</label>
				<input type="text" placeholder="輸入關鍵字" id="search-item" class="form-control" ng-model="input.searchItem">
			</div>
			
			<div class="table-responsive">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>料號</th>
							<th>項目名稱</th>
							<th>規格</th>
							<th>供應商</th>
							<th>價格</th>
							<th>單位</th>
							<th>MOQ</th>
							<th>安全庫存</th>
							<th>備註</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="item in data.itemList | filter:input.searchItem | orderBy:'list_key'">
							<td>{{item.list_key}}</td>
							<td>{{item.name}}</td>
							<td>{{item.spec}}</td>
							<td>{{item.supplier}}</td>
							<td>{{item.price}}</td>
							<td>{{item.dimension}}</td>
							<td>{{item.moq}}</td>
							<td>{{item.low_floor}}</td>
							<td>{{item.comment}}</td>
							<td>
								<button class="btn btn-primary" ng-click="editItem(item)">修改</button>
								<button class="btn btn-danger" ng-click="deleteItem(item)">刪除</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</tab>
		<tab heading="廠商清單">
			<h3>新增廠商</h3>
			<button class="btn btn-primary" ng-click="addSupplier()">新增</button>
			<h3>搜尋</h3>
			<div class="form-group">
				<label for="search-supplier" class="sr-only">搜尋</label>
				<input type="text" placeholder="輸入關鍵字" id="search-supplier" class="form-control" ng-model="input.searchSupplier">
			</div>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>名稱</th>
							<th>聯絡人</th>
							<th>電話</th>
							<th>手機</th>
							<th>傳真</th>
							<th>地址</th>
							<th>email</th>
							<th>統編</th>
							<th>備註</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="supplier in data.suppliers | filter:input.searchSupplier">
							<td>{{supplier.name}}</td>
							<td>{{supplier.contact}}</td>
							<td>{{supplier.tel}}</td>
							<td>{{supplier.phone}}</td>
							<td>{{supplier.fax}}</td>
							<td>{{supplier.address}}</td>
							<td>{{supplier.email}}</td>
							<td>{{supplier.receipt_number}}</td>
							<td>{{supplier.comment}}</td>
							<td>
								<button class="btn btn-primary" ng-click="editSupplier(supplier)">修改</button>
								<button class="btn btn-danger" ng-click="removeSupplier(supplier)">刪除</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</tab>
	</tabset>
</div>

<script type="text/ng-template" id="add-count.html">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>料號</th>
					<th>項目</th>
					<th>規格</th>
					<th>供應商</th>
					<th>價格</th>
					<th>庫存量</th>
					<th>安全庫存</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{param.item.list_key}}</td>
					<td>{{param.item.name}}</td>
					<td>{{param.item.spec}}</td>
					<td>{{param.item.supplier}}</td>
					<td>{{param.item.price}}</td>
					<td>
						<span class="text-success" ng-class="{'text-warning': param.item.count/param.item.low_floor < 1.2, 'text-danger': param.item.count <= param.item.low_floor}">
							{{param.item.count}}
						</span> 
						&rarr; 
						<span class="text-success" ng-class="{'text-warning': (param.item.count + param.number)/param.item.low_floor < 1.2, 'text-danger': (param.item.count + param.number) <= param.item.low_floor}">
							{{param.item.count + param.number}}
						</span>
					</td>
					<td>{{param.item.low_floor}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<p>備註︰ {{param.item.comment ? param.item.comment : '無'}}</p>
	<label for="number">進貨數量</label>
	<input type="number" class="form-control form-lg" ng-model="param.number" min="0" require>
</script>

<script type="text/ng-template" id="edit-item.html">
	<div class="row">
		<div class="form-group col-sm-6">
			<label for="list-key">料號</label>
			<input type="text" class="form-control form-lg" ng-model="param.list_key" id="list-key" placeholder="輸入料號">
		</div>
		<div class="form-group col-sm-6">
			<label for="name">名稱</label>
			<input type="text" class="form-control form-lg" ng-model="param.name" id="name" placeholder="輸入名稱">
		</div>
		<div class="form-group col-sm-6">
			<label for="spec">規格</label>
			<input type="text" class="form-control form-lg" ng-model="param.spec" id="spec" placeholder="輸入規格">
		</div>
		<div class="form-group col-sm-6">
			<label for="supplier">供貨商</label>
			<input type="text" class="form-control form-lg" ng-model="param.supplier" id="supplier" placeholder="輸入廠商">
		</div>
		<div class="form-group col-sm-6">
			<label for="price">價格</label>
			<input type="number" class="form-control form-lg" ng-model="param.price" id="price" step="0.01" placeholder="輸入價格">
		</div>
		<div class="form-group col-sm-6">
			<label for="dimension">單位</label>
			<input type="text" class="form-control form-lg" ng-model="param.dimension" id="dimension" placeholder="輸入單位">
		</div>
		<div class="form-group col-sm-6">
			<label for="moq">最少出貨量</label>
			<input type="number" class="form-control form-lg" ng-model="param.moq" id="moq" placeholder="輸入MOQ">
		</div>
		<div class="form-group col-sm-6">
			<label for="low-floor">安全庫存</label>
			<input type="number" class="form-control form-lg" ng-model="param.low_floor" id="low-floor" placeholder="輸入安全水位">
		</div>
		<div class="form-group col-md-12">
			<label for="comment">備註</label>
			<textarea ng-model="param.comment" class="form-control form-lg" id="comment" rows="4" placeholder="備註"></textarea>
		</div>
	</div>
</script>

<script type="text/ng-template" id="checkout-order.html">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>申請單位</th>
					<th>申請人</th>
					<th>料號</th>
					<th>項目</th>
					<th>規格</th>
					<th>供應商</th>
					<th>數量</th>
					<th>價格</th>
					<th>總價</th>
					<th>庫存量</th>
					<th>安全庫存</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{param.order.team_name}}</td>
					<td>{{param.order.user_name}}</td>
					<td>{{param.item.list_key}}</td>
					<td>{{param.item.name}}</td>
					<td>{{param.item.spec}}</td>
					<td>{{param.item.supplier}}</td>
					<td>{{param.order.number}}</td>
					<td>{{param.item.price}}</td>
					<td>{{param.item.price * param.order.number}}</td>
					<td>
						<span class="text-success" ng-class="{'text-warning': param.item.count/param.item.low_floor < 1.2, 'text-danger': param.item.count <= param.item.low_floor}">
							{{param.item.count}}
						</span> 
						&rarr; 
						<span class="text-success" ng-class="{'text-warning': (param.item.count - param.order.number)/param.item.low_floor < 1.2, 'text-danger': (param.item.count - param.order.number) <= param.item.low_floor}">
							{{param.item.count - param.order.number}}
						</span>
					</td>
					<td>{{param.item.low_floor}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<p>備註︰ {{param.item.comment ? param.item.comment : '無'}}</p>
</script>

<script type="text/ng-template" id="edit-supplier.html">
	<div class="row">
		<div class="form-group col-sm-6">
			<label for="name">廠商名稱</label>
			<input type="text" placeholder="輸入名稱" id="name" class="form-control" ng-model="param.name">
		</div>
		<div class="form-group col-sm-6">
			<label for="contact">聯絡人</label>
			<input type="text" placeholder="輸入聯絡人" id="contact" class="form-control" ng-model="param.contact">
		</div>
		<div class="form-group col-sm-6">
			<label for="tel">電話</label>
			<input type="text" placeholder="輸入電話" id="tel" class="form-control" ng-model="param.tel">
		</div>
		<div class="form-group col-sm-6">
			<label for="phone">手機</label>
			<input type="text" placeholder="輸入手機" id="phone" class="form-control" ng-model="param.phone">
		</div>
		<div class="form-group col-sm-6">
			<label for="fax">傳真</label>
			<input type="text" placeholder="輸入傳真" id="fax" class="form-control" ng-model="param.fax">
		</div>
		<div class="form-group col-sm-6">
			<label for="address">地址</label>
			<input type="text" placeholder="輸入地址" id="address" class="form-control" ng-model="param.address">
		</div>
		<div class="form-group col-sm-6">
			<label for="email">email</label>
			<input type="text" placeholder="輸入email" id="email" class="form-control" ng-model="param.email">
		</div>
		<div class="form-group col-sm-6">
			<label for="reciept_number">統編</label>
			<input type="text" placeholder="輸入統編" id="reciept_number" class="form-control" ng-model="param.receipt_number">
		</div>
		<div class="form-group col-sm-12">
			<label for="comment">備註</label>
			<textarea placeholder="輸入備註" id="comment" class="form-control" rows="3" ng-model="param.comment"></textarea>
		</div>
	</div>
</script>

<script type="text/ng-template" id="show-supplier.html">
	<div class="row">
		<div class="col-sm-6">
			<h4>廠商名稱</h4>
			<p>{{param.name}}</p>
		</div>
		<div class="col-sm-6">
			<h4>聯絡人</h4>
			<p>{{param.contact}}</p>
		</div>
		<div class="col-sm-6">
			<h4>電話</h4>
			<p>{{param.tel}}</p>
		</div>
		<div class="col-sm-6">
			<h4>手機</h4>
			<p>{{param.phone}}</p>
		</div>
		<div class="col-sm-6">
			<h4>傳真</h4>
			<p>{{param.fax}}</p>
		</div>
		<div class="col-sm-6">
			<h4>地址</h4>
			<p>{{param.address}}</p>
		</div>
		<div class="col-sm-6">
			<h4>email</h4>
			<p>{{param.email}}</p>
		</div>
		<div class="col-sm-6">
			<h4>統編</h4>
			<p>{{param.receipt_number}}</p>
		</div>
		<div class="col-sm-12">
			<h4>備註</h4>
			<p>{{param.comment}}</p>
		</div>
	</div>
</script>
