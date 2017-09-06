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
				Modifications History <small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
				<li class="active" id="bc-rule-name">History</li>
			</ol>
		</section>			
		
		<!-- Main content -->
    	<section class="content">
    		<div id='alert'></div>	
			<!-- Horizontal Form -->
	        <div class="box box-info">
	          <div class="box-header with-border">
	            <h3 class="box-title">History Items</h3>
	          </div>             
	          <div class="box-body">
	            <table id="history" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
	              <thead>
	              <tr>
	                <th>Date</th>
	                <th>User</th>
	                <th>Item</th>	                
	                <th>Name</th>
	                <th>Action</th>
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
function do_clear_history()
{
	  Pace.start();
	  clear_history(
		function(data, code) {		
		    $('#confirm-action').modal('hide');		    
			Pace.stop();
			refresh_history();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to clear history</div>');
		}		
	  );
}

function confirm_clear_history()
{
	  $('#confirm-action').find('.modal-header').html("Clear History");
	  $('#confirm-action').find('#modal-message').html("This will remove all entries, do you want to proceed?");	 
	  $('#confirm-action').find('.btn-ok').show(); 
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'do_clear_history()');
	  $('#confirm-action').modal('show');
}

function syntaxHighlight(json) {
    if (typeof json != 'string') {
         json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}

function display_data(title, data)
{
    var pretty_data = '<pre>' + syntaxHighlight(data) + '</pre>';
    $('#confirm-action').find('.modal-header').html(title);
    $('#confirm-action').find('#modal-message').html(pretty_data);	  
    $('#confirm-action').find('.btn-ok').hide();
    $('#confirm-action').modal('show');
}

function refresh_history() 
{
    $('#history').DataTable().ajax.reload();
}

function initHistoryTable()
{
  // Load table
  var table;
	  		
  table = $('#history').DataTable({
      dom: "Bfrtip",
      paging: true,
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
			  data.action 	= 'gethistory';
		  }
	  },   
      columns: [
    	{ data: "date", width: "20%" },
	    { data: "user_name", width: "10%" },	    
		{ 
			data: "item_type", 
			width: "15%",
			render: function (data, type, row) 
	    	{
				if (data != undefined) {
					var icon_label = 'fa-question-o';
					var text_label 	= data;
					if (data == 'file') {
						icon_label = 'fa-file-o';
						text_label  = 'File';
					} else if (data == 'rule') {
						icon_label = 'fa-star';
						text_label  = 'Rule';
					}						
					return "<span class='fa " + icon_label + " table-menu'></span> " + text_label;
				}
				else {
					return "";
				}
	        }
		},
		{ 
			data: "item_name", 
			width: "15%",
			render: function (data, type, row) 
	    	{
				var text_label 	= data;
				if (row.action == 'delete') {
					text_label  = "<i><span class='fa fa-lock table-menu' data-toggle='tooltip' title='Removed items cannot be open' style='padding-right: 5px;'></span> " + text_label + "</i>";
				}	
				else if (row.action == 'recyclebin') {
					text_label  = "<span class='fa fa-lock table-menu' data-toggle='tooltip' title='Removed items cannot be open' style='padding-right: 5px;'></span> "
						+ "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>recycle.php' target='_blank'>" + text_label + " (#" + row.item_id + ")" + "</a>";
				}
				else if (row.item_type == 'file') {
					text_label  = "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>file.php?id=" + row.item_id + "' target='_blank'>" + text_label + " (#" + row.item_id + ")" + "</a>";
				}
				else if (row.item_type == 'rule') {
					text_label  = "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + row.item_id + "' target='_blank'>" + text_label + " (#" + row.item_id + ")" + "</a>";
				}
				return text_label;
	        }			
		},	
		{ 
			data: "action",
			width: "20%",
			render: function (data, type, row) 
	    	{
				var text_label 	= data;
				if (data != undefined) {
					var class_label = 'label-primary';
					if (data == 'delete') {
						class_label = 'label-danger';
						text_label  = 'Deleted';
					} else if (data == 'edit') {
						class_label = 'label-warning';
						text_label  = 'Edited';
					} else if (data == 'add') {
						class_label = 'label-success';
						text_label  = 'Added';
					} else if (data == 'recyclebin') {
						class_label = 'label-danger';
						text_label  = 'Recycle Bin';
					} else if (data == 'restore') {
						class_label = 'label-success';
						text_label  = 'Restored';
					}			
					return "<span class='label " + class_label + "' style='font-size: 12px;'>" + text_label + "</span>";
				}
				else {
					return "";
				}
	        }
	    },	
		{ 
	    	data: "actions", 
	    	width: "15%",
	    	render: function (data, type, row) 
	    	{
	    		return ""
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='View new data' OnClick='display_data(\"New data\", " + row.item_value + ")'>"
	    		+ "<span class='fa fa-eye table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='View old data' OnClick='display_data(\"Old data\", " + row.item_old_value + ")'>"
	    		+ "<span class='fa fa-eye table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 0, "desc" ]],
      select: true,
      buttons: [
          {
              text: "Clear History",
              action: confirm_clear_history
          },
          {
			  text: "Refresh",
			  action: refresh_history
          }
        ],
  });  
}

function initHistory() 
{
    'use strict';	
	$.when(
		initHistoryTable()
	).always(function(a1, a2, a3, a4) {
		Pace.stop();
	});
}

$(function () {	 
	initHistory();
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
