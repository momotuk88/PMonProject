<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
function savePhoto($file,$name){
	$min_wid = '380x640';
	$big_wid = '280x440';
	$quality = 100;
	$crop = true;
	if( !empty( $file ) ){
		$DIR_IMG_BIG = ROOT_DIR . '/file/photo/';
		$DIR_IMG_MIN = ROOT_DIR . '/file/photo/small/';
		$DIR_IMG_TMP = ROOT_DIR . '/file/photo/tamp/';
		if( !is_dir( $DIR_IMG_BIG ) ) mkdir( $DIR_IMG_BIG, 0777 ); chmod($DIR_IMG_BIG, 0777);
		if( !is_dir( $DIR_IMG_MIN ) ) mkdir( $DIR_IMG_MIN, 0777 ); chmod($DIR_IMG_MIN, 0777);
		if( !is_dir( $DIR_IMG_TMP ) ) mkdir( $DIR_IMG_TMP, 0777 ); chmod($DIR_IMG_TMP, 0777);
		move_uploaded_file($file['tmp_name'], $DIR_IMG_TMP . $file['name']);
		$filesys = explode("/", $file['name']);
		$filesys = explode(".", end($filesys));
		$FILE_UP = time() . '_' . $name . '.' . end( $filesys );
		$CropImg = $config['url'] . $DIR_IMG_TMP. $file['name'];
		# Класс для работы с Изображением
		$resizeImg = new resize($CropImg);
		# Кроп большой картинки
		$ExWidBigImg = explode('x', $big_wid);
		if( isset( $ExWidBigImg ) AND $crop ){
			$resizeImg->resizeImage($ExWidBigImg[0], $ExWidBigImg[1], 'crop');
			if( $quality ){
				$resizeImg->saveImage($DIR_IMG_BIG.$FILE_UP, $quality);
			}else{
				$resizeImg->saveImage($DIR_IMG_BIG.$FILE_UP, 80);
			}
		}else{
			copy($DIR_IMG_TMP.$file['name'],$DIR_IMG_BIG.$FILE_UP);
		}
		# Кроп маленькой картинки
		$ExWidMinImg = explode('x', $min_wid);
		if( isset( $ExWidMinImg ) AND $crop ){
			$resizeImg->resizeImage($ExWidMinImg[0],$ExWidMinImg[1],'crop');
			if( $quality ){
				$resizeImg->saveImage($DIR_IMG_MIN.$FILE_UP,$quality);
			}else{
				$resizeImg->saveImage($DIR_IMG_MIN.$FILE_UP, 80);
			}
		}
		unlink($DIR_IMG_TMP . $file['name']);
		return $FILE_UP;
	}
}