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
<!-- DataTables -->
<link rel="stylesheet" href="plugins/datatables/css/dataTables.bootstrap.css">
<link rel="stylesheet" href="plugins/datatables/extensions/Buttons/css/buttons.bootstrap.min.css">
<link rel="stylesheet" href="plugins/datatables/extensions/Select/css/select.bootstrap.min.css">
<link rel="stylesheet" href="plugins/datatables/extensions/Editor/css/editor.bootstrap.min.css">
<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
<link rel="stylesheet" href="dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
<!-- Pace style -->
<link rel="stylesheet" href="plugins/pace/pace.min.css">
<!-- tags -->
<link rel="stylesheet" href="plugins/tagmanager/css/tagmanager.css" />

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
<style type="text/css">
.form-group input[type="checkbox"] {
    display: none;
}

.form-group input[type="checkbox"] + .btn-group > label span {
    width: 20px;
}

.form-group input[type="checkbox"] + .btn-group > label span:first-child {
    display: none;
}
.form-group input[type="checkbox"] + .btn-group > label span:last-child {
    display: inline-block;   
}

.form-group input[type="checkbox"]:checked + .btn-group > label span:first-child {
    display: inline-block;
}
.form-group input[type="checkbox"]:checked + .btn-group > label span:last-child {
    display: none;   
}
.myTag 
{
    background-color: #428bca !important;
    color: #fff !important;   
    font-size: 14px;
}
.tm-input
{
	margin-bottom: 0px !important;
}

.content
{
	min-height: 0px !important;
	padding-bottom: 0px !important;
}

pre 
{
	padding: 5px; 
	margin: 5px; 
	height: auto;
    max-height: 500px;
    overflow: auto;
    word-break: normal !important;
    word-wrap: normal !important;
    white-space: pre !important;
}
.string 	{ color: green; }
.number 	{ color: darkorange; }
.boolean 	{ color: blue; }
.null 		{ color: magenta; }
.key 		{ color: red; }
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
		<section class="content-header">
			<h1>
				Recycle Bin <small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
				<li class="active" id="bc-rule-name">Recycle</li>
			</ol>
		</section>			
		
		<!-- Main content -->
    	<section class="content">
    		<div id='alert'></div>	
			<!-- Horizontal Form -->
	        <div class="box box-info">
	          <div class="box-header with-border">
	            <h3 class="box-title">Removed Rules</h3>
	          </div>             
	          <div class="box-body">
	            <table id="rules" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
	              <thead>
	              <tr>
	                <th>Rule</th>
	                <th>Author</th>
	                <th>File</th>
	                <th>Threat</th>
	                <th>Tags</th>                         
	                <th>Last Modified</th>
	                <th>Created</th>
	                <th>Actions</th>
	              </tr>
	              </thead>
	              <tbody>              
	              </tbody>
	            </table>
	          </div>
	          <!-- /.box-body -->          
	        </div>
	        
	        <div class="modal fade" id="confirm-action" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		    <div class="modal-dialog">
		        <div class="modal-content">
		            <div class="modal-header">
		            </div>
		            <div class="modal-body">
		              <form class="form-horizontal" role="form">
				        <fieldset>	
				          <span id="modal-message"></span>			          			
				        </fieldset>		        
				      </form>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		                <a class="btn btn-danger btn-ok" OnClick="">Confirm</a>
		            </div>
		        </div>
		    </div>
		  </div>
	        
        </section>
        
	</div>
	<!-- /.content-wrapper -->

	<?php  include(__DIR__."/footer.php"); ?> 
	<?php  include(__DIR__."/right-nav.php"); ?> 
  
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  	$.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- PACE -->
<script src="plugins/pace/pace.min.js"></script>
<!-- ChartJS 2.2.2 -->
<script src="plugins/chartjs/2.2.2/Chart.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- AdminLTE App -->
<script src="plugins/ace/ace.js"></script>
<!-- tags -->
<script type="text/javascript" src="plugins/tagmanager/js/tagmanager.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/js/jquery.dataTables.js"></script>
<script src="plugins/datatables/js/dataTables.bootstrap.js"></script>
<script src="plugins/datatables/extensions/Buttons/js/dataTables.buttons.js"></script>
<script src="plugins/datatables/extensions/Buttons/js/buttons.bootstrap.js"></script>
<script src="plugins/datatables/extensions/Select/js/dataTables.select.js"></script>
<script src="plugins/datatables/extensions/Editor/js/dataTables.editor.js"></script>
<script src="plugins/datatables/extensions/Editor/js/editor.bootstrap.js"></script>
<script src="plugins/datatables/extensions/Editor/js/editor.tinymce.js"></script>
<script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables/extensions/Responsive/js/responsive.bootstrap.min.js"></script>
<!-- Charts -->
<script src="dist/js/main.js"></script>
<script>
function rule_open(id)
{
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + id);
}

function do_clear_recycle_bin()
{
	  Pace.start();
	  clear_recycle(
		function(data, code) {		
		    $('#confirm-action').modal('hide');		    
			Pace.stop();
			refresh_recycle_bin();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to clear recycle bin</div>');
		}		
	  );
}

function items_delete()
{
	  var selected = $('#rules').DataTable().rows( { selected: true } );
	  var selected_ids = [];
	  selected.every( function ( index, tableLoop, rowLoop ) {
		    var data = this.data();
		    selected_ids.push(data.id);
	  } );
	
	  Pace.start();
	  delete_rules_from_recycle_bin(selected_ids, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rules removed.</div>');
			
			Pace.stop();
			refresh_recycle_bin();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove rules</div>');
		}		
	  );
}

function rule_delete(id)
{
	  Pace.start();
	  delete_rule_from_recycle_bin(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule removed.</div>');
			
			Pace.stop();
			refresh_recycle_bin();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove rule</div>');
		}		
	  );	
}

function rule_restore(id)
{
	  Pace.start();
	  restore_rule(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule restored.</div>');
			
			Pace.stop();
			refresh_recycle_bin();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to restore rule</div>');
		}		
	  );	
}

function confirm_clear_recycle_bin()
{
	  $('#confirm-action').find('.modal-header').html("Clear Recycle Bin");
	  $('#confirm-action').find('#modal-message').html("This will remove all entries, do you want to proceed?");	 
	  $('#confirm-action').find('.btn-ok').show(); 
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'do_clear_recycle_bin()');
	  $('#confirm-action').modal('show');
}

function confirm_items_delete()
{
	  var count = $('#rules').DataTable().rows( { selected: true } ).count();
	  if (count == 0) {
	  	  return;
	  }
	
	  $('#confirm-action').find('.modal-header').html("Remove recycled items");
	  $('#confirm-action').find('#modal-message').html("This will remove selected entries, do you want to proceed?");	 
	  $('#confirm-action').find('.btn-ok').show(); 
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'items_delete()');
	  $('#confirm-action').modal('show');
}

function confirm_rule_delete(id)
{
	  $('#confirm-action').find('#confirm-file-name').hide();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("Rule removal");
	  $('#confirm-action').find('#modal-message').html("This will remove the rule, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'rule_delete(' + id + ')');
	  $('#confirm-action').modal('show');
}

function confirm_rule_restore(id)
{
	  $('#confirm-action').find('#confirm-file-name').hide();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("Rule restoration");
	  $('#confirm-action').find('#modal-message').html("This will restore the rule, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'rule_restore(' + id + ')');
	  $('#confirm-action').modal('show');
}

function refresh_recycle_bin() 
{
    $('#rules').DataTable().ajax.reload();
}

function initRecycleTable()
{
  // Load table
  var table;
	  		
  table = $('#rules').DataTable({
      dom: "Bfrtip",
      paging: true,
      pageLength: 25,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: true,
      processing: false,
      serverSide: false,
      responsive: true,
      //deferLoading: 0,
      fnInitComplete: function (oSettings, json) {
		  Pace.stop();
      },
      ajax: {
		  type: "GET",
		  dataType: "json",
		  url: "api.php",
		  data: function( data ) {
			  data.action = 'getrecycle';
		  }
	  },   
      columns: [
    	{ 
  	    	data: "name", 
  	    	width: "15%",
  	    	render: function (data, type, row) 
  	    	{
  	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + row.id + "'>" + data + " (#" + row.id + ")" + "</a>";
  	        }
  	    },
  	    { data: "author", width: "10%" },
  	    { data: "file_name", width: "10%" },
  	    { 
  			data: "threat",
  			width: "15%",
  			render: function (data, type, row) 
  	    	{
  				if (data != undefined) {
  					return "<span class='label label-danger' style='font-size: 12px;'>" + data + "</span>";
  				}
  				else {
  					return "";
  				}
  	        }
  	    },
  		{ 
  			data: "tags",
  			width: "15%",
  			render: function (data, type, row) 
  	    	{
  				content = "";
  				$.each(data, function(index, item) {
  					content += "<span class='label label-success' style='margin-right:5px; font-size: 12px;'>" + item + "</span>";
  				});
  				return content;
  	        }
  	    },	    
  		{ data: "last_modified", width: "10%" },
  		{ data: "created", width: "10%" },
  		{ 
  	    	data: "action", 
  	    	width: "15%",
  	    	render: function (data, type, row) 
  	    	{
  	    		return ""
  	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='View rule' OnClick='rule_open(" + row.id + ")'>"
  	    		+ "<span class='fa fa-eye table-menu'></span>"
  	    		+ "</button> "
  	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Restore rule' OnClick='confirm_rule_restore(" + row.id + ")'>"
  	    		+ "<span class='fa fa-ban table-menu'></span>"
  	    		+ "</button> "		    		
  	    		+ "<button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete rule' OnClick='confirm_rule_delete(" + row.id + ")'>"
  	    		+ "<span class='fa fa-trash table-menu'></span>"
  	    		+ "</button> ";
  	        }
  	    }
      ],
      order: [[ 0, "desc" ]],
      select: true,
      buttons: [
          {
        	  text: "<i class='fa fa-ban'></i>",
              titleAttr: 'Clear Recycle Bin',
              action: confirm_clear_recycle_bin
          },
          {
        	  text: "<i class='fa fa-refresh'></i>",
              titleAttr: 'Refresh',
			  action: refresh_recycle_bin
          },
          {
          	  text: "<i class='fa fa-trash'></i>",
              titleAttr: 'Delete',
              action: confirm_items_delete
          }
      ]
  });  
}

function initRecycle() 
{
    'use strict';	
	$.when(
		initRecycleTable()
	).always(function(a1, a2, a3, a4) {
		Pace.stop();
	});
}

$(function () {	 
	initRecycle();
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
