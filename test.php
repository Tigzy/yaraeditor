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
  $test_id = "";
  if (isset($_GET["id"])) {
  	$test_id = $_GET["id"];
  }
  
  if (empty($test_id)) {
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
body .modal-dialog 
{
	 width: 60%;
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
			Tests Set<small>Detailed View</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>index.php"><i class="fa fa-home"></i> Home</a></li>
			<li><a href="<?php echo $GLOBALS["config"]["urls"]["baseUrl"]; ?>tests.php"> Tests</a></li>
			<li class="active" id="bc-test-name"><?php echo $test_id ?></li>
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
            <h3 class="box-title">Tests Set Information</h3>
            <div class="btn-group pull-right" style="padding-right: 10px">
				<button id="save-button" class="btn btn-warning jsbtn" data-toggle='tooltip' title='Save file' OnClick="ruleset_update(<?php echo $test_id ?>)"><span class="fa fa-save"></span></button>
			</div>			
          </div>             
          <div class="box-body">            
          	<div class="row">			   
				<section class="col-lg-12 connectedSortable">	
					<div class="row" style="padding-bottom: 10px">
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="test-id"><span class="fa fa-hashtag" style="padding-right: 10px"></span>Tests Set ID</span>
								<input type="text" id="test-id" class="form-control" placeholder="" aria-describedby="test-id" readonly>
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="test-name"><span class="fa fa-asterisk" style="padding-right: 10px"></span>Tests Set name</span>
								<input type="text" id="test-name" class="form-control" placeholder="" aria-describedby="test-name">
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="test-name"><span class="fa fa-asterisk" style="padding-right: 10px"></span>Rule name <a id="link-rule" href="#" target="_blank"> (open)</a></span>
								<input type="text" id="rule-name" class="form-control" placeholder="start typing..." aria-describedby="rule-name">
							</div>
						</div>
						<div class="control-group col col-lg-12" style="padding-bottom: 10px;">
							<div class="input-group">
								<span class="input-group-addon" id="test-last-modified"><span class="fa fa-calendar" style="padding-right: 10px"></span>Last Modified</span>
								<input type="text" id="test-last-modified" class="form-control" placeholder="" aria-describedby="test-last-modified" readonly>
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
            <h3 class="box-title">Tests</h3>
          </div>             
          <div class="box-body">
            <table id="tests" class="table table-bordered table-striped dt-responsive" width="100%" cellspacing="0">
              <thead>
              <tr>
                <th>Status</th>
                <th>Type</th>
                <th>Item</th>
                <th>Results</th>                
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
			            <label class="col-sm-1 control-label" id="test-label"></label>
			            <div class="col-sm-11">
			              <textarea id="test-string" style="width: 100%; height: 100px"></textarea>
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
  function ruleset_update(id)
  {
	  var name = $('input#test-name').val();
	  var rule_id = $('input#rule-name').attr('item_id');
	  
	  Pace.start();
	  update_testset( id, name, rule_id,
		function(data, code) {		
		  	$('#edit-file').modal('hide');	
		  	
			var ruleset_name 	= data.name;
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rules Set ' + ruleset_name + ' updated.</div>');
			
			Pace.stop();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to update rules set</div>');
		}		
	  );
  }

  function test_add(type)
  {
	  var string_value = $('#edit-test').find('textarea#test-string').val();
	  
	  Pace.start();
	  add_test( <?php echo $test_id ?>, type, string_value,
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

  function test_run(id)
  {	  
	  Pace.start();
	  run_test( id,
		function(data, code) {		
		  	$('#edit-test').modal('hide');	
			if (!data.valid) {
				$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Rule is invalid</div>');
			}
			else if (!data.has_matches) {		  	
				$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Test failed.</div>');
			}
			else {		  	
				$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Test passed.</div>');
			}	
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to run test</div>');
		}		
	  );
  }

  function test_update(id, type)
  {
	  var content = $('#edit-test').find('textarea#test-string').val();
	  
	  Pace.start();
	  update_test( id, type, content,
		function(data, code) {		
		  	$('#edit-test').modal('hide');			  	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Test updated.</div>');
			
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#edit-file').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to update test</div>');
		}		
	  );
  }
  
  function test_delete(id)
  {
	  Pace.start();
	  delete_test(id, 
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Test removed.</div>');
			
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to remove test</div>');
		}		
	  );	
  }

  function test_copy(id)
  {	  
	  Pace.start();
	  copy_test(id,
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
			$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Test copied.</div>');
					    
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to copy test</div>');
		}		
	  );	
  }

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

  function fill_information()
  {
	  Pace.start();
	  get_testset(<?php echo $test_id; ?>,
		function(data, code) {		
		    $('#confirm-action').modal('hide');	
		    $('li#bc-test-name').text(data.name);
		    $('a#link-rule').attr("href", "<?php echo $GLOBALS["config"]["urls"]["baseUrl"] ?>view.php?id=" + data.rule_id);

		    var label = data.rule_name + ' (#' + data.rule_id + ')';
		    $('input#rule-name').val(label);
		    $('input#test-name').val(data.name);	
		    $('input#test-id').val(data.id);		
		    $('input#test-last-modified').val(data.last_modified);
		    
			Pace.stop();
			refresh_tests();
		},
		function(message, error) {
			$('#confirm-action').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable find test data</div>');
		}		
	  );
  }

  function confirm_test_copy(id)
  {
	  $('#confirm-action').find('#confirm-file-name').show();
	  $('#confirm-action').find('.modal-header').html("Test copy");
	  $('#confirm-action').find('#modal-message').html("This will copy the test, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'test_copy(' + id + ')');
	  $('#confirm-action').modal('show');
  }
		  
  function confirm_test_delete(id)
  {
	  $('#confirm-action').find('#confirm-file-name').hide();
	  $('#confirm-action').find('#new-file-name').val("");	
	  $('#confirm-action').find('.modal-header').html("Test removal");
	  $('#confirm-action').find('#modal-message').html("This will remove the test, do you want to proceed?");	  
	  $('#confirm-action').find('.btn-ok').attr('OnClick', 'test_delete(' + id + ')');
	  $('#confirm-action').modal('show');
  }

  function show_add_testset_string_ansi()
  {
	  $('#edit-test').find('.modal-header').html("Add Test");
	  $('#edit-test').find('label#test-label').html("String Value (ANSI)");
	  $('#edit-test').find('textarea#test-string').val("");
	  $('#edit-test').find('.btn-ok').attr('OnClick', 'test_add(\"string_ansi\")');
	  $('#edit-test').modal('show');	
	  $('#edit-test').find('textarea#test-string').focus();  
  }

  function show_add_testset_string_unicode()
  {
	  $('#edit-test').find('.modal-header').html("Add Test");
	  $('#edit-test').find('label#test-label').html("String Value (UNICODE)");
	  $('#edit-test').find('textarea#test-string').val("");
	  $('#edit-test').find('.btn-ok').attr('OnClick', 'test_add(\"string_unicode\")');
	  $('#edit-test').modal('show');	
	  $('#edit-test').find('textarea#test-string').focus();  
  }

  function show_update_test(id)
  {
	  Pace.start();
	  get_test( id,
		function(data, code) {		
			Pace.stop();

			if (data.type == 'string_ansi') {
				$('#edit-test').find('label#test-label').html("String Value (ANSI)");
			}
			else if (data.type == 'string_unicode') {
				$('#edit-test').find('label#test-label').html("String Value (UNICODE)");
			}

			$('#edit-test').find('.modal-header').html("Edit File");
			$('#edit-test').find('textarea#test-string').val(data.content);
			$('#edit-test').find('.btn-ok').attr('OnClick', "test_update(" + id + ",\"" + data.type + "\")");
			$('#edit-test').modal('show');	
			$('#edit-test').find('textarea#test-string').focus();  
		},
		function(message, error) {
			$('#edit-test').modal('hide');
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get test</div>');
		}		
	  );
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

  function test_open(id)
  {
	  Pace.start();
	  get_test( id,
		function(data, code) {		
			Pace.stop();

			var pretty_data = '<pre>' + syntaxHighlight(data.results) + '</pre>';
		    if (data.results == '') {
		  	  pretty_data = '';
		    } 
	      
	        $('#confirm-action').find('.modal-header').html(data.content);
	        $('#confirm-action').find('#modal-message').html(pretty_data);	  
	        $('#confirm-action').find('.btn-ok').hide();
	        $('#confirm-action').modal('show'); 
		},
		function(message, error) {
			$('#confirm-action').modal('hide'); 
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get test</div>');
		}		
	  );
  }

  function refresh_tests() 
  {
      $('#tests').DataTable().ajax.reload();
  }
  
  //============================================
	  
  var table;
  
  $(function () {
	// Rule name autocomplete
    $( "input#rule-name" ).autocomplete({
    	source: search_rulename,
    	select: autocomplete_select
    });
    $( "input#rule-name" ).autocomplete( "option", "appendTo", "#rule-name-container" );
	  	
	fill_information();
		
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
			  data.action 	= 'getteststable';
			  data.id		= <?php echo $test_id; ?>;
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
			data: "type",
			width: "10%",
			render: function (data, type, row) 
	    	{
				if (data != undefined) {
					var icon_label = 'fa-question-o';
					var text_label 	= data;
					if (data == 'file') {
						icon_label = 'fa-file-o';
						text_label  = 'File';
					} else if (data == 'string_ansi') {
						icon_label = 'fa-pencil-square-o';
						text_label  = 'String (ANSI)';
					} else if (data == 'string_unicode') {
						icon_label = 'fa-pencil-square-o';
						text_label  = 'String (UNICODE)';
					}					
					return "<span class='fa " + icon_label + " table-menu'></span> " + text_label;
				}
				else {
					return "";
				}
	        }
	    },	
	    { data: "content", width: "20%" },	
	    { 
		    data: "results", 
		    width: "25%",
		    render: function (data, type, row) 
	    	{
	    		return JSON.stringify(data);
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
	    		+ "<button type='button' class='btn btn-sm btn-success' data-toggle='tooltip' title='Run test' OnClick='test_run(" + row.id + ")'>"
	    		+ "<span class='fa fa-play table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-primary' data-toggle='tooltip' title='View test' OnClick='test_open(" + row.id + ")'>"
	    		+ "<span class='fa fa-eye table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Edit test' OnClick='show_update_test(" + row.id + ")'>"
	    		+ "<span class='fa fa-pencil table-menu'></span>"
	    		+ "</button> "
	    		+ "<button type='button' class='btn btn-sm btn-warning' data-toggle='tooltip' title='Copy test' OnClick='confirm_test_copy(" + row.id + ")'>"
	    		+ "<span class='fa fa-clone table-menu'></span>"
	    		+ "</button> "	    		
	    		+ "<button type='button' class='btn btn-sm btn-danger' data-toggle='tooltip' title='Delete test' OnClick='confirm_test_delete(" + row.id + ")'>"
	    		+ "<span class='fa fa-trash table-menu'></span>"
	    		+ "</button> ";
	        }
	    }
      ],
      order: [[ 4, "desc" ]],
      select: true,
      buttons: [
        {
        	extend: 'collection',
        	text: "<i class='fa fa-plus'></i>",
            titleAttr: 'Add Test',
            autoClose: true,
            buttons: [ 
            	{
                    text: "String (ANSI)",
                    action: show_add_testset_string_ansi
                },
                {
                    text: "String (UNICODE)",
                    action: show_add_testset_string_unicode
                }/*,
                {
                    text: "File (Soon)",
                    action: show_add_testset_string
                }*/
    		]
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