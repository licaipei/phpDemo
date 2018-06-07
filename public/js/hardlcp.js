/*
 * Founded in 2013-08-16 
 * Author: hardlcp
 * e-mail:464018128@qq.com
*/
$(document).ready(function(){
	//留言表单
	var items_array = [
		{ name:"name",simple:"姓名",focusMsg:'必填',min:1,max:32},
		{ name:"phone",type:'telephone',simple:"手机号码",focusMsg:'必填'},
		{ name:"email",type:'mail',simple:"邮箱",focusMsg:'必填'},
		{ name:"content",simple:"留言内容",focusMsg:'必填',min:10,max:300},
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#board").skygqCheckAjaxForm({
		items			: items_array
	});
	//简历表单
	var items_arrayjob = [
		{ name:"name",simple:"姓名",focusMsg:'必填',min:1,max:32},
		{ name:"phone",type:'telephone',simple:"手机号码",focusMsg:'必填'},
		{ name:"email",type:'mail',simple:"邮箱",focusMsg:'必填'},
		{ name:"address",simple:"现居地址",focusMsg:'必填'},
		{ name:"zy",simple:"所学专业",focusMsg:'必填'},
		{ name:"tc",simple:"专业特长",focusMsg:'必填',min:10,max:300},
		{ name:"content",simple:"个人简历",focusMsg:'必填',min:10,max:500},
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#joinbox").skygqCheckAjaxForm({
		items			: items_arrayjob
	});
	//用户登录表单验证
	var items_array0 = [
		{ name:"uname",simple:"用户名",focusMsg:'必填',min:1,max:32},
		{ name:"upass",type:'password',simple:"用户密码",focusMsg:'必填',min:6,max:32},
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#login").skygqCheckAjaxForm({
		items			: items_array0
	});
	//用户注册表单验证	
	var items_array1 = [
		{ name:"uname",type:"username",simple:"登录名",focusMsg:"数字和英文及下划线和.的组合，开头为字母，4-20个字符",min:4,max:20,ajax:{method:'post',url:app+'/index/repeat-user.html',success_msg:'可以使用',failure_msg:'已经存在'}},
		{ name:"upass",type:'password',simple:"用户密码",focusMsg:'数字和英文及下划线的组合，6-20个字符',min:6,max:20},
		{ name:"reupass",type:'eq',to:'upass',simple:"确认密码",focusMsg:'输入您上面填写的密码'},
		{ name:"email",type:"mail",simple:"电子邮箱",require:false,focusMsg:'请填写真实并且最常用的邮箱',ajax:{method:'post',url:app+'/index/repeat-user.html',success_msg:'邮箱可以使用',failure_msg:'邮箱已被使用'}},
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#register").skygqCheckAjaxForm({
		items			: items_array1
	});
	//用户找回密码表单验证
	var items_array2 = [
		{ name:"loginuser",simple:"用户名",focusMsg:'必填'},
		{ name:"email",type:"mail",simple:"您的注册邮箱",focusMsg:'请填写你的注册邮箱'},
	  { name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#repassform").skygqCheckAjaxForm({
		items			: items_array2
	});	
	//修改密码	
	var items_array3 = [
		{ name:"password",type:'password',simple:"用户密码",focusMsg:'数字和英文及下划线的组合，6-20个字符',min:6,max:20},
		{ name:"repassword",type:'eq',to:'password',simple:"确认密码",focusMsg:'输入您上面填写的密码'},
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#newpass").skygqCheckAjaxForm({
		items			: items_array3
	});
	//发布论坛信息	
	var items_array4 = [
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#forumpost").skygqCheckAjaxForm({
		items			: items_array4
	});
	//回复主题	
	var items_array5 = [
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#forumback").skygqCheckAjaxForm({
		items			: items_array5
	});
	//跟帖回复	
	var items_array6 = [
		{ name:"checkcode",simple:"验证码",focusMsg:'必填',ajax:{method:'post',url:app+'/index/checkcode.html',success_msg:'正确',failure_msg:'不正确'}}
	];
	$("#replay").skygqCheckAjaxForm({
		items			: items_array6
	});
	
});
//密码安全强度检测 
function CharMode(iN){ 
	if (iN>=48 && iN <=57) //数字 
	return 1; 
	if (iN>=65 && iN <=90) //大写字母 
	return 2; 
	if (iN>=97 && iN <=122) //小写 
	return 4; 
	else 
	return 8; //特殊字符 
} 
//bitTotal函数 
//计算出当前密码当中一共有多少种模式 
function bitTotal(num){ 
	modes=0; 
	for (i=0;i<4;i++){ 
	if (num & 1) modes++; 
		num>>>=1; 
	} 
	return modes; 
} 
//checkStrong函数 
//返回密码的强度级别 
function checkStrong(sPW){ 
	if (sPW.length<=4) 
	return 0; //密码太短 
	Modes=0; 
	for (i=0;i<sPW.length;i++){ 
	//测试每一个字符的类别并统计一共有多少种模式. 
		Modes|=CharMode(sPW.charCodeAt(i)); 
	} 
	return bitTotal(Modes); 
} 
//pwStrength函数 
//当用户放开键盘或密码输入框失去焦点时,根据不同的级别显示不同的颜色 
function pwStrength(pwd){ 
	O_color="#ffd099"; 
	L_color="#ff6600"; 
	M_color="#ff6600"; 
	H_color="#ff6600"; 
	if (pwd==null||pwd==''){ 
		Lcolor=Mcolor=Hcolor=O_color; 
	} 
	else{ 
	S_level=checkStrong(pwd); 
	switch(S_level) { 
	case 0: 
	Lcolor=Mcolor=Hcolor=O_color; 
	case 1: 
	Lcolor=L_color; 
	Mcolor=Hcolor=O_color; 
	break; 
	case 2: 
	Lcolor=Mcolor=M_color; 
	Hcolor=O_color; 
	break; 
	default: 
	Lcolor=Mcolor=Hcolor=H_color; 
	} 
	} 
	document.getElementById("strength_L").style.background=Lcolor; 
	document.getElementById("strength_M").style.background=Mcolor; 
	document.getElementById("strength_H").style.background=Hcolor; 
	return; 
} 

/*
window.onload = function ()
{
	var aInput = document.getElementsByTagName("input");
	var i = 0;
	for (i = 0; i < aInput.length - 1; i++)
	{
		aInput[i].onfocus = function ()
		{
			this.className = "f-text-high"
		};
		aInput[i].onblur = function ()
		{
			this.className = "reg_text"
		}	
	}
};*/
function SetHome(obj,url){
    try{
        obj.style.behavior='url(#default#homepage)';
       obj.setHomePage(url);
   }catch(e){
       if(window.netscape){
          try{
              netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
         }catch(e){
              alert("抱歉，此操作被浏览器拒绝！\n\n请在浏览器地址栏输入about:config并回车然后将[signed.applets.codebase_principal_support]设置为'true'");
          }
       }else{
        alert("抱歉，您所使用的浏览器无法完成此操作。\n\n您需要手动将【"+url+"】设置为首页。");
       }
  }
}
function AddFavorite(title, url) {
  try {
      window.external.addFavorite(url, title);
  }
catch (e) {
     try {
       window.sidebar.addPanel(title, url, "");
    }
     catch (e) {
         alert("抱歉，您所使用的浏览器无法完成此操作。\n\n加入收藏失败，请使用Ctrl+D进行添加");
     }
  }
}
function setTab(name,cursel,n){
	for(i=1;i<=n;i++){
	var menu=document.getElementById(name+i);
	var con=document.getElementById("con_"+name+"_"+i);
	menu.className=i==cursel?"hover":"";
	con.style.display=i==cursel?"block":"none";
	}
}