<script>
<?php 
$list = json_en(get_user($SESS["uid"]));
echo "var user = $list;";
?>
</script>
<div ng-controller="Main">
	<div class="row">
		<div class="col-lg-6">		
			<h3>修改密碼</h3>
			<form ng-submit="changePass()" class="form-horizontal" name="formChangePass">
				<div class="form-group">
					<label for="old-pass" class="col-sm-2 control-label">舊密碼</label>
					<div class="col-sm-10">
					<input type="password" name="old-pass" id="old-pass" class="form-control old-pass" ng-model="input.oldPass">
					</div>
				</div>
				<div class="form-group">
					<label for="new-pass" class="col-sm-2 control-label">新密碼</label>
					<div class="col-sm-10">
					<input type="password" name="new-pass" id="new-pass" class="form-control new-pass" ng-model="input.newPass" required>
					</div>
				</div>
				<div class="form-group">
					<label for="check" class="col-sm-2 control-label">重覆確認</label>
					<div class="col-sm-10">
					<input type="password" name="check" id="check" class="form-control check-pass" ng-model="input.check" required>
					</div>
				</div>
				<div class="status-info">

				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<input type="submit" value="確認" class="btn btn-primary" ng-disabled="formChangePass.$invalid || data.loading">
					</div>
				</div>
			</form>
		</div>
		<div class="col-lg-6">
			<h3>使用者資訊</h3>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<th>工號</th>
						<th>姓名</th>
						<th>權限</th>
						<th>樓層</th>
						<th>部門</th>
					</thead>
					<tbody>
						<td>{{data.user.id}}</td>
						<td>{{data.user.name}}</td>
						<td>{{data.user.permission}}</td>
						<td>{{data.user.floor_name}}</td>
						<td>{{data.user.team_name}}</td>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
