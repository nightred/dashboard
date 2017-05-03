<?php require 'config.php'; ?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Systems Dashboard</title>

	<script type="text/javascript" src="//code.jquery.com/jquery-3.1.0.min.js" crossorigin="anonymous"></script>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<script type="text/JavaScript" src="js/init.js"></script>
	<script type="text/JavaScript" src="js/jquery.flot.js"></script>
	<script type="text/JavaScript" src="js/jquery.flot.time.js"></script>
	<script type="text/JavaScript" src="js/jquery.flot.orderBars.js"></script>

	<link rel="stylesheet" type="text/css" href="css/style.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<?php if(function_exists('pageHeader')) { pageHeader(); } ?>
	<div class="container-fluid content">

		<div class="row reduced-gutter">
			<div class="col-sm-4 col-md-4 col-lg-4 ">
				<div class="row reduced-gutter">
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="dataview" data-provider='lt' data-mod='agentcount' data-refresh='120'><div class="panel"><div class="panel-body">Loading Agent Counts, Please wait.....</div></div></div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-12">
						<div class="dataview" data-provider='lt' data-mod='latestalerts' data-refresh='60'><div class="panel"><div class="panel-body">Loading Latest Alerts, Please wait.....</div></div></div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="row reduced-gutter">
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='lt' data-mod='missingav' data-refresh='600'><div class="panel"><div class="panel-body">Loading Missing AV, Please wait.....</div></div></div>
							</div>
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='cw' data-mod='ticketcounts' data-refresh='600'><div class="panel"><div class="panel-body">Loading ticket counts, Please wait.....</div></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-md-4 col-lg-4 ">
				<div class="row reduced-gutter">
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="dataview" data-provider='lt' data-mod='lowdisk' data-refresh='300'><div class="panel"><div class="panel-body">Loading Low Disk Space, Please wait.....</div></div></div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="row reduced-gutter">
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='lt' data-mod='missingpatchs' data-refresh='600'><div class="panel"><div class="panel-body">Loading Missing Patchs, Please wait.....</div></div></div>
							</div>
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='lt' data-mod='repairWUA' data-refresh='600'><div class="panel"><div class="panel-body">Loading Windows Update Repair Needed, Please wait.....</div></div></div>
							</div>
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='lt' data-mod='needreboot' data-refresh='300'><div class="panel"><div class="panel-body">Loading Agents, Please wait.....</div></div></div>
							</div>
							<div class="col-sm-6 col-md-6 col-lg-6 ">
								<div class="dataview" data-provider='lt' data-mod='newagents' data-refresh='300'><div class="panel"><div class="panel-body">Loading Agents, Please wait.....</div></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4 col-md-4 col-lg-4 ">
				<div class="row reduced-gutter">
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="dataview" data-provider='lt' data-mod='serveroffline' data-refresh='30'><div class="panel"><div class="panel-body">Loading Offline Servers, Please wait.....</div></div></div>
					</div>
					<div class="col-sm-12 col-md-12 col-lg-12 ">
						<div class="dataview" data-provider='cw' data-mod='oldtickets' data-refresh='600'><div class="panel"><div class="panel-body">Loading oldest tickets, Please wait.....</div></div></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
