<?php

require 'Class.graph.php';

$graph = new graph();

$r  = "<!-- Loading module {$mod} -->";

switch ($mod) {
	case "graphtickets":
		$r .= $graph->getGraphTickets();
		break;
	case "graphopportunities":
		$r .= $graph->getGraphOpportunities();
		break;
	case "graphticketsbyboard":
		$r .= $graph->getGraphTicketsbyBoard();
		break;
	default:
		$r .= "No module {$mod} found!";
}

// Write the block data
echo $r;

?>
