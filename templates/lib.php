<?php 
if (USE_CDN) { 
?>
	<!-- js libs -->
	<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.23/angular.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.11.0/ui-bootstrap-tpls.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.1-beta.2/Chart.min.js"></script>
	<script src="lib/angles/angles.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/chroma-js/0.5.7/chroma.min.js"></script>
	<script src="lib/angular-animate/angular-animate.js"></script>

	<!-- css libs -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
<?php 
} else { 
?>
	<!-- js libs -->
	<script src="lib/angular/angular.js"></script>
	<script src="lib/angular-bootstrap/ui-bootstrap-tpls.js"></script>
	<script src="lib/chartjs/Chart.js"></script>
	<script src="lib/angles/angles.js"></script>
	<script src="lib/chroma-js/chroma.js"></script>
	<script src="lib/angular-animate/angular-animate.js"></script>

	<!-- css libs -->
	<link rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap.css">
	<link rel="stylesheet" href="lib/bootstrap/dist/css/bootstrap-theme.css">
	
	<!-- ezdialog -->
	<script src="lib/angular-ezdialog/dialog.js"></script>
	<link rel="stylesheet" href="lib/angular-ezdialog/dialog.css">
<?php
}
