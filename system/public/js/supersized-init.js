jQuery(function($){
	$("#onlogin").click(function(){
	  var username=$("#username").val();
	  var password=$("#userpass").val();
	  if(username==''){ 
	  	$(".tips").html('<div class="fa fa-exclamation-circle error"></div><div class="errortxt">&nbsp;请输入用户名</div>');
		return false;
	  }else if(password==''){
	  	$(".tips").html('<div class="fa fa-exclamation-circle error"></div><div class="errortxt">&nbsp;请输入密码</div>');
		return false;
	  }else{
		  $.post(app+"/index/login.html", { 
				username: username, 
				password: password 
		  },function(data){
  	    	if(data=="系统登录成功"){
				$(".tips").html('<div class="fa fa-check-circle seccess" style=""></div><div class="seccesstxt">&nbsp;登录成功</div>');
				window.setTimeout("window.location='"+app+"/index/index.html'",1500); 
			}else{
				$(".tips").html('<div class="fa fa-exclamation-circle error"></div><div class="errortxt">&nbsp;'+data+'</div>');
			}
		 });
	  }
	});
    $.supersized({

        // Functionality
        slide_interval     : 4000,    // Length between transitions
        transition         : 1,    // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
        transition_speed   : 1000,    // Speed of transition
        performance        : 1,    // 0-Normal, 1-Hybrid speed/quality, 2-Optimizes image quality, 3-Optimizes transition speed // (Only works for Firefox/IE, not Webkit)

        // Size & Position
        min_width          : 0,    // Min width allowed (in pixels)
        min_height         : 0,    // Min height allowed (in pixels)
        vertical_center    : 1,    // Vertically center background
        horizontal_center  : 1,    // Horizontally center background
        fit_always         : 0,    // Image will never exceed browser width or height (Ignores min. dimensions)
        fit_portrait       : 1,    // Portrait images will not exceed browser height
        fit_landscape      : 0,    // Landscape images will not exceed browser width

        // Components
        slide_links        : 'blank',    // Individual links for each slide (Options: false, 'num', 'name', 'blank')
        slides             : [    // Slideshow Images
                                 {image : public+'/images/2.jpg'},
                                 {image : public+'/images/4.jpg'},
                                 {image : public+'/images/3.jpg'}
                             ]

    });

});
