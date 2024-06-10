<?php

$srcpath = '/path/to/Src' ;  // 閲覧したいソースファイルの置き場所.
$bgcolor = '#181818';

$base_url= 'index.php';                  // CGI設置URL.

$git_url     = '';                       // Git連携の場合の参照先URL.
$redmine_url = '';                       // Redmine連携の場合の参照先URL.

// 再帰、行番号、拡張子...
$grep_opt = '-r -n --include=*.cpp --include=*.c --include=*.h --include=*.m --include=*.php --include=*.cc --include=*.shader --include=*.py';

// 読み込みサイズ MAX
$FILE_MAX_SIZE = 10*1024*1024 ; // 10 MB

// エンコードチェックサイズ.
$CHECK_SIZE    = 0x4000;


/*******************************************************
 *  ヘッダーHTML文字列(1)
 *******************************************************
 */
$html_header1 = <<< EOM1
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset='utf-8'>

<meta name="viewport" content="width=device-width, initial-scale=1" />
EOM1;

/*******************************************************
 *  ヘッダーHTML文字列(2)
 *******************************************************
 */
$html_header2 = <<< EOM2
<style type="text/css">

a.link    {color: #ffccee;}
a.visited {color: #aaccee;}

a {text-decoration: none; color: #ffccee;}
a.link    {color: #ffccee;}
a.visited {color: #aaccee;}

.list {
	background-color: $bgcolor;
	color: white;
	font-family: sans-serif;
	font-size: 90%;
}

.grep {
	background-color: $bgcolor;
	color: white;
	font-family: 'VL ゴシック',monospace;
	font-size: 90%;
}

.src {
	background-color: $bgcolor;
	color: white;
	font-size: 90%;
	a {text-decoration: none; color: #ffccee;}
	a.link    {color: #ffccee;}
	a.visited {color: #aaccee;}
}

.c.geshi .de1, .c.geshi .de2 {
	font: normal normal 1em/1.2em 'VL ゴシック',monospace; margin:0; padding:0; background:none; vertical-align:top;
}
.c.geshi  {font-family: 'VL ゴシック',monospace;}
.c.geshi .imp {font-weight: bold; color: red;}
.c.geshi li, .c.geshi .li1 {font-weight: normal; vertical-align:top;}
.c.geshi .ln {width:1px;text-align:right;margin:0;padding:0 2px;vertical-align:top;}
.c.geshi .li2 {font-weight: bold; vertical-align:top;}
.c.geshi .kw1 {color: #4c9cd6;}
.c.geshi .kw2 {color: #c397d8;}
.c.geshi .kw3 {color: #c397d8;}
.c.geshi .kw4 {color: #4c9cd6;}
.c.geshi .co1 {color: #57b64a;}
.c.geshi .co2 {color: #e69d85;}
.c.geshi .coMULTI {color: #57b64a;}
.c.geshi .es0 {color: #c0c099;}
.c.geshi .es1 {color: #c0c099;}
.c.geshi .es2 {color: #66c099;}
.c.geshi .es3 {color: #66c099;}
.c.geshi .es4 {color: #66c099;}
.c.geshi .es5 {color: #c06699;}
.c.geshi .br0 {color: #c099c0;}
.c.geshi .sy0 {color: #339933;}
.c.geshi .st0 {color: #c0e0e0;}
.c.geshi .nu0 {color: #e78c45;}
.c.geshi .nu6 {color: #e78c45;}
.c.geshi .nu8 {color: #e78c45;}
.c.geshi .nu12 {color: #e78c45;}
.c.geshi .nu16 {color: #e78c45;}
.c.geshi .nu17 {color: #e78c45;}
.c.geshi .nu18 {color: #e78c45;}
.c.geshi .nu19 {color: #e78c45;}
.c.geshi .me1 {color: #c0c0c0;}
.c.geshi .me2 {color: #c0c0c0;}
.c.geshi span.xtra { display:block; }
</style>

</head>

<body bgcolor=$bgcolor>
EOM2;

?>
