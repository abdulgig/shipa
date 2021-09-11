<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="assets/images/favicon.png">
    <title>Shipa Delivery - Contact Center</title>

    <!-- Bootstrap Sweet Alert -->
    <link href="plugins/bootstrap-sweetalert/sweet-alert.css" rel="stylesheet" type="text/css">

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="plugins/morris/morris.css">

    <!--Chartist Chart CSS -->
    <link rel="stylesheet" href="plugins/chartist/css/chartist.min.css">

    <!-- App css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="plugins/switchery/switchery.min.css">

    <!-- Plugins Css -->
    <link href="plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <link href="plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <link href="plugins/clockpicker/css/bootstrap-clockpicker.min.css" rel="stylesheet">
    <link href="plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>

    <script src="assets/js/modernizr.min.js"></script>
 <style>
 .dotgreeg {
  height: 22px;
  width: 22px;
  background-color: #33CC00;
  border-radius: 50%;
  display: inline-block;
  margin-left: 74%;
  border: 3px solid #38AB38;
}
.dot {
  height: 22px;
  width: 22px;
  background-color: #a2f289;
  border-radius: 50%;
  display: inline-block;
  margin-left: 78%;
  border: 3px solid #f8c90d;
}

.daterangepicker {
margin-left:60px !important; 
left:20px !important;
width:40%;

}
.buttons{ 
width:100px; background:#38AB38;
}
</style>
</head>
<body>
<?php
//include "useragent-live.php";

//include "outbound-backend.php";

include 'pagination/Zebra_Pagination.php';

$records_per_page = 50;
$pagination_open = new Zebra_Pagination();
$pagination_open->navigation_position( isset( $_GET[ 'navigation_position' ] ) && in_array( $_GET[ 'navigation_position' ], array( 'left', 'right' ) ) ? $_GET[ 'navigation_position' ] : 'outside' );
if ( isset( $_GET[ 'reversed' ] ) )$pagination_open->reverse( true );
$pagination_open->records( $countRes );
$pagination_open->labels( '< Prev', 'Next >' );
$pagination_open->records_per_page( $records_per_page );


 
?>
   
<header id="topnav">
    <div class="topbar-main">
        <div class="container">

            <!-- Logo container-->
            <div class="logo">
                <!-- Text Logo -->
                <!--<a href="index.html" class="logo">-->
                <!--Zircos-->
                <!--</a>-->
                <!-- Image Logo -->
                <a href="index.html" class="logo">
                    <img src="assets/images/logo.png" alt="" height="30">
                </a>

            </div>
            <!-- End Logo container-->


            <div class="menu-extras">
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>
            <!-- end menu-extras -->

        </div> <!-- end container -->
    </div>
    <!-- end topbar-main -->

    <div class="navbar-custom">
        <div class="container">
            <div id="navigation">
                <!-- Navigation Menu-->
                <ul class="navigation-menu">
                    <li class="has-submenu">
                        <a href="inbound.php" class="color-white"><i class="mdi mdi-view-dashboard icon-white"></i>Inbound Reports</a>
                    </li>
                    <li class="has-submenu">
                       <a href="outbound.php" class="color-white"><i class="mdi mdi-phone icon-white"></i>Outbound Reports</a> 
                    </li>
                    <li class="has-submenu">
                       <a href="useragent.php" class="color-white"><i class="mdi mdi-account icon-white"></i>Live Agents</a>
                    </li>
					  <li class="has-submenu">
                       <a href="callbycountry.php" class="color-white"><i class="mdi mdi-account icon-white"></i>Call By Country</a>
							 
                    </li>
                    <li class="has-submenu f-right">
                        <a href="#" class="color-white" id="time-now"></a>
                    </li>
                </ul>
                <!-- End navigation menu -->
            </div> <!-- end #navigation -->
        </div> <!-- end container -->
    </div> <!-- end navbar-custom -->
</header>
<div class="wrapper">
    <div class="container">
        
            <div class="row">
                  <div class="page-title-box">                   
                     <h4 class="page-title ">
                        <img class="icon-colored" src="assets/images/icons/netsuite.svg" title="" alt="netsuite icons">
                        Netsuite Outbound Agents
                     </h4>
                     <p class="m-t-5"> This section allows you to see the statistics of your outbound agents dialing via Netsuite. This module also allows you to track the status of the agents 
					<span style="color:#38AB38; font-weight:bold;">LIVE</span>	</p>	                      
	              </div>
               </div>
			
				<div class="row">			
			       <div class="col-lg-6 col-md-4 col-sm-6"> 
				   <span id="RefreshIncalls"></span>
				
                 </div>
			
				 <div class="col-lg-6 col-md-4 col-sm-6"> 
				 <span id="RefreshIdle"></span>
				  <!-- <div class="col-md-12" style="border:2px solid #f3f3f3; margin-bottom:10px; padding: 12px;font-weight:bold; color:#999;">
                       Agents IDLE: <?php //echo $idleRes;?> <span class="dot"></span>
                    </div> -->
                 </div>
			  </div>
			
			<div class="row">
			<div class="page-title-box">
                     
                     <h4 class="page-title ">
                        <img class="icon-colored" src="assets/images/ccagent.png" title="" alt="ccagent icons">
                        Live Agents
                     </h4>
                     <p class="m-t-5">By default, all agents who made calls today would be listed. Their status would be shown as well.</p>                      
	              </div>
			
			</div>
            <!-- start user aget data lis -->
			<div class="row">			                           
                 <div class="table-responsive">
				     <span id="RefreshAgentList"></span>
	
				
                 </div>
                           
			
			
			</div>
			
			<!-- end user aget data lis -->
			
			<!-- start agent report box -->
			<br/>
		
			
        
    </div>
</div>










<div id="popuploader" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body" style="text-align: center;">
                <h2><B>Loading Data . . .</B></h2> 
                <img src="loader.gif"/>
            </div>
        </div>

    </div>
</div>
<!-- jQuery  -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/detect.js"></script>
<script src="assets/js/fastclick.js"></script>
<script src="assets/js/jquery.blockUI.js"></script>
<script src="assets/js/waves.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="plugins/switchery/switchery.min.js"></script>

<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables/dataTables.bootstrap.js"></script>

<script src="plugins/datatables/dataTables.buttons.min.js"></script>
<script src="plugins/datatables/buttons.bootstrap.min.js"></script>
<script src="plugins/datatables/jszip.min.js"></script>
<script src="plugins/datatables/pdfmake.min.js"></script>
<script src="plugins/datatables/vfs_fonts.js"></script>
<script src="plugins/datatables/buttons.html5.min.js"></script>
<script src="plugins/datatables/buttons.print.min.js"></script>
<script src="plugins/datatables/dataTables.fixedHeader.min.js"></script>
<script src="plugins/datatables/dataTables.keyTable.min.js"></script>
<script src="plugins/datatables/dataTables.responsive.min.js"></script>
<script src="plugins/datatables/responsive.bootstrap.min.js"></script>
<script src="plugins/datatables/dataTables.scroller.min.js"></script>
<script src="plugins/datatables/dataTables.colVis.js"></script>
<script src="plugins/datatables/dataTables.fixedColumns.min.js"></script>

<!-- jQuery  -->
<script src="plugins/waypoints/jquery.waypoints.min.js"></script>
<script src="plugins/counterup/jquery.counterup.min.js"></script>

<!--Morris Chart-->
<script src="plugins/morris/morris.min.js"></script>
<script src="plugins/raphael/raphael-min.js"></script>
<!-- <script src="assets/pages/jquery.morris.init_2.js"></script> -->

<!-- Flot chart -->
<script src="plugins/flot-chart/jquery.flot.min.js"></script>
<script src="plugins/flot-chart/jquery.flot.time.js"></script>
<script src="plugins/flot-chart/jquery.flot.tooltip.min.js"></script>
<script src="plugins/flot-chart/jquery.flot.resize.js"></script>
<script src="plugins/flot-chart/jquery.flot.pie.js"></script>
<script src="plugins/flot-chart/jquery.flot.selection.js"></script>
<script src="plugins/flot-chart/jquery.flot.stack.js"></script>
<script src="plugins/flot-chart/jquery.flot.orderBars.min.js"></script>
<script src="plugins/flot-chart/jquery.flot.crosshair.js"></script>
<!-- <script src="assets/pages/jquery.flot.init.js"></script> -->

<!-- App js -->
<script src="assets/js/jquery.core.js"></script>
<script src="assets/js/jquery.app.js"></script>

<script src="plugins/moment/moment.js"></script>
<script src="plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="plugins/clockpicker/js/bootstrap-clockpicker.min.js"></script>
<script src="plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Init js -->
<script src="assets/pages/jquery.form-pickers.init.js"></script>
<!-- <script src="assets/pages/jquery.datatables.init.js"></script> -->


<script>

function CheckRefresh() {
    $.ajax({
        url: 'getagent_ajax.php',       
         success: function (data) {      
         $('#RefreshAgentList').html(data); //output to html
        }
    });
}

$(document).ready(CheckRefresh); // Call on page load
setInterval(CheckRefresh, 2000); //every 120 secs



// check incall service
function CheckIncall() {
    $.ajax({
        url: 'IncallService.php',       
         success: function (data) {       
         $('#RefreshIncalls').html(data); //output to html
        }
    });
}

$(document).ready(CheckIncall); // Call on page load
setInterval(CheckIncall, 2000); //every 120 secs

// check idle call service
function CheckIdle() {
    $.ajax({
        url: 'IdleService.php',       
         success: function (data) {       
         $('#RefreshIdle').html(data); //output to html
        }
    });
}

$(document).ready(CheckIdle); // Call on page load
setInterval(CheckIdle, 2000); //every 120 secs






    function fnviewagent(user) {
        $('#popuploader').modal('show');
        window.location.href = 'agentdetails.php?user='+user;
    }
    function fnsubmitsearch(){
        $('#popuploader').modal('show');
        return true;
    }
    $(document).ready(function () {
        $('#datatable').dataTable({ "paging":   false,});
        $('#datatable-keytable').DataTable({keys: true,  "paging":   false,});
        $('#datatable-responsive').DataTable();
        $('#datatable-colvid').DataTable({
            "dom": 'C<"clear">lfrtip',
            "colVis": {
                "buttonText": "Change columns"
            }
        });
        $('#datatable-scroller').DataTable({
            ajax: "../plugins/datatables/json/scroller-demo.json",
            deferRender: true,
            scrollY: 380,
            scrollCollapse: true,
            scroller: true
        });
        var table = $('#datatable-fixed-header').DataTable({fixedHeader: true});
        var table = $('#datatable-fixed-col').DataTable({
            scrollY: "300px",
            scrollX: true,
            "paging":   false,
            scrollCollapse: true,
            paging: false,
            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1
            }
        });

        $('#reportrange2').daterangepicker({
            "showDropdowns": true,
            "linkedCalendars": false,
            "startDate": Date.now(),
            "endDate": "10/12/2021"
        }, function(start, end, label) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        });

        function resetForm(){ //alert('k');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#reportrange span').html('');
            $('#select').val('');
        }
    });
    //TableManageButtons.init();

    $('#submit').on("click",function(e){
        //e.preventDefault();
        $('#page').val('');
        return true;
    });

</script>

<script>
    var dt = new Date();
    var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    var time = days[dt.getDay()] + " " + months[dt.getMonth()] + " " + dt.getDate() + ", " + dt.getFullYear() + " " + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
    document.getElementById("time-now").innerHTML = time;
</script>


<script type="text/javascript">
function exportExcell(){
	var start_date = "<?php echo $_REQUEST['start_date']?>";
	var end_date = "<?php echo $_REQUEST['end_date']?>";
	var search = "<?php echo $_REQUEST['search']?>";
	$('#popuploader').modal('show');
	$.ajax({
			data: {"key":1},
			url: "https://azr-cc01-001.shipa.delivery/carrydialer/survey_reports/export.php?key=1",
			type: "POST",
			success: function (response) {
				var link = document.createElement("a");
				link.href = 'https://azr-cc01-001.shipa.delivery/carrydialer/survey_reports/export.php?key=1&start_date='+start_date+'&end_date='+end_date+'&search='+search;
				//set the visibility hidden so it will not effect on your web-layout
				link.style = "visibility:hidden";
				//this part will append the anchor tag and remove it after automatic click
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
				$('#popuploader').modal('hide');
			},
			error: function (data) {
								
	
			}
		});	
}
</script>

</body>
</html>
