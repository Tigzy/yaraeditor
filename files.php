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
			Files <small>Detailed View</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>files.php"> Files</a></li>
		</ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div id='alert'></div>
      <!-- Your Page Content Here -->
      <div id='content'>                 
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Files</h3>
          </div>             
          <div class="box-body">
            <table id="files" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
              <thead>
              <tr>
                <th>File</th>
                <th>Imports</th>
                <th>Rules</th>                
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
			            <label class="col-sm-1 control-label" for="textinput">File name</label>
			            <div class="col-sm-10">
			              <input type="text" id="new-file-name" placeholder="MyFile" class="form-control">
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
	  
	  <div class="modal fade" id="edit-file" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
			    </div>
	            <div class="modal-body">
			      <form class="form-horizontal" role="form">
			        <fieldset>
			          <!-- Text input-->
			          <div class="form-group">
			            <label class="col-sm-1 control-label" for="textinput">Name</label>
			            <div class="col-sm-10">
			              <input type="text" id="new-file-name" placeholder="MyFile" class="form-control">
			            </div>
			          </div>				          			
			        </fieldset>			        
			        <fieldset>
			          <!-- Text input-->
			          <div class="form-group">			            	          	
			          	<label class="col-sm-1 control-label" for="textinput">Imports</label>
					    <div class="col-sm-10">
						    <select class="form-control" id="file-imports" multiple="">
						    	<?php foreach($GLOBALS["config"]["available_imports"] as $import) { ?>		
						      	<option value="<?php echo $import ?>"><?php echo $import ?></option>
						      	<?php } ?>
						    </select> 
					    </div>
			          </div>				          			
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
  function file_delete(id)
  {
	  Pace.start();
	  delete_file(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> File removed.</div>');
			
			Pace.stop();
			refresh_files();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove file</div>');
		}		
	  );	
  }

  function file_copy(id)
  {
	  var file_name = $('#confirm-action').find('#new-file-name').val();	  
	  
	  Pace.start();
	  copy_file(id, file_name,
		function(data, code) {		
		    $('#confirm-action').modal('hide');			    		    

		    if (file_name == "") {
				file_name = data.name;
			}

			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> File ' + file_name + ' created.</div>');
					    
			Pace.stop();
			refresh_files();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to copy file</div>');
		}		
	  );	
  }

  function file_open(id)
  {
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>file.php?id=" + id);
  }

  function file_export(id)
  {
	  Pace.start();
	  export_file( id,
		function(data, code) {		
		  	Pace.stop();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to export file</div>');
		}		
	  );
  }

  function file_add()
  {
	  var name = $('#edit-file').find('#new-file-name').val();
	  var imports = $('#file-imports').val();
	  
	  Pace.start();
	  add_file( name, imports,
		function(data, code) {		
		  	$('#edit-file').modal('hide');	
		  	
			var file_name 	= data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> File ' + file_name + ' created.</div>');
			
			Pace.stop();
			refresh_files();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to add file</div>');
		}		
	  );
  }

  function file_update(id)
  {
	  var name = $('#edit-file').find('#new-file-name').val();
	  var imports = $('#file-imports').val();
	  
	  Pace.start();
	  update_file( id, name, imports,
		function(data, code) {		
		  	$('#edit-file').modal('hide');	
		  	
			var file_name 	= data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> File ' + file_name + ' updated.</div>');
			
			Pace.stop();
			refresh_files();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to update file</div>');
		}		
	  );
  }

  function confirm_file_copy(id)
  {
	  $('#confirm-action').find('#confirm-file-name').show();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("File copy");
	  $('#confirm-action').find('#modal-message').html("This will copy the file and ALL THE RULES inside, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'file_copy(' + id + ')');
	  $('#confirm-action').modal('show');
  }
		  
  function confirm_file_delete(id)
  {
	  $('#confirm-action').find('#confirm-file-name').hide();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("File removal");
	  $('#confirm-action').find('#modal-message').html("This will remove the file and ALL THE RULES inside, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'file_delete(' + id + ')');
	  $('#confirm-action').modal('show');
  }

  function refresh_files() 
  {
      $('#files').DataTable().ajax.reload();
  }

  function show_add_file()
  {
	  $('#edit-file').find('.modal-header').html("Add File");
	  $('#edit-file').find('#new-file-name').val("");
	  $('#edit-file').find("select#file-imports option:selected").prop("selected", false);
	  $('#edit-file').find('.btn-ok').attr('OnClick', 'file_add()');
	  $('#edit-file').modal('show');	
	  $('#edit-file').find('#new-file-name').focus();  
  }

  function show_update_file(id)
  {
	  Pace.start();
	  get_file( id,
		function(data, code) {		
			Pace.stop();

			$('#edit-file').find('.modal-header').html("Edit File");
			$('#edit-file').find('#new-file-name').val(data.name);
			$('#edit-file').find('.btn-ok').attr('OnClick', 'file_update(' + id + ')');
			$('#edit-file').find("select#file-imports option:selected").prop("selected", false);
			$.each(data.imports, function(i,e){
				var item = $('#edit-file').find("select#file-imports option[value='" + e + "']");
				$('#edit-file').find("select#file-imports option[value='" + e + "']").prop("selected", true);
		    });				
			$('#edit-file').modal('show');	
			$('#edit-file').find('#new-file-name').focus();  
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get file</div>');
		}		
	  );
  }
  
  //============================================
	  
  var table;
  
  $(function () {
    $('#edit-file').on('shown.bs.modal', function () {
    	$('#edit-file').find('#new-file-name').focus();
	});
	
	table = $('#files').DataTable({
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
			  data.action 	= 'getfilestable';
			  data.folder	= -1;
		  },
	  },   
      columns: [
    	{ 
	    	data: "name", 
	    	width: "25%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>file.php?id=" + row.id + "'>" + data + " (#" + row.id + ")" + "</a>";
	        }
	    },
	    { 
			data: "imports",
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
		{ data: "rules", width: "20%" },
		{ data: "last_modified", width: "10%" },
		{ data: "created", width: "10%" },
		{ 
	    	data: "action", 
	    	width: "15%",
	    	render: function (data, type, row) 
	    	{
	    		return ""
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Open file' OnClick='file_open(" + row.id + ")'>"
	    		+ "<span class='fa fa-external-link table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Export file' OnClick='file_export(" + row.id + ")'>"
	    		+ "<span class='fa fa-download table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Edit file' OnClick='show_update_file(" + row.id + ")'>"
	    		+ "<span class='fa fa-pencil table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Copy file' OnClick='confirm_file_copy(" + row.id + ")'>"
	    		+ "<span class='fa fa-clone table-menu'></span>"
	    		+ "</button> "	    		
	    		+ "<button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete file' OnClick='confirm_file_delete(" + row.id + ")'>"
	    		+ "<span class='fa fa-trash table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 3, "desc" ]],
      select: true,
      buttons: [
        {
            text: "<i class='fa fa-refresh'></i>",
            titleAttr: 'Refresh',
            action: refresh_files
        },
        {
        	text: "<i class='fa fa-plus'></i>",
            titleAttr: 'Add File',
            action: show_add_file
        }
      ],
    });    
  });
</script>
</body>
</html>