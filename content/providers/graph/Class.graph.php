<?php

class graph {

	function getGraphTickets() {

		$r  = <<<EOF
<div id='GraphNewTickets' style='height: 250px; width: 100%;'></div>
<script type='text/JavaScript'>
$(function() {
	var options = {
		lines:{
			show:true
		},
		points:{
			show:true
		},
		xaxis:{
			mode: 'time',
			timeformat: "%d",
			tickSize: [1, "day"],
			minTickSize: [2, 'day']
		},
		legend:{
			position:'nw',
			backgroundOpacity: 0
		}
	};
	function loadData() {
		function onDataRecived(data) {
			$.plot('#GraphNewTickets', data, options);
		}
		$.getJSON('providers.php?prov=cw&mod=graphticketsjson', onDataRecived);
	}
	loadData();
	setInterval(loadData, 60*1000);
});
</script>
EOF;

		return($this->getPanel('Ticket Stats', $r, false));
	}

	function getGraphOpportunities() {

		$r  = <<<EOF
<div id='GraphNewOppotunities' style='height: 150px; width: 100%;'></div>
<script type='text/JavaScript'>
$(function() {
	var options = {
		bars:{
			align: "center"
		},
		xaxis:{
			mode: 'time',
			timeformat: "%d",
			tickSize: [1, "day"],
			minTickSize: [2, 'day']
		},
		legend:{
			position:'nw',
			backgroundOpacity: 0
		},
		colors: ["#34752f", "#0072bb"]
	};
	function loadData() {
		function onDataRecived(data) {
			$.plot('#GraphNewOppotunities', data, options);
		}
		$.getJSON('providers.php?prov=cw&mod=graphopportunitiesjson', onDataRecived);
	}
	loadData();
	setInterval(loadData, 60*1000);
});
</script>
EOF;

		return($this->getPanel('Opportunities', $r, false));
	}

	function getGraphTicketsbyBoard() {

		$r  = <<<EOF
<div id='GraphTicketsbyBoard' style='height: 250px; width: 100%;'></div>
<script type='text/JavaScript'>
$(function() {
	var options = {
		lines:{
			show:true
		},
		points:{
			show:true
		},
		xaxis:{
			mode: 'time',
			timeformat: "%d",
			tickSize: [1, "day"],
			minTickSize: [2, 'day']
		},
		legend:{
			position:'nw',
			backgroundOpacity: 0
		}
	};
	function loadData() {
		function onDataRecived(data) {
			$.plot('#GraphTicketsbyBoard', data, options);
		}
		$.getJSON('providers.php?prov=cw&mod=graphticketsbyboardjson', onDataRecived);
	}
	loadData();
	setInterval(loadData, 60*1000);
});
</script>
EOF;

		return($this->getPanel('New Tickets per Board', $r, false));
	}

	// Display build functions
	private function getTimeStamp() {
		$u = time()*1000;

		$r  = "<div class='small pull-right'>";
		$r .= "<span class='glyphicon glyphicon-time' aria-hidden='true'></span> ";
		$r .= "<span class='refresh-counter' data-timestamp='{$u}'>Just Now</span></div>";

		return $r;
	}

	private function getPanel($name, $content, $timestamp=true) {
		$r  = "<div class='panel'><div class='panel-heading'>";
		$r .= "<span class='glyphicon glyphicon-refresh pull-right btn-refresh' aria-hidden='true'></span>";
		$r .= "<h3 class='panel-title'>{$name}</h3>";
		$r .= "</div>";
		$r .= "<div class='panel-body'>";
		$r .= $content;
		if ($timestamp) {
			$r .= $this->getTimeStamp();
		};
		$r .= "</div></div>";

		return $r;
	}

}

?>
