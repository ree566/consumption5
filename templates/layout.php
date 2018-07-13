<!DOCTYPE html>
<html class="<?=$PAGE?>" lang="zh-tw" ng-app="App">

<head>
	<!-- metas -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- title -->
	<title>耗材管理系統</title>

	<?php include(__DIR__ . "/lib.php") ?>
	
	<!-- my libs -->
	<script src="js/settings.js.php"></script>
	<script src="js/common.js"></script>
	<script src="js/dialog.js"></script>
	<script src="js/<?=$PAGE?>.js"></script>
	<link rel="stylesheet" href="style.css">
</head>

<body>

<?php if (isset($SESS)) { ?>
<nav class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#nav-collapse" ng-click="navCollapse=!navCollapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<div class="navbar-brand">
				歡迎！<?=$SESS["name"]?>
			</div>
		</div>
		<div class="navbar-collapse text-right" id="nav-collapse" collapse="!navCollapse">
			<span class="navbar-left">
				<a href="report.php" class="btn btn-default navbar-btn">報表</a>
			<?php if($SESS["permission"] == 2){ ?>
				<a href="order.php" class="btn btn-default navbar-btn">領取耗材</a>
			<?php } if ($SESS["permission"] >= 3 && $SESS["permission"] <= 4) {?>
				<a href="repository.php" class="btn btn-default navbar-btn">管理儲存庫</a>
			<?php } if ($SESS["permission"] >= 4) { ?>
				<a href="control.php" class="btn btn-default">管理</a>
			<?php } if ($SESS["permission"] >= 5) { ?>
				<a href="floor.php" class="btn btn-default">設定樓層</a>
			<?php } ?>
			</span>
			<span class="navbar-right">
			<?php if ($SESS["permission"] >= 2) { ?>
				<a href="account.php" class="btn btn-default navbar-btn">修改密碼</a>
			<?php } ?>
				<a href="logout.php" class="btn btn-default navbar-btn">登出</a>
			</span>
		</div>
	</div>
</nav>
<?php } ?>
<h1 id="title"><?=$TITLE?></h1>

<section class="container clearfix">
	<?=$BODY?>
</section>

<?php //include 'debug-footer.php';?>

<footer class="container-fluid text-right">	
	&copy; 2014-<?php echo date("Y");?> Advantech.com.tw<br>
</footer>

</body>
</html>
