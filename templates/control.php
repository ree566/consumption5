<script>
<?php 
$list = json_encode(get_user_all());
echo "var users = $list;";

$list = json_encode(get_team_all());
echo "var teams = $list;";

$list = json_encode(get_floor_all());
echo "var floors = $list;";

$list = json_encode($SESS);
echo "var user = $list;";
?>
</script>

<div ng-controller="Main">
	<tabset class="tab-animation">
		<tab heading="管理使用者">
			<h3>新增使用者</h3>
			<p>
				使用者權限說明︰<br>
				<code>1</code> - 訪客，只能查詢資料<br>
				<code>2</code> - 領耗材人員<br>
				<code>3</code> - 倉庫管理員<br>
				<code>4</code> - 樓層管理員，可以管理該樓使用者、部門、也可以管理倉庫<br>
				<code>5</code> - 總管理員，可以管理所有使用者、部門、設定樓層<br>
			</p>
			<p>
				<button class="btn btn-primary" ng-click="addUser()">新增</button>
			</p>
			<p>
				或是以多行文字匯入使用者清單，格式為︰<code>工號 姓名 [區塊 [權限 [樓層]]]</code>，<code>[]</code> 內為選填。每行一人。若省略部門和權限，預設的部門為自己的部門，權限為 2。
			</p>
			<div class="form-group">
				<textarea rows="5" class="form-control" placeholder="Example: A-0001 王小明 部門A 2" ng-model="input.importText"></textarea>
			</div>
			<button class="btn btn-success" type="button" ng-click="importUser()">匯入</button>
			<form ng-submit="search()" name="formSearch">
				<h3>搜尋</h3>
				<div class="row">
					<div class="form-group col-sm-10">
						<label for="searchTerm" class="sr-only control-label">輸入關鍵字（名稱、工號……）</label>
						<input type="text" class="form-control" placeholder="輸入關鍵字（名稱、工號……）" ng-model="input.searchTerm" id="searchTerm">
					</div>
					<div class="form-group col-sm-2">
						<button class="btn btn-primary" type="reset" ng-click="input.searchTerm=null">重設</button>
					</div>
				</div>
			</form>
			<div class="table-responsive">
				<table class="table item-list table-striped">
					<thead>
						<tr>
							<th>工號</th>
							<th>姓名</th>
							<th>樓層</th>
							<th>區塊</th>
							<th>權限</th>
							<th>#</th>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="user in data.users | filter:{floor_id: (data.user.permission < 5 ? data.user.floor_id : '')} | filter:input.searchTerm | orderBy:'id'">
							<td>{{user.id}}</td>
							<td>{{user.name}}</td>
							<td>{{user.floor_name}}</td>
							<td>{{user.team_name}}</td>
							<td>{{user.permission}}</td>
							<td>
								<button class="btn btn-primary" ng-click="editUser(user)">修改</button>
								<button class="btn btn-danger" ng-click="deleteUser(user)">刪除</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</tab>
		<tab heading="管理區塊">
			<h3>新增區塊</h3>
			<p>
				<button class="btn btn-primary" ng-click="addTeam()">新增</button>
			</p>
			<h3>搜尋</h3>
			<form ng-submit="search()" name="formSearch">
				<div class="row">
					<div class="form-group col-sm-10">
						<label for="teamSearchTerm" class="sr-only control-label">輸入關鍵字（名稱、工號……）</label>
						<input type="text" class="form-control" placeholder="輸入關鍵字（名稱、工號……）" ng-model="input.teamSearchTerm" id="teamSearchTerm">
					</div>
					<div class="form-group col-sm-2">
						<button class="btn btn-primary" type="reset" ng-click="input.teamSearchTerm=null">重設</button>
					</div>
				</div>
			</form>
			<div class="table-responsive">
				<table class="table item-list table-striped">
					<thead>
						<th>樓層</th>
						<th>名稱</th>
						<th>#</th>
					</thead>
					<tbody>
						<tr ng-repeat="team in data.teams | filter:input.teamSearchTerm | filter:{'floor_id': (data.user.permission < 5 ? data.user.floor_id : '')}">
							<td>{{team.floor_name}}</td>
							<td>{{team.name}}</td>
							<td>
								<button class="btn btn-primary" ng-click="editTeam(team)">修改</button>
								<button class="btn btn-danger" ng-click="deleteTeam(team)">刪除</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</tab>
	</tabset>
</div>

<script type="text/ng-template" id="edit-user.html">
	<div class="form-group">
		<label for="id">工號</label>
		<input type="text" class="form-control" ng-model="param.user.id" id="id" ng-disabled="param.edit" required>
	</div>
	<div class="form-group">
		<label for="name">姓名</label>
		<input type="text" class="form-control" ng-model="param.user.name" id="name" required>
	</div>
	<div class="form-group">
		<label for="floor">樓層</label>
		<select class="form-control" id="floor" ng-model="floor_id" ng-options="floor.id as floor.name for floor in param.floors" ng-disabled="param.my.permission < 5" ng-init="floor_id = (param.teams | filter:{id: param.user.team_id})[0].floor_id" required></select>
	</div>
	<div class="form-group">
		<label for="team">區塊</label>
		<select class="form-control" id="team" ng-model="param.user.team_id" ng-options="team.id as team.name for team in param.teams | filter:{'floor_id': floor_id}" required></select>
	</div>
	<div class="form-group">
		<label for="permission">權限</label>
		<input type="number" class="form-control" min="1" max="{{param.my.permission - 1}}" ng-model="param.user.permission" required>
	</div>
</script>

<script type="text/ng-template" id="edit-team.html">
	<div class="form-group">
		<label for="floor">樓層</label>
		<select class="form-control" id="floor" ng-model="param.team.floor_id" ng-options="floor.id as floor.name for floor in param.floors" ng-disabled="param.my.permission < 5" required></select>
	</div>
	<div class="form-group">
		<label for="name">名稱</label>
		<input type="text" class="form-control" ng-model="param.team.name" id="name">
	</div>
</script>
