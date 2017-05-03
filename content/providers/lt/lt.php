<?php

require 'labtech.php';
$labtech = new labtech();


	$r  = "<!-- Loading module {$mod} -->";

	switch ($mod) {
		case "agentcount":
			$r .= $labtech->getAgentStatus();
			break;
		case "serveroffline":
			$r .= $labtech->getOfflineServers();
			break;
		case "missingav":
			$r .= $labtech->getMissingAV();
			break;
		case "missingpatchs":
			$r .= $labtech->getMissingPatch();
			break;
		case "latestalerts":
			$r .= $labtech->getLatestAlerts();
			break;
		case "newagents":
			$r .= $labtech->getNewAgents();
			break;
		case "lowdisk":
			$r .= $labtech->getLowDisk();
			break;
		case "needreboot":
			$r .= $labtech->getNeedReboot();
			break;
		case "repairWUA":
			$r .= $labtech->getRepairWUA();
			break;
		default:
			$r .= "No module {$mod} found!";
	}

	echo $r;

?>
