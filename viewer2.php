<?php

header('Content-Type: text/html; charset=UTF-8');

$search_word = '';

$tab4='&nbsp;&nbsp;&nbsp;&nbsp;';

/* 以下の２つは呼び出し元から引き継ぐ固定値.
 global $srcpath;  // 閲覧したいソースファイルの置き場所ROOT '/'で終わる.
 global $base_url; // このCGIの設置URL 「.php」 で終わる.
 */
/*******************************************************
 *  ホームアイコンのリンクを用意.
 *******************************************************
 */
function mk_form($path,$subpath,$url1)
{
	global $base_url;
	global $git_url;
	global $redmine_url;
	global $tab4;
	global $search_word;
	
	$path    = cut_topslash($path);
	$subpath = cut_topslash($subpath);

	$s  = '<form action="' . $base_url . '/' . $subpath . '?cmd=search" method="post">';
	$s .= '<a href="' . $base_url .                   '">□</a>';
	$s .= '<a href="' . $base_url . '/'  . $subpath . '">一覧</a>&nbsp;';
	$s .= '<a href="' . $base_url . '/'  . '">/</a>';
	$s .= $url1 . $tab4 ;
	if( $git_url != '') {                       // Git連携の場合の参照先URL.
		$s .= '<a href="' . $git_url . $path  . '">' . '[Gitで見る]' . '</a>';
	}
	if( $redmine_url != '') {                       // Redmine連携の場合の参照先URL.
		$s .= '<a href="' . $redmine_url . $path  . '">' . '[RedMine]' . '</a>';
	}
	$s .= '<input type="text" name="word" value="' . $search_word . '" size="20" />';
	$s .= '<input type="submit" value="検索" />';
	$s .= '<input type="checkbox" name="case" value="1" />大文字小文字の同一視';
	$s .= '</form>';

	
	return $s;
}

/*******************************************************
 *  PATH_INFO を先頭dir と それ以外に分割する.
 *******************************************************
 */
function get_subinfo($base,$s)
{
	// まず、$s の先頭1文字は'/' であると仮定する.
	// それを除いた'/' を探して、その間の文字列を最初のdirとする.
	if($s == '') {
		return array('','');
	}
	$n = strpos($s,'/',1);
	if($n == false) {
		$s = $s . '/';
		$n = strpos($s,'/',1);
	}
	
	$d1 = substr($s,1,$n-1); // 最初のディレクトリ(プロジェクトDirのこと).
	$d2 = substr($s,$n);     // 最初のディレクトリを除いたパス名.

	if($d2 == '/') { // トリッキー.
		if(!is_dir($base . $d1)) {
			return array($d2,$d1);
		}
	}
	return array($d1,$d2);
}


function modify_lang($s,$ext)
{
	$lang = '.' . $ext . '.';
	return str_replace('.c.',$lang,$s);
}

/*******************************************************
 *  ヘッダー出力関数
 *******************************************************
 */
function head1($path,$subpath,$url1)
{
	global $html_header1;
	global $html_header2;
	global $search_word;
	
	// 行頭の '/' はトル.
	//if(substr($title,0,1)=='/') {$title=substr($title,1);}
	
	// パス名をトル.
	$title = cut_path1($path);

	$ext1 = getext1($path); // 拡張子から言語推定
	
	echo   $html_header1;
	echo   "<title>$title</title>\n";

//	echo   $html_header2;
	echo   modify_lang($html_header2,$ext1);

	//
	// ファイル名を表示::
	//
	echo "<div class=\"list\"><font color=#eeeeee>\n";
	$search_word = $_POST['word'];

	echo mk_form($path,$subpath,$url1);
	echo "</div>\n\n";
}

/*******************************************************
 *  ディレクトリ文字列作成
 *******************************************************
 */
function splitdir1($dir)
{
	if($dir==''){
		return '/';
	}

	$n=strlen($dir);
	if(substr($dir,$n-1,1)!='/') {
		$dir = $dir . '/';
	}
	return $dir;
}

/*******************************************************

 *******************************************************
 */
function cut_topslash($dir)
{
	if(substr($dir,0,1)=='/') {
		$dir = substr($dir,1);
	}
	return $dir;
}

/*******************************************************
 *  ファイル名を URLリンクにする
 *******************************************************
 */
function mk_url1($dir,$path1)
{
	global  $base_url;
	if($dir==''){
		return $dir;
	}
	return '<a href="' . $base_url . '/' . $path1 . '">' . $dir . "</a>";
}

/*******************************************************
 *  ディレクトリ名とファイル名をそれぞれ URLリンクにする
 *******************************************************
 */
function mk_url($dir)
{
	if($dir==''){
		return $dir;
	}
	$dir1=explode('/',$dir);
	$n = count($dir1);
	$s = '';
	$p = '';
	for($i=0;$i<$n;$i++) {
		$sep = '/';
		if($p=='') {$sep='';}
		$p = $p . $sep . $dir1[$i];
		$s = $s . $sep . mk_url1($dir1[$i],$p);
	}
	return $s;
}

/*******************************************************
 *  ディレクトリ名とファイル名をそれぞれ URLリンクにする
 *******************************************************
 */
function mkurl_for_DL($dir)
{
	if($dir==''){
		return $dir;
	}
	$dir1=explode('/',$dir);
	$n = count($dir1);
	$s = '';
	$p = '';
	for($i=0;$i<$n;$i++) {
		$sep = '/';
		if($p=='') {$sep='';}
		$p = $p . $sep . $dir1[$i];
		if($i==($n-1)) {
			$s = $s . $sep . mkurlDL($dir1[$i],$p);
		}else{
			$s = $s . $sep . mk_url1($dir1[$i],$p);
		}
	}
	return $s;
}

/*******************************************************
 *  拡張子により非表示にしたいファイル?
 *******************************************************
 */
function ignore_ext($name)
{
	$ext = strrchr($name,'.');
	if($ext == '.meta') {
		return TRUE;
	}
	return FALSE;
}
/*******************************************************
 *   パス名をトル.
 *******************************************************
 */
function cut_path1($name)
{
	$file = strrchr($name,'/');
	if($file == FALSE) {
		return $name;
	}
	return substr($file,1);
}
/*******************************************************
 *  ディレクトリ一覧
 *******************************************************
 */
function dirlist1($basepath,$subpath,$filename)
{
	global  $base_url;
	global  $tab4;
	$path = $basepath . $subpath . $filename;
	
	$list = scandir($path);
	$dir1 = splitdir1($filename);
	
	echo '<div class="list">';
	foreach( $list as $file1 ) {
		if(ignore_ext($file1)) {continue;} // .metaは除外.

		$path1= $basepath .       $subpath . $dir1 . $file1;
		$url1 = $base_url . '/' . $subpath . $dir1 . $file1;

		$sep1 = '';
		if( $file1 != '..' ) {
			if(is_dir($path1)) { $sep1 = '<font color=YELLOW>/</font>';}
		}else{
			if($subpath == '') {
				continue; // ROOTでは .. で戻ってはいけない.
			}
		}
		$link1 = '<a href="' . $url1 . '">' . $file1 . $sep1 . "</a>\n";
		if($file1 != '.') {
			echo $tab4 . $link1 . "<br>\n";
		}
	}
	echo "</div>\n\n";
	echo "</body>\n";
	echo "</html>\n";
}

/*******************************************************
 *  ファイル読み込み
 *******************************************************
 */
function fget_contents($filename)
{
	global  $FILE_MAX_SIZE;

	$size1 = filesize($filename);
	$maxsize = $FILE_MAX_SIZE;
	if($size1 > $maxsize) {
		return file_get_contents($filename,false,null,0,$maxsize);
	}
	
	return file_get_contents($filename);
}
/*******************************************************
 *  全ファイル一覧
 *******************************************************
 */
function filelist1($basepath,$subpath,$filename)
{
	global  $base_url;
	global  $tab4;

	$path = $basepath . $subpath . $filename;
	
	$filelist = fget_contents($path);
	$list     = explode("\n",$filelist);
	
	$dir1 = $subpath . '/';
	echo '<div class="list">';
	foreach( $list as $file1 ) {
		$url1 = $base_url . '/' . $dir1 . $file1;
		$sep1 = '';
		if(strrchr($file1,'.')==FALSE) {$sep1 = '/';}
		$link1 = '<a href="' . $url1 . '">' . $file1 . $sep1 . "</a>\n";
		if($file1 != '.') {
			echo $tab4 . $link1 . "<br>\n";
		}
	}
	echo "</div>\n\n";
	echo "</body>\n";
	echo "</html>\n";
}

/*******************************************************
 *  
 *******************************************************
 */
function mk_png_url($filename)
{
	global  $base_url;

	$path = $filename;
	$path2 = $path . "?P=1";
	return '<img src="' . $base_url . $path2 . '">' ;
}
/*******************************************************
 *  ファイル名を DLリンクにする
 *******************************************************
 */
function mkurlDL($dir,$path1)
{
	global  $base_url;
	if($dir==''){
		return $dir;
	}
	return '<a href="' . $base_url . '/' . $path1 . '?P=2"> ' . $dir . "(Click to DOWNLOAD) </a>";
}

/*******************************************************
 *  ビューワー本体
 *******************************************************
 */
function viewer1($basepath,$subpath,$filename)
{
	require_once 'geshi/geshi.php';
	$fromLang='SJIS';
	$toLang='UTF-8';

	$path = $basepath . $subpath . $filename;
	$ext  = getext1($filename); // 拡張子から言語推定
		
	global $html_foot;

	$source  = fget_contents($path);
	$txtmode = is_utf8text($source);  // バイナリーファイルなら 0.
	if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $source)) {
		// UTF-8 BOMを削除する.
		$source = substr($source,3);
	} else {
		if( $txtmode == 0 ) {
			// UTF-8 でなかった.
			$txtmode = is_sjistext($source);  // バイナリーファイルなら 0.
			if($txtmode==1) {
				// SJIS だった.
				if(mb_check_encoding($source,$fromLang)) {
					// SJISなら UTF-8に直す.
					$source = mb_convert_encoding($source,$toLang,$fromLang);
				}
			}
		}
	}
	if($txtmode == 0) { // バイナリー.
		viewer_bin($basepath,$subpath,$filename);
		return;
	}
	// ソースコード、テキスト.
	
	$geshi = new GeSHi($source,$ext);
	$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);
	$geshi->set_overall_class('geshi');
	$geshi->set_tab_width(4);
	$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 10);

	$geshi->set_code_style("");
	$geshi->set_overall_style("");
    $geshi->enable_classes(true);
	$ret= $geshi->parse_code();

	echo '<div class="src">';
	echo   $ret;
	echo "</div>\n";

	echo "</body>\n";
	echo "</html>\n";
}

/*******************************************************
 *  バイナリービューワー
 *******************************************************
 */
function viewer_bin($basepath,$subpath,$filename)
{
	global $html_foot;

	$path = $basepath . $filename;
	$url1 = mkurl_for_DL($filename);
	$option = '';
	$line = ' -l4000 ';
	$omit = "<br>　　・・・(省略)・・・　全表示は?L=0をパラメータに書く.<br>";
	$isdds = 0;
	echo "<div><font color=#eeeeee>\n";
	echo $url1 ."<br>\n";
	echo "</div>\n\n";


	if(is_dir($path)) {
		return dirlist1($basepath,$subpath,$filename);
	}
	if(isset($_GET['L'])) {
		$line = '-s' . $_GET['L'];
	}
	$ext = get_ext($path);
	if(is_png($ext)) {
		$isdds=1;
		if( $line == '') {
			$line = ' -l100 ';
			$omit = "<br>　　・・・(省略)・・・　全表示は?L=0をパラメータに書く.<br>";
		}
		$url2 = mk_png_url($filename);
		echo $url2 . "<br>\n";
	}

	$src = `./xdump $line '$path'`;
	$src = str_replace('<','&lt;',$src);
	$src = str_replace('>','&gt;',$src);

	echo '<div class="dump"><pre>';
	echo $src;
	echo $omit;
	echo "</pre></div>\n\n";
	echo $html_foot;
}

/*******************************************************
 *  MDビューワー本体
 *******************************************************
 */
function pandoc($basepath,$subpath,$filename)
{
	$path = $basepath . $subpath . $filename;
	$ext  = getext1($filename); // 拡張子から言語推定

	$md = `pandoc $path`;
	
	if($md=="") {return -1;}
	
	echo '<div class="src">';
	echo $md;
	echo "</div>\n";

	echo "</body>\n";
	echo "</html>\n";
	return 0;
}

function get_finfo($path)
{
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime  = finfo_file($finfo, $path);
	finfo_close($finfo);
	
	return $mime; // "application/zip" e.t.c.
}

/*
 *     @note 仕様
 *     - 1バイトコード   \u0～  \u7F(0000 0000 0aaa bbbb)-->0aaa bbbb
 *     - 2バイトコード  \u80～ \u7FF(0000 0aaa bbbb cccc)-->110a aabb  10bb cccc
 *     - 3バイトコード \u800～\uFFFF(aaaa bbbb cccc dddd)-->1110 aaaa  10bb bbcc  10cc dddd
 * */

//
//  utf8の1バイト目を見て、1文字当たりのバイト数を返す.
//
function u8size($c)
{
	if($c ==0x09) {return  1;}  // 1byte code;
	if($c ==0x0a) {return  1;}  // 1byte code;
	if($c ==0x0d) {return  1;}  // 1byte code;
	if($c < 0x20) {return -1;}  // ctrl  code;
	if($c < 0x7f) {return  1;}  // ascii code;
	if($c < 0xc0) {return -1;}  // utf8 error;
	if($c < 0xe0) {return  2;}  // 2byte code;
	if($c < 0xf0) {return  3;}  // 3byte code;
	return -1;
}
function u8second($c)
{
	if(($c >=0x80) && ($c < 0xc0)) return 1;
	return -1;
}
/*******************************************************
 *  テキストファイルかどうかを判定する.(UTF-8を仮定してみる)
 *******************************************************
 */
function is_utf8text($str)
{
	global  $CHECK_SIZE;

	$len = strlen($str);
	if($len >= $CHECK_SIZE) {$len=$CHECK_SIZE;}
	for ($pos=0; $pos<$len ; ) {
		$c = substr($str,$pos,1);
		$n = u8size(ord($c));    // printf("c=$n $c\n");
		if($n==(-1)) {return 0;} // NOT UTF-8 Stream.
		if($n>=2) { 
			if(u8second(ord(substr($str,$pos+1,1)))==(-1)) {return 0;} // NOT UTF-8 Stream.
		}
		if($n==3) { 
			if(u8second(ord(substr($str,$pos+2,1)))==(-1)) {return 0;} // NOT UTF-8 Stream.
		}
		$pos += $n;
	}
	return 1; // OK.
}

function sjissize($c)
{
	if($c ==0x09) {return  1;}  // 1byte code;
	if($c ==0x0a) {return  1;}  // 1byte code;
	if($c ==0x0d) {return  1;}  // 1byte code;
	if($c < 0x20) {return -1;}  // ctrl  code;
	if($c < 0x7f) {return  1;}  // ascii code;
	if(($c >= 0x81) && ($c <0xa0)) {return  2;}//SJIS
	if(($c >= 0xe0) && ($c <0xfd)) {return  2;}//SJIS
	return -1;
}
function sjissecond($c)
{
	if(($c >=0x40) && ($c < 0xfd)) return 1;
	return -1;
}
/*******************************************************
 *  テキストファイルかどうかを判定する.(SJIS を仮定してみる)
 *******************************************************
 */
function is_sjistext($str)
{
	global  $CHECK_SIZE;

	$len = strlen($str);
	if($len >= $CHECK_SIZE) {$len=$CHECK_SIZE;}
	for ($pos=0; $pos<$len ; ) {
		$c = substr($str,$pos,1);
		$n = sjissize(ord($c));  //printf("c=$n $c\n");
		if($n==(-1)) {return 0;} // NOT UTF-8 Stream.
		if($n>=2) { 
			if(sjissecond(ord(substr($str,$pos+1,1)))==(-1)) {return 0;} // NOT SJIS Stream.
		}
		$pos += $n;
	}
	return 1; // OK.
}


/*******************************************************
 *  ビューワー本体
 *******************************************************
 */
function view_html($basepath,$subpath,$filename)
{
	global $html_foot;

	$path = $basepath . $filename;
	$url1 = mk_url($filename);
	$option = '';
	$line = '';
	$omit = '';
	$isdds = 0;

	header('Content-Type: text/html; charset=UTF-8');

	if(is_dir($path)) {
		echo "<div><font color=#eeeeee>\n";
		echo $url1 ."<br>\n";
		echo "</div>\n\n";
		return dirlist1($basepath,$subpath,$filename);
	}
	$source = fget_contents($path);
	echo $source;
	echo $html_foot;
}

function sanitize1($s)
{
	$s = str_replace('&','&amp;',$s);
	$s = str_replace('<','&lt;',$s);
	$s = str_replace('>','&gt;',$s);
	$s = str_replace(' ','&nbsp;',$s);
	return $s;
}

function escape_s_quote($s)
{
	$s = str_replace("'" ,'\\' . "'" ,$s);
	return $s;
}
/*******************************************************
 *  検索
 *******************************************************
 */
function grep1($basepath,$subpath,$filename)
{
	global  $base_url;
	global  $tab4;
	global  $grep_opt;

	$subpath = cut_topslash($subpath);

	$path = $basepath . $filename;
	
	$filelist = fget_contents($path);
	$list     = explode("\n",$filelist);
	$word     = $_POST['word'];
	$case     = $_POST['case'];
	$result   = [];
	$ret      = 1;
	$case_opt = '';
	
	if($case == '1' ) {$case_opt = ' -i';}
	
	echo '<div class="grep">';
	echo 'Grep:' . $word . "<br>\n";
	
	$word1 = escape_s_quote($word);
	chdir($basepath . $subpath);
	$cmds = 'grep ' . $grep_opt . $case_opt . " '" . $word1 . "'";
	exec($cmds , $result , $ret);

	$dir1 = $subpath . '/';
	echo "<table>\n";
	foreach( $result as $line1 ) {
		$aa = explode(':',$line1,3); // 3分割
		$file1= $aa[0];
		$num1 = $aa[1];
		$str1 = sanitize1($aa[2]);
		
		$url1 = $base_url . '/' . $dir1 . $file1 . '#' . $num1;
		$sep1 = '';
		if(strrchr($file1,'.')==FALSE) {$sep1 = '/';}
		$link1 = '<a href="' . $url1 . '">' . $file1 . " ($num1)" .$sep1 
		       . "</a> $tab4 | $str1\n";
		echo "<tr><td nowrap>" . $link1 . "</td></tr>\n";
	}
	echo "</table>\n";
	echo '<hr>' . "<br>\n";
	echo "</div>\n\n";
	echo "</body>\n";
	echo "</html>\n";
}


/*******************************************************
 *  拡張子から言語推定
 *******************************************************
 */
function getext1($filename)
{
	$e=strrchr($filename,'.');
	$e=strtolower($e);
	if($e==FALSE)  {	return "text";	}
	if($e=='.cc')  {	return "cpp";	}
	if($e=='.cpp') {	return "cpp";	}
	if($e=='.css') {	return "css";	}
	if($e=='.c')   {	return "c";	}
	if($e=='.h')   {	return "c";	}
	if($e=='.m')   {	return "c";	}
	if($e=='.md')  {	return "md";	}
	if($e=='.asm') {	return "asm";	}
	if($e=='.cs')  {	return "csharp";}
	if($e=='.lisp'){	return "lisp";  }
	if($e=='.lua') {	return "lua";	}
	if($e=='.php') {	return "php";	}
	if($e=='.py')  {	return "python";}
	if($e=='.perl'){	return "perl";	}
	if($e=='.pl')  {	return "perl";	}
	if($e=='.php') {	return "php";	}
	if($e=='.evt') {	return "c";	}
	if($e=='.java'){	return "java";}
	if($e=='.js') {		return "javascript";}
	if($e=='.ts') {		return "javascript";}
	if($e=='.tsx') {	return "javascript";}

	return 'text';
}

function get_ext($dir)
{
	if($dir==''){
		return $dir;
	}

	$n=strrchr($dir,'.');
    if($n != FALSE) {
		return $n; // えーーーーPHP
	}
    return $dir;
}

function is_html($ext)
{
	$ext = strtolower($ext);
	if($ext == '.htm') {return 1;}
	if($ext == '.html')  {return 1;}
	return 0;
}
function is_png($ext)
{
	$ext = strtolower($ext);
	if($ext == '.png') {return 1;}
	if($ext == '.jpg') {return 1;}
	if($ext == '.jpeg') {return 1;}
	if($ext == '.gif') {return 1;}
	return 0;
}
/*******************************************************
 *  ビューワー 動作モード判定
 *******************************************************
   $basepath : ソースコード置き場の設置 rootdir (Linux上の)
               最後に'/' が付いている.
   $subpath  : ソースコード置き場のsubdir名 (先頭の階層dir名)
               '/' は一切付いていない.
   $filename : subdirの後ろのディレクトリを含むパス名. 必ず'/'で始まる.

               上記を順番に連結した文字列がLinux上のフルパス名となる.
 *******************************************************
 */
function src2viewer($basepath)
{
	// PATH_INFO を解析.
	$path_info = $_SERVER["PATH_INFO"];
	$row = get_subinfo($basepath,$path_info);
	$subpath  = $row[0];
	$filename = $row[1];
	
	$path = $basepath . $subpath . $filename;
	$url1 = mk_url($subpath . $filename);
	$option = '';
	$title = 'Php Source Browser';
	// 検索?
	if($_REQUEST['cmd']=='search') {
		head1($title,$subpath,$url1);
		printf("<hr>:%s:%s:%s:<hr>",$basepath,$subpath,$filename);
		return grep1($basepath,$subpath,$filename);
	}
	// ディレクトリ?
	if(is_dir($path)) {
		head1($title,$subpath,$url1);
		//printf("<hr>:%s:%s:%s:<hr>",$basepath,$subpath,$filename);
		return dirlist1($basepath,$subpath,$filename);
	}
	// 全ファイル一覧?
	$n=strpos($filename,'filelist');
	if($n != false) {
		if($n > 0) {
			head1($filename,$subpath,$url1);
			//printf("<hr>:%s:%s:%s:<hr>",$basepath,$subpath,$filename);
			return filelist1($basepath,$subpath,$filename);
		}
	}
	// ソースコード表示.

	$ext = get_ext($filename);
	if(is_html($ext)) {
		return view_html($basepath,$filename);
	}
	if(isset($_GET['R'])) { //生データを取得
		$source = fget_contents($path);
		echo $source;
		return;
	}
	if(isset($_GET['P'])) { //生データを画像として取得
		$ctype = 'image;';
		if($_GET['P']=="2") {
			$ctype = 'binary;';
		}
		header('Content-Type: ' . $ctype);
		$source = fget_contents($path);
		echo $source;
		return;
	}

	head1($filename,$subpath,$url1);
	//printf("<hr>:%s:%s:%s:<hr>",$basepath,$subpath,$filename);
	$extmode = getext1($path);
	if($extmode == "md") {
		$rc = pandoc($basepath,$subpath,$filename); // MarkDown ビューワー
		if($rc!=(-1)) {
			return 0;
		}
	}
	return viewer1($basepath,$subpath,$filename); // ソースコード・ビューワー
}

