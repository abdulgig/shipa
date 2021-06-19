<?php
require_once("inc/db.php");

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

//print_r($liveagents); die;

while ($resultagent = $get_live_agaent->fetch()) 
{
    $vicidial_netsuite_agents[] = $resultagent; 
}
