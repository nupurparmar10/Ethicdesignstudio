$(document).ready(function() {
	$("html, body").animate({scrollTop: 0 }, 1000);

    if ( $(window).height() > 1100 ) { $(".examBox").css("opacity", "10"); }

	$(".menu li").click(function() {
		var idHead = $(this).attr("data-id");
		$("html, body").animate({scrollTop: $("#"+idHead).offset().top - 120 }, 1000);
	});
});

$(function(){
    $(window).bind('scroll', function() {
        $('div.examBox').each(function() {
            var post = $(this);
            var position = post.position().top - $(window).scrollTop() - 400;
            
            if (position <= 0) {
            	var index = post.index();
            	$(".menu li").removeClass("act"); 
                $(".menu li").eq(index).addClass("act");

                $(".examBox").removeClass("act"); 
                $(".examBox").eq(index).addClass("act");
            } else {
            	var index = post.index();
            	post.removeClass("selected");
            }
        });        
    });
});