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

