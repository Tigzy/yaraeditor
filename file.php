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
  $file_id = "";
  if (isset($_GET["id"])) {
  	$file_id = $_GET["id"];
  }
  
  if (empty($file_id)) {
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
  <!-- bootstrap wysihtml5 - text editor -->
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
  -->
  <link rel="stylesheet" href="dist/css/skins/skin-blue.min.css">
  <!-- Pace style -->
  <link rel="stylesheet" href="plugins/pace/pace.min.css">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  
  <style type="text/css">
  	body .modal-dialog {
	    width: 60%;
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
			File <small>Detailed View</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>files.php"> Files</a></li>
			<li class="active" id="bc-file-name"><?php echo $file_id ?></li>
		</ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div id='alert'></div>
      <!-- Your Page Content Here -->
      <div id='content'>  
        <!-- Horizontal Form -->
        <div class="box box-primary">
          <div class="box-header with-border">
            <h3 class="box-title">File Information</h3>
            <div class="btn-group pull-right" style="padding-right: 10px">
				<button id="save-button" class="btn btn-warning jsbtn" data-toggle='tooltip' title='Save file' OnClick="file_update(<?php echo $file_id ?>)"><span class="fa fa-save"></span></button>
			</div>	
			<div class="btn-group pull-right" style="padding-right: 10px">
				<button id="export-button" class="btn btn-warning jsbtn" data-toggle='tooltip' title='Import file' OnClick="show_import_file()"><span class="fa fa-upload"></span></button>
			</div>
            <div class="btn-group pull-right" style="padding-right: 10px">
				<button id="export-button" class="btn btn-primary jsbtn" data-toggle='tooltip' title='Export file' OnClick="file_export(<?php echo $file_id ?>)"><span class="fa fa-download"></span></button>
			</div>				
          </div>             
          <div class="box-body">            
          	<div class="row">			   
				<section class="col-lg-12 connectedSortable">	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="file-id"><span class="fa fa-hashtag" style="padding-right: 10px"></span>File ID</span>
								<input type="text" id="file-id" class="form-control" placeholder="" aria-describedby="file-id" readonly>
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="file-name"><span class="fa fa-asterisk" style="padding-right: 10px"></span>File name</span>
								<input type="text" id="file-name" class="form-control" placeholder="" aria-describedby="file-name">
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="file-last-modified"><span class="fa fa-calendar" style="padding-right: 10px"></span>Last Modified</span>
								<input type="text" id="file-last-modified" class="form-control" placeholder="" aria-describedby="file-last-modified" readonly>
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="file-imports"><span class="fa fa-plug" style="padding-right: 10px"></span>Imports</span>
								<select class="form-control" id="file-imports" multiple="">
							    	<?php foreach($GLOBALS["config"]["available_imports"] as $import) { ?>		
							      	<option value="<?php echo $import ?>"><?php echo $import ?></option>
							      	<?php } ?>
							    </select> 
							</div>
						</div>
					</div>	
				</section>
			</div>
          </div>
          <!-- /.box-body -->          
        </div>
      </div>
                     
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Rules</h3>
          </div>             
          <div class="box-body">
            <table id="rules" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
              <thead>
              <tr>
                <th>Rule</th>
                <th>Author</th>
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
	  
	  <div class="modal fade" id="import-file" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
			    </div>
	            <div class="modal-body">
			      <form id="form-add-test">
			        <fieldset>
			          <!-- Text input-->
			          <div id="form-text" class="form-group">
			            <label class="col-sm-1 control-label" id="import-label"></label>
			            <div class="col-sm-11">
			              <textarea id="import-string" style="width: 100%; height: 400px"></textarea>
			            </div>
			          </div>			          			          			
			        </fieldset>			        
			      </form>				      
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	                <a class="btn btn-danger btn-ok" OnClick="">Import</a>
	            </div>
	        </div>
	    </div>
	  </div>
	  
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
<!-- Bootstrap 3.3.6 -->
<script src="plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- TinyMCE -->
<script src="plugins/tinymce/js/tinymce/tinymce.min.js"></script>
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
<!-- SlimScroll -->
<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>
<!-- Select2 -->
<!--<script src="plugins/select2/select2.full.min.js"></script>-->
<!-- Bootstrap WYSIHTML5 -->
<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- PACE -->
<script src="plugins/pace/pace.min.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
      
<script src="dist/js/main.js"></script>
      
<script>
  function file_update(id)
  {
	  var name = $('input#file-name').val();
	  var imports = $('select#file-imports').val();
	  
	  Pace.start();
	  update_file( id, name, imports,
		function(data, code) {		
		  	$('#edit-file').modal('hide');	
		  	
			var file_name 	= data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> File ' + file_name + ' updated.</div>');
			
			Pace.stop();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to update file</div>');
		}		
	  );
  }

  function file_export(id)
  {
	  Pace.start();
	  export_file( id,
		function(data, code) {		
		  	Pace.stop();
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to export file</div>');
		}		
	  );
  }
  
  function rule_delete(id)
  {
	  Pace.start();
	  delete_rule(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule moved into recycle bin.</div>');
			
			Pace.stop();
			refresh_rules();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to move rule into recycle bin</div>');
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

  function fill_information()
  {
	  Pace.start();
	  get_file(<?php echo $file_id; ?>,
		function(data, code) {		
		    $('#confirm-action').modal('hide');	

		    $('li#bc-file-name').text(data.name);
		    $('input#file-name').val(data.name);	
		    $('input#file-id').val(data.id);		
		    $('input#file-last-modified').val(data.last_modified);

		    $.each(data.imports, function(i,e){
		        $("select#file-imports option[value='" + e + "']").prop("selected", true);
		    });		
		    
			Pace.stop();
			refresh_rules();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable find file data</div>');
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

  function rules_import()
  {
	  var string_value = $('#import-file').find('textarea#import-string').val();
	  
	  Pace.start();
	  import_rules( <?php echo $file_id ?>, string_value,
		function(data, code) {		
		  	$('#import-file').modal('hide');			  	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rules imported with success.</div>');
			
			Pace.stop();
			refresh_rules();
		},
		function(message, error) {
			$('#import-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to import rules</div>');
		}		
	  );
  }

  function rule_add()
  {
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>edit.php?file_id=" + <?php echo $file_id ?>);
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
	  $('#confirm-action').find('#modal-message').html("This will put the rule into recycle bin, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'rule_delete(' + id + ')');
	  $('#confirm-action').modal('show');
  }

  function refresh_rules() 
  {
      $('#rules').DataTable().ajax.reload();
  }

  function show_import_file()
  {
	  $('#import-file').find('.modal-header').html("Import Rule(s)");
	  $('#import-file').find('label#import-label').html("Content");
	  $('#import-file').find('textarea#import-string').val("");  
	  $('#import-file').find('textarea#import-string').attr('placeholder', 'put your rule(s) content here');
	  $('#import-file').find('.btn-ok').attr('OnClick', 'rules_import()');
	  $('#import-file').modal('show');	
	  $('#import-file').find('textarea#import-string').focus();  
  }
  
  //============================================
	  
  var table;
  
  $(function () {	
	fill_information();
		
	table = $('#rules').DataTable({
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
      fnInitComplete: function (oSettings, json) {
		  Pace.stop();
      },
      ajax: {
		  type: "GET",
		  dataType: "json",
		  url: "api.php",
		  data: function( data ) {
			  data.action 	= 'getrules';
			  data.file		= <?php echo $file_id; ?>;
		  },
	  },   
      columns: [
    	{ 
	    	data: "name", 
	    	width: "20%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + row.id + "'>" + data + " (#" + row.id + ")" + "</a>";
	        }
	    },
	    { data: "author", width: "10%" },
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
			width: "20%",
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
      order: [[ 4, "desc" ]],
      select: true,
      buttons: [
        {
        	text: "<i class='fa fa-refresh'></i>",
            titleAttr: 'Refresh',
            action: refresh_rules
        },
        {
        	text: "<i class='fa fa-plus'></i>",
            titleAttr: 'Add Rule',
            action: rule_add
        }
      ],
    });    
  });
</script>
</body>
</html>