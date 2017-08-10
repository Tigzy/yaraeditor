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
    font-size: 16px;
}
.tm-input
{
	margin-bottom: 0px !important;
	display: none;
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
				<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"> Files</a></li>		
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
			                <label for="isprivate" class="btn btn-primary">
			                    <span class="glyphicon glyphicon-ok"></span>
			                    <span></span>
			                </label>
			                <label for="isprivate" class="btn btn-default active">
			                    Private
			                </label>
			            </div>
			            <input type="checkbox" name="isglobal" id="isglobal" autocomplete="off" onclick="return false;" />
			            <div class="btn-group" style="padding-right: 10px">
			                <label for="isglobal" class="btn btn-primary">
			                    <span class="glyphicon glyphicon-ok"></span>
			                    <span></span>
			                </label>
			                <label for="isglobal" class="btn btn-default active">
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
			<div class="row">

				<section class="col-lg-12">
					<div class="form-group">
					  <label for="rule-name">Preview</label>
					  <div id="preview" style="height: 350px; width: 100%"></div>	
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
        autoWidth: true,
        processing: false,
        serverSide: false,
        responsive: true,        
        select: true,
        columns: [
            { data: "name" },
            { data: "value" }
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
        autoWidth: true,
        processing: false,
        serverSide: false,
        responsive: true,        
        select: true,
        columns: [
            { data: "name" },
            { data: "value" }
        ]
    });	

    $('#strings').on( 'draw.dt', function () {
    	onRuleFormChanged();
    } );

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
