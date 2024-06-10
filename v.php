<?php

require_once 'config.php';
require_once 'viewer2.php';

/*******************************************************
 *  メイン
 *******************************************************
 */
function main()
{
	// 以下の２つは固定値.
	global $srcpath;  // 閲覧したいソースファイルの置き場所ROOT '/'で終わる.
	global $base_url; // このCGIの設置URL 「.php」 で終わる.

	$srcpath = '/var/www/html/pub/Src/' ;  // 閲覧したいソースファイルの置き場所.
	$base_url = $_SERVER["SCRIPT_NAME"];

	src2viewer($srcpath);
}

main();

?>
