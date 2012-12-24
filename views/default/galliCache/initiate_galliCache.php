<?php
if(elgg_is_logged_in()){
	return;
}
?>	
//<script>
// Initiate galliCache
$(document).ready(function() {	
	galliCache_update_tokens();
});	

function galliCache_update_tokens(){
	$("input[name='__elgg_ts']").each(function(){
		var field = $(this);
		field.val(elgg.security.token.__elgg_ts);
	});	
	$("input[name='__elgg_token']").each(function(){
		var field = $(this);
		field.val(elgg.security.token.__elgg_token);
	});	
}	