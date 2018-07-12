<form method="post" role="form" class="login-block">
	<div class="form-group">
		<label for="account">帳號︰</label>
		<input type="text" class="form-control" placeholder="輸入工號" name="uid">
	</div>
	<div class="form-group">
		<label for="pass">密碼︰</label>
		<input type="password" class="form-control" placeholder="輸入密碼" name="pwd">
	</div>
	<div class="form-group text-danger status-info">
	<?php if (isset($LOGIN_FAILED)) { ?>
		登入失敗！
	<?php } ?>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default">登入</button>
		<small class="btn-text"><a href="document.html">使用手冊</a></small>
	</div>
</form>
