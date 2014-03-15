<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
require_once("../modules/class.song.php");
require_once("../modules/class.user.php");

$ret = array();
/*
if(!isset($_POST['name']) || !isset($_POST['singer'])){
	echo "Need 2 params!".$_POST['name'];
	exit();
}
*/
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetFolder = '/listen-his-songs/music'; // Relative to the root
	$targetPath = $_SERVER['DOCUMENT_ROOT'] .'/'. $targetFolder;
	$targetFile = rtrim($targetPath,'/') . '/';//$_FILES['Filedata']['name'];
	
	// Validate the file type
	$fileTypes = array('mp3'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'],$fileTypes)) {
		$song = new Song();
		$songName = $_REQUEST['name'];
		$singerName = $_REQUEST['singer'];
		$albumName = $_REQUEST['album'];
		if($albumName == "null") $albumName = null;
		if($song->isExist($songName, $singerName, $albumName)){
			// ����ļ��Ѿ����ڣ��ͷ��ش�����1
			$ret['ret'] = 1;
			$ret['msg'] = "MP3 file had been uploaded by some other body!";
		}else {
			$sid = $song->insert($songName, $singerName, null, $albumName);
			move_uploaded_file($tempFile,$targetFile.$sid."song.mp3");
			$src = "http://localhost/listen-his-songs/music/$sid"."song.mp3";
			$song->updateSrc($sid, $src);
			$user = new User($_REQUEST['openid']);
			$user->listenANewSong($sid);
			// �����ķ�����Ϊ0
			$ret['ret'] = '0';
			$ret['src'] = $src;
			$ret['msg'] = "No error!";
			$ret['sname'] = $songName;
			$ret['singer'] = $singerName;
		}
	} else {
		// �ļ����Ͳ��ԣ����ش�����2
		$ret['ret'] = 2;
		$ret['msg'] = 'Invalid file type.';
	}
} else {
	// û��ѡ���ļ�
	$ret['ret'] = 3;
	$ret['msg'] = 'No mp3 file was uploaded.';
}

echo json_encode($ret);
?>