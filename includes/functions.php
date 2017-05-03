<?php



function pageHeader() {
	$page = strtolower(str_ireplace('.php', '', basename($_SERVER['PHP_SELF'])));
?>
<nav class="navbar navbar-static-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<div class="navbar-brand">
				LabTech ConnectWise Dashboard
			</div>
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li <?php if($page == 'index') { echo 'class="active"'; }; ?> ><a href="index.php">Home</a></li>
				<li <?php if($page == 'tech') { echo 'class="active"'; }; ?> ><a href="tech.php">Tech</a></li>
			</ul>
		</div>
	</div>
</nav>
<?php
}

// Json Handler
function readJson($f) {
	if (!file_exists($f)) {
		die ('Missing datafile: ' . $f);
	}

	$raw = file_get_contents($f);
	$j = json_decode($raw, true);

	return $j;
}

?>
