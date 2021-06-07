<?php

require_once("inc/db.php");

$search = isset($_GET['search'])?$_GET['search']:"";
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
WHERE outcalls.agent ='$user' $condition $dateCondition GROUP BY outcalls.agent ORDER BY points desc LIMIT $offset, 50";

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
/*
 if($search == "" && $dateCondition == ""){
 $totalTalkSecSql = "SELECT SUM(call_answer_time) AS talk_sec_sum FROM outcalls  WHERE outcalls.agent ='$user'";
 }else{
 $totalTalkSecSql = "SELECT SUM(call_answer_time) AS talk_sec_sum FROM outcalls  WHERE outcalls.agent ='$user' $condition $dateCondition";
 }
 $totalTalkSecRes = $conn->query($totalTalkSecSql, PDO::FETCH_ASSOC);
 $totalTalkSec = $totalTalkSecRes->fetch();
 $totalTalkSec = $totalTalkSec['talk_sec_sum'];
 if($totalTalkSec > 1000){
 $totalTalkSec = $totalTalkSec/1000;
 $totalTalkSec = $totalTalkSec. " K";
 }
 */
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
 ORDER BY outcalls.call_answer_time desc LIMIT $offset, 50";

$res = $conn->query($query_agent, PDO::FETCH_ASSOC);

$closerLogs = array();
$closerLeadIds = array();

while ($result = $res->fetch()) {
    $closerLogs[] = $result;
    
}
