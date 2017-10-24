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
  $is_admin = UCUser::IsUserAdmin();
?>

<?php 
  $rule_id = "";
  if (isset($_GET["id"])) {
  	$rule_id = $_GET["id"];
  }
  
  if (empty($rule_id)) {
  	header("HTTP/1.0 404 Not Found");
  	exit;
  }
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
<!-- Comments -->
<link rel="stylesheet" href="plugins/jquery-comments/css/jquery-comments.css">
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
    background-color: #367fa9 !important;
    color: #fff !important; 
	padding: 6px;
	border-radius: 5px;
}
.tm-input
{
	margin-bottom: 0px !important;
	display: none;
}
.btn-check 
{
    padding: 4px 10px !important;
    font-size: 14px;
    font-weight: 400;
}

.wrapper
{
    background-color: white !important;
}

.wrapper > .content
{
    min-height: 0px !important;
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
				Rule content <small></small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>				
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>files.php"> Files</a></li>		
				<li class="active" id="bc-file-name" data-file-url-base="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>file.php">Unknown</li>
				<?php if ($rule_id != "") { ?>		
				<li class="active" id="bc-rule-name"><?php echo $rule_id ?></li>
				<?php } else { ?>
				<li class="active" id="bc-rule-name">New</li>
				<?php } ?>
			</ol>
		</section>
	
		<!-- Main content -->
		<form>
		<section class="content">
			<div id='alert'></div>
			
			<!-- Header -->
			<div class="row">			
				<section class="col-lg-12">
			        <div class="form-group">
			            <input type="checkbox" name="isprivate" id="isprivate" autocomplete="off" onclick="return false;" />
			            <div class="btn-group" style="padding-right: 10px">
			                <label for="isprivate" class="btn btn-primary btn-check">
			                    <span class="glyphicon glyphicon-ok"></span>
			                    <span></span>
			                </label>
			                <label for="isprivate" class="btn btn-default btn-check active">
			                    Private
			                </label>
			            </div>
			            <input type="checkbox" name="isglobal" id="isglobal" autocomplete="off" onclick="return false;" />
			            <div class="btn-group" style="padding-right: 10px">
			                <label for="isglobal" class="btn btn-primary btn-check">
			                    <span class="glyphicon glyphicon-ok"></span>
			                    <span></span>
			                </label>
			                <label for="isglobal" class="btn btn-default btn-check active">
			                    Global
			                </label>
			            </div>
			            <div class="btn-group" style="padding-right: 10px">
				            <div class="input-group">
								<input type="text" placeholder="Rule tags" class="tm-input"/>
							</div>
						</div>
						<div class="btn-group pull-right" style="padding-right: 10px">
							<button id="edit-button" class="btn btn-warning jsbtn" data-toggle='tooltip' title='Edit rule' OnClick="rule_update(<?php echo $rule_id ?>)"><span class="fa fa-pencil"></button>
						</div>	
						<div class="btn-group pull-right" style="padding-right: 10px">
							<button id="export-button" class="btn btn-primary jsbtn" data-toggle='tooltip' title='Export rule' OnClick="rule_export(<?php echo $rule_id ?>)"><span class="fa fa-download"></span></button>
						</div>				
			        </div>
			    </section>				
			</div>
			
			<!--  General info -->
			<div class="row">
				<section class="col-lg-12">	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="rule-file"><span class="fa fa-file" style="padding-right: 10px"></span>File</span>
								<select id="rule-file" class="form-control" aria-describedby="rule-file" disabled>
					                <option></option>
					          	</select>
							</div>
						</div>
					</div>			
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="rule-name"><span class="fa fa-asterisk" style="padding-right: 10px"></span>Rule name</span>
								<input type="text" id="rule-name" class="form-control" placeholder="MyRule" aria-describedby="rule-name" readonly>
							</div>
						</div>
					</div>	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="author"><span class="fa fa-user" style="padding-right: 10px"></span>Author</span>
								<input type="text" id="author" class="form-control" placeholder="John" aria-describedby="author" 
										name="<?php if ($rule_id == "") { echo $user->Id(); } else { echo -1; } ?>"
										value="<?php if ($rule_id == "") { echo $user->DisplayName(); } else { echo ""; } ?>" readonly>
							</div>
						</div>
					</div>	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="threat-name"><span class="fa fa-bug" style="padding-right: 10px"></span>Threat name</span>
								<input type="text" id="threat-name" class="form-control" placeholder="Tr.Gen0" aria-describedby="threat-name" readonly>
							</div>
						</div>
					</div>									
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="comment"><span class="fa fa-comment" style="padding-right: 10px"></span>Comment</span>
								<textarea class="form-control" rows="1" id="comment" readonly></textarea>
							</div>
						</div>
					</div> 
				</section>				
			</div>
			
			<!-- Metas -->
			<div class="row">
				<section class="col-lg-12">			
					<div id="search" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_metas"><span class="fa fa-gears"></span> Metas</a>
					      </h4>
					    </div>
					    <div id="collapse_metas" class="panel-collapse collapse">
						    <div class="panel-body">	
						    	<table id="metas" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
					              <thead>
					              <tr>
					                <th>Name</th>
					                <th>Value</th> 
					              </tr>
					              </thead>
					              <tbody>              
					              </tbody>
					            </table>			    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
			<!-- Strings -->
			<div class="row">
				<section class="col-lg-12">			
					<div id="search" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_strings"><span class="fa fa-pencil-square-o"></span> Strings</a>
					      </h4>
					    </div>
					    <div id="collapse_strings" class="panel-collapse collapse">
						    <div class="panel-body">	
						    	<table id="strings" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
					              <thead>
					              <tr>
					                <th>Name</th>
					                <th>Value</th> 
					              </tr>
					              </thead>
					              <tbody>              
					              </tbody>
					            </table>				    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
			<!-- Condition -->
			<div class="row">
				<section class="col-lg-12">
			        <div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12">
							<div class="input-group">
								<span class="input-group-addon" id="condition"><span class="fa fa-question-circle" style="padding-right: 10px"></span>Condition</span>
								<input type="text" id="condition" class="form-control" placeholder="any of them" aria-describedby="comment" readonly>
							</div>
						</div>
					</div>
				</section>			
			</div>

			<!-- Preview -->			
			<div class="row" style="padding-top: 10px;">
				<section class="col-lg-12">			
					<div id="div-preview" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_preview"><span class="fa fa-eye"></span> Preview</a>
					      </h4>
					    </div>
					    <div id="collapse_preview" class="panel-collapse collapse in">
						    <div class="panel-body">	
						    	 <div id="preview" style="height: 350px; width: 100%"></div>				    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
			<!-- Tests -->
			<div class="row">
				<section class="col-lg-12">			
					<div id="search" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_tests"><span class="fa fa-refresh"></span> Tests</a>
					        <span id="tests-badge" class="badge">0</span>
					      </h4>
					    </div>
					    <div id="collapse_tests" class="panel-collapse collapse">
						    <div class="panel-body">	
						    	<table id="tests" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
					              <thead>
					              <tr>
					                <th>Status</th>
					                <th>Name</th>
					                <th>Author</th>
					                <th>Rule</th>               
					                <th>Last Modified</th>
					                <th>Created</th>
					                <th>Actions</th>
					              </tr>
					              </thead>
					              <tbody>              
					              </tbody>
					           </table>			    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
			<!-- Comments -->
			<div class="row">
				<section class="col-lg-12">			
					<div id="search" class="panel-group">
					  <div class="panel panel-info">
					    <div class="panel-heading">
					      <h4 class="panel-title">
					        <a data-toggle="collapse" href="#collapse_comments"><span class="fa fa-comment"></span> Comments</a>
					        <span id="comments-badge" class="badge">0</span>
					      </h4>
					    </div>
					    <div id="collapse_comments" class="panel-collapse collapse in">
						    <div class="panel-body">	
						    	<div id="comments"></div>		    
							</div>				    
					    </div>
					  </div>
					</div>			
				</section>				
			</div>
			
		</section>
		</form>
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
<!-- Comments -->
<script src="plugins/jquery-comments/js/jquery-comments.js"></script>
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
function rule_update(id)
{
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>edit.php?id=" + id, "_self");
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

function comment_add(comment, success, error2)
{
	  Pace.start();
	  add_comment( <?php echo $rule_id ?>, comment, 
		function(data, code) {
		    Pace.stop();
	        success(comment);
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to post comment</div>');
			error2();
		}		
	  );
}

function comment_edit(comment, success, error2)
{
	  Pace.start();
	  edit_comment( comment, 
		function(data, code) {
		    Pace.stop();
	        success(comment);
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to post comment</div>');
			error2();
		}		
	  );
}

function comment_delete(comment, success, error2)
{
	  Pace.start();
	  delete_comment( comment, 
		function(data, code) {
		    Pace.stop();
	        success(comment);
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to delete comment</div>');
			error2();
		}		
	  );
}

function refresh_comments(success, error2)
{
	  Pace.start();
	  get_comments( <?php echo $rule_id ?>,
		function(data, code) {		
		  	$("#comments-badge").html(data.length.toString());
		  	Pace.stop();
	        success(data);
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get comments</div>');
			error2();
		}		
	  );
}

function refresh_tests() 
{
  $('#tests').DataTable().ajax.reload();
}

function testset_open(id)
{
  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>test.php?id=" + id);
}

$(function () {
	// Editor
	var preview_editor = ace.edit("preview");
	preview_editor.setTheme("ace/theme/xcode");
	preview_editor.getSession().setMode("ace/mode/yara");
	preview_editor.$blockScrolling = Infinity;
	preview_editor.setOptions({
        readOnly: true,
        highlightActiveLine: false,
        highlightGutterLine: false
    });

	// Comments
	$('#comments').comments({
		<?php if( $is_admin ) { ?>
		currentUserIsAdmin: true,
		<?php } ?>
		enableAttachments: false,
		<?php if( $user == NULL ) { ?>
		readOnly: true,
		<?php } ?>
		enablePinging: false,
		enableNavigation: false,
		enableReplying: true,
		enableHashtags: true,
		enableUpvoting: false,
		forceResponsive: true,
		roundProfilePictures: true,
	    getComments: refresh_comments,
	    postComment: comment_add,
	    putComment: comment_edit,
	    deleteComment: comment_delete
	});

	// Prevents top scrolling with clicking the buttons in dropdown-menu
    $('.action').click(function(e) {
    	e.preventDefault();
    });	
    
	// Tags
	// https://maxfavilli.com/jquery-tag-manager	
	$(".tm-input").tagsManager({
		hiddenTagListName: 'hiddenTagList',
		tagClass: 'myTag tm-tag-disabled'
	});
	$(".tm-input").on('tm:pushed', function(e, tag) {
		onRuleFormChanged();
	});
	$(".tm-input").on('tm:spliced', function(e, tag) {
		onRuleFormChanged();
	});
	$(".tm-input").on('tm:popped', function(e, tag) {
		onRuleFormChanged();
	});
	$(".tm-input").on('tm:emptied', function(e, tag) {
		onRuleFormChanged();
	});
    
    // Prevents top scrolling with clicking the buttons in dropdown-menu
    $('.jsbtn').click(function(e) {
    	e.preventDefault();
    });	
    
    // Metas
    // ===========================================================
    
    var table_metas = $('#metas').DataTable({
        dom: "frtip",
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        processing: false,
        serverSide: false,
        responsive: true,        
        select: true,
        columns: [
        	{ 
                data: "name",
              	width: "20%"
            },
            { 
    			data: "value",
    			width: "80%"
    	    }
        ]
    });	

    $('#metas').on( 'draw.dt', function () {
    	onRuleFormChanged();
    } );

    // Strings
    // ===========================================================
        
    var table_strings = $('#strings').DataTable({
        dom: "frtip",
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        processing: false,
        serverSide: false,
        responsive: true,        
        select: true,
        columns: [
        	{ 
                data: "name",
              	width: "20%"
            },
            { 
    			data: "value",
    			width: "80%",
    			render: function (data, type, row) 
    	    	{
    				return "<code>" + data
    		         .replace(/&/g, "&amp;")
    		         .replace(/</g, "&lt;")
    		         .replace(/>/g, "&gt;")
    		         .replace(/"/g, "&quot;")
    		         .replace(/'/g, "&#039;") + "</code>";
    	        }
    	    }
        ]
    });	

    $('#strings').on( 'draw.dt', function () {
    	onRuleFormChanged();
    } );

 	// Tests
    // ===========================================================

    var table_tests = $('#tests').DataTable({
      dom: "Bfrtip",
      paging: true,
      lengthChange: true,
      searching: true,
      ordering: true,
      info: true,
      autoWidth: false,
      processing: false,
      serverSide: false,
      responsive: true,
      fnInitComplete: function (oSettings, json) {
    	  $("#tests-badge").html($('#tests').DataTable().row().count());
		  Pace.stop();
      },
      ajax: {
		  type: "GET",
		  dataType: "json",
		  url: "api.php",
		  data: function( data ) {
			  data.action 			= 'gettestsettable';
			  data.rule_id			= <?php echo $rule_id ?>;
		  },
	  },   
      columns: [
    	  { 
  			data: "status",
  			width: "10%",
  			render: function (data, type, row) 
  	    	{
  				var text_label 	= data;
				if (data != undefined) {
					var class_label = 'label-primary';
					if (data == 'idle') {
						class_label = 'label-primary';
						text_label  = 'Idle';
					}	
					else if (data == 'passed') {
						class_label = 'label-success';
						text_label  = 'Passed';
					}
					else if (data == 'failed') {
						class_label = 'label-danger';
						text_label  = 'Failed';
					}		
					return "<span class='label " + class_label + "' style='font-size: 12px;'>" + text_label + "</span>";
				}
				else {
					return "";
				}
  	        }
  	    },
    	{ 
	    	data: "name", 
	    	width: "20%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>test.php?id=" + row.id + "'>" + data + " (#" + row.id + ")" + "</a>";
	        }
	    },
	    { 
	    	data: "author", 
	    	width: "10%"
	    },
	    { 
	    	data: "rule_name", 
	    	width: "25%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + row.rule_id + "'>" + data + " (#" + row.rule_id + ")" + "</a>";
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
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Open tests set' OnClick='testset_open(" + row.id + ")'>"
	    		+ "<span class='fa fa-external-link table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 4, "desc" ]],
      select: true,
      buttons: [
        {
        	text: "<i class='fa fa-refresh'></i>",
            titleAttr: 'Refresh',
            action: refresh_tests
        }
        
      ],
    }); 

    // Load rule
    loadRule(<?php echo $rule_id ?>);
});
</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>
