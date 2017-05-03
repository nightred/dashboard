<?php

class labtech {

	private $user = "user";
	private $pass = "pass";
	private $db = "labtech";
	private $server = "server";

	private $conn;
	private $agentCount = -1;

	function getAgentStatus() {
		$r = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr>";
		$r .= "<th></th><th class='text-center'>All</th><th class='text-center'>Win</th>";
		$r .= "<th class='text-center'>Mac</th><th class='text-center'>Nix</th>";
		$r .= "<th class='text-center'>Svr</th></tr></thead>";
		$r .= "<tbody><tr>";
		$r .= "<td class='text-right'>Online</td>";
		$r .= "<td class='text-right'>{$this->getOnlineAgentCount()}</td>";
		$r .= "<td class='text-right'>{$this->getOnlineWinCount()}</td>";
		$r .= "<td class='text-right'>{$this->getOnlineMacCount()}</td>";
		$r .= "<td class='text-right'>{$this->getOnlineLinuxCount()}</td>";
		$r .= "<td class='text-right'>{$this->getOnlineServerCount()}</td>";
		$r .= "</tr><tr>";
		$r .= "<td class='text-right'>Total</td>";
		$r .= "<td class='text-right'>{$this->getAgentCount()}</td>";
		$r .= "<td class='text-right'>{$this->getWinCount()}</td>";
		$r .= "<td class='text-right'>{$this->getMacCount()}</td>";
		$r .= "<td class='text-right'>{$this->getLinuxCount()}</td>";
		$r .= "<td class='text-right'>{$this->getServerCount()}</td>";

		$r .= "</tr></tbody></table></div>";

		return($this->getPanel('Agents Status', $r));
	}

	function getOfflineServers() {
		$list = $this->getOfflineServerList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Agent</th><th>last Checkin</th></tr></thead>";
		$r .= "<tbody>";
		if ($list) {
			foreach($list AS $row => $item){
				$r .= "<tr><td>";
				$r .= "<span class='glyphicon glyphicon-exclamation-sign offline' aria-hidden='true'></span>";
				if ($item['maintenancemode']) {
					$r .= "&nbsp;<span class='glyphicon glyphicon-briefcase maintenance' aria-hidden='true'></span>";
				}
				$r .= " {$item['client']} \\ {$item['name']}</td>";
				$r .= "<td class='small' nowrap>{$item['lastcontact']}</td>";
				$r .= "</tr>";
			};
		};
		$r .= "</tbody></table></div>";

		return($this->getPanel('Offline Servers', $r));
	}

	function getMissingAV() {
		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th></th><th>Total</th></tr></thead>";
		$r .= "<tbody>";
		$r .= "<tr><td>Windows</td><td class='text-right'>{$this->getMissingAVWinCount()}</td></tr>";
		$r .= "<tr><td>Mac</td><td class='text-right'>{$this->getMissingAVMacCount()}</td></tr>";
		$r .= "</tbody></table></div>";

		return($this->getPanel('AntiVirus Missing', $r));
	}

	function getMissingPatch() {
		$c = $this->getMissingPatchCount();
		$p = round($c/$this->getAgentCount()*100,1);

		$r  = "<div class='large-block'>";
		$r .= "<div class='count-issue text-center'>{$c}</div>";
		$r .= "<div class='text-center'>({$p}% of agents)</div>";
		$r .= "</div>";

		return($this->getPanel('10+ Patchs Out', $r));
	}

	function getLatestAlerts() {
		$list = $this->getLatestAlertsList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Time</th><th>Message</th></tr></thead>";
		$r .= "<tbody>";
		foreach($list AS $row => $item){
			$r .= "<tr><td class='small' nowrap>{$item['date']}</td><td>{$item['message']}</td></tr>";
		}
		$r .= "</tbody></table></div>";

		return($this->getPanel('Latest Alerts', $r));
	}

	function getNewAgents() {

		$r  = "<div class='large-block'>";
		$r .= "<div class='count-large text-center'>{$this->getNewAgentsCount()}</div>";
		$r .= "<div class='text-center'>(agents)</div>";
		$r .= "</div>";


		return($this->getPanel('Installs Last 24H', $r));
	}

	function getLowDisk() {
		$list = $this->getLowDiskList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Agent</th><th>Drive</th></tr></thead>";
		$r .= "<tbody>";
		foreach($list AS $row => $item){
			$r .= "<tr><td>{$item['client']} \\ {$item['agent']}</td><td>{$item['letter']}</td></tr>";
			$r .= "<tr><td colspan='2'><div class='progress progress-striped progress-bar-danger'>";
			$r .= "<div class='progress-bar progress-bar-success' role='progressbar' style='width: {$item['percent']}%;'>";
			$r .= "<span>{$item['free']}MB of {$item['size']}MB, {$item['percent']}% Free <span>";
			$r .= "</div></div></td></tr>";
		}
		$r .= "</tbody></table></div>";

		return($this->getPanel('Low Disk Space', $r));
	}

	function getNeedReboot() {
		$c = $this->getNeedRebootCount();
		$p = round($c/$this->getAgentCount()*100,1);

		$r  = "<div class='large-block'>";
		$r .= "<div class='count-issue text-center'>{$c}</div>";
		$r .= "<div class='text-center'>({$p}% of agents)</div>";
		$r .= "</div>";

		return($this->getPanel('Reboot Pending', $r));
	}

	function getRepairWUA() {
		$c = $this->getRepairWUACount();
		$p = round($c/$this->getAgentCount()*100,1);

		$r  = "<div class='large-block'>";
		$r .= "<div class='count-issue text-center'>{$c}</div>";
		$r .= "<div class='text-center'>({$p}% of agents)</div>";
		$r .= "</div>";

		return($this->getPanel('Repairing WUA', $r));
	}

	// Get the count of agents that need WUA repaired
	private function getRepairWUACount() {
		$sql  = "SELECT count(ComputerID) as count ";
		$sql .= "FROM v_extradatacomputers ";
		$sql .= "WHERE `Under Microsoft Patch Contract`=1 ";
		$sql .= "AND `WUA Repair Needed`=1 ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['count'];
		}

		return($r);
	}

	// Get the count of agents that need to be rebooted
	private function getNeedRebootCount() {
		$sql  = "SELECT count(ComputerID) as count ";
		$sql .= "FROM computers ";
		$sql .= "WHERE ((Flags & 1024) = 1024) ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['count'];
		}

		return($r);
	}

	// Get a list systems with low disk space
	private function getLowDiskList() {
		$sql  = "SELECT c.name as agent, cl.name as client, d.letter, d.size, d.free, (d.free/d.size*100) as percent ";
		$sql .= "from drives as d ";
		$sql .= "join computers as c on (d.computerid = c.ComputerID) ";
		$sql .= "join clients as cl on (c.ClientID = cl.ClientID) ";
		$sql .= "where d.size > 24576 ";
		$sql .= "AND d.FileSystem not in ('CDFS','UNKFS','DVDFS','FAT','FAT32','NetFS') ";
		$sql .= "AND d.missing=0 AND d.internal=1 ";
		$sql .= "AND d.free < (d.size * 0.05) ";
		$sql .= "ORDER BY percent ASC ";
		$sql .= "LIMIT 0, 5";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = mysql_fetch_array($result)) {
			$r[$i]['agent'] = $row['agent'];
			$r[$i]['client'] = $row['client'];
			$r[$i]['letter'] = $row['letter'];
			$r[$i]['size'] = number_format($row['size']);
			$r[$i]['free'] = number_format($row['free']);
			$r[$i]['percent'] = round($row['percent'], 2);
			$i++;
		}

		return($r);
	}

	// Get the count of agents added last 24 hours
	private function getNewAgentsCount() {
		$sql  = "SELECT COUNT(computerID) as systems ";
		$sql .= "FROM computers ";
		$sql .= "WHERE dateadded > DATE_ADD(NOW(),INTERVAL -24 HOUR)  ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get a list of the last 8 alerts
	private function getLatestAlertsList() {
		$sql  = "SELECT alertdate, message FROM alerts ";
		$sql .= "ORDER BY alertdate DESC ";
		$sql .= "LIMIT 0,6";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = mysql_fetch_array($result)) {
			$r[$i]['date'] = date("g:i a", strtotime($row['alertdate'])) . "<br />" . date("M j", strtotime($row['alertdate']));
			//$r[$i]['date'] = $row['alertdate'];
			if (strlen($row['message']) > 150) {
				$r[$i]['message'] = substr($row['message'], 0, 120) . ' ...';
			} else {
				$r[$i]['message'] = $row['message'];
			}
			$i++;
		}

		return($r);
	}

	// Get the count of systems missing more then 10 patchs
	private function getMissingPatchCount() {
		$sql  = "SELECT COUNT(*) as systems ";
		$sql .= "FROM ( SELECT c.computerid AS ID ";
		$sql .= "FROM v_hotfixes h ";
		$sql .= " JOIN  computers c ON (h.ComputerID = c.ComputerID) ";
		$sql .= " JOIN `v_extradatacomputers` AS edc ON (edc.`ComputerID`=c.`ComputerID`) ";
		$sql .= "WHERE h.Approved = 1  ";
		$sql .= " AND h.Installed = 0  ";
		$sql .= " AND c.OS NOT LIKE '%server%' ";
		$sql .= " AND edc.`Under Microsoft Patch Contract`=1 ";
		//$sql .= " AND edc.`WUA Repair Needed`<>1 ";
		$sql .= " AND c.LastContact >  DATE_ADD(NOW(),INTERVAL -10 DAY)  ";
		$sql .= "GROUP BY h.ComputerID HAVING COUNT(h.approved) > 10 ) as l ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the count of windows systems missing AV
	private function getMissingAVWinCount() {
		$sql  = "SELECT count(computerid) as systems FROM computers ";
		$sql .= "WHERE OS like '%Window%' ";
		$sql .= "AND VirusScanner = 0 ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the count of macs systems missing AV
	private function getMissingAVMacCount() {
		$sql  = "SELECT count(computerid) as systems FROM computers ";
		$sql .= "WHERE OS like '%darwin%' ";
		$sql .= "AND VirusScanner = 0 ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get a list of the offline servers
	private function getOfflineServerList() {
		$sql  = "SELECT c.name, c.domain, cl.name as client, c.lastcontact, ";
		$sql .= "CASE WHEN (SELECT COUNT(m.computerid)=1 from maintenancemode AS m  WHERE m.computerid=c.computerid AND m.timestart<NOW())=1 THEN 1 ";
		$sql .= "ELSE 0 END AS maintenancemode ";
		$sql .= "FROM computers as c ";
		$sql .= "JOIN clients as cl on (c.ClientID = cl.ClientID) ";
		$sql .= "WHERE c.LastContact < timestampadd(minute, -10, now()) ";
		$sql .= "AND c.OS LIKE '%server%' ";
		$sql .= "ORDER BY c.LastContact";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		$r = null;
		$i = 0;
		while($row = mysql_fetch_array($result)) {
			$r[$i]['name'] = $row['name'];
			$r[$i]['domain'] = $row['domain'];
			$r[$i]['client'] = $row['client'];
			$r[$i]['lastcontact'] = $row['lastcontact'];
			$r[$i]['maintenancemode'] = $row['maintenancemode'];
			$i++;
		}

		return($r);

	}

	// Get the total count for all agents
	private function getAllAgentCount() {
		$sql = "SELECT count(*) as systems FROM computers";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all agents
	private function getOnlineAgentCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE LastContact > timestampadd(minute, -10, now())";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the count for all Windows agents
	private function getWinCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%Windows%'";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all Online Servers agents
	private function getOnlineServerCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%server%' ";
		$sql .= "AND LastContact > timestampadd(minute, -10, now())";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all Servers agents
	private function getServerCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%server%' ";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all Windows agents
	private function getOnlineWinCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%Windows%' ";
		$sql .= "AND LastContact > timestampadd(minute, -10, now())";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the count for all Mac agents
	private function getMacCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%darwin%'";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all Mac agents
	private function getOnlineMacCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%darwin%' ";
		$sql .= "AND LastContact > timestampadd(minute, -10, now())";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the count for all Linux agents
	private function getLinuxCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%linux%'";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
	}

	// Get the online count for all Linux agents
	private function getOnlineLinuxCount() {
		$sql  = "SELECT count(*) as systems FROM computers ";
		$sql .= "WHERE os LIKE '%linux%' ";
		$sql .= "AND LastContact > timestampadd(minute, -10, now())";
		$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

		while($row = mysql_fetch_array($result)) {
			$r = $row['systems'];
		}

		return($r);
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

	private function getAgentCount() {
		if ($this->agentCount = -1) {
			$sql = "SELECT count(*) as systems FROM computers";
			$result = mysql_query($sql) or die ('Error in SQL: ' . $sql);

			while($row = mysql_fetch_array($result)) {
				$this->agentCount = $row['systems'];
			}
		}

		return $this->agentCount;
	}

	private function openDB() {
		$this->conn = mysql_connect($this->server, $this->user, $this->pass);
		if (!$this->conn) {
			die ('Unable to connect to the database server: ' . mysql_error());
		}

		$r = mysql_select_db($this->db, $this->conn);
		if (!$r) {
			die ('Unable to select database: ' . mysql_error());
		}
	}

	private function closeDB() {
		mysql_close($this->conn);
	}

	function __construct() {
		$this->openDB();
	}

	function __destruct() {
		$this->closeDB();
	}

}

?>
