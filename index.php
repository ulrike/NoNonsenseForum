<?php //display the index of threads in a folder

include "shared.php";

/* ====================================================================================================================== */

//which folder to show, not present for forum index. we have to change directory for `is_dir` to work,
//see <uk3.php.net/manual/en/function.is-dir.php#70005>
if ($path = preg_match ('/([^.\/]+)\//', @$_GET['path'], $_) ? $_[1] : '') chdir (APP_ROOT.$path);

//page number, obviously
$page = preg_match ('/^[0-9]+$/', @$_GET['page']) ? (int) $_GET['page'] : 1;

//submitted info for making a new thread
$NAME	= mb_substr (trim (stripslashes (@$_POST['username'])), 0, 18,    'UTF-8');
$PASS	= mb_substr (      stripslashes (@$_POST['password']),  0, 20,    'UTF-8');
$TITLE	= mb_substr (trim (stripslashes (@$_POST['title']   )), 0, 80,    'UTF-8');
$TEXT	= mb_substr (trim (stripslashes (@$_POST['text']    )), 0, 32768, 'UTF-8');

//has the user the submitted a new thread?
if ($SUBMIT = @$_POST['submit']) if (
	//`APP_ENABLED` (in 'shared.php') can be toggled to disable posting
	//the email check is a fake hidden field in the form to try and fool spam bots
	APP_ENABLED && @$_POST['email'] == "example@abc.com" && $NAME && $PASS && $TITLE && $TEXT
	&& checkName ($NAME, $PASS)
) {
	//the file on disk is a simplified version of the title
	$file = flattenTitle ($TITLE);
	//include the folder if present
	$url  = ($path ? rawurlencode ($path).'/' : '').$file;
	//if this file already exists (double-submission from back button?), redirect to it
	if (file_exists ("$file.xml")) header ("Location: http://".$_SERVER['HTTP_HOST']."/$url", true, 303);
	
	//write out the new thread as an RSS file
	file_put_contents ("$file.xml", template_tags (TEMPLATE_RSS, array (
		'TITLE'	=> htmlspecialchars ($TITLE, ENT_NOQUOTES, 'UTF-8'),
		'URL'	=> $url,
		'NAME'	=> htmlspecialchars ($NAME, ENT_NOQUOTES, 'UTF-8'),
		'DATE'	=> gmdate ('r'),
		'TEXT'	=> htmlspecialchars (formatText ($TEXT), ENT_NOQUOTES, 'UTF-8'),
	)));
	
	//create RSS thread for this folder (a feed of the newest threads)
	createRSSIndex ($path);
	
	//redirect to newley created thread
	header ("Location: http://".$_SERVER['HTTP_HOST']."/$url", true, 303);
}

/* ====================================================================================================================== */

//write the website header:
echo template_tags (TEMPLATE_HEADER, array (
	//HTML `<title>`
	'TITLE'		=> ($path ? htmlspecialchars ($path, ENT_NOQUOTES, 'UTF-8') : 'Forum Index').
		   	   ($page > 1 ? " · Page $page" : ""),
	'RSS_URL'	=> 'index.rss',
	'RSS_TITLE'	=> $path ? htmlspecialchars ($path, ENT_COMPAT, 'UTF-8') : "Forum Index",
	'NAV'		=> template_tags (TEMPLATE_HEADER_NAV, array (
		'MENU'	=> TEMPLATE_INDEX_MENU,
		'PATH'	=> $path ? template_tag (TEMPLATE_INDEX_PATH_FOLDER,
				'PATH', htmlspecialchars ($path, ENT_NOQUOTES, 'UTF-8')
			) : TEMPLATE_INDEX_PATH
	))
));

/* ---------------------------------------------------------------------------------------------------------------------- */

//get a list of folders
if ($folders = array_filter (
	//include only directories, but ignore directories starting with ‘.’ and the users / theme folders
	preg_grep ('/^(\.|users$|theme$)/', scandir ('.'), PREG_GREP_INVERT), 'is_dir'
)) {
	//string together the list
	foreach ($folders as $folder) {
		@$html .= template_tags (TEMPLATE_INDEX_FOLDER, array (
			'URL'	=> rawurlencode ($folder),
			'FOLDER'=> htmlspecialchars ($folder, ENT_NOQUOTES, 'UTF-8')
		));
	}
	
	//output
	echo template_tag (TEMPLATE_INDEX_FOLDERS, 'FOLDERS', $html); $html = "";
}

//get list of threads
$threads = array_fill_keys (preg_grep ('/\.xml$/', scandir ('.')) , 0);
foreach ($threads as $file => &$date) $date = filemtime ($file);
arsort ($threads, SORT_NUMERIC);

if ($threads) {
	//does this folder have a sticky list?
	$stickies = array ();
	if (file_exists ("sticky.txt")) {
		$stickies = array_fill_keys (file ("sticky.txt", FILE_IGNORE_NEW_LINES + FILE_SKIP_EMPTY_LINES), 0);
		foreach ($stickies as $sticky => &$date) $date = filemtime ($sticky);
		arsort ($stickies, SORT_NUMERIC);
		
		//remove the stickies from the thread list, then add them to the top of the list
		$threads = $stickies + array_diff_key ($threads, $stickies);
	}
	
	//paging (stickies are not included in the count as they appear on all pages)
	$pages = ceil ((count ($threads) - count ($stickies)) / APP_THREADS);
	$threads = $stickies + array_slice (array_diff_key ($threads, $stickies), ($page-1) * APP_THREADS, APP_THREADS);
	
	foreach ($threads as $file => $date) {
	
		$xml = simplexml_load_file ($file);
		$items = $xml->channel->xpath ('item');
		$last = reset ($items);
		
		@$html .= template_tags (TEMPLATE_INDEX_THREAD, array (
			'URL'      => flattenTitle ($xml->channel->title),
			'PAGE'     => count ($items) > 1 ? ceil ((count ($items) -1) / APP_POSTS) : 1,
			'STICKY'   => array_key_exists ($file, $stickies) ? TEMPLATE_STICKY : '',
			'TITLE'    => $xml->channel->title,
			'COUNT'    => count ($items),
			'DATEITME' => date ('c', strtotime ($last->pubDate)),
			'TIME'     => strtoupper (date (DATE_FORMAT, strtotime ($last->pubDate))),
			'NAME'     => $last->author
		));
	}
	
	echo template_tags (TEMPLATE_INDEX_THREADS, array (
		'THREADS' => $html,
		'PAGES'   => pageLinks ($page, $pages)
	)); $html = "";
}

/* ---------------------------------------------------------------------------------------------------------------------- */

//the new thread form
echo APP_ENABLED ? template_tags (TEMPLATE_INDEX_FORM, array (
	'NAME'	=> htmlspecialchars ($NAME,  ENT_COMPAT, 'UTF-8'),
	'PASS'	=> htmlspecialchars ($PASS,  ENT_COMPAT, 'UTF-8'),
	'TITLE'	=> htmlspecialchars ($TITLE, ENT_COMPAT, 'UTF-8'),
	'TEXT'	=> htmlspecialchars ($TEXT,  ENT_COMPAT, 'UTF-8'),
	'ERROR'	=> !$SUBMIT ? ERROR_NONE	//no problem? show default help text
		   : (!$NAME  ? ERROR_NAME	//the name is missing
		   : (!$PASS  ? ERROR_PASS	//the password is missing
		   : (!$TITLE ? ERROR_TITLE	//the title is missing
		   : (!$TEXT  ? ERROR_TEXT	//the message text is missing
		   : ERROR_AUTH))))		//the name / password pair didn’t match
)) : TEMPLATE_INDEX_FORM_DISABLED;

//and we’re all done
echo TEMPLATE_FOOTER;

?>