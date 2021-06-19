<?php
	$startDate = (isset($_REQUEST['start_date'])) ? $_REQUEST['start_date'] : "";
	$endDate = (isset($_REQUEST['end_date'])) ? $_REQUEST['end_date'] : "";
	$serach = (isset($_REQUEST['search'])) ? $_REQUEST['search'] : "";
	$user = (isset($_REQUEST['user'])) ? $_REQUEST['user'] : "";
	$exportUrl = "https://azr-cc01-001.shipa.delivery/carrydialer/survey_reports/export.php?key=2&start_date=".$startDate."&end_date=".$endDate."&search=".$serach."&user=".$user;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">

        <link rel="shortcut icon" href="assets/images/favicon.png">

        <title>Shipa Agent Details</title>

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
        <link href="plugins/bootstrap-tagsinput/css/bootstrap-tagsinput.css" rel="stylesheet" />
        <link href="plugins/multiselect/css/multi-select.css"  rel="stylesheet" type="text/css" />
        <link href="plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="plugins/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" />
        <link href="plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />

        <!-- DataTables -->
        <link href="plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/dataTables.colVis.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="plugins/datatables/fixedColumns.dataTables.min.css" rel="stylesheet" type="text/css"/>

        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->

        <script src="assets/js/modernizr.min.js"></script>
        <script type="text/javascript">
            var maxDate = "<?php echo date('m/d/Y')?>";
        </script>
    </head>
    <body>
        <?php 
            include "agentdetails-backend.php"; 
            include 'pagination/Zebra_Pagination.php';

            $records_per_page = 50;
            $pagination_open = new Zebra_Pagination();
            $pagination_open->navigation_position( isset( $_GET[ 'navigation_position' ] ) && in_array( $_GET[ 'navigation_position' ], array( 'left', 'right' ) ) ? $_GET[ 'navigation_position' ] : 'outside' );
            if ( isset( $_GET[ 'reversed' ] ) )$pagination_open->reverse( true );
            $pagination_open->records( $countRes );
            $pagination_open->labels( '< Prev', 'Next >' );
            $pagination_open->records_per_page( $records_per_page );
        ?>
        <!-- Navigation Bar-->
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
                                <a href="inbound.php" class="color-white"><i class="mdi mdi-view-dashboard icon-white"></i>Inbound Report</a>
                            </li>
                            <li class="has-submenu">
                            <!--    <a href="agentdetails.html" class="color-white"><i class="mdi mdi-phone icon-white"></i>Outbound Report</a> -->
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
        <!-- End Navigation Bar-->
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Agent Details - <?php echo $userData['full_name']?></h4>
                            <h5>Agent Extension = <?php echo $userData['user']?></h5>
 			    <p></p><p></p>
 		            <p>By default, only records for this specific agent are shown. The call recording is available for each inbound call and if the call was rated by a customer, it could also be seen. Call records are displayed in descending order, latest first.</p>		
			    <p></p>
                            <p>To select calls with a specific status, date range, queue name, customer rating or by transfer, please user the search filters below.</p>
                            <?php if(isset($_GET['start_date']) and $_GET['start_date'] !='' and isset($_GET['end_date']) and $_GET['end_date'] !=''){ ?>
                                <h4><B>Data from <?php echo $_GET['start_date'];?> To <?php echo $_GET['end_date'];?></B></h4>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
                <!-- Date Picker, Select, Search form -->
                <div class="row">
                    <form method="get" action="" onsubmit="return fnsubmitsearch();">
                        <input type="hidden" value="<?php echo @$_GET['user']?>" name="user">
                        <input type="hidden" value="" name="start_date" id="start_date">
                        <input type="hidden" value="" name="end_date" id="end_date">
                        <div class="col-sm-5 col-xs-12">
                            <div class="">
                                
                                <div class="">
                                    <div id="reportrange" class="pull-right form-control">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        <span></span>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                        <div class="col-sm-2 col-xs-12">
                            <div class="">
                                <div class="">
                                    <?php
                                        $cat = "";
                                        if(isset($_GET['cat']) && $_GET['cat'] != ""){
                                            $cat = $_GET['cat'];
                                        } 
                                    ?>
                                    <select class="form-control" name="cat" id="select">
                                        <option value="">Select</option>
                                        <optgroup label="By Queue">
                                            <option value="qu-all" <?php echo ($cat=='qu-all')?'selected':''; ?>>All</option>
                                            <?php foreach ($userCloserLogQueue as $value) { ?>
                                                <option value="qu-<?php echo $value ?>" <?php echo ($cat=='qu-'.$value)?'selected':''; ?>><?php echo $value ?></option>
                                            <?php }?>
                                        </optgroup>
                                        <optgroup label="By Call Status">
                                            <option value="cs-" <?php echo ($cat=='cs-')?'selected':''; ?>>All</option>
                                            <?php foreach ($userCloserLogStatus as $value) { ?>
                                                <option value="cs-<?php echo $value ?>" <?php echo ($cat=='cs-'.$value)?'selected':''; ?>><?php echo $value ?></option>
                                            <?php }?>
                                        </optgroup>
                                        <optgroup label="By Rating">
                                            <option value="rt-all" <?php echo ($cat=='rt-all')?'selected':''; ?>>All</option>
                                            <option value="rt-5" <?php echo ($cat=='rt-5')?'selected':''; ?>>Excellent</option>
                                            <option value="rt-4" <?php echo ($cat=='rt-4')?'selected':''; ?>>Good</option>
                                            <option value="rt-3" <?php echo ($cat=='rt-3')?'selected':''; ?>>Fair</option>
                                          <!--  <option value="rt-2" <?php echo ($cat=='rt-2')?'selected':''; ?>>Ok</option> -->
                                            <option value="rt-2" <?php echo ($cat=='rt-2')?'selected':''; ?>>Poor</option>
                                            <option value="rt-1" <?php echo ($cat=='rt-0')?'selected':''; ?>>Bad</option>
                                        </optgroup>
                                        <optgroup label="By Survey Transfers">
                                            <option value="st-all" <?php echo ($cat=='st-all')?'selected':''; ?>>All</option>
                                            <option value="st-tf" <?php echo ($cat=='st-tf')?'selected':''; ?>>Transferred to Survey</option>
                                            <option value="st-ntf" <?php echo ($cat=='st-ntf')?'selected':''; ?>>Not Transffered to Survey</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <div class="">
                                
                                <div class="">
                                    <input type="text" name="search" id="autocomplete" name="search"
                                    class="form-control" value="<?php echo @$_GET['search']?>" placeholder="Type here to search.."/>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col-sm-2 col-xs-12"><input type="submit" class="btn btn-primary" value="search"> <input type="button" class="btn btn-danger" value="Reset" onclick="resetForm()"></div>
                       
                    </form>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Inbound Survey Statistics</h4>
			   <p></p>
                            <p>You can see if a call was transferred by the agent to for survey or not.</p>
                            <p></p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card-box" style="height: 390px">
                                <h5 class="header-title m-t-0">Transfer Rate - How many calls to survey by this agent ?</h5>
                                <div id="morris-donut-example1" style="height: 300px;"></div>
                                <div class="text-center">
                                    <ul class="list-inline chart-detail-list">
                                        <li class="list-inline-item">
                                            <h5><i class="mdi mdi-checkbox-blank-circle m-r-5"></i>Transferred Calls</h5>

                                        </li>
                                        <li class="list-inline-item">
                                            <h5 class="text-info"><i class="mdi mdi-checkbox-blank-circle m-r-5"></i>Not Transferred</h5>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card-box" style="height: 390px">
                                <h5 class="header-title m-t-0">Customer Rating Split - How many 5s, 4s, 3s . .</h5>
                                <div id="simple-pie" class="ct-chart ct-golden-section simple-pie-chart-chartist" style="height: 300px"></div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="card-box" style="height: 390px">
                                <h4 class="header-title m-t-0">Calls by Queue</h4>
                                <div id="sparkline34" class="text-center"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="card-box" style="height: 390px">
                                <h4 class="header-title m-t-0">Call Disposition by Status</h4>
                                <!-- <div id="simple-pie_12" class="ct-chart ct-golden-section simple-pie-chart-chartist" style="height: 300px"></div> -->
                                <div id="simple-pie_12" class="ct-chart " style="height: 300px"></div>
                            </div>
                        </div>
                        <div class="col-sm-4"></div>
                        <div class="col-sm-4"></div>
                    </div>
                </div>
                <!-- Table Area -->
				
                <div class="row m-t-50">
                    <div class="col-sm-12">
                       <h3 class="page-title">Inbound Call Details</h3>
 			 <p></p>
                            <p>Inbound call received by this agent are shown below</p>
                          <p></p>
						<div class="text-right"><a href="javascript:void(0)" onclick="window.open('<?php echo $exportUrl;?>','_blank' );"><button id="exporttable" class="btn btn-success">Export</button></a></div>	
                        <p></p>
						<div class="table-responsive">
                            <table class="table m-0 table-colored table-custom text-center" id="datatable-buttons">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Customer Phone</th>
                                        <th class="text-center">Call Date</th>
                                        <th class="text-center">Talk Time</th>
                                        <th class="text-center">Queue</th>
                                        <th class="text-center">Call Status</th>
                                        <th class="text-center">Survey Transfer</th>
                                        <th class="text-center">Rating</th>
                                        <th class="text-center">Call Recording</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $c = 1;
                                        foreach ($closerLogs as $key => $closerLog) { ?>
                                       <tr>
                                        <th scope="row"><?php echo $c++; ?></th>
                                        <td><?php echo $closerLog['phone_number']?></td>
                                        <td><?php echo $closerLog['call_date']?></td>
                                        <td><?php echo $closerLog['talk_sec']?> secs</td>
                                        <td><?php echo $closerLog['campaign_id']?></td>
                                        <td><?php echo $closerLog['status']?></td>
                                           <td><?php
                                               if(isset($cat) and $cat == 'st-tf'){
                                                   echo 'Yes';
                                               }else if(isset($cat) and $cat == 'st-ntf'){
                                                   echo 'No';
                                               }else{
                                                   if(isset($userCallLogs[$closerLog['lead_id']]) and $userCallLogs[$closerLog['lead_id']] !=''){
                                                       echo 'Yes';
                                                   }else if(isset($closerLog['survey_option']) and $closerLog['survey_option'] >=1){
                                                       echo 'Yes';
                                                   }else{
                                                       echo 'No';
                                                   }
                                               }
                                               ?></td>
                                           <td>
                                               <div class="" style="cursor: pointer;">
                                                   <?php $total_rat = 5;
                                                   $current = $closerLog['survey_option'];
                                                   $white = $total_rat-$current;
                                                   if(isset($cat) and $cat == 'st-ntf'){
                                                       $current = 0;
                                                       $white = $total_rat-$current;
                                                   }
                                                   ?>
                                                   <?php
                                                   if($current >= 1){
                                                       for($i=1;$i<=$current;$i++){?>
                                                           <i class="fa fa-star text-danger" title="bad" data-score="1"></i>
                                                       <?php }
                                                   }?>
                                                   <?php
                                                   if($white >= 1){
                                                       for($i=1;$i<=$white;$i++){?>
                                                           <i class="fa fa-star-o text-muted" title="bad" data-score="1"></i>
                                                       <?php }
                                                   }?>
                                               </div>
                                           </td>
                                        <td><a href="<?php echo $closerLog['location'];?>" target="_blank"><img class="custom-icon-colored" src="assets/images/icons/video_file.svg" title="video_file.svg" alt="colored-icons" /></a> </td>
                                    </tr>
                                  <?php  }
                                          if(empty($closerLogs)){ ?>
                                        <tr><td colspan="9" align="center"><b class="text-danger">No Data Found!</b></td></tr>
                                          <?php }
                                    
                                  ?>
                                    
                    
                                </tbody>
                            </table>
                            <?php echo $pagination_open->render();?>
                        </div>
                    </div>
                </div>
                <!-- End of Table Area -->
                
            </div>
        </div>
        <div id="popuploader" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-body" style="text-align: center;">
			<h3><B>Loading Agent Data . . .</B></h3>
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

        <script src="plugins/moment/moment.js"></script>
     	<script src="plugins/timepicker/bootstrap-timepicker.js"></script>
     	<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
     	<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
     	<script src="plugins/clockpicker/js/bootstrap-clockpicker.min.js"></script>
         <script src="plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
         
        <script src="plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.min.js"></script>
        <script src="plugins/multiselect/js/jquery.multi-select.js"></script>
        <script src="plugins/jquery-quicksearch/jquery.quicksearch.js"></script>
        <script src="plugins/select2/js/select2.min.js"></script>
        <script src="plugins/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script src="plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js"></script>
        <script src="plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js"></script>
        <script src="plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

        <script src="plugins/autocomplete/jquery.mockjax.js"></script>
        <script src="plugins/autocomplete/jquery.autocomplete.min.js"></script>
		
        <script src="plugins/autocomplete/countries.js"></script>
        <script src="assets/pages/jquery.autocomplete.init.js"></script>

        <script src="assets/pages/jquery.form-advanced.init.js"></script>

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

        <!--Morris Chart-->
		<script src="plugins/morris/morris.min.js"></script>
		<script src="plugins/raphael/raphael-min.js"></script>
        <!-- <script src="assets/pages/jquery.morris.init.js"></script> -->

        <!--Chartist Chart-->
		<!-- <script src="plugins/chartist/js/chartist.min.js"></script>
        <script src="plugins/chartist/js/chartist-plugin-tooltip.min.js"></script> -->
        <!-- <script src="assets/pages/jquery.chartist.init.js"></script> -->

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
        
        <!-- Sparkline charts -->
        <script src="plugins/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="assets/pages/jquery.charts-sparkline.js"></script>

        <!-- Rating js -->
        <script src="plugins/raty-fa/jquery.raty-fa.js"></script>

        <!-- Counter js  -->
        <script src="plugins/waypoints/jquery.waypoints.min.js"></script>
        <script src="plugins/counterup/jquery.counterup.min.js"></script>

        <!-- App js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

        <!-- Init js -->
        <script src="assets/pages/jquery.form-pickers.init.js"></script>
        <script src="assets/pages/jquery.rating.js"></script>
         <script src="assets/pages/jquery.datatables.init.js"></script>
        <!-- <script src="assets/pages/jquery.dashboard.js"></script> -->
        <script>
            $(function() {
                $('#reportrange span').text('');
                $('#reportrange').daterangepicker({
                    opens: 'left'
                }, function(start, end, label) {
                    $('#start_date').val(start.format('YYYY-MM-DD'));
                    $('#end_date').val(end.format('YYYY-MM-DD'));
                    $('#reportrange span').text(start.format('YYYY-MM-DD')+' - '+end.format('YYYY-MM-DD'));
                    //console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));

                });
            });
        </script>
        <script>
            function fnsubmitsearch(){
                $('#popuploader').modal('show');
                return true;
            }
            $(document).ready(function () {
                $('#datatable').dataTable({"paging": false});
                $('#datatable-keytable').DataTable({keys: true, "paging": false});
                $('#datatable-responsive').DataTable({"paging": false});
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
                    scrollCollapse: true,
                    paging: false,
                    fixedColumns: {
                        leftColumns: 1,
                        rightColumns: 1
                    }
                });
            });
            //TableManageButtons.init();

            function resetForm(){
                console.log('-----');
                $('#start_date').val('');
                $('#end_date').val('');
                $('#reportrange span').html('');
                $("#select option").prop("selected", false);
                $('#select').val('');
                $('#autocomplete').val('');
            }
        </script>
        <script>
            var dt = new Date();
            var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
            var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var time = days[dt.getDay()] + " " + months[dt.getMonth()] + " " + dt.getDate() + ", " + dt.getFullYear() + " " + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
            document.getElementById("time-now").innerHTML = time;
        </script>

            <!-- Morris chart -->
    <script type="text/javascript">
        $transferRateArr = <?php print(json_encode($transferRateArr)); ?>;
        !function($) {
            "use strict";
            var MorrisCharts = function() {};
            //creates Donut chart
            MorrisCharts.prototype.createDonutChart = function(element, data, colors) {
                Morris.Donut({
                    element: element,
                    data: data,
                    resize: true, //defaulted to true
                    colors: colors
                });
            },
            MorrisCharts.prototype.init = function() {
                this.createDonutChart('morris-donut-example1', $transferRateArr, ["#DCDCDC", "#3ac9d6",'#F57A7A']);
            },
            //init
            $.MorrisCharts = new MorrisCharts, $.MorrisCharts.Constructor = MorrisCharts
        }(window.jQuery),

        //initializing 
        function($) {
            "use strict";
            $.MorrisCharts.init();
        }(window.jQuery);
    </script>

    <script type="text/javascript">
        ! function($) {
            "use strict";

            var FlotChart = function() {
                this.$body = $("body")
                this.$realData = []
            };

            //creates Pie Chart
            FlotChart.prototype.createPieGraph = function(selector, labels, datas, colors) {
                var data = [{
                    label : labels[0],
                    data : datas[0]
                }, {
                    label : labels[1],
                    data : datas[1]
                }, {
                    label : labels[2],
                    data : datas[2]
                },{
                    label : labels[3],
                    data : datas[3]
                },
                {  label : labels[4],
                    data : datas[4]
                } ,
                {  label : labels[5],
                    data : datas[5]
                } 
                ];
                var options = {
                    series : {
                        pie : {
                            show : true
                        }
                    },
                    legend : {
                        show : true
                    },
                    grid : {
                        hoverable : true,
                        clickable : true
                    },
                    colors : colors,
                    tooltip : true,
                    tooltipOpts : {
                        content : "%s, %p.0%"
                    }
                };

                $.plot($(selector), data, options);
            },

            //initializing various charts and components
            FlotChart.prototype.init = function() {
                //Pie graph data
                var pielabels = ["0s", "1s", "2s", "3s", "4s", "5s"];
                var datas = <?php echo json_encode($custRatingsArr)?>;
                var colors = ['#DCDCDC', '#b2dafd', '#188ae2', '#4bd396', "#f5707a", "#f9c851"];
                this.createPieGraph("#simple-pie", pielabels, datas, colors);
                //real time data representation
               // var plot = this.createRealTimeGraph('#flotRealTime', this.randomData(), ['#3ac9d6']);
                //plot.draw();
                var $this = this;
                /*function updatePlot() {
                    plot.setData([$this.randomData()]);
                    // Since the axes don't change, we don't need to call plot.setupGrid()
                    plot.draw();
                    setTimeout(updatePlot, $('html').hasClass('mobile-device') ? 500 : 500);
                }
                updatePlot();  */          
            },
            //init flotchart
            $.FlotChart = new FlotChart, $.FlotChart.Constructor =
            FlotChart

        }(window.jQuery),

        //initializing flotchart
        function($) {
            "use strict";
            $.FlotChart.init()
        }(window.jQuery);
    </script>

    <script type="text/javascript">
        Morris.Donut({
          element: 'sparkline34',
          data: <?php echo json_encode($callByQueues) ?>
        });

        Morris.Donut({
          element: 'simple-pie_12',
          data: <?php echo json_encode($districtionCalls) ?>
        });
    </script>

	

    </body>
</html>
