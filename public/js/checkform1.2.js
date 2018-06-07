/*
Jquery 表单验证插件 需要jquery1.3.2或者以上版本
By 甘强 2010.10
1.2版
*/

$.fn.extend({
	valid:function(settings){
		if( !$(this).is("form") ) return;
		var defaultSettings = {
			items		: [],
			isBindSubmit: true,
			isAlert		: false,
			rule 		: {
							"eng" : /^[A-Za-z]+$/,
							"chn" :/^[\u0391-\uFFE5]+$/,
							"mail" : /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/,
							"url" : /^http[s]?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/,
							"currency" : /^\d+(\.\d+)?$/,
							"number" : /^\d+$/,
							"int" : /^[0-9]{1,30}$/,
							"double" : /^[-\+]?\d+(\.\d+)?$/,
							"username" : /^[a-zA-Z]{1}([a-zA-Z0-9]|[._]){3,19}$/,
							"password" : /^(\w){6,20}$/,
							"safe" : />|<|,|\[|\]|\{|\}|\?|\/|\+|=|\||\'|\\|\"|:|;|\~|\!|\@|\#|\*|\$|\%|\^|\&|\(|\)|`/i,
							"dbc" : /[ａ-ｚＡ-Ｚ０-９！＠＃￥％＾＆＊（）＿＋｛｝［］｜：＂＇；．，／？＜＞｀～　]/,
							"qq" : /[1-9][0-9]{4,}/,
							"date" : /^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/,
							"telephone" : /^1\d{10}$/,
							"zipcode" : /^[1-9]\d{5}$/,
							"bodycard" : /^((1[1-5])|(2[1-3])|(3[1-7])|(4[1-6])|(5[0-4])|(6[1-5])|71|(8[12])|91)\d{4}((19\d{2}(0[13-9]|1[012])(0[1-9]|[12]\d|30))|(19\d{2}(0[13578]|1[02])31)|(19\d{2}02(0[1-9]|1\d|2[0-8]))|(19([13579][26]|[2468][048]|0[48])0229))\d{3}(\d|X|x)?$/,
							"ip" : /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/,

							// 函数规则
							"eq": function(arg1,arg2){ return arg1==arg2 ? true:false;},
							"gt": function(arg1,arg2){ return arg1>arg2 ? true:false;},
							"gte": function(arg1,arg2){ return arg1>=arg2 ? true:false;},
							"lt": function(arg1,arg2){ return arg1<arg2 ? true:false;},
							"lte": function(arg1,arg2){ return arg1<=arg2 ? true:false;}
							},
			msgSuffix	: {
							"eng" : "只能输入英文" ,
							"chn" : "只能输入汉字",
							"mail" : "格式不正确",
							"url" : "格式不正确",
							"currency" : "数字格式有误",
							"number" : "只能为数字",
							"int" : "只能为整数",
							"double" : "只能为带小数的数字",
							"username" :"用户名不合法",
							"password" : "只能为数字和英文及下划线的组合，6-20个字符",
							"safe" : "不能有特殊字符",
							"dbc" : "不能有全角字符",
							"qq" : "格式不正确",
							"date" : "格式不正确",
							"telephone" : "格式不正确",
							"zipcode" : "格式不正确",
							"bodycard" : "格式不正确",
							"ip" : "IP不正确",
							"eq": "必须等于",
							"gt": "必须大于",
							"gte": "必须大于或等于",
							"lt": "必须小于",
							"lte": "必须小于或等于"
							}
		};
		settings = $.extend(defaultSettings,settings);
		var msg = "", formObj = $(this) , checkRet = true, isAll,
		tipname = function(namestr){ return "tip_" + namestr.replace(/([a-zA-Z0-9])/g,"-$1"); },

		//规则类型匹配检测
		typeTest = function(){
			var result = true,args = arguments;
			if(settings.rule.hasOwnProperty(args[0])){
				var t = settings.rule[args[0]], v = args[1];
				result = args.length>2 ? t.apply(arguments,[].slice.call(args,1)):($.isFunction(t) ? t(v) :t.test(v));
			}
			return result;
		},

		//错误信息提示
		showError = function(fieldObj,filedName,warnInfo){
			checkRet = false;
			var tipObj = $("#"+tipname(filedName));
			if(tipObj.length>0) tipObj.remove();
			fieldObj.parent().append("<span class='Wrong' id='"+tipname(filedName)+"'> "+warnInfo+" </span>");
			if(settings.isAlert && isAll) msg += "\n" + warnInfo;
		},

		//正确信息提示
		showRight = function(fieldObj,filedName,SuccessInfo){
			var tipObj = $("#"+tipname(filedName));
			if(tipObj.length>0) tipObj.remove();
			if (!SuccessInfo)
			{
				SuccessInfo = '填写正确';
			}
			fieldObj.parent().append("<span class='Correct' id='"+tipname(filedName)+"'> "+ SuccessInfo +" </span>");
		},

		//focus时提示
		showExp = function(obj){
			var i = obj, fieldObj = $("[name='"+i.name+"']",formObj[0]);
			var tipObj = $("#"+tipname(i.name));
			if(tipObj.length>0) tipObj.remove();
			if (i.focusMsg){
				fieldObj.parent().append("<span class='Exp' id='"+tipname(i.name)+"'>"+ i.focusMsg +"</span>");
			}
		},

		//匹配对比值的提示名
		findTo = function(objName){
			var find;
			$.each(settings.items, function(){
				if(this.name == objName && this.simple){
					find = this.simple;	return false;
				}
			});
			if(!find) find = $("[name='"+objName+"']")[0].name;
			return find;
		},
		//ajax验证
		ajax = function (obj,fv,field){
			var i = obj, fieldObj = $("[name='"+i.name+"']",formObj[0]);
			var tipObj = $("#"+tipname(i.name));
			if(tipObj.length>0) tipObj.remove();
			var tipPosition = fieldObj.next().length>0 ? fieldObj.nextAll().eq(this.length-1):fieldObj.eq(this.length - 1);
			tipPosition.after("<span class='Exp' id='"+tipname(i.name)+"'>检测中......</span>");
			fv = encodeURI(fv);
			$.get(obj.ajax.url + "?" + obj.name + "=" + fv,function(data){
				if (data == 1){
					showRight(field,obj.name,obj.ajax.success_msg);
				}
				else if(data == 0){
					showError(field ,obj.name, obj.ajax.failure_msg);
				}
			});
		},

		//单元素验证
		fieldCheck = function(item){
			var i = item, field = $("[name='"+i.name+"']",formObj[0]);

			if(!field[0]) return;

			var warnMsg,
				fv = $.trim(field.val()),
				isRq = typeof i.require ==="boolean" ? i.require : true;
			if( isRq && ((field.is(":radio")|| field.is(":checkbox")) && !field.is(":checked")))
			{
				warnMsg =  i.message|| "请选择" + i.simple;
				showError(field ,i.name, warnMsg);
			}
			else if (isRq && fv == "" )
			{
				warnMsg =  i.message|| ( field.is("select") ? "请选择" :"请填写" ) + i.simple;
				showError(field ,i.name, warnMsg);
			}
			else if(fv != "")
			{
				if(i.min || i.max){
					var len = fv.length, min = i.min || 0, max = i.max;
					warnMsg =  i.message || (max? i.simple + "长度范围应在"+min+"~"+max+"之间":i.simple + "长度应大于"+min);

					if( (max && (len>max || len<min)) || (!max && len<min) ){
						showError(field ,i.name, warnMsg);	return;
					}
				}

				if(i.type){
					var matchVal = i.to ? $.trim($("[name='"+i.to+"']").val()) :i.value;
					var matchRet = matchVal ? typeTest(i.type,fv,matchVal) :typeTest(i.type,fv);

					warnMsg = i.message|| i.simple + settings.msgSuffix[i.type];
					if(matchVal && i.simple) warnMsg += (i.to ? findTo(i.to) +"的值" :i.value);

					if(!matchRet) {
						showError(field ,i.name, warnMsg);return;
					}else {
						showRight(field,i.name);
					}

				}

				if (i.ajax)
				{
					$.ajaxSetup({
					  async: !isAll // false使用同步方式执行AJAX，true使用异步方式执行ajax
					});
					ajax(i,fv,field);
				}
				else
				{
					showRight(field,i.name);
				}
			}
		},

		//元素组验证
		validate = function(){
			checkRet = true;
			$.each(settings.items, function(){
				isAll=true; fieldCheck(this);
			});
			if(settings.isAlert && msg != ""){
				alert(msg);	msg = "";
			}
			return checkRet;
		};

		//单元素事件绑定
		$.each(settings.items, function(){
			var field = $("[name='"+this.name+"']",formObj[0]);
			var obj = this,
			toExp = function(){showExp(obj);},
			toCheck = function(){ isAll=false; fieldCheck(obj);};
			if(field.is(":file") || field.is("select")){
				field.change(toCheck).focus(toExp);
			}else{
				field.blur(toCheck).focus(toExp);
			}
		});


		//提交事件绑定
		if(settings.isBindSubmit) {
			$(this).submit(validate);
		}else{
			return validate();
		}

	}

});