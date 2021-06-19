<?php
include "useragent-live.php";
?>

<?php if($idleRes!=='') { ?>

<div class="col-md-12" style="border:2px solid #f3f3f3; margin-bottom:10px; padding: 12px;font-weight:bold; color:#999;">
 Agents IDLE: <?php echo $idleRes;?> <span class="dot"></span>
 </div>
<?php } ?>
