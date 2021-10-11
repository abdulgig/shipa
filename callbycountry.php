<?php //require_once("inc/db.php"); ?>
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

 <link href = "https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
      <script src = "https://code.jquery.com/jquery-1.10.2.js"></script>
      <script src = "https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


    <script src="assets/js/modernizr.min.js"></script>


    <script>
        $(function () {
            $("#datep").datepicker();
        });
    </script>
</head>
<body>
<?php
include "callbycountry-backend.php";


//$country= "SELECT  * FROM countries WHERE phonecode!=0 Order By id ASC ";
//$outcalls ="SELECT COUNT(*) as country, ID from outcalls WHERE country!=0 Order By country ASC";

//$outcalls="SELECT COUNT(ot.country) as countrycount, ot.*, c.*
//FROM outcalls AS ot
//JOIN countries AS c ON ot.country = c.phonecode
//WHERE country!=0 GROUP BY ot.country ORDER BY ot.country ASC LIMIT 10";

$outcalls= "SELECT * FROM countries WHERE phonecode IN (1,973,20,98,964,972,962,965,961,968,970,974,966,963,90,971,967,44) ORDER BY id DESC";
$get_country = $conn->query($outcalls, PDO::FETCH_ASSOC);
while($resultcountry= $get_country->fetch()) 
{
  $vicidial_netsuite_country[] = $resultcountry; 
  //$vicidial_netsuite_country[] = $resultcountry; 
}


$custCallsSql = "SELECT  COUNT(*) as countrycount, c.name as cname FROM countries AS c
JOIN outcalls AS ot  ON ot.country = c.phonecode
WHERE c.phonecode!='' group BY c.name ORDER BY countrycount LIMIT 0,6";
$custCalls= $conn->query($custCallsSql, PDO::FETCH_ASSOC);
//$getcountcall = $custCalls->fetch();


//print_r($custCalls->fetch()); die;

while($countcountry= $custCalls->fetch()) 
{
  $counttotal_country[] = $countcountry['countrycount']; 
  $counttotal_countryname[] = $countcountry['cname']; 
 }
//print_r($counttotal_countryname); die;
?>
   
<?php include('header.php');?>

<div class="wrapper">
    <div class="container">
        
            <div class="row">
                  <div class="page-title-box">                   
                     <h4 class="page-title ">
                        <img class="icon-colored" src="assets/images/icons/globe.png" title="" alt="Glob">
                        Calls By Country
                     </h4
                     ><p class="m-t-5"> This section allows you to see the statistics of your outbound calls based upon countries. Below is the list of top 10 destinations dialed from the contact center. 
	              </div>
               </div>
			
				<div class="row">
                  <div class="page-title-box">                   
                     <h4 class="page-title ">
                        <img class="icon-colored" src="assets/images/icons/calendar.svg" title="" alt="calendar icons">
                        Select Date
                     </h4>
					  <p class="m-t-5"></p>
					  <div style="margin-left:-12px;">	
					   <form id="searchForm" method="get" action="" name="search" id="search" onsubmit="return fnsubmitsearch();">
					  
					 <div class="col-md-11"> 
					 <input type="hidden" value="<?php echo @$_GET['call']?>" name="call" id="call">
                      <input type="hidden" value="" name="start_date" id="start_date">
                      <input type="hidden" value="" name="end_date" id="end_date">
					  <div id="reportrange2" class="pull-left form-control">
                          <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                           <span></span>
                        </div>

					 
					 </div>
					     <div class="col-sm-1">
                            <input type="submit" class="btn btn-primary" value="search" id="submit"> 
                                
                            </div>
					 
					 </form>
					
                       </div>                   
	              </div>
               </div>
			  
			
			<div class="row">
			<div class="page-title-box">
                     
                     <h4 class="page-title ">
                         <img class="icon-colored" src="assets/images/chart.png" title="" alt="Top 10 countries">
                        Top Ten Destinations
                     </h4>
                     <p class="m-t-5">By default, top destinations with call count have been listed below</p>                      
	              </div>
			
			</div>
            <!-- start user aget data lis -->
			<div class="row">
			<div class="col-sm-7">			                           
            
	<table id="example" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr class="tabletr">
			<th style="width:15%">Sr. No</th>
                <th style="width:8%">Flag</th>
                <th style="width:30%">Country Name</th>
                <th style="width:15%">Name Code</th>
                <th style="width:15%">Dial Code</th>
				<th style="width:15%">Call Count</th>
              
            </tr>
        </thead>
        <tbody>
		<?php $i=1;?>
		<?php 
		 if($_GET['start_date']!='' and $_GET['end_date']!='')
		 {
		  $startdate = $_GET['start_date'];
		  $end_date =  $_GET['end_date'];
		  
		  //echo $startdate."end date".$end_date  ; die;
		 }
			
			
		$counter = 0;
		foreach($vicidial_netsuite_country as $key=>$row){ 
		if($startdate!=''){ //echo $startdate."end date".$end_date  ; die;
		$countcountry= "SELECT COUNT(country) AS countrycount FROM outcalls AS ot WHERE country='".$row['phonecode']."' AND (call_answer_time BETWEEN '".$startdate."' AND '".$end_date."') ";
		} else {
		$countcountry= "SELECT COUNT(country) AS countrycount FROM outcalls AS ot WHERE country='".$row['phonecode']."' ORDER BY countrycount DESC";
		
		}
		$get_countcountry = $conn->query($countcountry, PDO::FETCH_ASSOC);
		$countc= $get_countcountry->fetch();
		//print_r($countc);
		?>
            <tr>
			<td><?php echo $i; ?></td>
                <td><img src="assets/flags/<?php echo strtolower($row['iso']);?>.png"></td>
                <td><?php echo $row['name'];?></td>
                <td><?php echo $row['iso'] ;?></td>
                <td><?php echo "+".$row['phonecode'] ;?></td> 				
				 <td><?php echo $countc['countrycount'];?></td> 
			   </tr>
			<?php $i++; } ?>
			
            </tbody>     
    </table>
	
	</div>         
		<div class="col-sm-5">			                           
			<div class="card-box" style="height: 507px">
				<h4 class="header-title m-t-0">Calls by Country - Outbound Call Split</h4>
					<div id="pie-chart">
					<div id="pie-chart-container" class="flot-chart" style="height:300px"></div>
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
 <script>
$(document).ready(function() {
    $('#example').DataTable();
} );
      </script>
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
        window.location.href = 'agentdetails.php?user='+user;
    }
    function fnsubmitsearch(){
    	if(document.search.start_date.value=="")
		{
		alert("Please Enter Date  ");
		document.search.start_date.focus();
		return false;
		}
        //$('#popuploader').modal('show');
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
            "startDate": "10/06/2021",
            "endDate": "10/12/2021"
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
        $('#call').val('');
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
               // var pielabels = ["0s", "1s", "2s", "3s", "4s", "5s"];
			   var pielabels = <?php echo json_encode($counttotal_countryname)?>;
                var datas = <?php echo json_encode($counttotal_country)?>;
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
