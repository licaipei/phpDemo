<?php
//生成图像缩略图和生成验证码
class Image {
	/**
	 * 生成图像验证码
	 * 自己改写记录session $code
	 * Founded in 2013-08-16
	 * Author: hardlcp
	 * e-mail:464018128@qq.com
	 */
	static function vCode($width=70,$height=32,$codelen=4,$fontsize=16,$randval=NULL,$verifyName='code')
	{
		ob_end_clean();//清除之前的输出缓存，防止bom导致验证码不能显示
		if(!isset($_SESSION))
		{
			session_start();//如果没有开启，session，则开启session
		}
		if(empty($randval)){
			$charset = 'abcdefghkmnprstuvwxyz23456789'; //数字+字母，去除0,1,o,i,I,O五个难辨因子，生成验证码字母不区分大小写
			$_len = strlen($charset)-1;
			for ($i=0;$i<$codelen;$i++) $randval .= $charset[mt_rand(0,$_len)];
		}

		$_SESSION[$verifyName]= strtolower($randval);

		$width = ($codelen*10+10)>$width?$codelen*10+10:$width;

		$im = imagecreatetruecolor($width, $height);
		$back_color = imagecolorallocate($im, 235, 236, 237);
		//$r = array(225,255,255,223);
		//$g = array(225,236,237,255);
		//$b = array(225,236,166,125);
		//$key = mt_rand(0,3);
		
		//$back_color = imagecolorallocate($im, $r[$key],$g[$key],$b[$key]);    //背景色（随机）
		$boer_color = imagecolorallocate($im, 118, 151, 199);
		$text_color = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));

		imagefilledrectangle($im,0,$height,$width,0,$back_color);  //背景色（随机）
		// 画边框
		imagerectangle($im, 0, 0, $width-1, $height-1, $boer_color);
		// 画干扰线
		for($i=0;$i<10;$i++) {
			$font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagearc($im, mt_rand(- $width, $width), mt_rand(- $height, $height), mt_rand(30, $width * 2), mt_rand(20, $height * 2), mt_rand(0, 360), mt_rand(0, 360), $font_color);
		}
		// 画干扰点
		for($i=0;$i<150;$i++) {
			$font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
			imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $font_color);
		}

		$_x = $width / $codelen;
		for ($i=0;$i<$codelen;$i++) {
			$fontcolor = imagecolorallocate($im,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
			imagettftext($im,$fontsize,mt_rand(-20,20),$_x*$i+mt_rand(1,5),$height / 1.4,$text_color,'c:\\WINDOWS\\Fonts\\simsun.ttc',$randval[$i]);
		}

		Image::output($im,'png');
	}
		
	//生成缩略图
	static public function thumb($image, $thumbname, $domain = 'public', $maxWidth=200, $maxHeight=50, $interlace=true) {
		// 获取原图信息
		$info  = self::getImageInfo($image);
		if($info !== false) {
			$srcWidth  = $info['width'];
			$srcHeight = $info['height'];
			$type = strtolower($info['type']);
			$interlace  =  $interlace? 1:0;
			unset($info);
			$scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例
			if($scale>=1) {  // 超过原图大小不再缩略
				$width   =  $srcWidth;
				$height  =  $srcHeight;
			} else {  // 缩略图尺寸
				$width  = (int)($srcWidth*$scale);
				$height = (int)($srcHeight*$scale);
			}
				
			//sae平台上图片处理
			if( class_exists('SaeStorage')) {
				$saeStorage = new SaeStorage();
				$saeImage = new SaeImage();
				$saeImage->setData( file_get_contents($image) );
				$saeImage->resize($width, $height);
				$thumbname = str_replace(array('../', './'), '', $thumbname);
				return $saeStorage->write( $domain, $thumbname, $saeImage->exec() );
			}

			// 载入原图
			$createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
			$srcImg     = $createFun($image);
				
			//创建缩略图
			if($type!='gif' && function_exists('imagecreatetruecolor')) {
				$thumbImg = imagecreatetruecolor($width, $height);
			} else {
				$thumbImg = imagecreate($width, $height);
			}
			// 复制图片
			if(function_exists("ImageCopyResampled")) {
				imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
			} else {
				imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight);
			}
			if('gif'==$type || 'png'==$type) {
				$background_color  =  imagecolorallocate($thumbImg,  0,255,0);  //  指派一个绿色
				imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
			}
			// 对jpeg图形设置隔行扫描
			if('jpg'==$type || 'jpeg'==$type) {
				imageinterlace($thumbImg,$interlace);
			}
			$dir = dirname($thumbname);
			if ( !is_dir($dir) ) {
				@mkdir($dir, 0777, true);
			}
			// 生成图片
			$imageFun = 'image'.($type=='jpg'?'jpeg':$type);
			$imageFun($thumbImg,$thumbname);
			imagedestroy($thumbImg);
			imagedestroy($srcImg);
			return $thumbname;
		}
		return false;
	}

	/**
	 * 图片水印
	 * @$image  原图
	 * @$water 水印图片
	 * @$$waterPos 水印位置(0-9) 0为随机，其他代表上中下9个部分位置
	 */
	static public function water($image, $water, $waterPos =9) {
		//检查图片是否存在
		if (!file_exists($image) || !file_exists($water))
		return false;
		//读取原图像文件
		$imageInfo = self::getImageInfo($image);
		$image_w = $imageInfo['width']; //取得水印图片的宽
		$image_h = $imageInfo['height']; //取得水印图片的高
		$imageFun = "imagecreatefrom" . $imageInfo['type'];
		$image_im = $imageFun($image);

		//读取水印文件
		$waterInfo = self::getImageInfo($water);
		$w = $water_w = $waterInfo['width']; //取得水印图片的宽
		$h = $water_h = $waterInfo['height']; //取得水印图片的高
		$waterFun = "imagecreatefrom" . $waterInfo['type'];
		$water_im = $waterFun($water);

		switch ($waterPos) {
			case 0: //随机
				$posX = rand(0, ($image_w - $w));
				$posY = rand(0, ($image_h - $h));
				break;
			case 1: //1为顶端居左
				$posX = 0;
				$posY = 0;
				break;
			case 2: //2为顶端居中
				$posX = ($image_w - $w) / 2;
				$posY = 0;
				break;
			case 3: //3为顶端居右
				$posX = $image_w - $w;
				$posY = 0;
				break;
			case 4: //4为中部居左
				$posX = 0;
				$posY = ($image_h - $h) / 2;
				break;
			case 5: //5为中部居中
				$posX = ($image_w - $w) / 2;
				$posY = ($image_h - $h) / 2;
				break;
			case 6: //6为中部居右
				$posX = $image_w - $w;
				$posY = ($image_h - $h) / 2;
				break;
			case 7: //7为底端居左
				$posX = 0;
				$posY = $image_h - $h;
				break;
			case 8: //8为底端居中
				$posX = ($image_w - $w) / 2;
				$posY = $image_h - $h;
				break;
			case 9: //9为底端居右
				$posX = $image_w - $w;
				$posY = $image_h - $h;
				break;
			default: //随机
				$posX = rand(0, ($image_w - $w));
				$posY = rand(0, ($image_h - $h));
				break;
		}
		//设定图像的混色模式
		imagealphablending($image_im, true);
		imagecopy($image_im, $water_im, $posX, $posY, 0, 0, $water_w, $water_h); //拷贝水印到目标文件
		//生成水印后的图片
		$bulitImg = "image" . $imageInfo['type'];
		$bulitImg($image_im, $image);
		//释放内存
		$waterInfo = $imageInfo = null;
		imagedestroy($image_im);
	}

	static protected function getImageInfo($img) {
		$imageInfo = getimagesize($img);
		if( $imageInfo!== false) {
			$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
			$imageSize = filesize($img);
			$info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
			);
			return $info;
		} else {
			return false;
		}
	}

	static protected function output($im,$type='png',$filename='') {
		header("Content-type: image/".$type);
		$ImageFun='image'.$type;
		if(empty($filename)) {
			$ImageFun($im);
		}else{
			$ImageFun($im,$filename);
		}
		imagedestroy($im);
		exit;
	}
}
?>