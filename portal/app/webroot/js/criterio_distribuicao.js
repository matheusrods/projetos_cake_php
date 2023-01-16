
jQuery(document).ready(function() {
	
	jQuery(document).on("click",".row-move-up",function(){
		alert("sobe!");	
		return false;
	});

	jQuery(document).on("click",".row-move-down",function(){
		alert("desce!");
		return false;
	});
})