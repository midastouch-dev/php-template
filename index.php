<?php
/**
 * Run PHP Code
 * 
 * This script gives you the ability to quickly test snippets of PHP code locally.
 *
 * @copyright  Copyright 2011-2012, Website Duck LLC (http://www.websiteduck.com)
 * @link       http://github.com/websiteduck/Run-PHP-Code Run PHP Code
 * @license    MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
//This application is meant to be run locally and should not be made publicly accessible.
if (!in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) die();

define('NL', PHP_EOL);

if (isset($_POST['phprun_action']) && $_POST['phprun_action'] == 'download') {
	if (substr($_POST['phprun_filename'], -4) !== '.php') $_POST['phprun_filename'] .= '.php';
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename=' . $_POST['phprun_filename']);
	echo $_POST['phprun_code'];
	die();
}

if (isset($_POST['phprun_action']) && $_POST['phprun_action'] == 'run') {
	header('Expires: Mon, 16 Apr 2012 05:00:00 GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); 
	header('Cache-Control: no-store, no-cache, must-revalidate'); 
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');
	header('X-XSS-Protection: 0');
	ini_set('display_errors', 1);
	switch ($_POST['error_reporting'])
	{
		case 'fatal': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR); break;
		case 'warning': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING); break;
		case 'deprecated': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED); break;
		case 'notice': error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_WARNING | E_DEPRECATED | E_USER_DEPRECATED | E_NOTICE); break;
		case 'all': error_reporting(-1); break;
		case 'none': default: error_reporting(0); break;
	}
	$phprun_code = '?>' . ltrim($_POST['phprun_code']);
	ob_start();
	eval($phprun_code);
	$phprun_html = ob_get_clean();
	if (isset($_POST['pre_wrap'])) $phprun_html = '<pre>' . $phprun_html . '</pre>';
	if (isset($_POST['colorize'])) $phprun_html = '<link rel="stylesheet" href="css/colorize.css">' . $phprun_html;
	echo $phprun_html;
	die();
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Run PHP Code</title>
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="js/ace/ace.js" charset="utf-8"></script>
		<script type="text/javascript" src="js/run_php_code.js"></script>

		<link rel="shortcut icon" href="favicon.ico" >
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/run_php_code.css">
	</head>
	<body>		
		<form id="run_php_form" method="POST" action="" target="result_frame" onsubmit="run_php_form_submit()">
			<input type="hidden" name="phprun_action" value="run" />
			<input type="hidden" name="phprun_filename" value="" />
			<div id="title_bar">
				<div id="title">Run PHP Code</div>
				
				<div class="drop"><span>File</span>
					<div>
						<button class="button" id="btn_import" type="button">Remote Import...</button>
						<button class="button" id="btn_save" type="button">Save...</button>
					</div>
				</div
				><div class="drop"><span>Options</span>
					<div>
						<input type="checkbox" id="mnu_colorize" name="colorize" /><label for="mnu_colorize"><span></span> Colorize</label>
						<input type="checkbox" id="mnu_external_window" /><label for="mnu_external_window"><span></span> External Window</label>
						<input type="checkbox" id="mnu_pre_wrap" name="pre_wrap" /><label for="mnu_pre_wrap"><span></span> &lt;pre&gt;</label>
					</div>
				</div
				><div class="drop">
					<span><i class="icon-question"></i></span>
					<div id="help_window">
						<h2>Run PHP Code</h2>
						<p>Ctrl-Enter to Run Code</p>
						
						<p>
							<img src="img/website_duck.png" alt="" style="width: 50px; height: 50px;" /><br />
							Website Duck LLC<br />
						</p>
						
						<a class="button" href="https://github.com/websiteduck/Run-PHP-Code">GitHub Repo</a>
					</div>
				</div>
					
				<div id="button_container">
					<label>
						Error Reporting
						<select name="error_reporting">
							<option value="none">None</option>
							<option value="fatal" selected="selected">Fatal</option>
							<option value="warning">Warning</option>
							<option value="deprecated">Deprecated</option>
							<option value="notice">Notice</option>
							<option value="all">All</option>
						</select>
					</label>
					<button class="button" type="button" id="btn_reset"><i class="icon-eraser"></i> &nbsp; Clear</button>
					<button class="button" type="button" id="btn_run" title="Run (Ctrl+Enter)">Run &nbsp; <i class="icon-play"></i></button>
				</div>
			</div>
			
			<div id="code_div"></div>
			<input type="hidden" id="phprun_code" name="phprun_code" />
		</form>
		
		<div id="result_div"><iframe id="result_frame" name="result_frame"></iframe></div>		
		<div id="resize_bar"></div>
		
	</body>
</html>