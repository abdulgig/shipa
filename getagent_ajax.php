 <?php 
 include "useragent-backend.php"; 
 function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
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
						<th class="text-center">Last Call</th>                                                                             
					</tr>
					</thead>
					<tbody> 
					<?php 
					$counts = count($vicidial_netsuite_agents);
					if($counts > 0  ) { 
					foreach($vicidial_netsuite_agents as $key => $rowliveagent ) { 
				    $tileepapes= time_elapsed_string($rowliveagent['lastupdate']) ;
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
								<td><?php echo  $tileepapes; ?></td>
								<td><?php echo $rowliveagent['lastupdate'] ;?></td>
								<td>--</td>  
								                                                                                                   
							</tr>
							
							<?php } ?>
							
					<?php } else {
						echo "Records Not Found";
					}
						?>
						
					   
					</tbody>
				</table>                    