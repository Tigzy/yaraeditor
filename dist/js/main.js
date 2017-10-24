
//================================================
// Ajax calls

function get_storage_info(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getstorageinfo',
		dataType: 'json',
		type: 'get',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function get_uploadersdata(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getsubmissionsperuserdata',
		dataType: 'json',
		data: {},	
		type: 'get',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function get_last_comments(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getlastcomments',
		dataType: 'json',
		data: {},	
		type: 'get',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function get_tagsdata(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=gettagsdata',
		dataType: 'json',
		data: {},	
		type: 'get',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function export_file(file_id, onSuccess, onFailure)
{
	// Ajax isn't able to trigger downloads
	window.location.assign('api.php?action=exportfile&id=' + file_id);
	onSuccess();
}

function export_rule(rule_id, onSuccess, onFailure)
{
	// Ajax isn't able to trigger downloads
	window.location.assign('api.php?action=exportrule&id=' + rule_id);
	onSuccess();
}

function import_rules(file_id, content, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=importrules',
		dataType: 'json',	
		data: {id: file_id, content: content},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function import_rule(rule_id, content, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=importrule',
		dataType: 'json',	
		data: {id: rule_id, content: content},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_rule(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getrule',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function update_rule(rule_id, rule_content, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=updaterule',
		dataType: 'json',	
		data: {id: rule_id, content: rule_content},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function check_rule(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=yarachecksyntax',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_rule(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=moverulerecyclebin',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_rules(rule_ids, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=moverulesrecyclebin',
		dataType: 'json',	
		data: {ids: rule_ids},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function restore_rule(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=restorerule',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_rule_from_recycle_bin(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deleterule',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_rules_from_recycle_bin(rule_ids, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deleterules',
		dataType: 'json',	
		data: {ids: rule_ids},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function copy_rule(rule_id, rule_name, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=copyrule',
		dataType: 'json',	
		data: {id: rule_id, name: rule_name},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_files(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getfiles',
		dataType: 'json',	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_file(file_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getfile',
		dataType: 'json',	
		data: {id: file_id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_file(file_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletefile',
		dataType: 'json',	
		data: {id: file_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_files(file_ids, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletefiles',
		dataType: 'json',	
		data: {ids: file_ids},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function copy_file(file_id, file_name, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=copyfile',
		dataType: 'json',	
		data: {id: file_id, name: file_name},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function add_file(file_name, file_imports, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=addfile',
		dataType: 'json',	
		data: {name: file_name, imports: file_imports},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function update_file(file_id, file_name, file_imports, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=updatefile',
		dataType: 'json',	
		data: {id: file_id, name: file_name, imports: file_imports},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function clear_history(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=clearhistory',
		dataType: 'json',
		type: 'post',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function clear_recycle(onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=clearrecycle',
		dataType: 'json',
		type: 'post',				
		success: function(data) { 
			if (onSuccess) onSuccess(data); 				
		},
		error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure();             
        }
	});
}

function threat_search(request, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=searchthreat',
		dataType: 'json',	
		data: {request: request},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function rulename_search(request, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=searchrulename',
		dataType: 'json',	
		data: {request: request},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function add_testset(test_name, rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=addtestset',
		dataType: 'json',	
		data: {name: test_name, rule_id: rule_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_testset(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=gettestset',
		dataType: 'json',	
		data: {id: id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function update_testset(id, name, rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=updatetestset',
		dataType: 'json',	
		data: {id: id, name: name, rule_id: rule_id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_testset(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletetestset',
		dataType: 'json',	
		data: {id: id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_testsets(ids, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletetestsets',
		dataType: 'json',	
		data: {ids: ids},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function add_test(testset_id, type, content, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=addtest',
		dataType: 'json',	
		data: {id: testset_id, type: type, content: content},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_test(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=gettest',
		dataType: 'json',	
		data: {id: id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function update_test(id, type, content, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=updatetest',
		dataType: 'json',	
		data: {id: id, type: type, content: content},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_test(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletetest',
		dataType: 'json',	
		data: {id: id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function copy_test(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=copytest',
		dataType: 'json',	
		data: {id: id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function run_test(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=runtest',
		dataType: 'json',	
		data: {id: id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function run_testset(id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=runtestset',
		dataType: 'json',	
		data: {id: id},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function get_comments(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=getcomments',
		dataType: 'json',	
		data: {id: rule_id},	
		type: 'get',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function add_comment(rule_id, comment, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=addcomment',
		dataType: 'json',	
		data: {id: rule_id, comment: comment},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function edit_comment(comment, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=editcomment',
		dataType: 'json',	
		data: {comment: comment},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

function delete_comment(comment, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=deletecomment',
		dataType: 'json',	
		data: {comment: comment},	
		type: 'post',
		success: function(data, textStatus, xhr) { 
			if (onSuccess) onSuccess(data, xhr.status); 		
		},
        error: function(xhr, textStatus, errorThrown){
        	if (onFailure) onFailure(xhr.responseText, errorThrown);            
        }
	});
}

//================================================
// UI functions

var rule_imports = [];
function loadRule(rule_id, file_id) {
	
	// Globals
	rule_imports =[];
	
	// Clear
	var options = $("select#rule-file");
	options.find('option').remove();	
	$('#metas').DataTable().rows().clear();
	$('#strings').DataTable().rows().clear();
	
	// Preload files list
	get_files(
		function(data_files, code) {
			
			// Fill entries
			$.each(data_files, function(index, item) {
				options.append($("<option/>").val(item.id).text(item.name));				
			});
			
			// Preload content
			if (rule_id != -1) {		
				// Ajax post
				get_rule(rule_id, 
					function(data, code) {
							
						// Private
						$('input#isprivate').prop('checked', data.is_private);
						
						// Global
						$('input#isglobal').prop('checked', data.is_global);
						
						// Public
						$('input#ispublic').prop('checked', data.is_public);
					
						// File
						options.val( data.file_id );	
						$.each(data_files, function(index, item) {
							if (data.file_id == item.id) {
								var url = $('li#bc-file-name').data('file-url-base') + "?id=" + data.file_id;
								$('li#bc-file-name').html("<a href='" + url + "'> " + item.name + "</a>");
							}
						});
						
						// Rule name
						$("input#rule-name").val( data.name );	
						$('li#bc-rule-name').text( data.name );
						
						// Threat name
						$("input#threat-name").val( data.threat );
						
						// Comment
						$("textarea#comment").val( data.comment );
						
						// Author
						$("input#author").attr( 'name', data.author_id );
						$("input#author").val( data.author );
						
						// Tags
						for (var j=0, tag; tag=data.tags[j]; j++)				
							$(".tm-input").tagsManager('pushTag', tag, true);	// ignore events so we don't call AJAX while pushing here
						
						// Metas	
						for (var j=0, meta; meta=data.metas[j]; j++)		
							$('#metas').DataTable().row.add( {
								'DT_RowId'	: j,
								'name'		: meta.name,
					            'value'		: meta.value
							} ).draw( false );
											
						// Strings
						for (var j=0, string; string=data.strings[j]; j++)		
							$('#strings').DataTable().row.add( {
								'DT_RowId'	: j,
								'name'		: string.name,
					            'value'		: string.value
							} ).draw( false );
						
						// Condition
						$("input#condition").val( data.cond );
						
						// Imports
						get_file(data.file_id, 
							function(data, code) {	
							
								// Imports	
								for (var j=0, imp; imp=data.imports[j]; j++)		
									rule_imports.push(imp);								
							
								refreshRulePreview();
							},
							function(message, error) {	
								$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get rule: ' + message + ' (' + error + ')</div>');
							}
						);					
					},
					function(message, error) {
						$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get rule: ' + message + ' (' + error + ')</div>');
					}		
				);		
			}
			else {
				if (file_id != -1) {
					$("select#rule-file").val( file_id );
				}	
				
				// Condition
				$("input#condition").val( 'any of them' );
				
				refreshRulePreview();
			}
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to get files list: ' + message + ' (' + error + ')</div>');
		}		
	);
}

function onRuleFormChanged() {	
	refreshRulePreview();
}

function serializeRuleInput() {	
	
	var serialized = {};
	
	// Private
	serialized.is_private = ($('input#isprivate').is(":checked"));
	
	// Global
	serialized.is_global = ($('input#isglobal').is(":checked"));
	
	// Public
	serialized.is_public = ($('input#ispublic').is(":checked"));
	
	// File ID
	serialized.file_id = $("select#rule-file").val();
	
	// Rule name
	serialized.name = $("input#rule-name").val();	
		
	// Threat name
	serialized.threat = $("input#threat-name").val();
	
	// Comment
	serialized.comment = $("textarea#comment").val();
	
	// Author
	serialized.author_id = $("input#author").attr( 'name' );
	serialized.author 	 = $("input#author").val();
	
	// Tags
	serialized.tags = $(".tm-input").tagsManager('tags');
	
	// Metas
	serialized.metas = [];
	metas = $('#metas').DataTable().rows().data();	
	metas.each( function (d) {
		meta = {};
		meta.name 	= d.name;
		meta.value	= d.value; 
		serialized.metas.push(meta);
	} );	
	
	// Strings
	serialized.strings = [];
	strings = $('#strings').DataTable().rows().data();	
	strings.each( function (d) {
		string = {};
		string.name 	= d.name;
		string.value	= d.value; 
		serialized.strings.push(string);
	} );
	
	// Condition
	serialized.condition = $("input#condition").val();
	
	return serialized;
}

function isInt(value) 
{
    if (/^(\-|\+)?([0-9]+|Infinity)$/.test(value))
        return Number(value);
    return NaN;
}

function refreshRulePreview() {
	
	Pace.start();
	
	var editor = ace.edit("preview");
	
	//==================================================
	// Read input
	
	var input = serializeRuleInput();
	
	//==================================================
	// Sanitize
	
	if (input.name == "") input.name = "MyRule";
	input.name = input.name.replace(/ /g, '_');
	
	//==================================================
	// Build preview
	
	var default_content = "";
	if (input.comment != "") {
		default_content = default_content
			+ "/*\n"
			+ input.comment + "\n"
			+ "*/\n";
	}
	
	// Imports
	for (var j=0, imp; imp=rule_imports[j]; j++) {
		default_content = default_content
			+ "import \"" + imp + "\"\n";
	}
	if (rule_imports.length > 0) default_content = default_content + "\n";
	
	// Header
	default_content = default_content 
		+ (input.is_private ? "private " : "")
		+ (input.is_global  ? "global " : "")
		+ "rule " + input.name;

	// Tags
	if (input.tags.length > 0) {
		default_content = default_content 	
		+ " : "
		+ input.tags.join(" ");
	}
	
	default_content = default_content 		
		+ "\n{\n"
		
	//==================================================
	// Metas
		+ "  meta:\n"
	// Special metas
		+ "    author = \"" + input.author + "\"\n"
		+ "    threat = \"" + input.threat + "\"\n"
		+ "";
		
	// Regular metas
	input.metas.forEach( function (d) {
		var low_value 	= d.value.toLowerCase();
		var value 		= d.value;
		if (low_value == "true" || low_value == "false") {
			// Boolean, nothing to do.
		}
		else if(!isNaN(isInt(low_value))) {
			// Int, nothing to do
		}
		else {
			// String, needs quotes
			value = "\"" + d.value + "\"";
		}
		
		default_content = default_content 
			+ "    " + d.name + " = " + value + "\n";
	} );		
		
	//==================================================
	// Strings
	default_content = default_content 
		+ "  strings:\n"	
		
	input.strings.forEach( function (d) {
		default_content = default_content 
			+ "    " + d.name + " = " + d.value + "\n";
	} );	
		
	//==================================================
	// Footer
	default_content = default_content 
		+ "  condition:\n"
		+ "    " + input.condition + "\n"
		+ "}\n";
	editor.setValue(default_content);
	editor.clearSelection();
	
	Pace.stop();
}

function saveRule(rule_id) {
	Pace.start();
	
	// Disable buttons to prevent multiple clicks
	//$("#save-button").prop('disabled', true);
	//$("#cancel-button").prop('disabled', true);
	
	// Read results
	var input 		= serializeRuleInput();	

	// Serialize results
	var json_input 	= JSON.stringify(input);
	
	// Ajax post
	update_rule(rule_id, json_input, 
		function(data, code) {
			// Read results
			var rule_new_id 	= data.id;
			var rule_name 		= data.name;
		
			if (code == 201) {
				$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule ' + rule_name + ' created.</div>');
			}
			else {
				$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule ' + rule_name + ' updated.</div>');
			}			
			
			// Redirect to edit page
			Pace.stop();
			
			if (rule_id == undefined) {
				var url 				= window.location.href;
				url 					= url.split("?")[0];
				window.location.href 	= url + "?id=" + escape( rule_new_id );
			}
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to create rule: ' + message + ' (' + error + ')</div>');
		}		
	);	
}

function checkRule(rule_id) {
	Pace.start();
	
	// Remove Highlight
	var preview_editor = ace.edit("preview");	
	preview_editor.getSession().clearAnnotations();
	
	// Ajax post
	check_rule(rule_id, 
		function(data, code) {
			// Read results		
			if (data.valid) {
				$("#alert").html('<div class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-info-sign"></span> Rule is valid.</div>');
			}
			else {
				$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Rule has errors, please review: ' 
						+ '(line ' + data.error.line + ') ' + data.error.message 
						+ '</div>');
				
				// Highlight error	
				preview_editor.getSession().setAnnotations([{
				    row: data.error.line - 1,
				    column: 0,
				    text: data.error.message,
				    type: "error" // also warning and information
				}]);
			}
			
			Pace.stop();
		},
		function(message, error) {
			$("#alert").html('<div class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="glyphicon glyphicon-exclamation-sign"></span> Unable to check rule: ' + message + ' (' + error + ')</div>');
		}		
	);	
}