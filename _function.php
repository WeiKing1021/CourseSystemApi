<?php
	function getDirInfo($dir, $mode = 0) {
		// 回傳物件
		$list = array();
		$list['MESSAGE'] = 'SUCCESS';
		$list['RESULT'] = array();
		// 看有沒有目錄

		if (!file_exists($dir) || !fopen($dir, "r")) {
			$list['MESSAGE'] = 'FILE_NULL';
			return $list;
		}
		
		// 判斷是否為目錄
		if(is_dir($dir)) {
			// 開啟IO讀取目錄
			if ($dh = opendir($dir)) {
				// 迴圈讀取
				while (($file = readdir($dh)) !== false) {
					// 判斷是否為詭異的名字
					if ($file != '.' && $file != '..') {				
						// 放入資料
						// 是否為資料夾
						$list['RESULT'][$file]['IS_DIR'] = (is_dir($dir . '/' . $file) ? 1 : 0);	
						// 檔名
						$list['RESULT'][$file]['NAME'] = $file;
						// 修改時間
						$list['RESULT'][$file]['TIME'] = @date("Y-m-d", filectime($dir . '/' . $file));					
						// 完整路徑
						$list['RESULT'][$file]['FILE'] = $dir . '/' . $file;
						// 所屬目錄
						$list['RESULT'][$file]['PARENT_DIR'] = $dir;
						// 副檔名
						$tmp_ext = explode('.', $file);
						$list['RESULT'][$file]['EXT'] = end($tmp_ext);					
					}
				}
				closedir($dh);
			}
		}
		else {
			$list['MESSAGE'] = 'FILE_NOT_DIR';
			
			$_tmp = explode("/", $dir);
			$file = end($_tmp);
			
			// 放入資料
			// 是否為資料夾
			$list['RESULT'][$file]['IS_DIR'] = 0;
			// 檔名
			$list['RESULT'][$file]['NAME'] = $file;
			// 修改時間
			$list['RESULT'][$file]['TIME'] = @date("Y-m-d", filectime($dir . '/' . $file));	
			// 完整路徑
			$list['RESULT'][$file]['FILE'] = $dir;
			// 所屬目錄
			$list['RESULT'][$file]['PARENT_DIR'] = $dir;
			// 副檔名
			$_tmp = explode('.', $file);
			$list['RESULT'][$file]['EXT'] = end($_tmp);	
			
			return $list;
		}
		
		return $list;
    }	

	function test($text) {
		header("Content-type: image/png"); //設定圖檔格式
		$im = @imagecreatetruecolor(200, 200) or die("無法建立圖片！"); //建立一張全彩圖
		$text_color = imagecolorallocate($im, 255, 255, 255);  //設定文字顏色
		imagestring($im, 2, 5, 0, $text, $text_color);  //將字串加入圖片中
		imagestring($im, 2, 5, 10, $text, $text_color);  //將字串加入圖片中
		imagestring($im, 2, 5, 20, $text, $text_color);  //將字串加入圖片中
		imagepng($im);  //產生圖片
		imagedestroy($im);   //結束$im釋放記憶體
	}
?>