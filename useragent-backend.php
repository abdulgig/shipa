<?php

require_once("inc/db.php");

$search = isset($_GET['search'])?$_GET['search']:""; 
$condition = $dateCondition = "";
$offset = 0;
$lastDate = date('Y-m-d', strtotime('-2 day', strtotime(date('Y-m-d'))));
$start_date = $end_date = "";

if($search != ""){
    $params['vicidial_agent_log.phone_number'] = $search;
    $condition = " AND vicidial_agent_log.user IN (SELECT `user` FROM vicidial_users WHERE `user` LIKE '%$search%' OR full_name LIKE '%$search%') ";
}

if(isset($_GET['page']) && $_GET['page'] != ""){
  $offset = ($_GET['page'] - 1 )* 50;
}

if(isset($_GET['start_date']) && $_GET['start_date'] != "" && isset($_GET['end_date']) && $_GET['end_date'] != ""){
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

//if($start_date != "" && $end_date != ""){
//    $dateCondition = " AND vicidial_agent_log.event_time >= '$start_date' AND vicidial_agent_log.event_time <= '$end_date' ";
//}
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

// count total INCALLS
$incalls = "SELECT  COUNT(*) AS total_rows FROM vicidial_netsuite_agents AS total_rows WHERE status='INCALL' ";
$incallsRes = $conn->query($incalls)->fetch();
$incallsRes = $incallsRes['total_rows'];

// count total IDLE
$idle = "SELECT  COUNT(*) AS total_rows FROM vicidial_netsuite_agents AS total_rows WHERE status='IDLE' ";
$idleRes = $conn->query($idle)->fetch();
$idleRes = $idleRes['total_rows'];

$live_agent = "SELECT * FROM vicidial_netsuite_agents ";
$get_live_agaent = $conn->query($live_agent, PDO::FETCH_ASSOC);
$vicidial_netsuite_agents=array();
//$liveagents = $get_live_agaent->fetch();
while ($resultagent = $get_live_agaent->fetch()) 
{
    $vicidial_netsuite_agents[] = $resultagent; 
}


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
WHERE vicidial_agent_log.comments LIKE '%INBOUND%' $condition $dateCondition GROUP BY vicidial_agent_log.user ORDER BY points desc LIMIT $offset, 50";

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
//SUM(CASE WHEN call_status = ANSWER THEN 1 ELSE 0 END) Answer_Call
//echo "SELECT user_id, full_name FROM vicidial_users WHERE user_id IN ($userIdsConcate)";die;
$res2 = $conn->query("SELECT user, full_name FROM vicidial_users WHERE user IN ($userIdsConcate)", PDO::FETCH_ASSOC);

$users = array();
if($res2){

  while ($result2 = $res2->fetch()) {
      $users[$result2['user']] = $result2['full_name'];
  }
}

//$trfCallQry = "SELECT COUNT(*) AS total_trf_call, user_call_log.user FROM user_call_log
//INNER JOIN vicidial_agent_log ON user_call_log.lead_id = vicidial_agent_log.lead_id 
//WHERE vicidial_agent_log.lead_id IS NOT NULL AND user_call_log.call_type = 'BLIND_XFER' AND user_call_log.phone_number LIKE '%8000099991111' 
//AND vicidial_agent_log.comments LIKE '%INBOUND%' $condition $dateCondition
//GROUP BY user_call_log.user";

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

/*** widget starts ***/
$widgetCondition = "";
if($search != ""){
  $widgetCondition .=  " AND (vicidial_agent_log.user LIKE '%$search%') ";
}


if($search == "" && $dateCondition == ""){
  $totalInboundCallSql = "SELECT COUNT(*) AS total_inbound, MAX(event_time) as event_time FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' ORDER BY event_time DESC";
}else{
  $totalInboundCallSql = "SELECT COUNT(*) AS total_inbound, MAX(event_time) as event_time FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' $widgetCondition $dateCondition ORDER BY event_time DESC";
}
$totalInboundCallRes = $conn->query($totalInboundCallSql, PDO::FETCH_ASSOC);
$totalInboundCall = $totalInboundCallRes->fetch();

//$totalInboundCall = $totalInboundCall['total_inbound'];

/*if($totalInboundCall > 1000){
    $totalInboundCall = $totalInboundCall/1000;
    $totalInboundCall = $totalInboundCall. " K";
}*/

if($search == "" && $dateCondition == ""){
  $inboundCallLastTwoDaySql = "SELECT COUNT(*) AS current_inbound FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate'";
}else{
    $inboundCallLastTwoDaySql = "SELECT COUNT(*) AS current_inbound FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' $widgetCondition $dateCondition ";
}

$inboundCallLastTwoDayRes = $conn->query($inboundCallLastTwoDaySql, PDO::FETCH_ASSOC);
$inboundCallLastTwoDay = $inboundCallLastTwoDayRes->fetch();
$inboundCallLastTwoDay = $inboundCallLastTwoDay['current_inbound'];


if($search == "" && $dateCondition == ""){
    $totalTalkSecSql = "SELECT SUM(talk_sec) AS talk_sec_sum FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%'";
}else{
    $totalTalkSecSql = "SELECT SUM(talk_sec) AS talk_sec_sum FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' $condition $dateCondition";
}
$totalTalkSecRes = $conn->query($totalTalkSecSql, PDO::FETCH_ASSOC);
$totalTalkSec = $totalTalkSecRes->fetch();
$totalTalkSec = $totalTalkSec['talk_sec_sum'];
if($totalTalkSec > 1000){
    $totalTalkSec = $totalTalkSec/1000;
    $totalTalkSec = $totalTalkSec. " K";
}

if($search == "" && $dateCondition == ""){
    $currentTalkSecSql = "SELECT SUM(talk_sec) AS talk_sec_sum FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate'";
}else{
    $currentTalkSecSql = "SELECT SUM(talk_sec) AS talk_sec_sum FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' $widgetCondition $dateCondition";
}
$currentTalkSecRes = $conn->query($currentTalkSecSql, PDO::FETCH_ASSOC);
$currentTalkSec = $currentTalkSecRes->fetch();
$currentTalkSec = $currentTalkSec['talk_sec_sum'];

if($search == "" && $dateCondition == ""){
    $avgHandleSql = "SELECT SUM(talk_sec)/COUNT(*) AS avg_handle FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%'";
}else{
    $avgHandleSql = "SELECT SUM(talk_sec)/COUNT(*) AS avg_handle FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' $dateCondition";
}
$avgHandleRes = $conn->query($avgHandleSql, PDO::FETCH_ASSOC);
$avgHandle = $avgHandleRes->fetch();
$avgHandle = $totalTalkSec;

$currentAvgHandleRes = $conn->query("SELECT (SUM(talk_sec)/60)/COUNT(*) AS avg_handle FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate'", PDO::FETCH_ASSOC);
$currentAvgHandle = $currentAvgHandleRes->fetch();
$currentAvgHandle = $currentAvgHandle['avg_handle'];

if($search == "" && $dateCondition == ""){
    $surveyParticipationSql = "SELECT COUNT(*) AS survey_participation FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND survey_option > 0";
}else{
    $surveyParticipationSql = "SELECT COUNT(*) AS survey_participation FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND survey_option > 0 $dateCondition";
}

$surveyParticipationRes = $conn->query($surveyParticipationSql, PDO::FETCH_ASSOC);
$surveyParticipation = $surveyParticipationRes->fetch();
$surveyParticipation = $surveyParticipation['survey_participation'];

//for find last of SURVEY PARTICIPATION
$surveyParticipationLastSql = "SELECT call_date FROM user_call_log
INNER JOIN vicidial_agent_log ON user_call_log.lead_id = vicidial_agent_log.lead_id
WHERE vicidial_agent_log.lead_id IS NOT NULL AND user_call_log.call_type = 'BLIND_XFER' AND user_call_log.phone_number LIKE '%8000099991111' AND vicidial_agent_log.comments LIKE '%INBOUND%' ORDER BY call_date DESC LIMIT 1";
$surveyParticipationLastRes = $conn->query($surveyParticipationLastSql, PDO::FETCH_ASSOC);
$surveyParticipationLast = $surveyParticipationLastRes->fetch();

if($search == "" && $dateCondition == ""){
    $currSurveyPartSql = "SELECT COUNT(*) AS survey_participation FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND survey_option > 0 AND DATE(event_time) >= '$lastDate'";
}else{
    $currSurveyPartSql = "SELECT COUNT(*) AS survey_participation FROM vicidial_agent_log  WHERE comments LIKE '%INBOUND%' AND survey_option > 0 $dateCondition";
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
    $dateCondCloserLog = " AND vicidial_closer_log.call_date >= '$start_date' AND vicidial_closer_log.call_date <= '$end_date' ";
}

if($search == "" && $dateCondition == ""){
    $totalCallsSql = "SELECT COUNT(*) total_calls FROM vicidial_closer_log WHERE lead_id IS NOT NULL AND DATE(call_date) >= '$lastDate'";
}else{
    $totalCallsSql = "SELECT COUNT(*) total_calls FROM vicidial_closer_log WHERE lead_id IS NOT NULL $graphCondition $dateCondCloserLog";
}
//$totalCallsRes = $conn->query($totalCallsSql, PDO::FETCH_ASSOC);
//$totalCalls = $totalCallsRes->fetch();
//$totalCalls = $totalCalls['total_calls'];
$totalCalls = $inboundCallLastTwoDay; // Temporary adjustment
if($search == "" && $dateCondCloserLog == ""){
    $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM vicidial_closer_log INNER JOIN user_call_log ON user_call_log.lead_id = vicidial_closer_log.lead_id WHERE user_call_log.call_type = 'BLIND_XFER' and user_call_log.phone_number like '%8000099991111' AND DATE(vicidial_closer_log.call_date) >= '$lastDate'";
   }else{ $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM vicidial_closer_log INNER JOIN user_call_log ON user_call_log.lead_id = vicidial_closer_log.lead_id WHERE user_call_log.call_type = 'BLIND_XFER' and user_call_log.phone_number like '%8000099991111' $graphCondition $dateCondCloserLog";
}

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
        array('label' => 'Transferred Calls', 'value' => $totalTransferedCalls),
        array('label' => 'NoT Transferred', 'value'=> $total_not_transferred) //$total_not_transferred = $totalCalls - $totalTransferedCalls;
                        );
}

if($dateCondition == ""){
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_one,
      SUM(IF(survey_option = '2', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_two,
      SUM(IF(survey_option = '3', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_three,
      SUM(IF(survey_option = '4', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_four,
      SUM(IF(survey_option = '5', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate') AS opt_five
    FROM vicidial_agent_log";
}else{
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_one,
      SUM(IF(survey_option = '2', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_two,
      SUM(IF(survey_option = '3', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_three,
      SUM(IF(survey_option = '4', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_four,
      SUM(IF(survey_option = '5', 1, 0) AND comments LIKE '%INBOUND%' $dateCondition) AS opt_five
    FROM vicidial_agent_log";
}
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

if($search == "" && $dateCondCloserLog){
    $inboundQueueSql = "SELECT COUNT(*) AS total_call, campaign_id FROM `vicidial_closer_log` WHERE DATE(vicidial_closer_log.call_date) >= '$lastDate' GROUP BY campaign_id";
}else{
    $inboundQueueSql = "SELECT COUNT(*) AS total_call, campaign_id FROM `vicidial_closer_log` WHERE vicidial_closer_log.user LIKE '%$search%' $dateCondCloserLog GROUP BY campaign_id";
}
$inboundQueues = array();
$inboundQueueRes = $conn->query($inboundQueueSql, PDO::FETCH_ASSOC);
while($queue = $inboundQueueRes->fetch()){
  $inboundQueues[] = $queue;
}
