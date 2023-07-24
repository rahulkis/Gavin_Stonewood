var jwcfe_settings = (function($, window, document) {
	var MSG_INVALID_NAME = WcfeAdmin.MSG_INVALID_NAME;

	/*------------------------------------
	*---- ON-LOAD FUNCTIONS - SATRT -----
	*------------------------------------*/
	$(function() {
		$('input[name=fname]').bind('input', function(){
			$(this).val(function(_, v){
				return v.replace(/\s+/g, '');
			});
		});
	});
	
    $( "#jwcfe_new_field_form_pp" ).dialog({
	  	modal: true,
		width: 700,
		//height: 400,
		resizable: false,
		autoOpen: false,
		buttons: [{
			text: "Add New Field",
			click: function() {
				$('<input>').attr({
					type: 'hidden',
					vaule: 'yes',
					id: 'foo',
					name: 'save_fields'
				}).appendTo('#jwcfe_checkout_fields_form');
				var result = jwcfe_add_new_row( this );
				var form = $("#jwcfe_checkout_fields_form");
				if(result){
					form.submit(); 
				}
			}
		}]
	});
	
	$( "#jwcfe_edit_field_form_pp" ).dialog({
	  	modal: true,
		width: 700,
		//height: 400,
		resizable: false,
		autoOpen: false,
		buttons: [{
			text: "Save",
			click: function() {
				$('<input>').attr({
					type: 'hidden',
					vaule: 'yes',
					id: 'foo',
					name: 'save_fields'
				}).appendTo('#jwcfe_checkout_fields_form');
				var result = jwcfe_update_row( this );
				
				var form = $("#jwcfe_checkout_fields_form");
				if(result){
					
					form.submit(); 
					
				}
			}
		}]
	});
	
	$('select.jwcfe-enhanced-multi-select').select2({
		placeholder: "Select validations",
		minimumResultsForSearch: 10,
		allowClear : true,
	}).addClass('enhanced');
				
	$( ".jwcfe_remove_field_btn" ).click( function() {
		var form =  $(this.form);		
		
		$('#jwcfe_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			$(this).closest('tr').remove();
	  	});	  	
	});
	
	$('#jwcfe_checkout_fields tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: 'td.sort',
		scrollSensitivity:40,
		helper:function(e,ui){
			ui.children().each(function(){
				$(this).width($(this).width());
			});
			ui.css('left', '0');
			return ui;
		}
	});
	
	$("#jwcfe_checkout_fields tbody").on("sortstart", function( event, ui ){
		ui.item.css('background-color','#f6f6f6');										
	});
	$("#jwcfe_checkout_fields tbody").on("sortstop", function( event, ui ){
		ui.item.removeAttr('style');
		jwcfe_prepare_field_order_indexes();
	});
	
	
	_openNewFieldForm = function openNewFieldForm(tabName){
		if(tabName == 'billing' || tabName == 'shipping' || tabName == 'additional' || tabName == 'account'){
			tabName = tabName+'_';	
		}
		
		var form = $("#jwcfe_new_field_form_pp");
		jwcfe_clear_form(form);

		form.find("select[name=ftype]").change();
		form.find("input[name=fclass]").val('form-row-wide');
		
	  	$( "#jwcfe_new_field_form_pp" ).dialog( "open" );
	}
	
	function jwcfe_add_new_row(form){
		var name  = $(form).find("input[name=fname]").val();
		var type  = $(form).find("select[name=ftype]").val();
		var label = $(form).find("input[name=flabel]").val();
		var placeholder = $(form).find("input[name=fplaceholder]").val();
		var optionsList = $(form).find("input[name=foptions]").val();
		var maxlength = $(form).find("input[name=fmaxlength]").val();
		
		var fieldClass = $(form).find("select[name=fclass]").val();
		var labelClass = $(form).find("input[name=flabelclass]").val();
		
		var required = $(form).find("input[name=frequired]").prop('checked');
		
		var enabled  = $(form).find("input[name=fenabled]").prop('checked');
		
		var showinemail = $(form).find("input[name=fshowinemail]").prop('checked');
		var showinorder = $(form).find("input[name=fshowinorder]").prop('checked');
		
		var validations = $(form).find("select[name=fvalidate]").val();
		
	
		var err_msgs = '';
		if(name == ''){
			err_msgs = 'Name is required';
		}else if(!isHtmlIdValid(name)){
			err_msgs = MSG_INVALID_NAME;
		
		}else if(type == ''){
			err_msgs = 'Type is required';
		}else if(optionsList == ''){
			if(type == 'select'){
				err_msgs = 'Options is required';
			}
			
		}
		
		
		
		if(err_msgs != ''){
			$(form).find('.err_msgs').html(err_msgs);
			return false;
		}
				
		
		required = required ? 1 : 0;
		
		enabled  = enabled ? 1 : 0;
		
		showinemail = showinemail ? 1 : 0;
		showinorder = showinorder ? 1 : 0;
		
		validations = validations ? validations : '';
		
		var index = $('#jwcfe_checkout_fields tbody tr').size();
		
		var newRow = '<tr class="row_'+index+'">';
		newRow += '<td width="1%" class="sort ui-sortable-handle">';
		newRow += '<input type="hidden" name="f_order['+index+']" class="f_order" value="'+index+'" />';
		newRow += '<input type="hidden" name="f_custom['+index+']" class="f_custom" value="1" />';
		newRow += '<input type="hidden" name="f_name['+index+']" class="f_name" value="'+name+'" />';
		newRow += '<input type="hidden" name="f_name_new['+index+']" class="f_name_new" value="'+name+'" />';
		newRow += '<input type="hidden" name="f_type['+index+']" class="f_type" value="'+type+'" />';
		newRow += '<input type="hidden" name="f_label['+index+']" class="f_label" value="'+label+'" />';		
		newRow += '<input type="hidden" name="f_placeholder['+index+']" class="f_placeholder" value="'+placeholder+'" />';		
		newRow += '<input type="hidden" name="f_options['+index+']" class="f_options" value="'+optionsList+'" />';
		newRow += '<input type="hidden" name="f_maxlength['+index+']" class="f_maxlength" value="'+maxlength+'" />';		
		
		newRow += '<input type="hidden" name="f_class['+index+']" class="f_class" value="'+fieldClass+'" />';
		newRow += '<input type="hidden" name="f_label_class['+index+']" class="f_label_class" value="'+labelClass+'" />';
	
		
		newRow += '<input type="hidden" name="f_required['+index+']" class="f_required" value="'+required+'" />';

		
		newRow += '<input type="hidden" name="f_enabled['+index+']" class="f_enabled" value="'+enabled+'" />';
		
		newRow += '<input type="hidden" name="f_show_in_email['+index+']" class="f_show_in_email" value="'+showinemail+'" />';
		newRow += '<input type="hidden" name="f_show_in_order['+index+']" class="f_show_in_order" value="'+showinorder+'" />';
				
		newRow += '<input type="hidden" name="f_validation['+index+']" class="f_validation" value="'+validations+'" />';
		newRow += '<input type="hidden" name="f_deleted['+index+']" class="f_deleted" value="0" />';
		newRow += '</td>';		
		newRow += '<td ><input type="checkbox" /></td>';		
		newRow += '<td class="name">'+name+'</td>';
		newRow += '<td class="id">'+type+'</td>';
		newRow += '<td>'+label+'</td>';
		newRow += '<td>'+placeholder+'</td>';
		newRow += '<td>'+validations+'</td>';
		if(required == true){
			newRow += '<td class="status"><span class="status-enabled tips">Yes</span></td>';
		}else{
			newRow += '<td class="status">-</td>';
		}
		
		if(enabled == true){
			newRow += '<td class="status"><span class="status-enabled tips">Yes</span></td>';
		}else{
			newRow += '<td class="status">-</td>';
		}
		
		newRow += '<td><button type="button" onclick="openEditFieldForm(this)">Edit</button></td>';
		newRow += '</tr>';
		
		$('#jwcfe_checkout_fields tbody tr:last').after(newRow);
		return true;
	}
				
	_openEditFieldForm = function openEditFieldForm(elm, rowId){
		var row = $(elm).closest('tr')
		
		var is_custom = row.find(".f_custom").val();

		var name  = row.find(".f_name").val();
		var type  = row.find(".f_type").val();
		var label = row.find(".f_label").val();
		var placeholder = row.find(".f_placeholder").val();
		var maxlength = row.find(".f_maxlength").val();
		var optionsList = row.find(".f_options").val();
		var field_classes = row.find(".f_class").val();
		var label_classes = row.find(".f_label_class").val();
		
		var required = row.find(".f_required").val();
		
		
		var enabled = row.find(".f_enabled").val();
		var validations = row.find(".f_validation").val();	
		
		var showinemail = row.find(".f_show_in_email").val();
		var showinorder = row.find(".f_show_in_order").val();
		
		is_custom = is_custom == 1 ? true : false;
		
		required = required == 1 ? true : false;
		
		enabled  = enabled == 1 ? true : false;
		
		validations = validations.split(",");
		
		showinemail = showinemail == 1 ? true : false;
		showinorder = showinorder == 1 ? true : false;
		
		showinemail = is_custom == true ? showinemail : true;
		showinorder = is_custom == true ? showinorder : true;
								
		var form = $("#jwcfe_edit_field_form_pp");
		form.find('.err_msgs').html('');
		form.find("input[name=rowId]").val(rowId);
		form.find("input[name=fname]").val(name);
		form.find("input[name=fnameNew]").val(name);
		form.find("select[name=ftype]").val(type);
		form.find("input[name=flabel]").val(label);
		form.find("input[name=fplaceholder]").val(placeholder);
		form.find("input[name=fmaxlength]").val(maxlength);
		form.find("input[name=foptions]").val(optionsList);
		
		form.find("select[name=fclass]").val(field_classes);
		form.find("input[name=flabelclass]").val(label_classes);
		form.find("select[name=fvalidate]").val(validations).trigger("change");
		

		form.find("input[name=frequired]").prop('checked', required);

		form.find("input[name=fenabled]").prop('checked', enabled);		
		
		form.find("input[name=fshowinemail]").prop('checked', showinemail);	
		form.find("input[name=fshowinorder]").prop('checked', showinorder);	
				
		form.find("select[name=ftype]").change();
		$( "#jwcfe_edit_field_form_pp" ).dialog( "open" );
		
		form.find("input[name=fnameNew]").prop('disabled', true);	

		if(is_custom == false){
			form.find("select[name=ftype]").prop('disabled', true);
			form.find("input[name=fshowinemail]").prop('disabled', true);
			form.find("input[name=fshowinorder]").prop('disabled', true);
			form.find("input[name=flabel]").focus();
		}else{
			form.find("select[name=ftype]").prop('disabled', false);
			form.find("input[name=fshowinemail]").prop('disabled', false);
			form.find("input[name=fshowinorder]").prop('disabled', false);
		}
	}
	
	function jwcfe_update_row(form){
		var rowId = $(form).find("input[name=rowId]").val();
		
		var name  = $(form).find("input[name=fnameNew]").val();
		var type  = $(form).find("select[name=ftype]").val();
		var label = $(form).find("input[name=flabel]").val();
		var placeholder = $(form).find("input[name=fplaceholder]").val();
		var maxlength = $(form).find("input[name=fmaxlength]").val();
		var optionsList = $(form).find("input[name=foptions]").val();

		var fieldClass = $(form).find("select[name=fclass]").val();
		var labelClass = $(form).find("input[name=flabelclass]").val();
		var access = $(form).find("input[name=faccess]").prop('checked');
		var required = $(form).find("input[name=frequired]").prop('checked');

		var enabled  = $(form).find("input[name=fenabled]").prop('checked');
		
		var showinemail = $(form).find("input[name=fshowinemail]").prop('checked');
		var showinorder = $(form).find("input[name=fshowinorder]").prop('checked');
		
		var validations = $(form).find("select[name=fvalidate]").val();
				
		var err_msgs = '';
		if(name == ''){
			err_msgs = 'Name is required';
		}else if(!isHtmlIdValid(name)){
			err_msgs = MSG_INVALID_NAME;
		}else if(type == ''){
			err_msgs = 'Type is required';
		}
		
		if(err_msgs != ''){
			$(form).find('.err_msgs').html(err_msgs);
			return false;
		}
		
		
		access = access ? 1 : 0;
		
		
		
		required = required ? 1 : 0;
		
		enabled  = enabled ? 1 : 0;
		
		showinemail = showinemail ? 1 : 0;
		showinorder = showinorder ? 1 : 0;
		
		validations = validations ? validations : '';
				
		var row = $('#jwcfe_checkout_fields tbody').find('.row_'+rowId);
		row.find(".f_name").val(name);
		row.find(".f_type").val(type);
		row.find(".f_label").val(label);
		row.find(".f_placeholder").val(placeholder);
		row.find(".f_maxlength").val(maxlength);
		row.find(".f_options").val(optionsList);

		row.find(".f_class").val(fieldClass);
		row.find(".f_label_class").val(labelClass);
		
		
		row.find(".f_required").val(required);
		
		row.find(".f_enabled").val(enabled);
		
		row.find(".f_show_in_email").val(showinemail);
		row.find(".f_show_in_order").val(showinorder);
		row.find(".f_validation").val(validations);	
		
		row.find(".td_name").html(name);
		row.find(".td_type").html(type);
		row.find(".td_label").html(label);
		row.find(".td_placeholder").html(placeholder);
		row.find(".td_validate").html(""+validations+"");
		row.find(".td_required").html(required == 1 ? '<span class="status-enabled tips">Yes</span>' : '-');
		
		row.find(".td_enabled").html(enabled == 1 ? '<span class="status-enabled tips">Yes</span>' : '-');
		return true;
	}
	
	_removeSelectedFields = function removeSelectedFields(){
		$('#jwcfe_checkout_fields tbody tr').removeClass('strikeout');
		$('#jwcfe_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			//$(this).closest('tr').remove();
			var row = $(this).closest('tr');

			if(!row.hasClass("strikeout")){
				row.addClass("strikeout");
				row.fadeOut();
			}
			row.find(".f_deleted").val(1);
			row.find(".f_edit_btn").prop('disabled', true);
			//row.find('.sort').removeClass('sort');
	  	});	
	}
	
	_enableDisableSelectedFields = function enableDisableSelectedFields(enabled){
		$('#jwcfe_checkout_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			var row = $(this).closest('tr');
			if(enabled == 0){
				if(!row.hasClass("jwcfe-disabled")){
					row.addClass("jwcfe-disabled");
				}
			}
			
			else{
				if(!row.hasClass("jwcfe-disabled")){
					alert("Field is already enabled.")
				}
				row.removeClass("jwcfe-disabled");				
			}
			
			row.find(".f_edit_btn").prop('disabled', enabled == 1 ? false : true);
			row.find(".td_enabled").html(enabled == 1 ? '<span class="status-enabled tips">Yes</span>' : '-');
			row.find(".f_enabled").val(enabled);
	  	});	
	}
	
	function jwcfe_clear_form( form ){
		form.find('.err_msgs').html('');
		form.find("input[name=fname]").val('');
		form.find("input[name=fnameNew]").val('');
		form.find("select[name=ftype]").prop('selectedIndex',0);
		form.find("input[name=flabel]").val('');
		form.find("input[name=fplaceholder]").val('');
		form.find("input[name=fmaxlength]").val('');
		form.find("input[name=foptions]").val('');
		
		form.find("input[name=fclass]").val('');
		form.find("input[name=flabelclass]").val('');
		form.find("select[name=fvalidate] option:selected").removeProp('selected');
		
		form.find("input[name=frequired]").prop('checked', true);
		
		form.find("input[name=fenabled]").prop('checked', true);
		form.find("input[name=fshowinemail]").prop('checked', true);
		form.find("input[name=fshowinorder]").prop('checked', true);
	}
	
	function jwcfe_prepare_field_order_indexes() {
		$('#jwcfe_checkout_fields tbody tr').each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index('#jwcfe_checkout_fields tbody tr') ) );
		});
	};
	
	_fieldTypeChangeListner = function fieldTypeChangeListner(elm){

		var type = $(elm).val();
		
		var form = $(elm).closest('form');

		showAllFields(form);
		if(type === 'select'){			
			form.find('.rowPlaceholder').hide();
			form.find('.rowValidate').hide();
			form.find('.rowMaxlength').hide();
			
		}
		
		
		else{			
			form.find('.rowOptions').hide();
		}			
	}
	
	function showAllFields(form){
		form.find('.rowLabel').show();
		form.find('.rowOptions').show();
		form.find('.rowPlaceholder').show();
		form.find('.rowValidate').show();
	}
	
	_selectAllCheckoutFields = function selectAllCheckoutFields(elm){
		var checkAll = $(elm).prop('checked');
		$('#jwcfe_checkout_fields tbody input:checkbox[name=select_field]').prop('checked', checkAll);
	}
	
	function isHtmlIdValid(id) {
		var re = /^[a-zA-Z\_]+[a-z0-9\-_]*$/;
		return re.test(id.trim());
	}
	
	return {
		
		openNewFieldForm : _openNewFieldForm,
		openEditFieldForm : _openEditFieldForm,
		removeSelectedFields : _removeSelectedFields,
		enableDisableSelectedFields : _enableDisableSelectedFields,
		fieldTypeChangeListner : _fieldTypeChangeListner,
		selectAllCheckoutFields : _selectAllCheckoutFields,
   	};
}(window.jQuery, window, document));	



function openNewFieldForm(tabName){
	jwcfe_settings.openNewFieldForm(tabName);		
}

function openEditFieldForm(elm, rowId){
	jwcfe_settings.openEditFieldForm(elm, rowId);		
}
	
function removeSelectedFields(){
	jwcfe_settings.removeSelectedFields();
}

function enableSelectedFields(){
	jwcfe_settings.enableDisableSelectedFields(1);
}

function disableSelectedFields(){
	jwcfe_settings.enableDisableSelectedFields(0);
}

function fieldTypeChangeListner(elm){	
	jwcfe_settings.fieldTypeChangeListner(elm);
}
	
function jwcfeSelectAllCheckoutFields(elm){
	jwcfe_settings.selectAllCheckoutFields(elm);
}