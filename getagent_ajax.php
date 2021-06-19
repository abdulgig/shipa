 <?php 
 include "useragent-live.php"; 
 
 //include "outbound-backend.php";
 
 function timeAgo($time_ago){
$cur_time 	= time();
$time_elapsed 	= $cur_time - $time_ago;
$seconds 	= $time_elapsed ;
$minutes 	= round($time_elapsed / 60 );
$hours 		= round($time_elapsed / 3600);
$days 		= round($time_elapsed / 86400 );
$weeks 		= round($time_elapsed / 604800);
$months 	= round($time_elapsed / 2600640 );
$years 		= round($time_elapsed / 31207680 );
// Seconds
if($seconds <= 60){
	echo "$seconds seconds ago";
}
//Minutes
else if($minutes <=60){
	if($minutes==1){
		echo "one minute ago";
	}
	else{
		echo "$minutes minutes ago";
	}
}
//Hours
else if($hours <=24){
	if($hours==1){
		echo "an hour ago";
	}else{
		echo "$hours hours ago";
	}
}
//Days
else if($days <= 7){
	if($days==1){
		echo "yesterday";
	}else{
		echo "$days days ago";
	}
}
//Weeks
else if($weeks <= 4.3){
	if($weeks==1){
		echo "a week ago";
	}else{
		echo "$weeks weeks ago";
	}
}
//Months
else if($months <=12){
	if($months==1){
		echo "a month ago";
	}else{
		echo "$months months ago";
	}
}
//Years
else{
	if($years==1){
		echo "one year ago";
	}else{
		echo "$years years ago";
	}
}
}


 
 ?>  


  <table class="table m-0 table-custom text-center">
					<thead class="thead-light" style="background:#f4f4f4;">
					<tr>
						<th class="text-center">Agent</th>
						<th class="text-center">Status</th>
						<th class="text-center">Dialed Number</th>
						<th class="text-center">Time Elaspsed</th>
						<th class="text-center">Status changed</th>                                            
				<!--		<th class="text-center">Last Call</th>    -->                                                                         
					</tr>
					</thead>
                    
					<?php 
					
					//print_r($vicidial_netsuite_agents); die;
					
					$counts = count($vicidial_netsuite_agents);
					if($counts > 0  ) { 
					foreach($vicidial_netsuite_agents as $key =>$rowliveagent ) { 
				    //$tileepapes= time_elapsed_string($rowliveagent['lastupdate']) ;
					$time_ago =strtotime($rowliveagent['lastupdate']);
                 
					?>                                      
							<tr> 
							
							                                                   
								<td><?php echo $rowliveagent['agent'] ;?></td>
								<td>
								<?php if($rowliveagent['status']=="INCALL") { ?>
								<span style="border-radius:50%;background-color: #38AB38;width: 14px;height: 13px;display: inline-block;"></span>
								<span style="color:#38AB38; font-weight:bold;"> <?php echo $rowliveagent['status'] ;?></span>
								<?php } else { ?>
								<span style="border-radius:50%;background-color:#FFCA33;width: 14px;height: 13px;display: inline-block;"></span>
								<span style="color:#FFCA33; font-weight:bold;"> <?php echo $rowliveagent['status'] ;?></span>
								
								<?php } ?>
								</td>
								<td><?php echo $rowliveagent['dialed_number'] ;?></td>
								<td> 
							      <?php echo timeAgo($time_ago); ?>
							    </td>
								<td><?php echo $rowliveagent['lastupdate'] ;?></td>
						<!--		<td><?php //echo $rowliveagent['dialed_number'] ;?></td>  -->
								                                                                                                   
							</tr>
							
							<?php } ?>
							
					<?php } else {
						echo "Records Not Found";
					}
						?>
						
					   

</table>	





















					   
