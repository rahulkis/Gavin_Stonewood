jQuery(document).ready(function()
{
	jQuery("#kpso_css_mobile_disabled").click(function(){
		if( jQuery("#kpso_css_mobile_disabled").attr("value") == "yes" )
		{ jQuery("#kpso_css_mobile_disabled").attr("value","no"); }
		else
		{ jQuery("#kpso_css_mobile_disabled").attr("value","yes"); }
	});

	jQuery("#kpso_js_mobile_disabled").click(function(){
		if( jQuery("#kpso_js_mobile_disabled").attr("value") == "yes" )
		{ jQuery("#kpso_js_mobile_disabled").attr("value","no"); }
		else
		{ jQuery("#kpso_js_mobile_disabled").attr("value","yes"); }
	});
	
	jQuery("#kpso_wp_rocket_support").click(function(){
		if( jQuery("#kpso_wp_rocket_support").attr("value") == "yes" )
		{ jQuery("#kpso_wp_rocket_support").attr("value","no"); }
		else
		{ jQuery("#kpso_wp_rocket_support").attr("value","yes"); }
	});
	
	jQuery("#kpso_white_label").click(function(){
		if( jQuery("#kpso_white_label").attr("value") == "yes" )
		{ jQuery("#kpso_white_label").attr("value","no"); }
		else
		{ jQuery("#kpso_white_label").attr("value","yes"); }
	});
	
	jQuery("#kpso_cartflows").click(function(){
		if( jQuery("#kpso_cartflows").attr("value") == "yes" )
		{ jQuery("#kpso_cartflows").attr("value","no"); }
		else
		{ jQuery("#kpso_cartflows").attr("value","yes"); }
	});
	
	jQuery("#kpso_video_mobile_disabled").click(function(){
		if( jQuery("#kpso_video_mobile_disabled").attr("value") == "yes" )
		{ jQuery("#kpso_video_mobile_disabled").attr("value","no"); }
		else
		{ jQuery("#kpso_video_mobile_disabled").attr("value","yes"); }
	});
	
});