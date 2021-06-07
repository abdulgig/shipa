<?php

require_once("inc/db.php");



if($_REQUEST['key']==1) {
	
$search = isset($_GET['search'])? $_GET['search']:""; 
$condition = $dateCondition = "";
$offset = 0;
$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
$start_date = $end_date = "";

if($search != ""){
    $params['vicidial_agent_log.phone_number'] = $search;
    $condition = " AND vicidial_agent_log.user IN (SELECT `user` FROM vicidial_users WHERE `user` LIKE '%$search%' OR full_name LIKE '%$search%') ";
}

if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}


if($start_date != "" && $end_date != ""){
    $dateCondition = " AND vicidial_agent_log.event_time >= '$start_date' AND vicidial_agent_log.event_time <= '$end_date' ";
}else{
    $end_date = date('Y-m-d');
    $start_date = $lastDate;
    $dateCondition = " AND vicidial_agent_log.event_time >= '$start_date' AND vicidial_agent_log.event_time <= '$end_date' ";
}

$countQry = "SELECT
    COUNT(*) AS total_rows
FROM (SELECT vicidial_agent_log.user FROM vicidial_agent_log 
    INNER JOIN recording_log ON vicidial_agent_log.lead_id = recording_log.lead_id
WHERE vicidial_agent_log.comments LIKE '%INBOUND%' $condition $dateCondition GROUP BY vicidial_agent_log.user) AS total_rows";
$countRes = $conn->query($countQry)->fetch();
$countRes = $countRes['total_rows'];

$query = "SELECT vicidial_agent_log.user, vicidial_agent_log.lead_id, vicidial_agent_log.survey_option, COUNT(vicidial_agent_log.user) AS total_user,SUM(vicidial_agent_log.talk_sec) AS cnt_talk_sec,
SUM(CASE WHEN survey_option >= 1 THEN 1
	WHEN survey_option = 0 THEN 0 END) survey_option_1 , 
SUM(CASE
        WHEN survey_option = 5 THEN 10
        WHEN survey_option = 4 THEN 8
        WHEN survey_option = 3 THEN 6
        WHEN survey_option = 2 THEN 2
        WHEN survey_option = 1 THEN -5
        WHEN survey_option = 0 THEN 0
    END) points FROM vicidial_agent_log 
WHERE vicidial_agent_log.comments LIKE '%INBOUND%' $condition $dateCondition GROUP BY vicidial_agent_log.user ORDER BY points desc LIMIT 100000";

$res = $conn->query($query, PDO::FETCH_ASSOC);

$agentLogs = array();
$userIds = array();
$leadIds = array();

while ($result = $res->fetch()) {
    $agentLogs[] = $result;
    $userIds[$result['user']] = $result['user'];
    $leadIds[$result['lead_id']] = $result['lead_id'];
}
$userIdsConcate = implode(",", $userIds);
$leadIdsConcate = implode(",", $leadIds);

//echo "SELECT user_id, full_name FROM vicidial_users WHERE user_id IN ($userIdsConcate)";die;
$res2 = $conn->query("SELECT user, full_name FROM vicidial_users WHERE user IN ($userIdsConcate)", PDO::FETCH_ASSOC);

$users = array();
if($res2){

  while ($result2 = $res2->fetch()) {
      $users[$result2['user']] = $result2['full_name'];
  }
}

$trfCallQry = "SELECT COUNT(*) AS total_trf_call, user_call_log.user FROM user_call_log
INNER JOIN vicidial_agent_log ON user_call_log.lead_id = vicidial_agent_log.lead_id
AND user_call_log.user = vicidial_agent_log.user
WHERE vicidial_agent_log.lead_id IS NOT NULL AND user_call_log.call_type = 'BLIND_XFER' AND user_call_log.phone_number LIKE '%8000099991111' 
AND vicidial_agent_log.comments LIKE '%INBOUND%' $condition $dateCondition
GROUP BY user_call_log.user";

//$res3 = $conn->query("SELECT lead_id FROM user_call_log WHERE lead_id IN ($leadIdsConcate)  GROUP BY lead_id", PDO::FETCH_ASSOC);
$res3 = $conn->query($trfCallQry, PDO::FETCH_ASSOC);
$leads = array();
while ($result3 = $res3->fetch()) {
    $leads[$result3['user']] = $result3['total_trf_call'];

}


$columnHeader = '';  
$columnHeader = "Ranking" . "\t" . "Agents" . "\t". "Calls Received" . "\t" . "Talk Time" . "\t" . "AHT" . "\t" . "Survey Transferred" . "\t" . "Rated Calls" . "\t" . "Points" . "\t";  
$setData = ''; 
$rowData = '';

foreach ($agentLogs as $key => $agentLog) {
	
	if(isset($agentLog['points']) and $agentLog['points'] !='')
	{
		if($agentLog['points']>=1){
			if($new_flg  == 0){
				$flg = 1;
			}
			$ranking = $i;
			$i++;
		}
		else{ $ranking = '0'; }
	}
	else { $ranking = '0'; }
	
	if(isset($users[$agentLog['user']])){ $agents= $users[$agentLog['user']].' / '.$agentLog['user']; }else{ $agents=  'N/A'.' / '.$agentLog['user'];}
	if(isset($leads[$agentLog['user']]) and $leads[$agentLog['user']] !=''){ $servey = $leads[$agentLog['user']];}else{ $servey = '0';}
	$taktime = number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', '').' mins';
	$aht = number_format((float)((number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', ''))/$agentLog['total_user']), 2, '.', '').' min/ call'; 
	
	$value = '"' . $ranking . '"' . "\t".'"' . $agents . '"' . "\t".'"' . $agentLog['total_user'] . '"' . "\t".'"' . $taktime . '"' . "\t".'"' . $aht . '"' . "\t".'"' . $servey . '"' . "\t".'"' . $agentLog['survey_option_1'] . '"' . "\t".'"' . $agentLog['points'] . '"' . "\t"; 
	$setData .= trim($value) . "\n"; 
}

header("Content-type: application/octet-stream");  
header("Content-Disposition: attachment; filename=InboudAgents.xls");  
header("Pragma: no-cache");  
header("Expires: 0");
  
echo ucwords($columnHeader) . "\n" . $setData . "\n"; 

}

if($_REQUEST['key']==2) {
$search = isset($_GET['search'])? $_GET['search']:""; 
$condition = $dateCondition = "";
$offset = 0;
$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
$start_date = $end_date = "";

	
	$user = "";
	if(!isset($_GET['user']) || @$_GET['user'] == "" ){
		header('Location: ' . 'inbound.php');
	}
	$user = $_GET['user'];

	$userCloserLogStatusRes = $conn->query("SELECT DISTINCT `status` FROM vicidial_closer_log WHERE `user` = '$user'", PDO::FETCH_ASSOC);
	$userCloserLogStatus = array();
	while ($r = $userCloserLogStatusRes->fetch()) {
		$userCloserLogStatus[] = $r['status'];
	}
	$userCloserLogQueueRes = $conn->query("SELECT DISTINCT campaign_id FROM `vicidial_closer_log` WHERE `user` = '$user' ORDER BY campaign_id", PDO::FETCH_ASSOC);
	$userCloserLogQueue = array();
	while ($r = $userCloserLogQueueRes->fetch()) {
		$userCloserLogQueue[] = $r['campaign_id'];
	}

	$userDetailQry = "SELECT user_id, `user`, full_name FROM vicidial_users WHERE `user` = '$user'";
	$userRes = $conn->query($userDetailQry, PDO::FETCH_ASSOC);
	$userData = $userRes->fetch();

	$search = isset($_GET['search'])?$_GET['search']:"";
	$params = array();
	$offset = 0;
	$start_date = $end_date = $cat ="";
	if(isset($_GET['page']) && $_GET['page'] != ""){

	}
	if(isset($_GET['page']) && $_GET['page'] != ""){
	  $offset = ($_GET['page'] - 1 )* 50;
	}
	if($search != ""){
		$params['vicidial_closer_log.phone_number'] = $search;
		$params['vicidial_closer_log.campaign_id'] = $search;
		$params['vicidial_closer_log.status'] = $search;
	}
	if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
	}

	$condition = $dateCondition = $ratingConditionVAL = "";
	if(!empty($params)){
		$condition = "AND (";
		foreach($params as $key => $val){
			if($condition == "AND ("){
				$condition .= $key." LIKE '%".$val."%'";
			}else{
				$condition .= " OR ".$key." LIKE '%".$val."%'";
			}
		}
		$condition.= ")";
	}
	if($start_date != "" && $end_date != ""){
		$dateCondition .= " AND vicidial_closer_log.call_date >= '$start_date' AND vicidial_closer_log.call_date <= '$end_date' ";
	}

	if(isset($_GET['cat']) && $_GET['cat'] != ""){
		$cat = $_GET['cat'];
		if(stristr($cat, 'cs') != false){
			$sta = explode("-", $cat);
			$sta = $sta[1];
			if($sta != "") $condition.= " AND vicidial_closer_log.status = '$sta'";
		}
		if(stristr($cat, 'rt') != false){
			$rat = explode("-", $cat);
			$rat = $rat[1];
			if($rat == 'all'){
				if($rat != "") $ratingConditionVAL = " AND vicidial_agent_log.survey_option >= 1";
			}else{
				if($rat != "") $ratingConditionVAL = " AND vicidial_agent_log.survey_option = '$rat'";
			}

		}
		if(stristr($cat, 'qu') != false){
			$qu = explode("-", $cat);
			$qu = $qu[1];
			if($qu != "") $condition = " AND vicidial_closer_log.campaign_id = '$qu'";
		   //; 
		}
		if(stristr($cat, 'st') != false){
			$st = explode("-", $cat);
			$st = $st[1];
			//if($st == 'tf') $condition = "AND vicidial_closer_log.lead_id IN (SELECT lead_id FROM user_call_log WHERE 'user' = '$user' AND lead_id IS NOT NULL) ";
			if($st == 'tf'){
				$sql_t = "SELECT user_call_log.lead_id FROM user_call_log INNER JOIN vicidial_agent_log ON user_call_log.lead_id = vicidial_agent_log.lead_id and user_call_log.user = vicidial_agent_log.user WHERE vicidial_agent_log.lead_id IS NOT NULL AND user_call_log.call_type = 'BLIND_XFER' AND user_call_log.phone_number LIKE '%8000099991111' AND vicidial_agent_log.comments LIKE '%INBOUND%' AND user_call_log.user = '$user' GROUP BY user_call_log.lead_id";
				$tf_res = $conn->query($sql_t, PDO::FETCH_ASSOC);
				$tfs = array();

				while ($result = $tf_res->fetch()) {
					$tfs[] = $result['lead_id'];
				}
				$tfs = implode(',',$tfs);

				$condition = "AND vicidial_closer_log.lead_id IN ($tfs) ";
			}
			if($st == 'ntf'){
				$sql_t = "SELECT user_call_log.lead_id FROM user_call_log INNER JOIN vicidial_agent_log ON user_call_log.lead_id = vicidial_agent_log.lead_id and user_call_log.user = vicidial_agent_log.user WHERE vicidial_agent_log.lead_id IS NOT NULL AND user_call_log.call_type = 'BLIND_XFER' AND user_call_log.phone_number LIKE '%8000099991111' AND vicidial_agent_log.comments LIKE '%INBOUND%' AND user_call_log.user = '$user' GROUP BY user_call_log.lead_id";
				$ntf_res = $conn->query($sql_t, PDO::FETCH_ASSOC);
				$ntfs = array();

				while ($result = $ntf_res->fetch()) {
					$ntfs[] = $result['lead_id'];
				}
				$ntfs = implode(',',$ntfs);
				$condition = "AND vicidial_closer_log.lead_id NOT IN ($ntfs) ";
			}
		}
		//AND vicidial_closer_log.lead_id IN (SELECT lead_id FROM user_call_log WHERE 'user' = '$user' AND lead_id IS NOT NULL);
	}

	$countQry = "SELECT 
		COUNT(*) AS total_rows
	FROM vicidial_closer_log 
		INNER JOIN vicidial_agent_log ON vicidial_agent_log.uniqueid = vicidial_closer_log.uniqueid
		INNER JOIN recording_log ON vicidial_closer_log.lead_id = recording_log.lead_id
		WHERE vicidial_closer_log.user = '$user' $condition $dateCondition $ratingConditionVAL";
	$countRes = $conn->query($countQry, PDO::FETCH_ASSOC);
	$countRes = $countRes->fetch();
	$countRes = $countRes['total_rows'];

	$query = "SELECT 
		vicidial_closer_log.user, vicidial_closer_log.phone_number,vicidial_closer_log.call_date,
		vicidial_agent_log.talk_sec,vicidial_closer_log.campaign_id,
		vicidial_closer_log.status,vicidial_agent_log.survey_option,
		recording_log.location, vicidial_agent_log.uniqueid,
		vicidial_agent_log.lead_id
	FROM vicidial_closer_log 
		INNER JOIN vicidial_agent_log ON vicidial_agent_log.uniqueid = vicidial_closer_log.uniqueid
		INNER JOIN recording_log ON vicidial_closer_log.lead_id = recording_log.lead_id
		WHERE vicidial_closer_log.user = '$user' and vicidial_agent_log.user = '$user' and recording_log.user = '$user' " . $condition ." $dateCondition $ratingConditionVAL
		order by vicidial_closer_log.call_date desc LIMIT 100000";
		
	$res = $conn->query($query, PDO::FETCH_ASSOC);

	$closerLogs = array();
	$closerLeadIds = array();

	while ($result = $res->fetch()) {
		$closerLogs[] = $result;
		if(isset($result['lead_id']) and $result['lead_id'] !='')
		$closerLeadIds[$result['lead_id']] = $result['lead_id'];
	}
//echo '<pre>';
//print_r($closerLogs);  die;
	$closerLeadIdsConcate = implode(",", $closerLeadIds);

	//$res2 = $conn->query("SELECT lead_id FROM user_call_log  WHERE lead_id IN ($closerLeadIdsConcate) GROUP BY lead_id", PDO::FETCH_ASSOC);
	//echo "SELECT lead_id FROM user_call_log  WHERE lead_id IN ($closerLeadIdsConcate) and call_type = 'BLIND_XFER' and phone_number like '%8000099991111' GROUP BY lead_id";exit;
	$res2 = $conn->query("SELECT lead_id FROM user_call_log  WHERE lead_id IN ($closerLeadIdsConcate) and call_type = 'BLIND_XFER' and phone_number like '%8000099991111' GROUP BY lead_id", PDO::FETCH_ASSOC);

	$userCallLogs = array();
	if($res2){
		while ($result2 = $res2->fetch()) {
			$userCallLogs[$result2['lead_id']] = $result2['lead_id'];
		}
	}

	$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
	
	$columnHeader = '';  
	$columnHeader = "#" . "\t" . "Customer Phone" . "\t". "Call Date" . "\t" . "Talk Time" . "\t" . "Queue" . "\t" . "Call Status" . "\t" . "Survey Transfer" . "\t" . "Rating" . "\t" . "Call Recording" . "\t";  
	$setData = ''; 
	$rowData = '';

	$c = 1;
    foreach ($closerLogs as $key => $closerLog) {
		$count = $c++;	
		$talksec = $closerLog['talk_sec'].' Sec';
		if(isset($cat) and $cat == 'st-tf'){ $serveyTrans= 'Yes'; }
		else if(isset($cat) and $cat == 'st-ntf'){ $serveyTrans= 'No'; }
		else{
			if(isset($userCallLogs[$closerLog['lead_id']]) and $userCallLogs[$closerLog['lead_id']] !=''){
			$serveyTrans= 'Yes';
			}
			else if(isset($closerLog['survey_option']) and $closerLog['survey_option'] >=1){
			   $serveyTrans= 'Yes';
			}else
			{
			  $serveyTrans= 'No';
			}
		}		
		
		$value = '"' . $count . '"' . "\t".'"' . $closerLog['phone_number'] . '"' . "\t".'"' . $closerLog['call_date'] . '"' . "\t".'"' . $talksec . '"' . "\t".'"' . $closerLog['campaign_id'] . '"' . "\t".'"' . $closerLog['status'] . '"' . "\t".'"' . $serveyTrans . '"' . "\t".'"' . $closerLog['survey_option'] . '"' . "\t".'"' . $closerLog['location'] . '"' . "\t"; 
		$setData .= trim($value) . "\n"; 
	}

	header("Content-type: application/octet-stream");  
	header("Content-Disposition: attachment; filename=InboudAgentsDetails.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
	echo ucwords($columnHeader) . "\n" . $setData . "\n";
}	

if($_REQUEST['key']==3) {
$search = isset($_GET['search'])? $_GET['search']:""; 
$condition = $dateCondition = "";
$offset = 0;
$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
$start_date = $end_date = "";
	
	
	if($search != ""){
    $params['outcalls.phone_number'] = $search;
    $condition = " AND outcalls.agent IN (SELECT `user` FROM vicidial_users WHERE `user` LIKE '%$search%' OR full_name LIKE '%$search%') ";
	}

	if(isset($_GET['page']) && $_GET['page'] != ""){
	  $offset = ($_GET['page'] - 1 )* 50;
	}

	if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
		$start_date = $_GET['start_date'];
		$end_date = $_GET['end_date'];
	}
	$user = "";
	/*if(!isset($_GET['user']) || @$_GET['user'] == "" ){
		header('Location: ' . 'outbound.php');
	}*/
	$user = $_GET['user'];


	//if($start_date != "" && $end_date != ""){
	//    $dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
	//}
	if($start_date != "" && $end_date != ""){
		$dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
	}else{
		$end_date = date('Y-m-d');
		$start_date = $lastDate;
		$dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
	}

	 /*$countQry = "SELECT
		COUNT(*) AS total_rows
	FROM (SELECT outcalls.agent FROM outcalls 
		INNER JOIN recording_log ON outcalls.lead_id = recording_log.lead_id
	WHERE outcalls.agent is not null $condition $dateCondition GROUP BY outcalls.agent) AS total_rows";
	$countRes = $conn->query($countQry)->fetch();
	$countRes = $countRes['total_rows'];*/

	 $query = "SELECT outcalls.agent, outcalls.survey_option, COUNT(outcalls.agent) AS total_user,SUM(outcalls.call_duration) AS cnt_talk_sec,
	SUM(CASE WHEN call_status IN ('ANSWER') then 1    END) answer , 
	SUM(CASE WHEN call_status NOT IN  ('ANSWER') then 1    END) unanswer , 
	COUNT(case when survey_option <=0  then 0 END ) unrated,
	COUNT(case when survey_option > 0  then 0 END ) rated,
	SUM(CASE
			WHEN survey_option = 5 THEN 10
			WHEN survey_option = 4 THEN 8
			WHEN survey_option = 3 THEN 6
			WHEN survey_option = 2 THEN 2
			WHEN survey_option = 1 THEN -5
			WHEN survey_option = 0 THEN 0
		END) points FROM outcalls 
	WHERE outcalls.agent is not null $condition $dateCondition GROUP BY outcalls.agent ORDER BY points desc LIMIT 100000";

	$res = $conn->query($query, PDO::FETCH_ASSOC);

	$agentLogs = array();
	$userIds = array();
	$leadIds = array();

	while ($result = $res->fetch()) {
		$agentLogs[] = $result;
		$userIds[$result['agent']] = $result['agent'];
	   // $leadIds[$result['lead_id']] = $result['lead_id'];
	}

	$userIdsConcate = implode(",", $userIds);
	//$leadIdsConcate = implode(",", $leadIds);

	//echo "SELECT user_id, full_name FROM vicidial_users WHERE user_id IN ($userIdsConcate)";die;
	if (!empty($userIdsConcate)){
	$res2 = $conn->query("SELECT user, full_name FROM vicidial_users WHERE user IN ($userIdsConcate)", PDO::FETCH_ASSOC);

	$users = array();
	if($res2){

	  while ($result2 = $res2->fetch()) {
		  $users[$result2['user']] = $result2['full_name'];
	  }
	}
	}



	$columnHeader = '';  
	$columnHeader = "Ranking" . "\t" . "Agents" . "\t". "Total Calls" . "\t" . "Talk Time" . "\t" . "AHT" . "\t" . "Answered" . "\t" . "Unanswered" . "\t" . "Rated/Unrated" . "\t" . "Points" . "\t";  
	$setData = ''; 
	$rowData = '';

	foreach ($agentLogs as $key => $agentLog) {
		
		if(isset($agentLog['points']) and $agentLog['points'] !='')
		{
			if($agentLog['points']>=1){
				if($new_flg  == 0){
					$flg = 1;
				}
				$ranking = $i;
				$i++;
			}
			else{ $ranking = '0'; }
		}
		else { $ranking = '0'; }
		
		if(isset($users[$agentLog['agent']])){ $agents= $users[$agentLog['agent']].' / '.$agentLog['agent']; }else{ $agents= 'N/A'.' / '.$agentLog['agent'];}
		$taktime = number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', '').' mins';
		$aht = number_format((float)((number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', ''))/$agentLog['total_user']), 2, '.', '').' min/ call'; 
		if(isset($agentLog['answer'])){ $answerd= $agentLog['answer'];}else{ $answerd= '0';}
		if(isset($agentLog['unanswer'])){ $unanswerd= $agentLog['unanswer'];}else{ $unanswerd= '0';}
		$rating = $agentLog['rated']."/".$agentLog['unrated'];
		
		$value = '"' . $ranking . '"' . "\t".'"' . $agents . '"' . "\t".'"' . $agentLog['total_user'] . '"' . "\t".'"' . $taktime . '"' . "\t".'"' . $aht . '"' . "\t".'"' . $answerd . '"' . "\t".'"' . $unanswerd . '"' . "\t".'"' . $rating . '"' . "\t".'"' . $agentLog['points'] . '"' . "\t"; 
		$setData .= trim($value) . "\n"; 
	}

	header("Content-type: application/octet-stream");  
	header("Content-Disposition: attachment; filename=OutboundAgents.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
	echo ucwords($columnHeader) . "\n" . $setData . "\n"; 
}


if($_REQUEST['key']==4) {
$search = isset($_GET['search'])? $_GET['search']:""; 
$condition = $dateCondition = "";
$offset = 0;
//$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
//$start_date = $end_date = "";

	if($search != ""){
    $params['outcalls.phone_number'] = $search;
    $condition = " AND outcalls.agent IN (SELECT `user` FROM vicidial_users WHERE `user` LIKE '%$search%' OR full_name LIKE '%$search%') ";
}

if(isset($_GET['page']) && $_GET['page'] != ""){
    $offset = ($_GET['page'] - 1 )* 50;
}

if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}
$user = "";
if(!isset($_GET['user']) || @$_GET['user'] == "" ){
    header('Location: ' . 'outbound.php');
}
$user = $_GET['user'];


$search = isset($_GET['search'])?$_GET['search']:"";
$params = array();
$offset = 0;
$start_date = $end_date = $cat ="";
if(isset($_GET['page']) && $_GET['page'] != ""){
    
}
if(isset($_GET['page']) && $_GET['page'] != ""){
    $offset = ($_GET['page'] - 1 )* 50;
}

if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

$condition = $dateCondition = $ratingConditionVAL = "";
if(!empty($params)){
    $condition = "AND (";
    foreach($params as $key => $val){
        if($condition == "AND ("){
            $condition .= $key." LIKE '%".$val."%'";
        }else{
            $condition .= " OR ".$key." LIKE '%".$val."%'";
        }
    }
    $condition.= ")";
}
if($start_date != "" && $end_date != ""){
    $dateCondition .= " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
}

if(isset($_GET['cat']) && $_GET['cat'] != ""){
    $cat = $_GET['cat'];
    if(stristr($cat, 'cs') != false){
        $sta = explode("-", $cat);
        $sta = $sta[1];
        echo "i am here->".$sta;
        if($sta != "" && $sta=='Answered') $condition.= " AND outcalls.call_status  in ('ANSWER')";
        elseif ($sta != "" && $sta=='Unanswered') $condition.= " AND outcalls.call_status not in ('ANSWER')";
    }
    if(stristr($cat, 'rt') != false){
        $rat = explode("-", $cat);
        $rat = $rat[1];
        if($rat == 'all'){
            if($rat != "") $ratingConditionVAL = " AND outcalls.survey_option >= 0";
        }else{
            if($rat != "") $ratingConditionVAL = " AND outcalls.survey_option = '$rat'";
        }
        
    }
    
    if(stristr($cat, 'st') != false){
        $st = explode("-", $cat);
        $st = $st[1];
        //if($st == 'tf') $condition = "AND vicidial_closer_log.lead_id IN (SELECT lead_id FROM user_call_log WHERE 'user' = '$user' AND lead_id IS NOT NULL) ";
        if($st == 'rating'){
            $ratingConditionVAL = " AND outcalls.survey_option >= 1";
            
            
        }
        if($st == 'Unrated'){
            $ratingConditionVAL = " AND outcalls.survey_option <=0";
        }
    }
    //AND vicidial_closer_log.lead_id IN (SELECT lead_id FROM user_call_log WHERE 'user' = '$user' AND lead_id IS NOT NULL);
}

//if($start_date != "" && $end_date != ""){
//    $dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
//}
if($start_date != "" && $end_date != ""){
    $dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
}else{
    $end_date = date('Y-m-d');
    $start_date = $lastDate;
    $dateCondition = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
}

/*$countQry = "SELECT
 COUNT(*) AS total_rows
 FROM (SELECT outcalls.agent FROM outcalls
 INNER JOIN recording_log ON outcalls.lead_id = recording_log.lead_id
 WHERE outcalls.agent ='$user' $condition $dateCondition GROUP BY outcalls.agent) AS total_rows";
 $countRes = $conn->query($countQry)->fetch();
 $countRes = $countRes['total_rows'];*/

$query = "SELECT outcalls.agent, outcalls.survey_option, COUNT(outcalls.agent) AS total_user,SUM(outcalls.call_duration) AS cnt_talk_sec,
SUM(CASE WHEN call_status IN ('ANSWER') then 1    END) answer ,
SUM(CASE WHEN call_status NOT IN  ('ANSWER') then 1    END) unanswer ,
COUNT(case when survey_option <=0  then 0 END ) unrated,
COUNT(case when survey_option > 0  then 0 END ) rated,
SUM(CASE
        WHEN survey_option = 5 THEN 10
        WHEN survey_option = 4 THEN 8
        WHEN survey_option = 3 THEN 6
        WHEN survey_option = 2 THEN 2
        WHEN survey_option = 1 THEN -5
        WHEN survey_option = 0 THEN 0
    END) points FROM outcalls
WHERE outcalls.agent ='$user' $condition $dateCondition GROUP BY outcalls.agent ORDER BY points desc LIMIT 100000";

$res = $conn->query($query, PDO::FETCH_ASSOC);

$agentLogs = array();
$userIds = array();
$leadIds = array();

while ($result = $res->fetch()) {
    $agentLogs[] = $result;
    $userIds[$result['agent']] = $result['agent'];
    // $leadIds[$result['lead_id']] = $result['lead_id'];
}

$userIdsConcate = implode(",", $userIds);
//$leadIdsConcate = implode(",", $leadIds);

//echo "SELECT user_id, full_name FROM vicidial_users WHERE user_id IN ($userIdsConcate)";die;
if (!empty($userIdsConcate)){
    $res2 = $conn->query("SELECT user, full_name FROM vicidial_users WHERE user IN ($userIdsConcate)", PDO::FETCH_ASSOC);
    
    $users = array();
    if($res2){
        
        while ($result2 = $res2->fetch()) {
            $users[$result2['user']] = $result2['full_name'];
        }
    }
}

/*** widget starts ***/
$widgetCondition = "";
if($search != ""){
    $widgetCondition .=  "  (outcalls.agent LIKE '%$search%') ";
}


if($search == "" && $dateCondition == ""){
    $totalOutboundCallSql = "SELECT COUNT(*) AS total_Outbound, MAX(call_answer_time) as call_answer_time FROM outcalls   ORDER BY call_answer_time DESC";
}else{
    $totalOutboundCallSql = "SELECT COUNT(*) AS total_Outbound, MAX(call_answer_time) as call_answer_time FROM outcalls  WHERE outcalls.agent ='$user' $condition $dateCondition ORDER BY call_answer_time DESC";
}
//echo $totalOutboundCallSql;
$totalOutboundCallRes = $conn->query($totalOutboundCallSql, PDO::FETCH_ASSOC);
$totalOutboundCall = $totalOutboundCallRes->fetch();
//print_r($totalOutboundCall); die();
//$totalOutboundCall = $totalOutboundCall['total_Outbound'];
/*
 if($totalOutboundCall > 1000){
 $totalOutboundCall = $totalOutboundCall/1000;
 $totalOutboundCall = $totalOutboundCall. " K";
 }*/
//echo  "----- ".$search."  ------  ".$dateCondition;
if($search == "" && $dateCondition==""){
    //echo "i am here";
    echo $outboundCallLastTwoDaySql = "SELECT COUNT(*) AS current_Outbound FROM outcalls  WHERE  DATE(call_answer_time) >= '$lastDate' and outcalls.call_status='ANSWER'";
}else{
    $outboundCallLastTwoDaySql = "SELECT COUNT(*) AS current_outbound FROM outcalls  WHERE outcalls.agent ='$user'  $condition $dateCondition $ratingConditionVAL ";
}
//echo "*****".$outboundCallLastTwoDaySql;
$outboundCallLastTwoDayRes = $conn->query($outboundCallLastTwoDaySql, PDO::FETCH_ASSOC);
$outboundCallLastTwoDay = $outboundCallLastTwoDayRes->fetch();
$outboundCallLastTwoDay = $outboundCallLastTwoDay['current_outbound'];


$outboundAnswerCallSql = "SELECT COUNT(*) AS current_outbound FROM outcalls  WHERE outcalls.agent ='$user'and outcalls.call_status='ANSWER'  $condition $dateCondition $ratingConditionVAL ";
$outboundAnswerCall = $conn->query($outboundAnswerCallSql, PDO::FETCH_ASSOC);
$outboundAnswerCallRecord = $outboundAnswerCall->fetch();
$outboundAnswerCallRecordTotal = $outboundAnswerCallRecord['current_outbound'];

//for find last of SURVEY PARTICIPATION
$surveyParticipationLastSql = "SELECT outcalls.call_answer_time,outcalls.survey_option
 FROM outcalls  WHERE outcalls.survey_option NOT IN (0)  $condition $dateCondition $ratingConditionVAL
 ORDER BY outcalls.call_answer_time DESC LIMIT 1";
$surveyParticipationLastRes = $conn->query($surveyParticipationLastSql, PDO::FETCH_ASSOC);
$surveyParticipationLast = $surveyParticipationLastRes->fetch();


if($search == "" && $dateCondition == ""){
    $currentTalkSecSql = "SELECT SUM(call_duration) AS talk_sec_sum FROM outcalls  WHERE  DATE(call_answer_time) >= '$lastDate'";
}else{
    $currentTalkSecSql = "SELECT SUM(call_duration) AS talk_sec_sum FROM outcalls  WHERE outcalls.agent ='$user'  $dateCondition $condition  $ratingConditionVAL";
}
$currentTalkSecRes = $conn->query($currentTalkSecSql, PDO::FETCH_ASSOC);
$currentTalkSec = $currentTalkSecRes->fetch();
$currentTalkSec = $currentTalkSec['talk_sec_sum'];

if($search == "" && $dateCondition == ""){
    $currSurveyPartSql = "SELECT COUNT(*) AS survey_participation FROM outcalls  WHERE  survey_option > 0 AND DATE(call_answer_time) >= '$lastDate'";
}else{
    $currSurveyPartSql = "SELECT COUNT(*) AS survey_participation FROM outcalls  WHERE outcalls.agent ='$user' AND survey_option > 0 $dateCondition $condition  $ratingConditionVAL";
}
$currSurveyPartRes = $conn->query($currSurveyPartSql, PDO::FETCH_ASSOC);
$currSurveyPart = $currSurveyPartRes->fetch();
$currSurveyPart = $currSurveyPart['survey_participation'];

/***** GRAPH CALCULATION *****/

$graphCondition = $dateCondCloserLog = "";
if($search != ""){
    $graphCondition.= " AND (vicidial_closer_log.user LIKE '%$search%') ";
}
if($start_date != "" && $end_date != ""){
    $dateCondCloserLog = " AND date_format(outcalls.call_answer_time,'%Y-%m-%d') >= '$start_date' AND date_format(outcalls.call_answer_time,'%Y-%m-%d') <= '$end_date' ";
}


//$totalCallsRes = $conn->query($totalCallsSql, PDO::FETCH_ASSOC);
//$totalCalls = $totalCallsRes->fetch();
//$totalCalls = $totalCalls['total_calls'];
$totalunanswered_sql="SELECT COUNT(*) unanswere FROM outcalls WHERE call_status not in ('ANSWER') and outcalls.agent='$user' $graphCondition $dateCondCloserLog $condition $dateCondition $ratingConditionVAL";
$totalunansweredRes = $conn->query($totalunanswered_sql, PDO::FETCH_ASSOC);
$totalCallsUnanswered = $totalunansweredRes->fetch();
$Unanswered_Count = $totalCallsUnanswered['unanswere'];


$totalCalls = $outboundCallLastTwoDay; // Temporary adjustment
if($search == "" && $dateCondCloserLog == ""){
    $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM outcalls WHERE call_status = 'ANSWER' and outcalls.agent='$user' and   DATE(outcalls.call_answer_time) >= '$lastDate' $condition $dateCondition $ratingConditionVAL";
}else{ $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM outcalls WHERE call_status = 'ANSWER' and  outcalls.agent='$user' $graphCondition $dateCondCloserLog $condition $dateCondition $ratingConditionVAL";
}
//echo $totalTransferedCallsSql;
$totalTransferedCallsRes = $conn->query($totalTransferedCallsSql, PDO::FETCH_ASSOC);
$totalTransferedCalls = $totalTransferedCallsRes->fetch();
$totalTransferedCalls = $totalTransferedCalls['matched_log'];
$total_not_transferred = $totalCalls - $totalTransferedCalls;
//print_r($totalTransferedCalls);die;
if($totalCalls != 0){
    $transferRate = ($totalTransferedCalls/$totalCalls)*100;
}else{
    $transferRate = 0;
}
if($totalTransferedCalls == 0 && $totalCalls == 0){
    $transferRateArr = array(
        array(
            'label' => 'No Data Available', 'value' => 100
        )
    );
}else{
    $transferRateArr = array(
        array('label' => 'Answered Calls', 'value' => $totalTransferedCalls),
        array('label' => 'Unanswered Calls', 'value'=> $Unanswered_Count) //$total_not_transferred = $totalCalls - $totalTransferedCalls;
    );
}

if($dateCondition == ""){
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_one,
      SUM(IF(survey_option = '2', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_two,
      SUM(IF(survey_option = '3', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_three,
      SUM(IF(survey_option = '4', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_four,
      SUM(IF(survey_option = '5', 1, 0)  AND DATE(call_answer_time) >= '$lastDate') AS opt_five
    FROM outcalls where outcalls.agent ='$user' $condition  $ratingConditionVAL";
}else{
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0)  $dateCondition) AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0)  $dateCondition) AS opt_one,
      SUM(IF(survey_option = '2', 1, 0)  $dateCondition) AS opt_two,
      SUM(IF(survey_option = '3', 1, 0)  $dateCondition) AS opt_three,
      SUM(IF(survey_option = '4', 1, 0)  $dateCondition) AS opt_four,
      SUM(IF(survey_option = '5', 1, 0)  $dateCondition) AS opt_five
    FROM outcalls where outcalls.agent ='$user'  $dateCondition $condition  $ratingConditionVAL ";
}
//echo $custRatingsSql;
$custRatingsRes = $conn->query($custRatingsSql, PDO::FETCH_ASSOC);
$custRatings = $custRatingsRes->fetch();
if($custRatings['opt_one'] == 0 && $custRatings['opt_two'] == 0 && $custRatings['opt_three'] == 0 && $custRatings['opt_four'] == 0 && $custRatings['opt_five'] == 0){
    $custRatingsArr[] = 1;
}else{
    $custRatingsArr[] = 0;//$custRatings['opt_zero'];
}
$custRatingsArr[] = $custRatings['opt_one'];
$custRatingsArr[] = $custRatings['opt_two'];
$custRatingsArr[] = $custRatings['opt_three'];
$custRatingsArr[] = $custRatings['opt_four'];
$custRatingsArr[] = $custRatings['opt_five'];

$userDetailQry = "SELECT user_id, `user`, full_name FROM vicidial_users WHERE `user` = '$user'";
$userRes = $conn->query($userDetailQry, PDO::FETCH_ASSOC);
$userData = $userRes->fetch();

$countQry = "SELECT Count(*) total_rows
 FROM outcalls
 WHERE outcalls.agent='$user' $condition $dateCondition $ratingConditionVAL";
$countRes = $conn->query($countQry, PDO::FETCH_ASSOC);
$countRes = $countRes->fetch();
$countRes = $countRes['total_rows'];

 $query_agent="SELECT outcalls.*, vicidial_users.full_name
 FROM outcalls   join vicidial_users ON outcalls.agent=vicidial_users.user
 WHERE outcalls.agent='$user'  $dateCondition $ratingConditionVAL $condition
 ORDER BY outcalls.call_answer_time desc LIMIT 100000";

$res = $conn->query($query_agent, PDO::FETCH_ASSOC);

$closerLogs = array();
$closerLeadIds = array();

while ($result = $res->fetch()) {
    $closerLogs[] = $result;
    
}
	
	$columnHeader = '';  
	$columnHeader = "#" . "\t" . "Customer Phone" . "\t". "Call Date" . "\t" . "Talk Time" . "\t". "Call Status" . "\t" . "Rated" . "\t" . "Rating" . "\t" . "Call Recording" . "\t";  
	$setData = ''; 
	$rowData = '';

	$c = 1;
    foreach ($closerLogs as $key => $closerLog) {
		$count = $c++;	
		$talksec = $closerLog['call_duration'].' Sec';
		if(isset($closerLog['survey_option']) and $closerLog['survey_option'] >=1){ $rated= 'Yes'; }
		else{ $rated= 'No'; }
		if ($closerLog['call_status']=='ANSWER'){ $recording=$audioFilePath.$closerLog['call_recording'];} else { $recording="";}
		
		$value = '"' . $count . '"' . "\t".'"' . $closerLog['customer_phone'] . '"' . "\t".'"' . $closerLog['call_answer_time'] . '"' . "\t".'"' . $talksec . '"' . "\t".'"' . $closerLog['call_status'] . '"' . "\t".'"' . $rated . '"' . "\t".'"' . $current . '"' . "\t".'"' . $recording . '"' . "\t"; 
		$setData .= trim($value) . "\n"; 
	}

	header("Content-type: application/octet-stream");  
	header("Content-Disposition: attachment; filename=OutboundAgentsDetails.xls");  
	header("Pragma: no-cache");  
	header("Expires: 0");  
	echo ucwords($columnHeader) . "\n" . $setData . "\n";
} 
 
 ?> 
