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
</head>
<body>
<?php
include "outbound-backend.php";
include 'pagination/Zebra_Pagination.php';

$records_per_page = 50;
$pagination_open = new Zebra_Pagination();
$pagination_open->navigation_position( isset( $_GET[ 'navigation_position' ] ) && in_array( $_GET[ 'navigation_position' ], array( 'left', 'right' ) ) ? $_GET[ 'navigation_position' ] : 'outside' );
if ( isset( $_GET[ 'reversed' ] ) )$pagination_open->reverse( true );
$pagination_open->records( $countRes );
$pagination_open->labels( '< Prev', 'Next >' );
$pagination_open->records_per_page( $records_per_page );

?>
<?php include('header.php');?>
<div class="wrapper">
    <div class="container">
        <div class="row" style="margin-right: 18px;margin-left: auto;">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Outbound Dashboard</h4>
                    <p></p>
                    <p>By default, the records for last 2 days are shown. In order to see records for specific dates, use the search filters below.</p>
		    <h5>NOTE : Time below is shown in Minutes</h5>
                    <?php if(isset($_GET['start_date']) and $_GET['start_date'] !='' and isset($_GET['end_date']) and $_GET['end_date'] !=''){ ?>
                        <h4><B>Data from <?php echo $_GET['start_date'];?> To <?php echo $_GET['end_date'];?></B></h4>
                    <?php }else{?>
                        <h4><B>Data from <?php echo $lastDate;?> To <?php echo date('Y-m-d');?></B></h4>
                    <?php }?>

                </div>
            </div>
        </div>
        <div class="row" style="margin-left: 0px;margin-right: 0px;">
            <div class="card-box">
                <div class="row">
                    <div class="col-sm-12">
                        <form id="searchForm" method="get" action="" name="search" id="search" onsubmit="return fnsubmitsearch();">
                            <input type="hidden" value="<?php echo @$_GET['page']?>" name="page" id="page">
                            <input type="hidden" value="" name="start_date" id="start_date">
                            <input type="hidden" value="" name="end_date" id="end_date">
                            <div class="col-sm-5">
                                <div class="">

                                    <div id="reportrange2" class="pull-right form-control">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        <span></span>
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="d-flex">
                                    <label class="search-agent">Search Agent : </label>
                                    <input type="text" name="search" value="<?php echo @$_GET['search']?>" id="autocomplete" class="form-control" placeholder="Type extension here to search.."/>
                                    <input type="submit" class="btn btn-primary" value="search" id="submit"> <input type="reset" class="btn btn-danger" value="Reset" onClick="resetForm()">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="box-default card-box widget-box-two widget-two-primary">
                    <i class="mdi mdi-chart-areaspline widget-two-icon"></i>
                    <div class="wigdet-two-content">
                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Outbound Calls">Total Outbound Calls</p>
                        <h2><span data-plugin="counterup"><?php echo $outboundCallLastTwoDay ?></span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                        <p class="text-muted m-0"><b>Last: </b> <?php echo date("d M, Y H:i:s", strtotime($totalOutboundCall['call_answer_time']));?></p>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <div class="card-box widget-box-two widget-two-primary">
                    <i class="mdi mdi-layers widget-two-icon"></i>
                    <div class="wigdet-two-content">
                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Total Call Time">Total Call Time</p>
                        <h2><span data-plugin="counterup"><?php echo number_format(($currentTalkSec/60), 2); ?></span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                        <p class="text-muted m-0"><b>Last: </b><?php echo date("d M, Y H:i:s", strtotime($totalOutboundCall['call_answer_time']));//$totalTalkSec ?></p>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <div class="card-box widget-box-two widget-two-primary">
                    <i class="mdi mdi-access-point-network widget-two-icon"></i>
                    <div class="wigdet-two-content">
                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Average Handle Time">Avg Handle Time/Call</p>
                        <h2><span data-plugin="counterup"><?php if($outboundAnswerCallRecordTotal && $currentTalkSec && $outboundAnswerCallRecordTotal > 0 && $currentTalkSec > 0){
                                    echo number_format(($currentTalkSec/60)/$outboundAnswerCallRecordTotal, 2);
                                }else{
                                    echo '0';
                                } ?></span> <small><i class="mdi mdi-arrow-up text-success"></i></small></h2>
                        <p class="text-muted m-0"><b>Last:</b> <?php echo 'None'; //$avgHandle ?></p>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-lg-3 col-md-6">
                <div class="card-box widget-box-two widget-two-primary">
                    <i class="mdi mdi-account-convert widget-two-icon"></i>
                    <div class="wigdet-two-content">
                        <p class="m-0 text-uppercase font-600 font-secondary text-overflow" title="Survey Participation">Survey Participation</p>
                        <h2><span data-plugin="counterup"><?php echo $currSurveyPart; ?> </span> <small><i class="mdi mdi-arrow-down text-danger"></i></small></h2>
                        <p class="text-muted m-0"><b>Last:<?php echo ($surveyParticipationLast['call_answer_time']) ? date("d M, Y H:i:s", strtotime($surveyParticipationLast['call_answer_time'])):'NA'; ?></b> </p>
                    </div>
                </div>
            </div><!-- end col -->
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Outbound Survey Statistics</h4>
                </div>
            </div>
            <div class="row" style="margin-right: 0px;margin-left: 0px;">
                <div class="col-sm-6">
                    <div class="card-box" style="height: 390px">
                        <h4 class="header-title m-t-0">Answering Rate</h4>
                        <div id="morris-donut-example" style="height: 300px;"></div>
                        <div class="text-center">
                            <ul class="list-inline chart-detail-list">
                                <li class="list-inline-item">
                                    <h5><i class="mdi mdi-checkbox-blank-circle m-r-5"></i>Answered Calls</h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 class="text-info"><i class="mdi mdi-checkbox-blank-circle m-r-5"></i>Unanswered Calls</h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card-box" style="height: 390px">
                        <h4 class="header-title m-t-0">Customer Rating Split</h4>
                        <div id="pie-chart">
                            <div id="pie-chart-container" class="flot-chart" style="height: 280px"></div>
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="row" style="margin-right: 10px;margin-left: 10px;">
                <div class="card-box">
                    <div class="page-title-box">
                        <h4 class="page-title">Agent Ranking based upon Customer Rating</h4>
                        <p class="m-t-15">This table shows the data of all outbound calls. To see the details for a specific agent, click the agent name or click the view link</p>
                    </div>

                    <div>
						<div class="text-right"><a href="javascript:void(0)" onClick="exportExcell()"><button id="exporttable" class="btn btn-success">Export</button></a></div>	
                        <!-- Table Area -->
                        <div class="row m-t-50" style="margin-top:10px !important;">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table m-0 table-colored table-custom text-center" id="datatable-buttons">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Ranking</th>
                                            <th class="text-center">Agents</th>
                                            <th class="text-center">Total Calls</th>
                                            <th class="text-center">Call Time</th>
                                            <th class="text-center">AHT</th>
                                            <th class="text-center">Answered</th>
                                            <th class="text-center">Unanswered</th>
                                             <th class="text-center">Rated/Unrated</th>
                                            <th class="text-center">Points</th>
                                          <!--  <th class="text-center">Call Recording</th> -->
                                            <th class="text-center noExl">Action</th> 
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1;
                                        $flg = 0;
                                        $new_flg = 0;
                                        //print_r($agentLogs);print_r($users);
                                        
                                        if($agentLogs){
                                            foreach ($agentLogs as $key => $agentLog) { ?>
                                                <tr>
                                                    <th scope="row">
                                                        <p class="thumb-sm img-circle" style="width: 100%;">
                                                            <?php if(isset($agentLog['points']) and $agentLog['points'] !=''){
                                                                if($agentLog['points']>=1){
                                                                    if($new_flg  == 0){
                                                                        $flg = 1;
                                                                    }
                                                                    echo $i;
                                                                    $i++;
                                                                }else{
                                                                    echo '0';
                                                                }
                                                            }else{
                                                                echo '0';
                                                            }?>
                                                            <?php if(isset($flg) and $flg == 1){
                                                                $flg = '';
                                                                $new_flg = 1;
                                                                ?>
                                                                <img src="cup.png" width="32px;" />
                                                            <?php }?>
                                                        </p>
                                                    </th>
                                                    <td>
                                                        <a href="<?php echo 'outbound-backend.php?user='.$agentLog['agent'] ?>">
                                                            <span class="m-0"><?php if(isset($users[$agentLog['agent']])){ echo $users[$agentLog['agent']]; }else{ echo 'N/A';} ?></span><br>
                                                            <span class="m-0 text-muted font-13"><small><?php echo $agentLog['agent'];?></small></span>
                                                        </a>
                                                    </td>
                                                    <td><?php echo $agentLog['total_user'] ?></td>
                                                    <td><?php echo number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', '') ?> mins</td>
                                                    <td><?php echo number_format((float)((number_format((float)($agentLog['cnt_talk_sec']/60), 2, '.', ''))/$agentLog['answer']), 2, '.', '') ?> min/call</td>
                                                    <td><?php if(isset($agentLog['answer'])){ echo $agentLog['answer'];}else{ echo '0';}?></td>
                                                    <td><?php if(isset($agentLog['unanswer'])){ echo $agentLog['unanswer'];}else{ echo '0';}?></td>
                                                    <td><?php echo $agentLog['rated']."/".$agentLog['unrated']?></td>
                                                    <td><?php echo $agentLog['points']?></td>

                                                  <!--  <td><a href="<?php //echo $agentLog['location'];?>" target="_blank"><img class="custom-icon-colored" src="assets/images/icons/video_file.svg" title="video_file.svg" alt="colored-icons" /></a></td> -->
                                                    <td class="noExl"><a onClick="fnviewagent('<?php echo $agentLog['agent'];?>');" href="javascript:;">View</a></td> 
                                                </tr>
                                            <?php }
                                        } else{ ?>

                                            <tr><td colspan="10"><b class="text-danger text-center">No Data Found</b></td></tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php echo $pagination_open->render();?>
                                </div>
                            </div>
                        </div>
                        <!-- End of Table Area -->
                    </div>
                </div>
            </div>
        </div>
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
    function fnviewagent(user) {
        $('#popuploader').modal('show');
        window.location.href = 'outbound_agentdetails.php?user='+user;
    }
    function fnsubmitsearch(){
    	if(document.search.start_date.value=="")
		{
		alert("Please Enter Date  ");
		document.search.start_date.focus();
		return false;
		}
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
            "startDate": "10/06/2020",
            "endDate": "10/12/2020"
        }, function(start, end, label) {
            $('#reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
        });

        function resetForm(){
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

<!-- Morris chart -->
<script type="text/javascript">
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


                // this.createStackedChart('morris-bar-stacked', $stckedData, 'y', ['a', 'b'], ['Series A', 'Series B'], ['#26a69a', '#ebeff2']);

                //creating donut chart
                /* var $donutData = [
                         {label: "Financial", value: 30},
                         {label: "Markets", value: 20}
                     ];*/
                var $transferRateArr = <?php print(json_encode($transferRateArr)); ?>;
                this.createDonutChart('morris-donut-example', $transferRateArr, ["#DCDCDC", "#3ac9d6",'#F57A7A']);
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
                {
                    label : labels[4],
                    data : datas[4]
                },
                {
                    label : labels[5],
                    data : datas[5]
                }];
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
                this.createPieGraph("#pie-chart #pie-chart-container", pielabels, datas, colors);

                //real time data representation
                //var plot = this.createRealTimeGraph('#flotRealTime', this.randomData(), ['#3ac9d6']);
                //plot.draw();
                var $this = this;
                /* function updatePlot() {
                     plot.setData([$this.randomData()]);
                     // Since the axes don't change, we don't need to call plot.setupGrid()
                     plot.draw();
                     setTimeout(updatePlot, $('html').hasClass('mobile-device') ? 500 : 500);
                 }

                 updatePlot();*/


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
function exportExcell(){
	var start_date = "<?php echo $_REQUEST['start_date']?>";
	var end_date = "<?php echo $_REQUEST['end_date']?>";
	var search = "<?php echo $_REQUEST['search']?>";
	$('#popuploader').modal('show');
	$.ajax({
			data: {"key":3},
			url: "https://azr-cc01-001.shipa.delivery/carrydialer/survey_reports/export.php?key=3",
			type: "POST",
			success: function (response) {
				var link = document.createElement("a");
				link.href = 'https://azr-cc01-001.shipa.delivery/carrydialer/survey_reports/export.php?key=3&start_date='+start_date+'&end_date='+end_date+'&search='+search;
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
