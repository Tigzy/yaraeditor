
//================================================
// Ajax calls

function export_file(file_id, onSuccess, onFailure)
{
	// Ajax isn't able to trigger downloads
	window.location.assign('api.php?action=exportfile&id=' + file_id);
	onSuccess();
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

function delete_rule(rule_id, onSuccess, onFailure)
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

function copy_rule(rule_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=copyrule',
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

function copy_file(file_id, onSuccess, onFailure)
{
	return $.ajax({
		url: 'api.php?action=copyfile',
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

//================================================
// UI functions

function loadRule(rule_id, file_id) {
	
	// Preload files list
	get_files(
		function(data_files, code) {
			
			// Fill entries
			var options = $("select#rule-file");
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
					
						// File
						$("select#rule-file").val( data.file_id );	
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
						
						// Refresh
						refreshRulePreview();
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
	
	// Header
	default_content = default_content 
		+ (input.is_private ? "private " : "")
		+ ((input.is_global && !input.is_private) ? "global " : "")
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
		+ (input.author.length > 0 ? ("    author = \"" + input.author + "\"\n") : "")
		+ (input.threat.length > 0 ? ("    threat = \"" + input.threat + "\"\n") : "")
		+ "";
		
	// Regular metas
	input.metas.forEach( function (d) {
		var low_value 	= d.value.toLowerCase();
		var value 		= d.value;
		if (low_value == "true" || low_value == "false") {
			// Boolean, nothing to do.
		}
		else if(!isNaN(parseInt(low_value))) {
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

