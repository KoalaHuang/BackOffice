<?php
/*
	myLOG($word,$newline));
	$word: var to be logged. If it's file name with .php or .html like __FILE__, it will add timestap and new line
	$newline: boolean. if it's true, it will start new line. default it's true
	mylog.txt is stored at web root folder.
*/

function myLOG($word='',$newline=true) {
	$fp = fopen($_SERVER['DOCUMENT_ROOT']."/mylog.txt","a") or die("Unable to open file!");
	$saveTimestamp = false;
	flock($fp, LOCK_EX) ;
	if (is_string($word)) {
		if (strstr($word,".php") || strstr($word,".html")) {
			fwrite($fp,"<".strftime("%Y%m%d%H%M%S",time())."> -- ".$word."\n") or die("unable to write");
			$saveTimestamp = true;
		}
	}
	if (!($saveTimestamp)) {
		if ($newline) {
			fwrite($fp,var_export($word,true)."\n") or die("unable to write");
		}else{
			fwrite($fp,var_export($word,true)." ") or die("unable to write");
		}
	}
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>
