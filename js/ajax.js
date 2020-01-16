function avihasta_load_ajax(id){
	var post_id = id ;
	//alert(post_id);
	jQuery.ajax({
		url : avihasta_ajax_url.ajax_url,
		type : 'post',
		data : {
			action : 'handle_ajax_request',
			id :  post_id
			//var1 : 'check'		
		},
		success : function(response){
			//alert(id);
			jQuery('#ajax-response-'+id).html(response);
		}
	});
	
}

function avihasta_load_ajax_v2(id, uid){
	var post_id = id ;
	var uni_id = uid;
	//alert(post_id);
	jQuery.ajax({
		url : avihasta_ajax_url.ajax_url,
		type : 'post',
		data : {
			action : 'handle_ajax_request',
			id :  post_id,
			uid: uni_id
			//var1 : 'check'		
		},
		success : function(response){
			//alert(id);
			jQuery('#ajax-response-'+id).html(response);
		}
	});
	
}
