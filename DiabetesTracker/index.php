<?php
	include "includes/lib.php";
	session_start();
?>
<!DOCTYPE html> 
<html>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<head>
		<title>Diabetes Tracker</title>
	<script src="../js/jquery-2.1.0.min.js"></script>
	<script src="../bower_components/platform/platform.js"></script>
  	<link rel="import" href="../bower_components/font-roboto/roboto.html">
  	<link rel="import" href="../bower_components/core-header-panel/core-header-panel.html">
  	<link rel="import" href="../bower_components/core-drawer-panel/core-drawer-panel.html">
  	<link rel="import" href="../bower_components/core-scaffold/core-scaffold.html">
  	<link rel="import" href="../bower_components/core-menu/core-menu.html">
  	<link rel="import" href="../bower_components/core-icons/core-icons.html">
  	<link rel="import" href="../bower_components/core-item/core-item.html">
  	<link rel="import" href="../bower_components/core-toolbar/core-toolbar.html">
  	<link rel="import" href="../bower_components/paper-tabs/paper-tabs.html">
  	<link rel="import" href="../bower_components/paper-shadow/paper-shadow.html">
  	<link rel="import" href="../bower_components/paper-input/paper-input.html">
  	<link rel="import" href="../bower_components/paper-button/paper-button.html">
  	<link rel="import" href="../bower_components/paper-dialog/paper-dialog.html">
  	<link rel="import" href="../bower_components/paper-fab/paper-fab.html">
  	<link rel="import" href="../bower_components/paper-icon-button/paper-icon-button.html">
  	<link rel="import" href="./components/stat-box.html">
  	<link rel="import" href="./components/new-measurement-form.html">
  	<link rel="import" href="./components/measurement.html">
	<script type="text/javascript" src="js/script.js"></script>
	<script type="text/javascript" src="flot/jquery.flot.js"></script>
	<script type="text/javascript" src="flot/jquery.flot.selection.js"></script>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<style>	
		body {
			margin-top:65px;
		}
    	core-toolbar {
      		color: #f1f1f1;
      		fill: #f1f1f1;
      		background-image: linear-gradient(left top, rgb(11,158,80) 0%, rgb(14,53,158) 100%) !important;
			background-image: -o-linear-gradient(left top, rgb(11,158,80) 0%, rgb(14,53,158) 100%) !important;
			background-image: -moz-linear-gradient(left top, rgb(11,158,80) 0%, rgb(14,53,158) 100%) !important;
			background-image: -webkit-linear-gradient(left top, rgb(11,158,80) 0%, rgb(14,53,158) 100%) !important;
			background-image: -ms-linear-gradient(left top, rgb(11,158,80) 0%, rgb(14,53,158) 100%) !important;
			background-image: -webkit-gradient(linear,left top,right bottom,color-stop(0, rgb(11,158,80)),color-stop(1, rgb(14,53,158))) !important;
    	}
    	paper-dialog {
    		border:1px solid black;
    	}
    	#stat-box {
    		margin:5px;
    	}
    	#trackerWrap {
    		text-align:center;
    		padding:10px;
    	}
	</style>
	</head>
	<body unresolved touch-action="auto">
		<core-toolbar id="navheader" style="background-color:#03a9f4; position:fixed; top:0px; width:100%; z-index:1;">
		    <span flex>Diabetes Tracker</span>
		    <?php
		    if ($userLoggedIn){
		    	echo '<a href="logout.php"><paper-button raisedButton label="'.$_SESSION['UNAME'].'"></paper-button></a>';
		    	//echo '<div class="user">Logged in as: '.$_SESSION['UNAME'].'</div>';
		    	//echo '<div class="login"><a href="logout.php">Logout</a></div>';
		    	// After Successful Authentication
		    }else{
		    	//echo '<div class="login"><a href="login_process.php">Login With Gmail</a></div>';
		    	echo '<a href="login_process.php"><paper-button raisedButton label="Login"></paper-button></a>';
		    	// login page link
		 	}
			?>
		</core-toolbar>
				
		<div id="trackerWrap">
			<stat-box week="<?php echo getAverage(7); ?>" month="<?php echo getAverage(30); ?>" threeMonth="<?php echo getAverage(90); ?>" allTime="<?php echo getAverage(0); ?>"></stat-box>
			
			<div id="chartWrap">
				<div id="chart"></div>
				<div id="overview"></div>
				<paper-shadow z="1"></paper-shadow>
			</div>
			<div style="clear:both;"></div>
			
					<?php if($userLoggedIn){
						echo '<paper-button raisedButton onClick="toggleDialog(\'dialog-new\');" label="Add Measurement" style="margin:10px 0 0 0;"></paper-button>';
					}
					?>
			<div id="measurements-list"></div>
			<paper-fab id='moreRowsButton' icon="add" style="color:white; background-color:#03a9f4;"></paper-fab>
		</div>
		
			<?php if($userLoggedIn): ?>
				<paper-dialog id="dialog-new" opened="false">
					<new-measurement-form userid="<?php echo $_SESSION["USERID"]; ?>"></new-measurement-form>
				</paper-dialog>
			<?php endif; ?>
		<script>
			
    		function toggleDialog(id) {
      			var dialog = document.querySelector('paper-dialog[id=' + id + ']');
      			dialog.toggle();
    		};
  
			//Perform initialization script functions
			getMeasurements(startRecs, numRecs);
		</script>
	</body>
</html>