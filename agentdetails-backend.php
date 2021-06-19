<?php

require_once("inc/db.php");

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
    order by vicidial_closer_log.call_date desc LIMIT $offset, 50";
    
$res = $conn->query($query, PDO::FETCH_ASSOC);

$closerLogs = array();
$closerLeadIds = array();

while ($result = $res->fetch()) {
    $closerLogs[] = $result;
    if(isset($result['lead_id']) and $result['lead_id'] !='')
    $closerLeadIds[$result['lead_id']] = $result['lead_id'];
}

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


/* GRAPH CALCULATION  */
if($condition == "" && $dateCondition == ""){
    $totalCallsResSql = "SELECT COUNT(*) total_calls FROM vicidial_closer_log WHERE `user` = '$user' AND lead_id IS NOT NULL AND DATE(call_date) >= '$lastDate'";
}else{
    $totalCallsResSql = "SELECT COUNT(*) total_calls FROM vicidial_closer_log WHERE `user` = '$user' AND lead_id IS NOT NULL $condition $dateCondition";
}
$totalCallsRes = $conn->query($totalCallsResSql, PDO::FETCH_ASSOC);
$totalCalls = $totalCallsRes->fetch();
$totalCalls = $totalCalls['total_calls'];

if($condition == "" && $dateCondition == ""){
    $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM vicidial_closer_log INNER JOIN user_call_log ON user_call_log.lead_id = vicidial_closer_log.lead_id WHERE user_call_log.call_type = 'BLIND_XFER' and user_call_log.phone_number like '%8000099991111' AND vicidial_closer_log.user = '$user' AND DATE(vicidial_closer_log.call_date) >= '$lastDate'";
}else{
    $totalTransferedCallsSql = "SELECT COUNT(*) matched_log FROM vicidial_closer_log INNER JOIN user_call_log ON user_call_log.lead_id = vicidial_closer_log.lead_id WHERE user_call_log.call_type = 'BLIND_XFER' and user_call_log.phone_number like '%8000099991111' AND vicidial_closer_log.user = '$user' $condition $dateCondition";
}
$totalTransferedCallsRes = $conn->query( $totalTransferedCallsSql, PDO::FETCH_ASSOC);
$totalTransferedCalls = $totalTransferedCallsRes->fetch();

$totalTransferedCalls = $totalTransferedCalls['matched_log'];
if($totalCalls != 0){
    $transferRate = ($totalTransferedCalls/$totalCalls)*100;
}else{
    $transferRate = 0;
}
$not_transfer_call = $totalCalls - $totalTransferedCalls;
if($totalTransferedCalls == 0 && $totalCalls == 0){
    $transferRateArr[] = array('label' => 'No data', 'value' => 100);
}else{
    $transferRateArr = array(array('label' => 'Transferred Calls', 'value' => $totalTransferedCalls), array('label' => 'Not Transferred', 'value'=> $not_transfer_call));
}

if($start_date != "" && $end_date != ""){
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_one,
      SUM(IF(survey_option = '2', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_two,
      SUM(IF(survey_option = '3', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_three,
      SUM(IF(survey_option = '4', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_four,
      SUM(IF(survey_option = '5', 1, 0) AND comments LIKE '%INBOUND%' AND DATE(event_time) >= '$lastDate' AND `user` = '$user') AS opt_five
    FROM vicidial_agent_log";
}elseif ($ratingConditionVAL != "") {
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_one,
      SUM(IF(survey_option = '2', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_two,
      SUM(IF(survey_option = '3', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_three,
      SUM(IF(survey_option = '4', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_four,
      SUM(IF(survey_option = '5', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user') AS opt_five
    FROM vicidial_agent_log";
}else{
    $dateCond = " AND vicidial_agent_log.event_time >= '$start_date' AND vicidial_agent_log.event_time <= '$end_date' ";
    $dateCond = '';
    $custRatingsSql = "SELECT
      SUM(IF(survey_option = '0', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_zero,
      SUM(IF(survey_option = '1', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_one,
      SUM(IF(survey_option = '2', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_two,
      SUM(IF(survey_option = '3', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_three,
      SUM(IF(survey_option = '4', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_four,
      SUM(IF(survey_option = '5', 1, 0) AND comments LIKE '%INBOUND%' AND `user` = '$user' $dateCond) AS opt_five
    FROM vicidial_agent_log";
}
//echo $custRatingsSql;exit;
$custRatingsRes = $conn->query($custRatingsSql, PDO::FETCH_ASSOC);
$custRatings = $custRatingsRes->fetch();

//echo '<pre>';print_r($custRatings);echo '</pre>';exit;

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

if($condition == "" && $dateCondition == ""){
    $callByQueueSql = "SELECT COUNT(*) as total_calls, campaign_id FROM vicidial_closer_log WHERE USER = '$user' AND DATE(call_date) >= $lastDate GROUP BY campaign_id";
}else{
    $callByQueueSql = "SELECT COUNT(*) as total_calls, campaign_id FROM vicidial_closer_log WHERE USER = '$user' $condition $dateCondition GROUP BY campaign_id";
}
$callByQueueRes = $conn->query($callByQueueSql, PDO::FETCH_ASSOC);
$callByQueues = array();
while($call = $callByQueueRes->fetch()){
  $callByQueues[] = array('label' => $call['campaign_id'], 'value' => $call['total_calls']);
}
if(empty($callByQueues)){
    $callByQueues[] = array('label' => 'No data', 'value' => 100);
}

if($condition == "" && $dateCondition == ""){
    $districtionSql = "SELECT COUNT(*) AS status_count, `status` FROM vicidial_closer_log WHERE `user` = '$user' AND DATE(call_date) >= $lastDate GROUP BY `status`";
}else{
    $districtionSql = "SELECT COUNT(*) AS status_count, `status` FROM vicidial_closer_log WHERE `user` = '$user' $condition $dateCondition GROUP BY `status`";
}
$districtionRes = $conn->query($districtionSql, PDO::FETCH_ASSOC);
$districtionCalls = array();
while($call = $districtionRes->fetch()){
  $districtionCalls[] = array('label' => $call['status'], 'value' => $call['status_count']);
}
if(empty($districtionCalls)){
    $districtionCalls[] = array('label' => 'No data', 'value' => 100);
}
