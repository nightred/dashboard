<?php

require 'connectwise.php';

$connectwise = new connectwise();

$r  = "<!-- Loading module {$mod} -->";

switch ($mod) {
	case "ticketcounts":
		$r .= $connectwise->getTicketCounts();
		break;
	case "ticketsbyboard":
		$r .= $connectwise->getTicketsByBoard();
		break;
	case "salescall":
		$r .= $connectwise->getSalesCall();
		break;
	case "overbudget":
		$r .= $connectwise->getOverBudget();
		break;
	case "oldtickets":
		$r .= $connectwise->getOldTickets();
		break;
	case "graphticketsjson":
		header('Content-type: application/json');
		$r  = $connectwise->getGraphTicketsJson();
		break;
	case "graphopportunitiesjson":
		header('Content-type: application/json');
		$r  = $connectwise->getGraphOpportunitiesJson();
		break;
	case "graphticketsbyboardjson":
		header('Content-type: application/json');
		$r  = $connectwise->getGraphTicketsbyBoardJson();
		break;
	default:
		$r .= "No module {$mod} found!";
}

// Write the block data
echo $r;

?>
