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
  <!-- JQuery-UI -->
  <link rel="stylesheet" href="plugins/jQueryUI/jquery-ui.min.css">
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
      
      <!-- Breadcrumb -->
     <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol>-->
    </section>

    <!-- Main content -->
    <section class="content">

      <div id='alert'></div>
      <!-- Your Page Content Here -->
      <div id='content'>                 
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Tests</h3>
          </div>             
          <div class="box-body">
            <table id="tests" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
              <thead>
              <tr>
                <th>Status</th>
                <th>Name</th>
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
			          <div id="confirm-test-name" class="form-group">
			            <label class="col-sm-1 control-label" for="textinput">Test name</label>
			            <div class="col-sm-10">
			              <input type="text" id="new-test-name" placeholder="MyFile" class="form-control">
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
	  
	  <div class="modal fade" id="edit-test" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
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
			              <input type="text" id="new-test-name" placeholder="MyTest" class="form-control">
			            </div>
			          </div>				          			
			        </fieldset>			        
			        <fieldset>
			          <!-- Text input-->
			          <div class="form-group">			            	          	
			          	<label class="col-sm-1 control-label" for="textinput">Rule Name</label>
					    <div id="rule-name-container" class="col-sm-10">
						    <input type="text" id="rule-name" class="form-control" placeholder="start typing..." aria-describedby="rule-name">
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
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jQueryUI/jquery-ui.min.js"></script>
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
  function search_rulename(request, response)
  {
	    Pace.start();
	      rulename_search(request.term, 
			function(data, code) {	
	    	    var data_format = [];
				for (var index in data) {
					var data_obj = {};
					data_obj.value = data[index].id;
					data_obj.label = data[index].name + ' (#' + data[index].id + ')';
					data_format.push(data_obj);
				}				  	
				Pace.stop();
				response(data_format);
			},
			function(message, error) {
			}		
		  );
  }

  function autocomplete_select(event, ui)
  {
	  $("input#rule-name").val(ui.item.label); // display the selected text
      $("input#rule-name").attr('item_id',ui.item.value); // save selected id to hidden input
      return false;
  }
  
  function testset_delete(id)
  {
	  Pace.start();
	  delete_testset(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Test set removed.</div>');
			
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove test set</div>');
		}		
	  );	
  }

  function testset_run(id)
  {	  
	  Pace.start();
	  run_testset( id,
		function(data, code) {		
		  	$('#edit-test').modal('hide');	
			if (data == 'failed') {
				$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Test failed, please open to fix</div>');
			}
			else {		  	
				$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Tests passed.</div>');
			}	
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to run tests</div>');
		}		
	  );
  }

  function testset_open(id)
  {
	  window.open("<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>test.php?id=" + id);
  }

  function testset_add()
  {
	  var name 		= $('#edit-test').find('input#new-test-name').val();
	  var rule_id 	= $('#edit-test').find('input#rule-name').attr('item_id');
	  
	  Pace.start();
	  add_testset( name, rule_id,
		function(data, code) {		
		  	$('#edit-test').modal('hide');	
		  	
			var testset_name = data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Tests Set ' + testset_name + ' created.</div>');
			
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#edit-test').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to add tests set</div>');
		}		
	  );
  }

  function testset_update(id)
  {
	  var name 		= $('#edit-test').find('input#new-test-name').val();
	  var rule_id 	= $('#edit-test').find('input#rule-name').attr('item_id');
	  
	  Pace.start();
	  update_testset( id, name, rule_id,
		function(data, code) {		
		  	$('#edit-test').modal('hide');	
		  	
			var testset_name 	= data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Tests set ' + testset_name + ' updated.</div>');
			
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#edit-test').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to update tests set</div>');
		}		
	  );
  }
		  
  function confirm_testset_delete(id)
  {
	  $('#confirm-action').find('#confirm-test-name').hide();
	  $('#confirm-action').find('#new-test-name').val("");	
	  $('#confirm-action').find('.modal-header').html("Tests Set removal");
	  $('#confirm-action').find('#modal-message').html("This will remove the tests set and ALL THE TESTS inside, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'testset_delete(' + id + ')');
	  $('#confirm-action').modal('show');
  }

  function refresh_tests() 
  {
      $('#tests').DataTable().ajax.reload();
  }

  function show_add_testset()
  {
	  $('#edit-test').find('.modal-header').html("Add Test Set");
	  $('#edit-test').find('input#new-test-name').val("");
	  $('#edit-test').find("input#rule-name").val("");
	  $('#edit-test').find('.btn-ok').attr('OnClick', 'testset_add()');
	  $('#edit-test').modal('show');	
	  $('#edit-test').find('#new-test-name').focus();  
  }

  function show_update_testset(id)
  {
	  Pace.start();
	  get_testset(id,
		function(data, code) {		
			Pace.stop();

			var label = data.rule_name + ' (#' + data.rule_id + ')';
			$('#edit-test').find('.modal-header').html("Edit Test Set");
			$('#edit-test').find('input#new-test-name').val(data.name);
			$('#edit-test').find('input#rule-name').val(label);
			$('#edit-test').find('input#rule-name').attr('item_id',data.rule_id);
			$('#edit-test').find('.btn-ok').attr('OnClick', 'testset_update(' + id + ')');			
			$('#edit-test').modal('show');	
			$('#edit-test').find('#new-test-name').focus(); 
		},
		function(message, error) {
			$('#edit-test').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get test set</div>');
		}		
	  );
  }
  
  //============================================
	  
  var table;
  
  $(function () {
    $('#edit-test').on('shown.bs.modal', function () {
    	$('#edit-test').find('#new-test-name').focus();
	});

 	// Rule name autocomplete
    $( "input#rule-name" ).autocomplete({
    	source: search_rulename,
    	select: autocomplete_select
    });
    $( "input#rule-name" ).autocomplete( "option", "appendTo", "#rule-name-container" );
	
	table = $('#tests').DataTable({
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
			  data.action 	= 'gettestsettable';
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
	    	width: "30%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>test.php?id=" + row.id + "' target='_blank'>" + data + " (#" + row.id + ")" + "</a>";
	        }
	    },
	    { 
	    	data: "rule_name", 
	    	width: "25%",
	    	render: function (data, type, row) 
	    	{
	    		return "<a href='" + "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + row.rule_id + "' target='_blank'>" + data + " (#" + row.rule_id + ")" + "</a>";
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
	    		+ "<button type='button' class='btn btn-sm btn-success' data-toggle='tooltip' title='Run test' OnClick='testset_run(" + row.id + ")'>"
	    		+ "<span class='fa fa-play table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='Open tests set' OnClick='testset_open(" + row.id + ")'>"
	    		+ "<span class='fa fa-external-link table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Edit file' OnClick='show_update_testset(" + row.id + ")'>"
	    		+ "<span class='fa fa-pencil table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete file' OnClick='confirm_testset_delete(" + row.id + ")'>"
	    		+ "<span class='fa fa-trash table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 4, "desc" ]],
      select: true,
      buttons: [
        {
        	text: "<i class='fa fa-plus'></i>",
            titleAttr: 'Add Tests Set',
            action: show_add_testset
        },
        {
        	text: "<i class='fa fa-refresh'></i>",
            titleAttr: 'Refresh',
            action: refresh_tests
        }
      ],
    });    
  });
</script>
</body>
</html>