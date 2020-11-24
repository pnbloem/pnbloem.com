var HaikuPlayer = (function ($){
	var i = 0;
	$(function(){
		window.setInterval(function(){step();}, 300);	
	})
	
	function step(){
		$(".syl").not(".read").first().addClass("read");
		if (i === ($(".syl").length + 3)) {
			$(".syl").removeClass("read");
			i = 0;
		}
		i++;
	}
})($);
	