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
				Rule search <small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
				<li class="active" id="bc-rule-name">Search</li>
			</ol>
		</section>
	
		<!-- Main content -->
		<form>
		<section class="content">
			<div id='alert'></div>
					
			<!--  Quick Search -->
			<div class="row">
				<section class="col-lg-12">	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="quick-search"><span class="fa fa-magic" style="padding-right: 10px"></span>Quick Search</span>
								<input type="text" id="quick-search" class="form-control" placeholder="Type anything..." aria-describedby="quick-search" onkeyup="onRuleSearchChanged()">
							</div>
						</div>
					</div>
				</section>				
			</div>	
						
			<!-- Advanced Search -->
			<div class="row">
				<section class="col-lg-12">			
					<div id="search" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_search"><span class="fa fa-search"></span> Advanced Search (matches all of them)</a>
					      </h4>
					    </div>
					    <div id="collapse_search" class="panel-collapse collapse">
						    <div class="panel-body">	
						    	<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="rule-file"><span class="fa fa-file" style="padding-right: 10px"></span>File</span>
											<select id="rule-file" aria-describedby="file-descr" class="selectpicker form-control" data-live-search="true" onchange="onRuleSearchChanged()">
								                <option value="none"></option>
								          	</select>
										</div>
									</div>
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="fav-descr"><span class="fa fa-lock"></span> Private</span>
											<select id="rule-private" aria-describedby="private-descr" class="selectpicker form-control" data-live-search="true" onchange="onRuleSearchChanged()">
												<option value="none"> </option>
											    <option value="no-private">No</option>
											    <option value="private">Yes</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="fav-descr"><span class="fa fa-globe"></span> Global</span>
											<select id="rule-global" aria-describedby="global-descr" class="selectpicker form-control" data-live-search="true" onchange="onRuleSearchChanged()">
												<option value="none"> </option>
											    <option value="no-global">No</option>
											    <option value="global">Yes</option>
											</select>
										</div>
									</div>
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="rule-name"><span class="fa fa-asterisk" style="padding-right: 10px"></span>Rule name</span>
											<input type="text" id="rule-name" class="form-control" placeholder="Some rule name..." aria-describedby="rule-name" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
								</div>	
								<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="rule-tags"><span class="fa fa-tags" style="padding-right: 10px"></span>Tags</span>
											<input type="text" id="rule-tags" class="form-control" placeholder="Some tag..." aria-describedby="rule-tags" onkeyup="onRuleSearchChanged()"/>
										</div>
									</div>
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="rule-author"><span class="fa fa-user" style="padding-right: 10px"></span>Author</span>
											<select id="rule-author" aria-describedby="global-descr" class="selectpicker form-control" data-live-search="true" onchange="onRuleSearchChanged()">
								                <option value="none"></option>
								          	</select>
										</div>
									</div>
								</div>	
								<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="threat-name"><span class="fa fa-bug" style="padding-right: 10px"></span>Threat name</span>
											<input type="text" id="threat-name" class="form-control" placeholder="Some threat name..." aria-describedby="threat-name" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="comment"><span class="fa fa-comment" style="padding-right: 10px"></span>Comment</span>
											<input type="text" id="comment" class="form-control" placeholder="Some comment..." aria-describedby="comment" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
								</div>
								<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="metas"><span class="fa fa-gears" style="padding-right: 10px"></span>Metas</span>
											<input type="text" id="metas" class="form-control" placeholder="Some meta name or value..." aria-describedby="metas" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="strings"><span class="fa fa-pencil-square-o" style="padding-right: 10px"></span>Strings</span>
											<input type="text" id="strings" class="form-control" placeholder="Some string name or value..." aria-describedby="strings" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
								</div> 
								<div class="row" style="padding-bottom: 10px">
									<div class="control-group col col-lg-4">
										<div class="input-group">
											<span class="input-group-addon" id="condition"><span class="fa fa-question-circle" style="padding-right: 10px"></span>Condition</span>
											<input type="text" id="condition" class="form-control" placeholder="Some condition..." aria-describedby="condition" onkeyup="onRuleSearchChanged()">
										</div>
									</div>
								</div> 	    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
		</section>
		</form>
		
		<!-- Main content -->
    	<section class="content">
			<!-- Horizontal Form -->
	        <div class="box box-info">
	          <div class="box-header with-border">
	            <h3 class="box-title">Search Results</h3>
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
				          <!-- Text input-->
				          <div id="confirm-file-name" class="form-group">
				            <label class="col-sm-1 control-label" for="textinput">Rule name</label>
				            <div class="col-sm-10">
				              <input type="text" id="new-file-name" placeholder="MyRule" class="form-control">
				            </div>
				          </div>	
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
function rule_view(id)
{
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + id, "_self");
}

function rule_delete(id)
{
	  Pace.start();
	  delete_rule(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule removed.</div>');
			
			Pace.stop();
			refresh_rules();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove rule</div>');
		}		
	  );	
}

function rule_copy(id)
{
	  var rule_name = $('#confirm-action').find('#new-file-name').val();
	  
	  Pace.start();
	  copy_rule(id, rule_name,
		function(data, code) {		
		    $('#confirm-action').modal('hide');	

		    if (rule_name == "") {
		    	rule_name = data.name;
			}
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule ' + rule_name + ' created.</div>');
					    
			Pace.stop();
			refresh_rules();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to copy rule</div>');
		}		
	  );	
}

function rule_open(id)
{
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + id);
}

function rule_export(id)
{
	  Pace.start();
	  export_rule( id,
		function(data, code) {		
		  	Pace.stop();
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to export rule</div>');
		}		
	  );
}

function rule_update(id)
{
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>edit.php?id=" + id);
}

function confirm_rule_copy(id)
{
	  $('#confirm-action').find('#confirm-file-name').show();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("Rule copy");
	  $('#confirm-action').find('#modal-message').html("This will copy the rule, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'rule_copy(' + id + ')');
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

function refresh_rules() 
{
    $('#rules').DataTable().ajax.reload();
}

function refreshFiles()
{
	return get_files(function(data, code) {
		// Fill entries
		var options = $("select#rule-file");
		$.each(data, function(index, item) {
			options.append($("<option/>").val(item.id).text(item.name));				
		});
    }); 
}

function refreshUsers()
{
	return get_uploadersdata(function(data) {
		// Fill entries
		var options = $("select#rule-author");
		$.each(data, function(index, item) {
			options.append($("<option/>").val(item.uploader).text(item.name));				
		});
    }); 
}

function refreshSearch( data ) 
{
  	data.action 		= 'searchrules';

  	var value = $("input#quick-search").val();	
	if (value != "") {
		data.quick = value;
	}
  	
	var value = $('select#rule-file').val();
	if (value != 'none') {
		data.file = value;
	}
	  
	var value = $('select#rule-private').val();
	if (value == 'no-private') {
		data.is_private = false;
	} else if (value == 'private') {
		data.is_private = true;
	}

	var value = $('select#rule-global').val();
	if (value == 'no-global') {
		data.is_global = false;
	} else if (value == 'global') {
		data.is_global = true;
	}
	
	var value = $("input#rule-name").val();	
	if (value != "") {
		data.name = value;
	}

	var value = $("input#rule-tags").val();	
	if (value != "") {
		data.tags = value;
	}

	var value = $("select#rule-author").val();	
	if (value != 'none') {
		data.author = value;
	}
	
	var value = $("input#threat-name").val();	
	if (value != "") {
		data.threat = value;
	}

	var value = $("input#comment").val();	
	if (value != "") {
		data.comment = value;
	}

	var value = $("input#metas").val();	
	if (value != "") {
		data.metas = value;
	}

	var value = $("input#strings").val();	
	if (value != "") {
		data.strings = value;
	}

	var value = $("input#condition").val();	
	if (value != "") {
		data.condition = value;
	}
}

function initRulesTable()
{
  // Load table
  var table;
	  		
  table = $('#rules').DataTable({
      dom: "frtip",
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
		  data: refreshSearch,
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
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Export file' OnClick='rule_export(" + row.id + ")'>"
	    		+ "<span class='fa fa-download table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Edit rule' OnClick='rule_update(" + row.id + ")'>"
	    		+ "<span class='fa fa-pencil table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Copy rule' OnClick='confirm_rule_copy(" + row.id + ")'>"
	    		+ "<span class='fa fa-clone table-menu'></span>"
	    		+ "</button> "	    		
	    		+ "<button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete rule' OnClick='confirm_rule_delete(" + row.id + ")'>"
	    		+ "<span class='fa fa-trash table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 0, "asc" ]],
      select: true
  });  
}

function initSearch() 
{
    'use strict';	
	$.when(
		refreshFiles(),
		refreshUsers(),
		initRulesTable()
	).always(function(a1, a2, a3, a4) {
		Pace.stop();
	});
}

function onRuleSearchChanged()
{
	refresh_rules();
}

$(function () {	 
	initSearch();
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
