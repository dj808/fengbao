$.ajax({
	type:"get",
	url:"../html/yye-footer.html",
	async:true,
	success:function(res){
		$(".myfooter").html(res);
	}
});