<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->

<?php 
  require_once(__DIR__."/src/config.php");
  require_once(__DIR__."/src/lib/usercake/init.php");
  if (!UCUser::CanUserAccessUrl($_SERVER['PHP_SELF'])){die();}
  $user = UCUser::getCurrentUser();
?> 

<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $user_settings->WebsiteName() ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="plugins/ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- Pace style -->
  <link rel="stylesheet" href="plugins/pace/pace.min.css">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">
  <link rel="stylesheet" href="plugins/jqcloud/jqcloud.css">	
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <style type="text/css">
    .table-responsive {
		min-height: 400px !important;
	}
	
	ul#dropdown-item-actions,
	ul#dropdown-item-actions {
	    z-index: 10000;
	}
  </style>
  
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php  include(__DIR__."/top-nav.php"); ?> 
  <?php  include(__DIR__."/left-nav.php"); ?> 
  
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!--<section class="content-header">
      <h1>
        Page Header
        <small>Optional description</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol>
    </section>-->

    <!-- Main content -->
    <section class="content">
    
    	<!-- Line Chart row -->
		<div class="row">
		
			<!-- Left col -->
			<section class="col-lg-4 connectedSortable">
				<div class="box box-default">
	                <div class="box-header with-border">
	                  <h3 class="box-title">Repository Information</h3>
	                  <div class="box-tools pull-right">
	                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
	                  </div>
	                </div>
	                <!-- /.box-header -->
	                <div class="box-body">
	                   <span style="font-weight: bold; color: black;">YaraEditor (Web) v<?php echo $GLOBALS["config"]["version"]?></span>
						<br/>
						<span>Files: </span>
						<span id="files-count" style="font-weight: bold; color: black;"> Not available</span>
						<br/>
						<span>Rules: </span>
						<span id="rules-count" style="font-weight: bold; color: black;"> Not available</span>
	                </div>
	                <!-- /.box-body -->
              	</div>
			</section>	
			
			<!-- Left col -->
			<section class="col-lg-4 connectedSortable">
				<!-- USERS LIST -->
              <div class="box box-danger">
                <div class="box-header with-border">
                  <h3 class="box-title">Best Uploaders</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                  <ul class="users-list clearfix">                    
                  </ul>
                  <!-- /.users-list -->
                </div>
                <!-- /.box-body -->
              </div>
              <!--/.box -->
			</section>
			
			<!-- Right col -->
			<section class="col-lg-4 connectedSortable">
			  	<!-- TAGS CHART -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Tags</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool"
								data-widget="collapse">
								<i class="fa fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<div id="tags-cloud" style="height: 300px;"></div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</section>		
		
			<!-- Left col -->
			<section class="col-lg-12 connectedSortable">
				<!-- AREA CHART -->
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Submissions</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool"
								data-widget="collapse">
								<i class="fa fa-minus"></i>
							</button>
						</div>
					</div>
					<div class="box-body">
						<div class="chart">
							<canvas id="areaChartSubmissions" height="350"></canvas>
						</div>
					</div>
					<!-- /.box-body -->
				</div>
				<!-- /.box -->
			</section>					
			
		</div>
		<!-- /.row (Line Chart row) -->	

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <?php  include(__DIR__."/footer.php"); ?> 
  <?php  include(__DIR__."/right-nav.php"); ?> 
  
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- MRF -->
<!--<script src="plugins/jQueryUpload/js/vendor/jquery.min.js"></script>-->
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<!--<script src="plugins/jQueryUpload/js/vendor/jquery.ui.widget.js"></script>-->
<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<!-- Bootstrap needs to be placed AFTER jquery-ui because of tootltip conflicts -->
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- ChartJS 2.2.2 -->
<script src="plugins/chartjs/2.2.2/Chart.min.js"></script>
<!-- JQCloud -->
<script src="plugins/jqcloud/jqcloud.js"></script>
<!-- PACE -->
<script src="plugins/pace/pace.min.js"></script>
<!-- The main application script -->
<script src="dist/js/main.js"></script>

<script>
function initStats() 
{
    'use strict';	
	$.when(
		refreshRepoInformation(),
		refreshTimeLine(),
		refreshUploaders(),
		refreshTags()
	).always(function(a1, a2, a3, a4) {
		Pace.stop();
	});
}

//This method will trigger a "get_files" ajax call because of the pagination
function refreshRepoInformation()
{
	return get_storage_info(function(data) {
		var span_count	= document.getElementById("files-count");
		if (span_count) {
			span_count.innerHTML = data["files"];
		}	
		span_count = document.getElementById("rules-count");
		if (span_count) {
			span_count.innerHTML = data["rules"];
		}		
	});
}

function refreshTimeLine(days_count) 
{	
	return $.ajax({
    	 type: "GET",
         dataType: "json",
         url: "api.php?action=getsubmissionsdata",
         data: {days_count: days_count},	
         success: function(data)
         {
        	var color_front = "rgba(52, 152, 219,1.0)";
        	var color_back = "rgba(52, 152, 219,0.5)";
        	var color_front2 = "rgba(231, 76, 60,1.0)";
        	var color_back2 = "rgba(231, 76, 60,0.5)";
    	    var areaChartCanvas = $("#areaChartSubmissions").get(0).getContext("2d");    	    
    	    var areaChartData 	= {
    	      labels: data.labels,
    	      datasets: [
    	        {
					label: "Submissions",
    	        	fill: true,
					lineTension: 0.1,
					backgroundColor: color_back,
					borderColor: color_front,
					borderCapStyle: 'butt',
					borderDash: [],
					borderDashOffset: 0.0,
					borderJoinStyle: 'miter',
					pointBorderColor: color_front,
					pointBackgroundColor: "#fff",
					pointBorderWidth: 1,
					pointHoverRadius: 5,
					pointHoverBackgroundColor: color_front,
					pointHoverBorderColor: "rgba(220,220,220,1)",
					pointHoverBorderWidth: 2,
					pointRadius: 1,
					pointHitRadius: 10,
					spanGaps: true,
					data: data.points,
					yAxisID: "1",
    	        }
    	      ]
    	    };    	    
    	    var areaChartOptions = {
    	      maintainAspectRatio: false,
    	      responsive: true,
    	      title: {
    	            display: false,
    	      },
    	      legend: {
	  	            display: false,
	  	      },
	  	      scales: {
		  	      yAxes: [{
		  	         position: "left",
		  	         "id": "1"
		  	       }]
	  	      }
    	    };    	    
    	    //Create the line chart
    	    var areaChart = new Chart.Line(areaChartCanvas, {
    	    	data: areaChartData,
    	    	options: areaChartOptions
    	    });
         }
     });
}

function refreshUploaders()
{
	return get_uploadersdata(function(data) {
		$.each(data, function(index, value){
			var user_display = 
			"<li>"
            + (value.avatar ? "<img alt='' height='72px' width='72px' class='img-circle' src='data:image/png;base64," + value.avatar + "'>"
            : "<img alt='' height='72px' width='72px' class='img-cicrle' src='dist/img/noavatar.jpg'>")
            + "<a class='users-list-name' href='#'>" + value.name + "</a>"
            + "<span class='users-list-date'>" + value.count + " files</span>"
            + "</li>";
			$(".users-list").append(user_display);
	    });
    }); 
}

function refreshTags()
{
	return get_tagsdata(function(data) {
		var $c = $('#tags-cloud');
		var words = [];
		$.each(data, function(key, value){
			var word 	= {};
			word.text 	= key;
			word.weight = value;
			word.link	= { href: 'index.php?tag=' + key, target: "_blank" };
			words.push(word);
		});
	    $c.jQCloud(words);
    }); 
}

$(function() {
	initStats();
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
