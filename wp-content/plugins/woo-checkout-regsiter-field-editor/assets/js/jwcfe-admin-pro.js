var jwcfe_settings = (function($, window, document) {
	
	var MSG_INVALID_NAME = WcfeAdmin.MSG_INVALID_NAME;
	
	var OPTION_ROW_HTML  = '<tr>';
        OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:180px;"/></td>';
		OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>';
		
		OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="jwcfeAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>';
		OPTION_ROW_HTML += '<td class="action-cell"><a href="javascript:void(0)" onclick="jwcfeRemoveOptionRow(this)" class="btn btn-red"  title="Remove option">x</a></td>';
		OPTION_ROW_HTML += '<td class="action-cell sort ui-sortable-handle"></td>';
		OPTION_ROW_HTML += '</tr>';
		
		

	/*------------------------------------
	*---- ON-LOAD FUNCTIONS - SATRT -----
	*------------------------------------*/
	$(function() {
	
	$('input[name=fname]').bind('input', function(){
		$(this).val(function(_, v){
		  return v.replace(/\s+/g, '');
		});
	});
	
	$( ".jwcfe_tabs" ).tabs();
	
    $( "#jwcfe_new_field_form_pp" ).dialog({
	  	modal: true,
		width: 800,
		//height: 400,
		resizable: false,
		autoOpen: false,
		buttons: [
		{
					text: "Cancel",
					click: function() { $( this ).dialog( "close" ); }	
					
				},
		{
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
		width: 800,
		//height: 400,
		resizable: false,
		autoOpen: false,
		buttons: [
		{
					text: "Cancel",
					click: function() {
					$( this ).dialog( "close" ); }	
				},
		{
			text: "Update Field",
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
		placeholder: "Plese Select",
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
	
	});
	
	
	
	
	_saveCustomFieldForm = function saveCustomFieldForm(loaderPath,donePath){
		var formData = $('#jwcfe_custom_options_form').serializeArray();
	
	var data = {
				formdata: formData,
                action: 'save_custom_form_fields'
				
            };
	
			
			$.ajax({
			dataType : "html",
			type: 'POST',
			url: WcfeAdmin.ajaxurl,
			data: data,
			beforeSend: function() {
			var loaderimg = loaderPath;
			$("body").append("<div class='jwcfe_spinner'><img src='"+loaderimg+"' /></div>");

			},
			success: function(data){
				var loaderimg = donePath;
				$("body .jwcfe_spinner").html("<img src='"+loaderimg+"' />");
				
				setTimeout(function(){
				 $("body .jwcfe_spinner").remove();
				}, 500)
				
				
				
			}
			
		})
	}
	function setup_enhanced_multi_select(form){
		form.find('select.jwcfe-enhanced-multi-select2').each(function(){
			
			
				$(this).select2({
					minimumResultsForSearch: 10,
					allowClear : true,
					placeholder: $(this).data('placeholder')
				}).addClass('enhanced');
				
				/*$.ui.dialog.prototype._allowInteraction = function(e) {
					return !!$(e.target).closest('.ui-dialog, .ui-datepicker, .select2-drop').length;
				};*/
			
		});
		
	}
		
	
	_openNewFieldForm = function openNewFieldForm(tabName){
		if(tabName == 'billing' || tabName == 'shipping' || tabName == 'additional' || tabName == 'account'){
			tabName = tabName+'_';	
		}
		
		var form = $("#jwcfe_new_field_form_pp");
		jwcfe_clear_form(form);
		
		form.find("select[name=ftype]").change();
		form.find("select[name=fclass]").val('form-row-wide');
		
	  	$( "#jwcfe_new_field_form_pp" ).dialog( "open" );
	}
	
	function jwcfe_add_new_row(form){
		var name  = $(form).find("input[name=fname]").val();
		var type  = $(form).find("select[name=ftype]").val();
		var label = $(form).find("input[name=flabel]").val();
		var text = $(form).find("textarea[name=ftext]").val();
		var placeholder = $(form).find("input[name=fplaceholder]").val();
		
		var price = $(form).find("input[name=i_price]").val();
		var price_unit = $(form).find("input[name=i_price_unit]").val();
		var price_type = $(form).find("select[name=i_price_type]").val();
		var taxable = $(form).find("select[name=i_taxable]").val();
		var tax_class = $(form).find("select[name=i_tax_class]").val();
		
		
		var min_time = $(form).find("input[name=i_min_time]").val();
		var max_time = $(form).find("input[name=i_max_time]").val();
		
		var time_step = $(form).find("input[name=i_time_step]").val();
		var time_format = $(form).find("select[name=i_time_format]").val();
		
		
		var maxlength = $(form).find("input[name=fmaxlength]").val();
		
		var options_json = get_options(form);
		
		var frules_action  = $(form).find("select[name=i_rules_action]").val();
		
		var frules = $(form).find("input[name=f_rules]").val();
		
		var frules_action_ajax  = $(form).find("select[name=i_rules_action_ajax]").val();
		
		var rules_json = get_conditional_rules(form, false);
		
		var frules_ajax = $(form).find("input[name=f_rules_ajax]").val();
		
		var rules_ajax_json = get_conditional_rules(form, true);
		
		
		
		

		var fieldClass = $(form).find("select[name=fclass]").val();
	
		
		var labelClass = $(form).find("input[name=flabelclass]").val();
		
		var access = $(form).find("input[name=faccess]").prop('checked');
		var required = $(form).find("input[name=frequired]").is(':checked');
		
		
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
		
		text = text.replace(/"/g, "\\'");

		var index = $('#jwcfe_checkout_fields tbody tr').size();
	
		var newRow = '<tr class="row_'+index+'">';
		newRow += '<td width="1%" class="sort ui-sortable-handle">';
		newRow += '<input type="hidden" name="f_order['+index+']" class="f_order" value="'+index+'" />';
		newRow += '<input type="hidden" name="f_custom['+index+']" class="f_custom" value="1" />';
		newRow += '<input type="hidden" name="f_name['+index+']" class="f_name" value="'+name+'" />';
		newRow += '<input type="hidden" name="f_name_new['+index+']" class="f_name_new" value="'+name+'" />';
		newRow += '<input type="hidden" name="f_type['+index+']" class="f_type" value="'+type+'" />';
		newRow += '<input type="hidden" name="f_label['+index+']" class="f_label" value="'+label+'" />';
		newRow += '<input type="hidden" name="f_text['+index+']" class="f_text" value="'+text+'" />';		
		newRow += '<input type="hidden" name="f_placeholder['+index+']" class="f_placeholder" value="'+placeholder+'" />';		
		newRow += '<input type="hidden" name="f_maxlength['+index+']" class="f_maxlength" value="'+maxlength+'" />';		
		newRow += '<input type="hidden" name="f_options['+index+']" class="f_options" value="'+options_json+'" />';
		newRow += '<input type="hidden" name="f_rules_action['+index+']" class="f_rules_action" value="'+frules_action+'" />';
		newRow += '<input type="hidden" name="f_rules['+index+']" class="f_rules" value="'+rules_json+'" />';
		newRow += '<input type="hidden" name="f_rules_action_ajax['+index+']" class="f_rules_action_ajax" value="'+frules_action_ajax+'" />';
		newRow += '<input type="hidden" name="f_rules_ajax['+index+']" class="f_rules_ajax" value="'+rules_ajax_json+'" />';
		
		newRow += '<input type="hidden" name="f_class['+index+']" class="f_class" value="'+fieldClass+'" />';
		newRow += '<input type="hidden" name="f_label_class['+index+']" class="f_label_class" value="'+labelClass+'" />';
		
		newRow += '<input type="hidden" name="f_access['+index+']" class="f_access" value="'+access+'" />';
		
		newRow += '<input type="hidden" name="f_required['+index+']" class="f_required" value="'+required+'" />';
		
		newRow += '<input type="hidden" name="f_enabled['+index+']" class="f_enabled" value="'+enabled+'" />';
		
		newRow += '<input type="hidden" name="f_show_in_email['+index+']" class="f_show_in_email" value="'+showinemail+'" />';
		newRow += '<input type="hidden" name="f_show_in_order['+index+']" class="f_show_in_order" value="'+showinorder+'" />';
		
		newRow += '<input type="hidden" name="i_price['+index+']" class="f_price" value="'+price+'" />';
		newRow += '<input type="hidden" name="i_price_unit['+index+']" class="f_price_unit" value="'+price_unit+'" />';
		newRow += '<input type="hidden" name="i_price_type['+index+']" class="f_price_type" value="'+price_type+'" />';
		newRow += '<input type="hidden" name="i_taxable['+index+']" class="f_taxable" value="'+taxable+'" />';
		newRow += '<input type="hidden" name="i_tax_class['+index+']" class="f_tax_class" value="'+tax_class+'" />';
				
		newRow += '<input type="hidden" name="i_min_time['+index+']" class="i_min_time" value="'+min_time+'" />';
		newRow += '<input type="hidden" name="i_max_time['+index+']" class="i_max_time" value="'+max_time+'" />';
		
		newRow += '<input type="hidden" name="i_time_step['+index+']" class="i_time_step" value="'+time_step+'" />';
		newRow += '<input type="hidden" name="i_time_format['+index+']" class="i_time_format" value="'+time_format+'" />';
	
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
		
		newRow += '<td><button type="button" onclick="openEditFieldForm(this,'+index+')">Edit</button></td>';
		newRow += '</tr>';
		
		
		$('#jwcfe_checkout_fields tbody tr:last').after(newRow);
		return true;
	}
	
	 /*----------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS - SATRT -----
	*----------------------------------------------*/
	var RULE_OPERATOR_SET = {
		"cart_contains" : "Cart contains", "cart_not_contains" : "Cart not contains", "cart_only_contains" : "Cart only contains",
		"cart_subtotal_eq" : "Cart subtotal equals to", "cart_subtotal_gt" : "Cart subtotal greater than", "cart_subtotal_lt" : "Cart subtotal less than",
		"cart_total_eq" : "Cart total equals to", "cart_total_gt" : "Cart total greater than", "cart_total_lt" : "Cart total less than",
		"user_role_eq" : "User role equals to",
		//"count_eq" : "Product count equals to", "count_gt" : "Product count greater than", "count_lt" : "Product count less than",
	};
	var RULE_OPERATOR_SET_NO_TYPE = ["cart_subtotal_eq", "cart_subtotal_gt", "cart_subtotal_lt", "cart_total_eq", "cart_total_gt", "cart_total_lt", 
									 "user_role_eq", "user_role_ne"];
	
	var RULE_OPERAND_TYPE_SET = {"product" : "Product", "category" : "Category"};
	
	var OP_AND_HTML  = '<label class="thpl_logic_label">AND</label>';
		OP_AND_HTML += '<a href="javascript:void(0)" onclick="jwcfeRemoveRuleRow(this)" class="thpl_logic_link" title="Remove">X</a>';
	var OP_OR_HTML   = '<tr class="thpl_logic_label_or"><td colspan="4" align="center">OR</td></tr>';
	
	var OP_HTML = '<a href="javascript:void(0)" class="thpl_logic_link" onclick="jwcfeAddNewConditionRow(this, 2)" title="">+</a>';
		OP_HTML += '<a href="javascript:void(0)" onclick="jwcfeRemoveRuleRow(this)" class="thpl_logic_link" title="Remove">X</a>';
	
	var CONDITION_HTML = '', CONDITION_SET_HTML = '', CONDITION_SET_HTML_WITH_OR = '', RULE_HTML = '', RULE_SET_HTML = '';
	
	$(function() {
		
	    CONDITION_HTML  = '<tr class="jwcfe_condition">';
		CONDITION_HTML += '<td width="25%">'+ prepareRuleOperatorSet('') +'</td>';
		CONDITION_HTML += '<td width="25%">'+ prepareRuleOperandTypeSet('') +'</td>';	
		CONDITION_HTML += '<td width="25%" class="thpladmin_rule_operand"><input type="text" name="i_rule_operand" style="width:200px;"/></td>';
		CONDITION_HTML += '<td class="actions">'+ OP_HTML +'</td></tr>';
		
	    CONDITION_SET_HTML  = '<tr class="jwcfe_condition_set_row"><td>';
		CONDITION_SET_HTML += '<table class="jwcfe_condition_set" width="100%" style=""><tbody>'+CONDITION_HTML+'</tbody></table>';
		CONDITION_SET_HTML += '</td></tr>';
		
	    CONDITION_SET_HTML_WITH_OR  = '<tr class="jwcfe_condition_set_row"><td>';
		CONDITION_SET_HTML_WITH_OR += '<table class="jwcfe_condition_set" width="100%" style=""><thead>'+OP_OR_HTML+'</thead><tbody>'+CONDITION_HTML+'</tbody></table>';
		CONDITION_SET_HTML_WITH_OR += '</td></tr>';
	
	    RULE_HTML  = '<tr class="jwcfe_rule_row"><td>';
		RULE_HTML += '<table class="jwcfe_rule" width="100%" style=""><tbody>'+CONDITION_SET_HTML+'</tbody></table>';
		RULE_HTML += '</td></tr>';	
		
	    RULE_SET_HTML  = '<tr class="jwcfe_rule_set_row"><td>';
		RULE_SET_HTML += '<table class="jwcfe_rule_set" width="100%"><tbody>'+RULE_HTML+'</tbody></table>';
		RULE_SET_HTML += '</td></tr>';
	});
	
	function prepareRuleOperatorSet(value){
		var html = '<select name="i_rule_operator" style="width:200px;" value="'+ value +'" onchange="jwcfeRuleOperatorChangeListner(this)">';
		html += '<option value=""></option>';
		for(var index in RULE_OPERATOR_SET) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERATOR_SET[index]+'</option>';
		}
		html += '</select>';
		return html;
	}
	
	function prepareRuleOperandTypeSet(value){
		var html = '<select name="i_rule_operand_type" style="width:200px;" onchange="jwcfeRuleOperandTypeChangeListner(this)" value="'+ value +'">';
		html += '<option value=""></option>';
		for(var index in RULE_OPERAND_TYPE_SET) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERAND_TYPE_SET[index]+'</option>';
		}
		html += '</select>';
		return html;
	}
	
	function prepareRuleOperandSet(operand_type, operand, operator){
		var html = '<input type="hidden" name="i_rule_operand_hidden" value="'+operand+'"/>';
		if(operand_type === "product"){
			html += $("#jwcfe_product_select").html();
			
		}else if(operand_type === "category"){
			html += $("#jwcfe_product_cat_select").html();
			
		}else if(operator === "user_role_eq" || operator === "user_role_ne"){
			html += $("#jwcfe_user_role_select").html();
			
		}else{
			html += '<input type="text" name="i_rule_operand" style="width:200px;" value="'+operand+'"/>';
		}
		return html;
	}
	
	function is_condition_with_no_operand_type(operator){
		if(operator && $.inArray(operator, RULE_OPERATOR_SET_NO_TYPE) > -1){
			return true;
		}
		return false;
	}
	
	function is_valid_condition(condition){
		if(condition["operand_type"] && condition["operator"]){
			return true;
		}else if(is_condition_with_no_operand_type(condition["operator"]) && condition["operand"]){
			return true;
		}
		return false;
	}
	
	this.ruleOperandTypeChangeListner = function(elm){
		var operand_type = $(elm).val();
		
		var condition_row = $(elm).closest("tr.jwcfe_condition");
		var target = condition_row.find("td.thpladmin_rule_operand");
		var value = condition_row.find("input[name='i_rule_operand_hidden']").val();
		
		if(operand_type === 'product'){
			target.html( $("#jwcfe_product_select").html() );
			setup_enhanced_multi_select(condition_row);
			
		}else if(operand_type === 'category'){
			target.html( $("#jwcfe_product_cat_select").html() );
			setup_enhanced_multi_select(condition_row);
			
		}else{
			value = value ? value : '';
			target.html( '<input type="text" name="i_rule_operand" style="width:200px;" value="'+value+'"/>' );
		}	
	}
	
	/*this.ruleOperandTypeChangeListner = function(elm){
		e.data.prepareRequest( "GET", $(elm).val(), "" );
		e.data.dock( $(this).val(), $(this) );		
	}*/
	
	_add_new_rule_row = function add_new_rule_row(elm, op){
		var condition_row = $(elm).closest('tr');
		condition = {};
		condition["operand_type"] = condition_row.find("select[name=i_rule_operand_type]").val();
		condition["operator"] = condition_row.find("select[name=i_rule_operator]").val();
		condition["operand"] = condition_row.find("select[name=i_rule_operand]").val();
		
		if(is_condition_with_no_operand_type(condition["operator"])){
			condition["operand_type"] = '';
			//condition["operand"] = condition_row.find("input[name=i_rule_operand]").val();
			if(condition["operator"] != "user_role_eq" && condition["operator"] != "user_role_ne"){
				condition["operand"] = condition_row.find("input[name=i_rule_operand]").val();
			}
		}
		
		if(!is_valid_condition(condition)){
			alert('Please provide a valid condition.');
			return;
		}
		
		if(op == 1){
			var conditionSetTable = $(elm).closest('.jwcfe_condition_set');
			var conditionSetSize  = conditionSetTable.find('tbody tr.jwcfe_condition').size();
			
			if(conditionSetSize > 0){
				$(elm).closest('td').html(OP_AND_HTML);
				conditionSetTable.find('tbody tr.jwcfe_condition:last').after(CONDITION_HTML);
			}else{
				conditionSetTable.find('tbody').append(CONDITION_HTML);
			}
		}else if(op == 2){
			var ruleTable = $(elm).closest('.jwcfe_rule');
			var ruleSize  = ruleTable.find('tbody tr.jwcfe_condition_set_row').size();
			
			if(ruleSize > 0){
				ruleTable.find('tbody tr.jwcfe_condition_set_row:last').after(CONDITION_SET_HTML_WITH_OR);
			}else{
				ruleTable.find('tbody').append(CONDITION_SET_HTML);
			}
		}	
	}
	
	_remove_rule_row = function remove_rule_row(elm){
		var ctable = $(elm).closest('table.jwcfe_condition_set');
		var rtable = $(elm).closest('table.jwcfe_rule');
		
		$(elm).closest('tr.jwcfe_condition').remove();
		
		var cSize = ctable.find('tbody tr.jwcfe_condition').size();
		if(cSize == 0){
			ctable.closest('tr.jwcfe_condition_set_row').remove();
		}else{
			ctable.find('tbody tr.jwcfe_condition:last').find('td.actions').html(OP_HTML);	
		}
		
		rSize = rtable.find('tbody tr.jwcfe_condition_set_row').size();
		if(cSize == 0 && rSize == 0){
			rtable.find('tbody').append(CONDITION_SET_HTML);
		}
	}
	
	function get_conditional_rules(elm, ajaxFlag){
		var rulesTable;
		
		if(ajaxFlag){
			rulesTable = $(elm).find(".jwcfe_conditional_rules_ajax tbody");
		}else{
			rulesTable = $(elm).find(".jwcfe_conditional_rules tbody");	
		}
		
		var conditionalRules = [];
		rulesTable.find("tr.jwcfe_rule_set_row").each(function() {
			var ruleSet = [];
			$(this).find("table.jwcfe_rule_set tbody tr.jwcfe_rule_row").each(function() {
				var rule = [];															 
				$(this).find("table.jwcfe_rule tbody tr.jwcfe_condition_set_row").each(function() {
					var conditions = [];
					$(this).find("table.jwcfe_condition_set tbody tr.jwcfe_condition").each(function() {
						condition = {};
						if(ajaxFlag){
							condition["operand_type"] = $(this).find("input[name=i_rule_operand_type]").val();
							condition["value"] = $(this).find("input[name=i_rule_value]").val();
						}else{
							condition["operand_type"] = $(this).find("select[name=i_rule_operand_type]").val();	
						}
						condition["operator"] = $(this).find("select[name=i_rule_operator]").val();
						
						condition["operand"] = $(this).find("select[name=i_rule_operand]").val();
						
						
						if(is_condition_with_no_operand_type(condition["operator"])){
							condition["operand_type"] = '';
							
							if(condition["operator"] != "user_role_eq" && condition["operator"] != "user_role_ne"){
								condition["operand"] = $(this).find("input[name=i_rule_operand]").val();
							}
						}
						
						if(is_valid_condition(condition)){
							conditions.push(condition);
						}
					});
					if(conditions.length > 0){
						rule.push(conditions);
					}
				});
				if(rule.length > 0){
					ruleSet.push(rule);
				}
			});
			if(ruleSet.length > 0){
				conditionalRules.push(ruleSet);
			}
		});
		
		var conditionalRulesJson = conditionalRules.length > 0 ? JSON.stringify(conditionalRules) : '';
		conditionalRulesJson = encodeURIComponent(conditionalRulesJson);
		//conditionalRulesJson = conditionalRulesJson.replace(/"/g, "'");
		
		return conditionalRulesJson;
	}
		
	function populate_conditional_rules(form, conditionalRulesJson, ajaxFlag){
		var conditionalRulesHtml = "";
		if(conditionalRulesJson){
			try{
				conditionalRulesJson = decodeURIComponent(conditionalRulesJson);
				var conditionalRules = $.parseJSON(conditionalRulesJson);
				if(conditionalRules){
					jQuery.each(conditionalRules, function() {
						var ruleSet = this;	
						var rulesHtml = '';
						
						jQuery.each(ruleSet, function() {
							var rule = this;
							var conditionSetsHtml = '';
							
							var y=0;
							var ruleSize = rule.length;
							jQuery.each(rule, function() {
								var conditions = this;								   	
								var conditionsHtml = '';
								
								var x=1;
								var size = conditions.length;
								jQuery.each(conditions, function() {
									var lastRow = (x==size) ? true : false;
									var conditionHtml = populate_condition_html(this, lastRow, ajaxFlag);
									if(conditionHtml){
										conditionsHtml += conditionHtml;
									}
									x++;
								});
								
								var firstRule = (y==0) ? true : false;
								var conditionSetHtml = populate_condition_set_html(conditionsHtml, firstRule);
								if(conditionSetHtml){
									conditionSetsHtml += conditionSetHtml;
								}
								y++;
							});
							
							var ruleHtml = populate_rule_html(conditionSetsHtml);
							if(ruleHtml){
								rulesHtml += ruleHtml;
							}
						});
						
						var ruleSetHtml = populate_rule_set_html(rulesHtml);
						if(ruleSetHtml){
							conditionalRulesHtml += ruleSetHtml;
						}
					});
				}
			}catch(err) {
				alert(err);
			}
		}
		
		var conditionalRulesTable;
		if(ajaxFlag){
			conditionalRulesTable = form.find(".jwcfe_conditional_rules_ajax tbody");
		}else{
			conditionalRulesTable = form.find(".jwcfe_conditional_rules tbody");
		}
		
		if(conditionalRulesHtml){
			conditionalRulesTable.html(conditionalRulesHtml);
			setup_enhanced_multi_select(conditionalRulesTable);
			
			conditionalRulesTable.find('tr.jwcfe_condition').each(function(){
				var operantVal = $(this).find("input[name=i_rule_operand_hidden]").val();	
				operantVal = operantVal.split(",");
				
				$(this).find('select[name=i_rule_operand]').val(operantVal).trigger("change");
				
			});
			
			
			conditionalRulesTable.find("select[name=i_rule_operator]").each(function(){
				ruleOperatorChangeAction($(this), true);	
			});
			
		}else{
			if(ajaxFlag){
				conditionalRulesTable.html(RULE_SET_HTML_AJAX);
			}else{
				conditionalRulesTable.html(RULE_SET_HTML);
			}
			setup_enhanced_multi_select(conditionalRulesTable);
		}
	}
	
	function populate_rule_set_html(ruleHtml){
		var html = '';
		if(ruleHtml){
			html += '<tr class="jwcfe_rule_set_row"><td><table class="jwcfe_rule_set" width="100%"><tbody>';
			html += ruleHtml;
			html += '</tbody></table></td></tr>';
		}
		return html;
	}
	
	function populate_rule_html(conditionSetHtml){
		var html = '';
		if(conditionSetHtml){
			html += '<tr class="jwcfe_rule_row"><td><table class="jwcfe_rule" width="100%" style=""><tbody>';
			html += conditionSetHtml;
			html += '</tbody></table></td></tr>';
		}
		return html;
	}
	
	function populate_condition_set_html(conditionsHtml, firstRule){
		var html = '';
		if(conditionsHtml){
			if(firstRule){
				html += '<tr class="jwcfe_condition_set_row"><td><table class="jwcfe_condition_set" width="100%" style=""><tbody>';
				html += conditionsHtml;
				html += '</tbody></table></td></tr>';
			}else{
				html += '<tr class="jwcfe_condition_set_row"><td><table class="jwcfe_condition_set" width="100%" style=""><thead>'+OP_OR_HTML+'</thead><tbody>';
				html += conditionsHtml;
				html += '</tbody></table></td></tr>';
			}
		}
		return html;
	}
	
	function populate_condition_html(condition, lastRow, ajaxFlag){
		var html = '';
		if(condition){
			if(ajaxFlag){
				
				var actionsHtml = lastRow ? OP_HTML_AJAX : OP_AND_HTML_AJAX;
				
				html += '<tr class="jwcfe_condition">';
				html += '<td width="25%">'+ prepareRuleOperandSetAjax(condition.operand) +'</td>';
				html += '<td width="25%">'+ prepareRuleOperatorSetAjax(condition.operator) +'</td>';
				html += '<td width="25%">'+ prepareRuleValueSetAjax(condition.value) +'</td>';
				html += '<td class="actions">'+ actionsHtml +'</td></tr>';
		
				
		}else{
				var actionsHtml = lastRow ? OP_HTML : OP_AND_HTML;
				
				var opType = condition.operand_type ? condition.operand_type : '';
				var operand = condition.operand ? condition.operand : '';
				var operator = condition.operator ? condition.operator : '';
			
				html += '<tr class="jwcfe_condition">';
				html += '<td width="25%">'+ prepareRuleOperatorSet(operator) +'</td>';
				html += '<td width="25%">'+ prepareRuleOperandTypeSet(opType) +'</td>';
				html += '<td width="25%" class="thpladmin_rule_operand">'+ prepareRuleOperandSet(opType, operand, operator) +'</td>';
				html += '<td class="actions">'+ actionsHtml +'</td></tr>';	
			}
		}
		return html;
	}	
	
	function ruleOperatorChangeAction(elm, ignoreUserRole){
		var operator = $(elm).val();
		var condition_row = $(elm).closest("tr.jwcfe_condition");
		var operandType = condition_row.find("select[name=i_rule_operand_type]");
		var ruleValuElm = condition_row.find("input[name=i_rule_value]");
		
		if(operator === 'user_role_eq' || operator === 'user_role_ne'){
			if(ignoreUserRole){
				operandType.val('');
				operandType.prop("disabled", true);
			}else{
				operandType.val('');
				operandType.change();
				operandType.prop("disabled", true);
				
				var target = condition_row.find("td.thpladmin_rule_operand");
				target.html( $("#jwcfe_user_role_select").html() );
				setup_enhanced_multi_select(condition_row);
			}
		}else if(is_condition_with_no_operand_type(operator)){
			operandType.val('');
			operandType.change();
			operandType.prop("disabled", true);
		}else{
			operandType.prop("disabled", false);
		}	
	}
	
	this.ruleOperatorChangeListner = function(elm){
		ruleOperatorChangeAction(elm, false);
	}
		
   /*----------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS - SATRT -----
	*----------------------------------------------*/
	
   /*---------------------------------------------------
	*---- AJAX CONDITIONAL RULES FUNCTIONS - SATRT -----
	*---------------------------------------------------*/
	var RULE_OPERATOR_SET_AJAX = {
		"empty" : "Is empty", "not_empty" : "Is not empty",
		"value_eq" : "Value equals to", "value_ne" : "Value not equals to", "value_gt" : "Value greater than", "value_le" : "Value less than",
		"date_eq" : "Date equals to", "date_ne" : "Date not equals to", "date_gt" : "Date after", "date_lt" : "Date before", 
		"day_eq" : "Day equals to", "day_ne" : "Day not equals to",
		"checked" : "Is checked", "not_checked" : "Is not checked"
	};
	
	var OP_AND_HTML_AJAX  = '<label class="thpl_logic_label">AND</label>';
		OP_AND_HTML_AJAX += '<a href="javascript:void(0)" onclick="jwcfeRemoveRuleRowAjax(this)" class="thpl_logic_link" title="Remove">X</a>';
	
	var OP_HTML_AJAX = '<a href="javascript:void(0)" class="thpl_logic_link" onclick="jwcfeAddNewConditionRowAjax(this, 2)" title="">+</a>';
		OP_HTML_AJAX += '<a href="javascript:void(0)" onclick="jwcfeRemoveRuleRowAjax(this)" class="thpl_logic_link" title="Remove">X</a>';
	
	var CONDITION_HTML_AJAX = '', CONDITION_SET_HTML_AJAX = '', CONDITION_SET_HTML_WITH_OR_AJAX = '', RULE_HTML_AJAX = '', RULE_SET_HTML_AJAX = '';
	
	$(function() {
		
		
		
		CONDITION_HTML_AJAX  = '<tr class="jwcfe_condition">';
		CONDITION_HTML_AJAX += '<td width="25%">'+ prepareRuleOperandSetAjax('') +'</td>';
		CONDITION_HTML_AJAX += '<td width="25%">'+ prepareRuleOperatorSetAjax('') +'</td>';	
		CONDITION_HTML_AJAX += '<td width="25%"><input type="text" name="i_rule_value" style="width:200px;"/></td>';
		CONDITION_HTML_AJAX += '<td class="actions">'+ OP_HTML_AJAX +'</td></tr>';
	
		CONDITION_SET_HTML_AJAX  = '<tr class="jwcfe_condition_set_row"><td>';
		CONDITION_SET_HTML_AJAX += '<table class="jwcfe_condition_set" width="100%" style=""><tbody>'+CONDITION_HTML_AJAX+'</tbody></table>';
		CONDITION_SET_HTML_AJAX += '</td></tr>';
			
		CONDITION_SET_HTML_WITH_OR_AJAX  = '<tr class="jwcfe_condition_set_row"><td><table class="jwcfe_condition_set" width="100%" style="">';
		CONDITION_SET_HTML_WITH_OR_AJAX += '<thead>'+OP_OR_HTML+'</thead><tbody>'+CONDITION_HTML_AJAX+'</tbody>';
		CONDITION_SET_HTML_WITH_OR_AJAX += '</table></td></tr>';
		
		RULE_HTML_AJAX  = '<tr class="jwcfe_rule_row"><td>';
		RULE_HTML_AJAX += '<table class="jwcfe_rule" width="100%" style=""><tbody>'+CONDITION_SET_HTML_AJAX+'</tbody></table>';
		RULE_HTML_AJAX += '</td></tr>';	
		
		RULE_SET_HTML_AJAX  = '<tr class="jwcfe_rule_set_row"><td>';
		RULE_SET_HTML_AJAX += '<table class="jwcfe_rule_set" width="100%"><tbody>'+RULE_HTML_AJAX+'</tbody></table>';
		RULE_SET_HTML_AJAX += '</td></tr>';
	});
			   
	function prepareRuleOperatorSetAjax(value){
		var html = '<select name="i_rule_operator" style="width:200px;" value="'+ value +'" onchange="jwcfeRuleOperatorChangeListnerAjax(this)" >';
		html += '<option value=""></option>';
		for(var index in RULE_OPERATOR_SET_AJAX) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERATOR_SET_AJAX[index]+'</option>';
		}
		html += '</select>';
		return html;
	}

	
	function prepareRuleOperandSetAjax(value){

		var html = '<input type="hidden" name="i_rule_operand_type" value="field"/>';
		html += '<input type="hidden" name="i_rule_operand_hidden" value="'+value+'"/>';
		html += $("#jwcfe_checkout_fields_select").html();
		return html;
	}
	
	function prepareRuleValueSetAjax(value){
		
		if(value){
			var html = '<input type="text" name="i_rule_value" style="width:200px;" value="'+value+'" />';
		} else{
			var html = '<input type="text" name="i_rule_value" style="width:200px;" readonly />';
		}
		
		return html;
	}
	
	function isValidConditionAjax(condition){
		if(condition["operand_type"] && condition["operator"]){
			return true;
		}
		return false;
	}
	
	_add_new_rule_row_ajax = function addNewRuleRowAjax(elm, op){
		var condition_row = $(elm).closest('tr');
		
		condition = {};
		condition["operand_type"] = condition_row.find("input[name=i_rule_operand_type]").val();
		condition["operator"] = condition_row.find("select[name=i_rule_operator]").val();
		condition["operand"] = condition_row.find("select[name=i_rule_operand]").val();
		condition["value"] = condition_row.find("input[name=i_rule_value]").val();
		
		if(!isValidConditionAjax(condition)){
			alert('Please provide a valid condition.');
			return;
		}
		
		if(op == 1){
			var conditionSetTable = $(elm).closest('.jwcfe_condition_set');
			var conditionSetSize  = conditionSetTable.find('tbody tr.jwcfe_condition').size();
			
			if(conditionSetSize > 0){
				$(elm).closest('td').html(OP_AND_HTML_AJAX);
				conditionSetTable.find('tbody tr.jwcfe_condition:last').after(CONDITION_HTML_AJAX);
			}else{
				conditionSetTable.find('tbody').append(CONDITION_HTML_AJAX);
			}
			
			setup_enhanced_multi_select(conditionSetTable);
			
		}else if(op == 2){
			var ruleTable = $(elm).closest('.jwcfe_rule');
			var ruleSize  = ruleTable.find('tbody tr.jwcfe_condition_set_row').size();
			
			if(ruleSize > 0){
				ruleTable.find('tbody tr.jwcfe_condition_set_row:last').after(CONDITION_SET_HTML_WITH_OR_AJAX);
			}else{
				ruleTable.find('tbody').append(CONDITION_SET_HTML_AJAX);
			}
			
			setup_enhanced_multi_select(ruleTable);
		}
	}
	
	_remove_rule_row_ajax = function removeRuleRowAjax(elm){
		var ctable = $(elm).closest('table.jwcfe_condition_set');
		var rtable = $(elm).closest('table.jwcfe_rule');
		
		$(elm).closest('tr.jwcfe_condition').remove();
		
		var cSize = ctable.find('tbody tr.jwcfe_condition').size();
		if(cSize == 0){
			ctable.closest('tr.jwcfe_condition_set_row').remove();
		}else{
			ctable.find('tbody tr.jwcfe_condition:last').find('td.actions').html(OP_HTML_AJAX);	
		}
		
		rSize = rtable.find('tbody tr.jwcfe_condition_set_row').size();
		if(cSize == 0 && rSize == 0){
			rtable.find('tbody').append(CONDITION_SET_HTML_AJAX);
		}
		
		setup_enhanced_multi_select(rtable);
	}
		
	this.ruleOperatorChangeListnerAjax = function(elm){
		var operator = $(elm).val();
		var condition_row = $(elm).closest("tr.jwcfe_condition");
		var ruleValuElm = condition_row.find("input[name=i_rule_value]");
		
		if(operator === 'empty' || operator === 'not_empty' || operator === 'checked' || operator === 'not_checked'){
			ruleValuElm.val('');
			ruleValuElm.prop("readonly", true);
		}else{
			ruleValuElm.prop("readonly", false);
		}	
	}
   /*---------------------------------------------------
	*---- AJAX CONDITIONAL RULES FUNCTIONS - SATRT -----
	*---------------------------------------------------*/
				
	_openEditFieldForm = function openEditFieldForm(elm, rowId){
		var row = $(elm).closest('tr');
		
		
		var is_custom = row.find(".f_custom").val();

		var name  = row.find(".f_name").val();
		var type  = row.find(".f_type").val();
		var label = row.find(".f_label").val();
		var text = row.find(".f_text").val();
			
		var placeholder = row.find(".f_placeholder").val();
		
		var price = row.find(".f_price").val();
		var price_unit = row.find(".f_price_unit").val();
		var price_type = row.find(".f_price_type").val();
		var taxable = row.find(".f_taxable").val();
		var tax_class = row.find(".f_tax_class").val();
		
		var min_time = row.find(".i_min_time").val();
		var max_time = row.find(".i_max_time").val();
		
		var time_step = row.find(".i_time_step").val();
		var time_format = row.find(".i_time_format").val();
		
		
		var maxlength = row.find(".f_maxlength").val();
		var optionsList = row.find(".f_options").val();
		
		var field_classes = row.find(".f_class").val();
		
		var label_classes = row.find(".f_label_class").val();
		
		var access = row.find(".f_access").val();
		
		var required = row.find(".f_required").val();
		
		
		var enabled = row.find(".f_enabled").val();
		var validations = row.find(".f_validation").val();	
		
		var showinemail = row.find(".f_show_in_email").val();
		var showinorder = row.find(".f_show_in_order").val();
		text = text.replace(/"/g, "\\'");
		is_custom = is_custom == 1 ? true : false;
		
		access = access == 1 ? true : false;
		
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
		form.find("textarea[name=ftext]").val(text);
		
		form.find("input[name=fplaceholder]").val(placeholder);
		form.find("input[name=fmaxlength]").val(maxlength);
		
		
		var optionsJson = row.find(".f_options").val();
		populate_options_list(form, optionsJson);
		
		
		
		
	
		
		form.find("select[name=fclass]").val(field_classes);

		form.find("input[name=flabelclass]").val(label_classes);
		form.find("select[name=fvalidate]").val(validations).trigger("change");
		
		form.find("input[name=faccess]").prop('checked', access);
		
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
		var text = $(form).find("textarea[name=ftext]").val();
		var placeholder = $(form).find("input[name=fplaceholder]").val();
		var price = $(form).find("input[name=i_price]").val();
		var price_unit = $(form).find("input[name=i_price_unit]").val();
		var price_type = $(form).find("select[name=i_price_type]").val();
		var taxable = $(form).find("select[name=i_taxable]").val();
		var tax_class = $(form).find("input[name=i_tax_class]").val();
		
		var min_time = $(form).find("input[name=i_min_time]").val();
		var max_time = $(form).find("input[name=i_max_time]").val();
		
		var time_step = $(form).find("input[name=i_time_step]").val();
		var time_format = $(form).find("select[name=i_time_format]").val();
		
	
		var frules_action = $(form).find("select[name=i_rules_action]").val();
		var frules_action_ajax = $(form).find("select[name=i_rules_action_ajax]").val();
		

		var extoptionsList = $(form).find("select[name=fextoptions]").val();
		var fieldClass = $(form).find("select[name=fclass]").val();
		var labelClass = $(form).find("input[name=flabelclass]").val();
		
		var access = $(form).find("input[name=faccess]").prop('checked');
		var maxlength = $(form).find("input[name=fmaxlength]").val();
		var enabled  = $(form).find("input[name=fenabled]").prop('checked');
		
		var required = $(form).find("input[name=frequired]").prop('checked');
		
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
		extoptionsList = extoptionsList ? extoptionsList : '';
		var row = $('#jwcfe_checkout_fields tbody').find('.row_'+rowId);
		row.find(".f_name").val(name);
		row.find(".f_type").val(type);
		row.find(".f_label").val(label);
		row.find(".f_text").val(text);
		row.find(".f_placeholder").val(placeholder);
		
		row.find(".f_price").val(price);
		row.find(".f_price_unit").val(price_unit);
		row.find(".f_price_type").val(price_type);
		row.find(".f_taxable").val(taxable);
		row.find(".f_tax_class").val(tax_class);
		
		row.find(".i_min_time").val(min_time);
		row.find(".i_max_time").val(max_time);
		
		row.find(".i_time_step").val(time_step);
		row.find(".i_time_format").val(time_format);
		
		row.find(".f_maxlength").val(maxlength);
		
		row.find(".f_rules_action").val(frules_action);
		row.find(".f_rules_action_ajax").val(frules_action_ajax);
		
		var rules_json = get_conditional_rules(form, false);
		var rules_ajax_json = get_conditional_rules(form, true);
		
		var options_json = get_options(form);

		row.find(".f_options").val(options_json);
	   
		row.find(".f_rules").val(rules_json);
		row.find(".f_rules_ajax").val(rules_ajax_json);
		row.find(".f_extoptions").val(extoptionsList);
	
		
		row.find(".f_class").val(fieldClass);
		row.find(".f_label_class").val(labelClass);
		
		row.find(".f_access").val(access)
		
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
	
	 /*----------------------------------------
	*---- PRICE FIELD FUNCTIONS - START -----
	*----------------------------------------*/
	priceTypeChangeListener = function priceTypeChangeListener(elm){
		var row = $(elm).closest('tr');
		var priceType = $(elm).val();
		
		if(priceType === 'dynamic'){
			row.find("input[name=i_price]").prop('disabled', false);
			row.find("input[name=i_price]").css('width','100px');
			row.find('.thpl-dynamic-price-field').show();	
		}else{
			if(priceType === 'custom'){
				row.find("input[name=i_price]").val('');
				row.find("input[name=i_price_unit]").val('');
				row.find("input[name=i_price]").prop('disabled', true);
			}else{
				row.find("input[name=i_price]").prop('disabled', false);
			}
			
			row.find("input[name=i_price]").css('width','250px');
			row.find('.thpl-dynamic-price-field').hide();	
		}
	}
	
	show_price_fields = function show_price_fields(elm){
		var show = $(elm).prop('checked');
		if(show){
			$('tr.thwepo_price_row').show();
		}else{
			$('tr.thwepo_price_row').hide();
		}
	}
   /*--------------------------------------
	*---- PRICE FIELD FUNCTIONS - END -----
	*--------------------------------------*/
	
	
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
		form.find("input[name=foptions]").val('');
		form.find("input[name=fextoptions]").val('');
		form.find("select[name=fextoptions] option:selected").removeProp('selected');
		form.find("input[name=frules_action]").val('');
		form.find("input[name=frules]").val('');
		form.find("input[name=frules_action_ajax]").val('');
		form.find("input[name=frules_ajax]").val('');
		
		
		form.find("select[name=fclass]").val('');
		form.find("input[name=flabelclass]").val('');
		form.find("select[name=fvalidate] option:selected").removeProp('selected');
		
		form.find("input[name=faccess]").prop('checked', true);
		
		form.find("input[name=fenabled]").prop('checked', true);
		form.find("input[name=fshowinemail]").prop('checked', true);
		form.find("input[name=fshowinorder]").prop('checked', true);
	}
	
	
	
	/*------------------------------------
	*---- OPTIONS FUNCTIONS - SATRT -----
	*------------------------------------*/
	function get_options(elm){
		
		
		
		var optionsKey  = $(elm).find("input[name='i_options_key[]']").map(function(){ return $(this).val(); }).get();
		var optionsText = $(elm).find("input[name='i_options_text[]']").map(function(){ return $(this).val(); }).get();
		var optionsPrice = $(elm).find("input[name='i_options_price[]']").map(function(){ return $(this).val(); }).get();
		var optionsPriceType = $(elm).find("select[name='i_options_price_type[]']").map(function(){ return $(this).val(); }).get();
		
		var optionsSize = optionsText.length;
		var optionsArr = [];
		
		for(var i=0; i<optionsSize; i++){
			var optionDetails = {};
			optionDetails["key"] = optionsKey[i];
			optionDetails["text"] = optionsText[i];
			optionDetails["price"] = optionsPrice[i];
			optionDetails["price_type"] = optionsPriceType[i];
			
			optionsArr.push(optionDetails);
			
			/*if(!optionDetails["key"]){
				optionDetails["key"] = optionDetails["text"];
			}
			
			if(optionDetails["key"] && optionDetails["text"]){
				optionsArr.push(optionDetails);
			}*/
			
		}
		
		var optionsJson = optionsArr.length > 0 ? JSON.stringify(optionsArr) : '';
		optionsJson = encodeURIComponent(optionsJson);
		//optionsJson = optionsJson.replace(/"/g, "'");
		
		return optionsJson;
	}
	
	function populate_options_list(elm, optionsJson){
		var optionsHtml = "";
		
		if(optionsJson){
			try{
				optionsJson = decodeURIComponent(optionsJson);
				var optionsList = $.parseJSON(optionsJson);
		
				if(optionsList){
					jQuery.each(optionsList, function() {
						var op1Selected = this.price_type === 'percentage' ? 'selected' : '';
						var op2Selected = this.price_type === 'percentage_subtotal' ? 'selected' : '';
						var op3Selected = this.price_type === 'percentage_subtotal_ex_tax' ? 'selected' : '';
						var price = this.price ? this.price : '';
						var newkey = this.key.split('+').join(' ');
						var newtxt = this.text.split('+').join(' ');
						var html  = '<tr>';
						html += '<td style="width:190px;"><input type="text" name="i_options_key[]" value="'+newkey+'" placeholder="Option Value" style="width:180px;"/></td>';
						html += '<td style="width:190px;"><input type="text" name="i_options_text[]" value="'+newtxt+'" placeholder="Option Text" style="width:180px;"/></td>';
						
						
						html +='<td class="action-cell"><a href="javascript:void(0)" onclick="jwcfeAddNewOptionRow(this)" class="btn btn-blue" title="Add new option">+</a></td>';
						html += '<td class="action-cell"><a href="javascript:void(0)" onclick="jwcfeRemoveOptionRow(this)" class="btn btn-red"  title="Remove option">x</a></td>';
						html += '<td class="action-cell sort ui-sortable-handle"></td>';
						html += '</tr>';
						
						optionsHtml += html;
					});
				}
			}catch(err) {
				alert(err);
			}
		}
		
		var optionsTable = $(elm).find(".jwcfe-option-list tbody");
		if(optionsHtml){
			optionsTable.html(optionsHtml);
		}else{
			optionsTable.html(OPTION_ROW_HTML);
		}
	}
	
	addNewOptionRow = function addNewOptionRow(elm){
		
		
		var ptable = $(elm).closest('table');
		var optionsSize = ptable.find('tbody tr').size();
			
		if(optionsSize > 0){
			ptable.find('tbody tr:last').after(OPTION_ROW_HTML);
		}else{
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
		
		$('.accountdialog form .rowOptions input[name="i_options_price[]"]').hide();
		$('.accountdialog form .rowOptions select[name="i_options_price_type[]"]').hide();
		
	
	}
	
	removeOptionRow = function removeOptionRow(elm){
		var ptable = $(elm).closest('table');
		$(elm).closest('tr').remove();
		var optionsSize = ptable.find('tbody tr').size();
			
		if(optionsSize == 0){
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
	}
   /*------------------------------------
	*---- OPTIONS FUNCTIONS - END -------
	*------------------------------------*/
	
	function jwcfe_prepare_field_order_indexes() {
		$('#jwcfe_checkout_fields tbody tr').each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index('#jwcfe_checkout_fields tbody tr') ) );
		});
	};
	
	_fieldTypeChangeListner = function fieldTypeChangeListner(elm){

		var type = $(elm).val();
		
		var form = $(elm).closest('form');

		showAllFields(form);
		
		if(type === 'select' || type === 'multiselect' || type === 'radio' || type === 'checkboxgroup'){
			form.find('.rowPricing').hide();
			form.find('.rowPlaceholder').hide();
			form.find('.rowValidate').hide();
			form.find('.rowMaxlength').hide();
			form.find('.rowCustomText').hide();
		
		}

		else if(type === 'checkbox'){
			form.find('.rowPlaceholder').hide();
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowMaxlength').hide();
		}
		else if(type === 'textarea'){
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowCustomText').hide();
		}
		
		else if(type === 'hidden'){
			form.find('.rowRequired').hide();
			form.find('.rowAccess').hide();
			form.find('.rowMaxlength').hide();
			form.find('.rowLabel').hide();
			form.find('.rowValidate').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowOptions').hide();
			form.find('.rowPlaceholder').hide();
		}
		else if(type === 'customcontent'){
			form.find('.rowCustomText').show();
			form.find('.rowRequired').hide();
			form.find('.rowAccess').hide();
			form.find('.rowMaxlength').hide();
			form.find('.rowLabel').hide();
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowPlaceholder').hide();
			form.find('.rowPricing').hide();
			
		}
		else if(type === 'heading'){
			
			form.find('.rowRequired').hide();
			form.find('.rowAccess').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowPlaceholder').hide();
			form.find('.rowMaxlength').hide();
			
		}
		
		else if(type === 'password'){
			form.find('.rowRequired').show();
			form.find('.rowValidate').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowOptions').hide();
			form.find('.rowAccess').show();
		}
		
		else if(type === 'timepicker'){
			form.find('.rowTimepicker').show();
			form.find('.rowRequired').show();
			form.find('.rowMaxlength').hide();
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowAccess').show();
		}
		
		
		else if(type === 'date'){
			form.find('.rowMaxlength').hide();
			form.find('.rowOptions').hide();
			form.find('.rowValidate').hide();
			form.find('.rowCustomText').hide();
		}
		else if(type === 'file'){
			form.find('.rowExtoptions').show();
			form.find('.rowPricing').hide();
			form.find('.rowValidate').hide();
			form.find('.rowOptions').hide();
			form.find('.rowPlaceholder').hide();
			form.find('.rowCustomText').hide();
			form.find('.rowMaxlength').hide();
			
		}
		else if(type === 'number'){
			form.find('.rowOptions').hide();
			form.find('.rowValidate').hide();
			form.find('.rowCustomText').hide();

		}
		else{			
			
			form.find('.rowOptions').hide();
			form.find('.rowCustomText').hide();
		}
		
		$('.accountdialog form .rowOptions input[name="i_options_price[]"]').hide();
		$('.accountdialog form .rowOptions select[name="i_options_price_type[]"]').hide();
		$('.accountdialog form .rowPricing').hide();
		setup_enhanced_multi_select(form);
		
	}
	
	function showAllFields(form){
		form.find('.rowLabel').show();
		form.find('.rowOptions').show();
		form.find('.rowPlaceholder').show();
		form.find('.rowAccess').show();
		form.find('.rowRequired').show();
		form.find('.rowValidate').show();
		form.find('.rowExtoptions').hide();
		form.find('.rowTimepicker').hide();
		
		form.find('.rowPricing').show();
		
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
		saveCustomFieldForm : _saveCustomFieldForm,
		openNewFieldForm : _openNewFieldForm,
		openEditFieldForm : _openEditFieldForm,
		removeSelectedFields : _removeSelectedFields,
		enableDisableSelectedFields : _enableDisableSelectedFields,
		fieldTypeChangeListner : _fieldTypeChangeListner,
		selectAllCheckoutFields : _selectAllCheckoutFields,
		ruleOperatorChangeListner : ruleOperatorChangeListner,
		ruleOperandTypeChangeListner : ruleOperandTypeChangeListner,
		ruleOperatorChangeListnerAjax : ruleOperatorChangeListnerAjax,
		add_new_rule_row : _add_new_rule_row,
		remove_rule_row : _remove_rule_row,
		add_new_rule_row_ajax : _add_new_rule_row_ajax,
		priceTypeChangeListener  : priceTypeChangeListener,
		addNewOptionRow : addNewOptionRow,
		removeOptionRow : removeOptionRow,
		remove_rule_row_ajax : _remove_rule_row_ajax,
   	};
}(window.jQuery, window, document));	


function saveCustomFieldForm(loaderPath,donePath){
	jwcfe_settings.saveCustomFieldForm(loaderPath,donePath);		
}

function saveFieldForm(tabName,pluginPath){
	jwcfe_settings.saveFieldForm(tabName,pluginPath);		
}

function jwcfeRuleOperatorChangeListner(elm){
	jwcfe_settings.ruleOperatorChangeListner(elm);
}

function jwcfeFieldTypeChangeListner(elm){	
	jwcfe_settings.fieldTypeChangeListner(elm);
}


function jwcfeRuleOperandTypeChangeListner(elm){
	jwcfe_settings.ruleOperandTypeChangeListner(elm);
}

function jwcfeAddNewConditionRow(elm, op){
	jwcfe_settings.add_new_rule_row(elm, op);
}
function jwcfeAddNewConditionRowAjax(elm, op){
	jwcfe_settings.add_new_rule_row_ajax(elm, op);
}

function jwcfeRemoveRuleRow(elm){
	jwcfe_settings.remove_rule_row(elm);
}
function jwcfeRemoveRuleRowAjax(elm){
	jwcfe_settings.remove_rule_row_ajax(elm);
}

function jwcfeRuleOperatorChangeListnerAjax(elm){
	jwcfe_settings.ruleOperatorChangeListnerAjax(elm);
}

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


function jwcfeSelectAllCheckoutFields(elm){
	jwcfe_settings.selectAllCheckoutFields(elm);
}

function jwcfeAddNewOptionRow(elm){
	jwcfe_settings.addNewOptionRow(elm);
}
function jwcfeRemoveOptionRow(elm){
	jwcfe_settings.removeOptionRow(elm);
}


function jwcfePriceTypeChangeListener(elm){
	jwcfe_settings.priceTypeChangeListener(elm);
}
