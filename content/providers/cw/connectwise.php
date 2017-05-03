<?php

class connectwise {

	private $user = "user";
	private $pass = "pass";
	private $db = "cwwebapp_company";
	private $server = "server";

	private $conn;

	function getTicketCounts() {
		$r = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th></th><th>Tickets</th></tr></thead>";
		$r .= "<tbody><tr><td class='small'>New 24h:</td>";
		$r .= "<td class='text-right'>{$this->getNewTicketsCount()}</td>";
		$r .= "</tr><tr><td class='small'>Closed 24h</td>";
		$r .= "<td class='text-right'>{$this->getClosedTicketsCount()}</td>";
		$r .= "</tr><tr><td class='small'>Open</td>";
		$r .= "<td class='text-right'>{$this->getOpenTicketsCount()}</td>";
		$r .= "</tr></tbody></table></div>";
		return($this->getPanel('Ticket Counts', $r));
	}

	function getTicketsByBoard() {
		$list = $this->getTicketsByBoardList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Board</th><th>Tickets</th></tr></thead>";
		$r .= "<tbody>";
		foreach($list AS $row => $item){
			$r .= "<tr><td>{$item['board']}</td><td class='text-right'>{$item['count']}</td></tr>";
		}
		$r .= "</tbody></table></div>";

		return($this->getPanel('Tickets by Service Board', $r));
	}

	function getSalesCall() {
		$json = readJson('content/salescall.json');

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Name</th><th>Value</th></tr></thead>";
		$r .= "<tbody>";
		foreach($json AS $list => $agent){
			$r .= "<tr><td>{$agent['name']}</td><td class='text-right'>{$agent['call']}</td></tr>";
		}
		$r .= "</tbody></table></div>";

		return($this->getPanel('Sales Call', $r));
	}

	function getOverBudget() {
		$list = $this->getOverBudgetList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Ticket</th><th>Summary</th><th>Status</th><th>Budget</th></tr></thead>";
		$r .= "<tbody>";
		if ($list) {
			foreach($list AS $row => $item){
				$r .= "<tr><td>#{$item['ticket']}</td><td><em>{$item['client']}</em><br />{$item['summary']}</td><td>{$item['status']}</td>";
				$r .= "<td>{$item['hremaining']}h</td></tr>";
			};
		};
		$r .= "</tbody></table></div>";

		return($this->getPanel('Tickets Over Budgeted Hours', $r));
	}

	function getGraphTicketsJson() {
		$newArray = array();
		$closedArray = array();
		$sameDayArray = array();

		foreach($this->getNewTicketsByDayList() AS $row => $item){
			$newArray[] = array($item['date'], $item['count']);
		};

		foreach($this->getClosedTicketsByDayList() AS $row => $item){
			$closedArray[] = array($item['date'], $item['count']);
		};

		foreach($this->getSameDayTicketsByDayList() AS $row => $item){
			$sameDayArray[] = array($item['date'], $item['count']);
		};

		$r = array();
		$r[] = array(
			"label" => "Closed Same Day",
			"data" => $sameDayArray
		);
		$r[] = array(
			"label" => "Closed",
			"data" => $closedArray
		);
		$r[] = array(
			"label" => "New",
			"data" => $newArray
		);

		return(json_encode($r));
	}

	function getGraphTicketsbyBoardJson() {
		$BoardList = array();
		$r = array();

		foreach($this->getBoardList() AS $boards => $board){
			$list[] = array();

			foreach ($this->getTicketsByBoardbyDayList($board['board']) AS $row => $item){
				$list[] = array($item['date'], $item['count']);
			}

			$r[] = array(
				"label" => $board['board'],
				"data" => $list
			);

			$list = null;
		};

		return(json_encode($r));
	}

	function getGraphOpportunitiesJson() {
		$wonArray = array();
		$newArray = array();

		foreach($this->getWonOpportunitiesByDayList() AS $row => $item){
			$wonArray[] = array($item['date'], $item['count']);
		};

		foreach($this->getNewOpportunitiesByDayList() AS $row => $item){
			$newArray[] = array($item['date'], $item['count']);
		};


		$r = array();
		$r[] = array(
			"label" => "New Opportunities",
			"bars" => array(
				"fill" => true,
				"barWidth" => 24*60*60*300,
				"show" => true,
				"align" => "center",
				"lineWidth" => 1,
				"order" => 0
				),
			"data" => $newArray
		);
		$r[] = array(
			"label" => "Won Opportunities",
			"bars" => array(
				"fill" => true,
				"barWidth" => 24*60*60*300,
				"show" => true,
				"align" => "center",
				"lineWidth" => 1,
				"order" => 1
				),
			"data" => $wonArray
		);

		return(json_encode($r));
	}

	function getOldTickets() {
		$list = $this->getOldTicketsList();

		$r  = "<div class='table-responsive'><table class='table table-striped'>";
		$r .= "<thead><tr><th>Ticket</th><th>Summary</th><th>Resources</th><th>Age</th></tr></thead>";
		$r .= "<tbody>";
		if ($list) {
			foreach($list AS $row => $item){
				$r .= "<tr><td>#{$item['ticket']}<br /><em>{$item['status']}</em></td><td><em>{$item['client']}</em><br />{$item['summary']}</td>";
				$r .= "<td>{$item['resources']}</td><td>{$item['age']}d</td></tr>";
			};
		};
		$r .= "</tbody></table></div>";

		return($this->getPanel('Oldest Tickets', $r));
	}

	// Get a list of the oldest tickets
	private function getOldTicketsList() {
		$sql  = "SELECT TOP 5 summary, TicketNbr, status_description AS status, ";
		$sql .= "company_name, age, resource_list as resources ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE status_description NOT LIKE '>%' ";
		$sql .= "AND status_description NOT LIKE 'Completed' ";
		$sql .= "ORDER BY age desc";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$r = null;
		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['summary'] = $row['summary'];
			$r[$i]['status'] = $row['status'];
			$r[$i]['ticket'] = $row['TicketNbr'];
			$r[$i]['client'] = $row['company_name'];
			$r[$i]['age'] = $row['age'];
			$r[$i]['resources'] = $row['resources'];
			$i++;
		}

		return($r);
	}

	// Get a count of new tickets for the last 7 days
	private function getNewOpportunitiesByDayList() {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, Date_Became_Lead)) AS day, count(Opportunity_RecID) as count  ";
		$sql .= "FROM v_rpt_Opportunity ";
		$sql .= "WHERE Date_Became_Lead >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, Date_Became_Lead)) ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, Date_Became_Lead)) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000);
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a count of new tickets for the last 7 days
	private function getWonOpportunitiesByDayList() {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, date_closed)) AS day, count(Opportunity_RecID) as count  ";
		$sql .= "FROM v_rpt_Opportunity ";
		$sql .= "WHERE date_closed >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, date_closed)) ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, date_closed)) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000);
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a count of tickets per day for the listed board
	private function getTicketsByBoardbyDayList($board) {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) AS day, count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Entered_UTC >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "AND Board_Name LIKE '{$board}' ";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000)-6*60*60*1000;
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a count of same day ticket Closures
	private function getSameDayTicketsByDayList() {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC)) AS day, count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Resolved_UTC >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "AND ( ";
		$sql .= "status_description LIKE '>%'  ";
		$sql .= "OR status_description LIKE 'Completed'  ";
		$sql .= ") ";
		$sql .= "AND dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC)) = dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) ";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC))  ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC))  ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000)-6*60*60*1000;
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a count of new tickets for the last 7 days
	private function getNewTicketsByDayList() {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) AS day, count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Entered_UTC >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, Date_Entered_UTC)) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000)-6*60*60*1000;
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a count of new tickets for the last 7 days
	private function getClosedTicketsByDayList() {
		$sql  = "SELECT dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC)) AS day, count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Resolved_UTC >= DATEADD(day, -30, GETDATE()) ";
		$sql .= "AND ( ";
		$sql .= "status_description LIKE '>%' ";
		$sql .= "OR status_description LIKE 'Completed' ";
		$sql .= ")";
		$sql .= "GROUP BY dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC)) ";
		$sql .= "ORDER BY dateadd(DAY,0, datediff(day,0, Date_Resolved_UTC)) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['date'] = (strtotime($row['day'])*1000)-6*60*60*1000;
			$r[$i]['count'] = (string)$row['count'];
			$i++;
		}

		return($r);
	}

	// Get a list of the tickets on each service board
	private function getOverBudgetList() {
		$sql  = "SELECT TOP 10 summary, TicketNbr, status_description AS status, Hours_Actual, ";
		$sql .= "Hours_Budget, company_name, (Hours_Budget - Hours_Actual) AS hours_remaining ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE status_description NOT LIKE '>%' ";
		$sql .= "AND status_description NOT LIKE 'Completed' ";
		$sql .= "AND Hours_Budget > 0 ";
		$sql .= "AND (Hours_Budget - Hours_Actual) < 0 ";
		$sql .= "ORDER BY hours_remaining";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$r = null;
		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['summary'] = $row['summary'];
			$r[$i]['status'] = $row['status'];
			$r[$i]['ticket'] = $row['TicketNbr'];
			$r[$i]['client'] = $row['company_name'];
			$r[$i]['hremaining'] = (float)$row['hours_remaining'];
			$i++;
		}

		return($r);
	}

	// Get a list of the service boards
	private function getBoardList() {
		$sql  = "SELECT Board_Name as board ";
		$sql .= "FROM v_rpt_ServiceBoard ";
		$sql .= "WHERE Inactive_Flag = 0 ";
		$sql .= "AND BusGroup NOT LIKE 'Admin' ";
		$sql .= "AND Board_Name NOT LIKE '%Archive%' ";
		$sql .= "AND Board_Name NOT LIKE 'Maintenance' ";
		$sql .= "AND BusGroup NOT LIKE 'Sales' ";
		$sql .= "ORDER BY Board_Name ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['board'] = $row['board'];
			$i++;
		}

		return($r);
	}

	// Get a list of the tickets on each service board
	private function getTicketsByBoardList() {
		$sql  = "SELECT Board_Name as board, count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE status_description NOT LIKE '>%' ";
		$sql .= "AND status_description NOT LIKE 'Completed' ";
		$sql .= "GROUP BY Board_Name ";
		$sql .= "ORDER BY Board_Name ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		$i = 0;
		while($row = sqlsrv_fetch_array($result)) {
			$r[$i]['board'] = $row['board'];
			$r[$i]['count'] = $row['count'];
			$i++;
		}

		return($r);
	}

	// Get the count of tickets created in the last 24 hours
	private function getNewTicketsCount() {
		$sql  = "SELECT count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Entered_UTC >= DATEADD(day, -1, GETDATE()) ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		while($row = sqlsrv_fetch_array($result)) {
			$r = $row['count'];
		}

		return($r);
	}

	// Get the count of tickets closed in the last 24 hours
	private function getClosedTicketsCount() {
		$sql  = "SELECT count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE Date_Resolved_UTC >= DATEADD(day, -1, GETDATE())  ";
		$sql .= "AND ( ";
		$sql .= "status_description LIKE '>%' ";
		$sql .= "OR status_description LIKE 'Completed' ";
		$sql .= ")";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		while($row = sqlsrv_fetch_array($result)) {
			$r = $row['count'];
		}

		return($r);
	}

		// Get the count of tickets open tickets
	private function getOpenTicketsCount() {
		$sql  = "SELECT count(TicketNbr) as count ";
		$sql .= "FROM v_rpt_Service ";
		$sql .= "WHERE status_description NOT LIKE '>%' ";
		$sql .= "AND status_description NOT LIKE 'Completed' ";
		$result = sqlsrv_query($this->conn, $sql) or die ('Error in SQL: ' . $sql);

		while($row = sqlsrv_fetch_array($result)) {
			$r = $row['count'];
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

	// Database connector functions
	private function openDB() {
		$connInfo = array(
			"database" => $this->db,
			"UID" => $this->user,
			"PWD" => $this->pass,
			'ReturnDatesAsStrings' => true
		);
		$this->conn = sqlsrv_connect($this->server, $connInfo);
		if (!$this->conn) {
			die ('Unable to connect to the database server. ');
		}
	}

	private function closeDB() {
		sqlsrv_close($this->conn);
	}

	function __construct() {
		$this->openDB();
	}

	function __destruct() {
		$this->closeDB();
	}

}

?>
