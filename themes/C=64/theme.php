<?php //defines the website theme, keeping HTML in one place
/* ====================================================================================================================== */
/* NoNonsenseForum © Copyright (CC-BY) Kroc Camen 2010
   licenced under Creative Commons Attribution 3.0 <creativecommons.org/licenses/by/3.0/deed.en_GB>
   you may do whatever you want to this code as long as you give credit to Kroc Camen, <camendesign.com>
*/

/* how the theme works:
   ====================================================================================================================== */
/* to keep the PHP and HTML sparate we put HTML chunks into constants and use search and replace (via `template_tag` and
   `template_tags` in shared.php) to swap out “tags” in the form of “&__TAG__;” with the data from the PHP, or in other
   instances with another template. this keeps the PHP logic separate from the HTML it is outputting

   tags may be used once, more than once, or not at all in your templates */


/* common strings used throughout or for non-HTML purposes
   ---------------------------------------------------------------------------------------------------------------------- */
//the `date` format code used to print human readable dates into the HTML,
//see <php.net/manual/en/function.date.php> for documentation
define ('DATE_FORMAT', "d-M'y H:i");

//the HTML `<title>` string
define ('TEMPLATE_HTMLTITLE_SLUG',		'Camen Design Forum');		//always first
define ('TEMPLATE_HTMLTITLE_NAME',		' * &__NAME__;');		//added next, name of folder or thread
define ('TEMPLATE_HTMLTITLE_PAGE',		' * Page &__PAGE__;');		//added next, current page number
define ('TEMPLATE_HTMLTITLE_DELETE_THREAD',	' * Delete Thread?');		//on delete.php
define ('TEMPLATE_HTMLTITLE_DELETE_POST',	' * Delete Post?');		//on delete.php

//prepended to the thread title for each reply (like in e-mail)
define ('TEMPLATE_RE',				'RE: ');


/* the opening HTML and website header
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by index.php, thread.php & delete.php
   tags:
	&__HTMLTITLE__;	HTML `<title>`, see TEMPLATE_HTMLTITLE_* for construction
	&__RSS__;	URL to RSS feed for the current page
	&__ROBOTS__;	on delete pages, TEMPLATE_HEADER_ROBOTS is inserted here (to tell crawlers to ignore delete pages)
	&__NAV__;	a placeholder for a menu used on index / thread pages, but not delete / edit pages
			(see `TEMPLATE_HEADER_NAV` below)
*/
define ('TEMPLATE_HEADER', <<<HTML
<!doctype html>
<html><head>
	<meta charset="utf-8" />
	<title>&__HTMLTITLE__;</title>
	<link rel="stylesheet" href="/themes/C=64/theme.css" />
	<link rel="alternate" type="application/rss+xml" href="&__RSS__;" />
	<meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no" />&__ROBOTS__;
</head><body>

<header>
	<hgroup>
		<h1>**** Camen Design Forums v2 ****</h1>
		<h2>Copyright (CC-BY) 1984-2010 Kroc Camen</h2>
	</hgroup>
	<p>READY.</p>&__NAV__;
</header>

HTML
);
/* attached to:
	&__ROBOTS__;	TEMPLATE_HEADER
   tags:
	none
*/
define ('TEMPLATE_HEADER_ROBOTS', <<<HTML

	<meta name="robots" content="noindex, nofollow" />
HTML
);

//the nav menu for RSS, new / reply links
//some pages (like edit / delete) won’t include this (see above)
/* attached to:
	&__NAV__;	TEMPLATE_HEADER
   tags:
	&__MENU__;	`TEMPLATE_INDEX_MENU` is inserted here on index pages, and `TEMPLATE_THREAD_MENU` on thread pages
	&__PATH__;	the navigation heirarchy is placed here, this differs depending on a couple of factors:
			-	`TEMPLATE_INDEX_PATH` if on the home page
			-	`TEMPLATE_INDEX_PATH_FOLDER` if in a folder (links back to home page)
			-	`TEMPLATE_THREAD_PATH` on thread pages (in root folder)
			-	`TEMPLATE_THREAD_PATH_FOLDER` on threads in sub-folders (links back to folder)
*/
define ('TEMPLATE_HEADER_NAV', <<<HTML

	<nav>
&__MENU__;
&__PATH__;
	</nav>
HTML
);

//the menu items for the index page
/* attached to:
	&__MENU__;	TEMPLATE_HEADER_NAV
   tags:
	none 
*/
define ('TEMPLATE_INDEX_MENU', <<<HTML
		<a href="#new">Add Thread</a>
		<a href="index.rss">RSS</a>
HTML
);

//the menu items for a thread page
/* attached to:
	&__MENU__;	TEMPLATE_HEADER_NAV
   tags:
	&__RSS__;	URL to the RSS feed for this thread (the thread’s filename ending in “.xml”)
*/
define ('TEMPLATE_THREAD_MENU', <<<HTML
		<a href="#reply">Reply</a>
		<a href="&__RSS__;">RSS</a>
HTML
);

//the path navigation (on index pages), when on the home page
/* attached to:
	&__PATH__;	TEMPLATE_HEADER_NAV
   tags:
	none
*/
define ('TEMPLATE_INDEX_PATH', <<<HTML
		<ol>
			<li>• Index:</li>
		</ol>
HTML
);
//the path navigation (on index pages), when in a folder
/* attached to:
	&__PATH__;	TEMPLATE_HEADER_NAV
   tags:
	&__PATH__;	the name of the folder being viewed, HTML encoded
*/
define ('TEMPLATE_INDEX_PATH_FOLDER', <<<HTML
		<ol>
			<li>
				<a href="/">Index</a>
				<ol><li>&__PATH__;:</li></ol>
			</li>
		</ol>
HTML
);

//the path navigation (on thread pages)
/* attached to:
	&__PATH__;	TEMPLATE_HEADER_NAV
   tags:
	none
*/
define ('TEMPLATE_THREAD_PATH', <<<HTML
		<ol>
			<li><a href="/">Index</a></li>
		</ol>
HTML
);
//the path navigation (on thread pages), when in a folder
/* attached to:
	&__PATH__;	TEMPLATE_HEADER_NAV
   tags:
	&__URL__;	URL to the folder the thread is within
	&__PATH__;	HTML encoded name of the folder
*/
define ('TEMPLATE_THREAD_PATH_FOLDER', <<<HTML
		<ol>
			<li>
				<a href="/">Index</a>
				<ol><li><a href="&__URL__;">&__PATH__;</a></li></ol>
			</li>
		</ol>
HTML
);

/* the folders list on index pages
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into page by index.php
   tags:
	&__FOLDERS__;	a generated list of folders (see TEMPLATE_INDEX_FOLDER)
*/
define ('TEMPLATE_INDEX_FOLDERS', <<<HTML
<h2 id="folders">Folders</h2>
<dl>
&__FOLDERS__;</dl>

HTML
);
//a folder (appended in sequence)
/* attached to:
	&__FOLDERS__;	TEMPLATE_INDEX_FOLDERS
   tags:
	&__URL__;	URL of folder
	&__FOLDER__;	name of folder, HTML encoded
*/
define ('TEMPLATE_INDEX_FOLDER', <<<HTML
	<dt><a href="&__URL__;">&__FOLDER__;</a></dt>

HTML
);

/* the threads list on index pages (including page list)
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into page by index.php
   tags:
	&__THREADS__;	a generated list of thread links, see TEMPLATE_INDEX_THREAD
	&__PAGES__;	a generated list of page links, see TEMPLATE_PAGES_*
*/
define ('TEMPLATE_INDEX_THREADS', <<<HTML
<h2 id="list">Threads</h2>
<form method="get" action="http://google.com/search">
	Search
	<input type="hidden" name="as_sitesearch" value="${_SERVER['HTTP_HOST']}" /><!--
	--><input type="search" name="as_q" /><!--
	--><input type="submit" value="✓" />
</form>

<dl>
&__THREADS__;</dl>
<nav class="pages">
	Page &__PAGES__;
</nav>

HTML
);
//a thread link on an index page (appended in sequence)
/* attached to:
	&__THREADS__;	TEMPLATE_INDEX_THREADS
   tags:
	&__URL__;	URL to the thread
	&__STICKY__;	added to sticky threads to mark them as such (see `TEMPLATE_STICKY` below)
	&__TITLE__;	title of the thread, HTML encoded
	&__COUNT__;	number of posts in thread (including OP)
	&__DATETIME__;	timestamp (of last post in the thread) in "Sun, 17 Oct 2010 19:41:09 +0000" format
			for HTML5 `<time>` datetime attribute
	&__TIME__;	human-readable timestamp
	&__AUTHOR__;	name of last poster in thread
*/
define ('TEMPLATE_INDEX_THREAD', <<<HTML
	<dt><a href="&__URL__;"&__STICKY__;>&__TITLE__;</a> (&__COUNT__;)</dt>
	<dd>
		<time datetime="&__DATETIME__;">&__TIME__;</time>
		<b>&__AUTHOR__;</b>
	</dd>

HTML
);
//added to a thread to make it sticky
/* attached to:
	&__STICKY__;	TEMPLATE_INDEX_THREAD
   tags:
	none
*/
define ('TEMPLATE_STICKY', ' class="sticky"');

/* the page list
   ---------------------------------------------------------------------------------------------------------------------- */
//I should probably do this using LIs so generated content can be used to do commas and the designer has more freedom
define ('TEMPLATE_PAGES_PAGE',      '<a href="?page=&__PAGE__;#list">&__PAGE__;</a>');
define ('TEMPLATE_PAGES_CURRENT',   '<em>&__PAGE__;</em>');
define ('TEMPLATE_PAGES_GAP',       '…');
define ('TEMPLATE_PAGES_SEPARATOR', ',');

/* the new thread input form
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by index.php
   tags:
	&__NAME__;	the value of the input field named 'username', echoed back to maintain form state
	&__PASS__;	the password entered
	&__ERROR__;	a message / error depending on form state, see ERROR_* templates
	&__TITLE__;	the title of the new thread
	&__TEXT__;	the user’s message HTML encoded to go in a `<textarea>`
*/
define ('TEMPLATE_INDEX_FORM', <<<HTML
<form id="new" method="post" action="#new" enctype="application/x-www-form-urlencoded;charset=utf-8"><fieldset>
	<legend>Add Thread</legend>
	
	<label>Name:
		<input name="username" type="text" size="28" maxlength="18" required autocomplete="on"
	          value="&__NAME__;" />
	</label>
	<label>Password:
		<input name="password" type="password" size="28" maxlength="20" required autocomplete="on"
	          value="&__PASS__;" />
	</label>
	<label class="email">Email:
		<input name="email" type="text" value="example@abc.com" required autocomplete="off" />
		(Leave this as-is, it’s a trap!)
	</label>
	
	&__ERROR__;
	
	<label>Title:
		<input name="title" type="text" size="28" maxlength="80" required autocomplete="off"
		    value="&__TITLE__;" />
	</label>
	<label>Message:
		<textarea name="text" cols="40" rows="23" maxlength="32768" required autocomplete="off"
		>&__TEXT__;</textarea>
	</label>
	
	<p id="rules">
		<input name="submit" type="submit" value="Submit" />
		
		There’s only 1 rule: don’t be an arse. Rule #2 is Kroc makes up the rules.
	</p>
</fieldset></form>
HTML
);
//this is inserted instead of the input form above if `FORUM_ENABLED` is false
/* attached to:
	nothing, inserted directly into the page by index.php
   tags:
	none
*/
define ('TEMPLATE_INDEX_FORM_DISABLED', <<<HTML
<h1>Add Thread</h1>
<p class="error">
	Sorry, posting is currently disabled.
</p>
HTML
);

/* form error messages
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	&__ERROR__;	TEMPLATE_INDEX_FORM, TEMPLATE_THREAD_FORM, TEMPLATE_DELETE_FORM
   tags:
	none
*/
define ('ERROR_NONE',  '<p>There is no need to "register", just enter the name + password you want.</p>');
define ('ERROR_NAME',  '<p class="error">Enter a name. You’ll need to use this with the password each time.</p>');
define ('ERROR_PASS',  '<p class="error">Enter a password. It’s so you can re-use your name each time.</p>');
define ('ERROR_TITLE', '<p class="error">You need to enter the title of your new discussion thread</p>');
define ('ERROR_TEXT',  '<p class="error">Well, write a message!</p>');
define ('ERROR_AUTH',  '<p class="error">That name is taken. Provide the password for it, or choose another name. (password typo?)</p>');

/* the first post in a thread
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by thread.php
   tags:
	&__TITLE__;	Title of the thread
	&__DELETE__;	if delete is allowed, TEMPLATE_DELETE is inserted here
	&__DATETIME__;	timestamp in "Sun, 17 Oct 2010 19:41:09 +0000" format for HTML5 `<time>` datetime attribute
	&__TIME__;	Human readable timestamp
	&__NAME__;	Name of thread originator
	&__TEXT__;	The post message text, HTML formatted and encoded
*/
define ('TEMPLATE_THREAD_FIRST', <<<HTML
<h1>&__TITLE__;</h1>

<article id="1" class="op">
	<header>&__DELETE__;
		<time datetime="&__DATETIME__;" pubdate>&__TIME__;</time>
		<a href="#1">#1.</a> <b>&__NAME__;</b>
	</header>
	
	&__TEXT__;
</article>

HTML
);
/* the delete button 
   attached to:
	&__DELETE__;	TEMPLATE_THREAD_FIRST, TEMPLATE_POST
   tags:
	&__URL__;	The URL to delete a thread
*/
define ('TEMPLATE_DELETE', <<<HTML

		<a class="delete" rel="noindex nofollow" href="&__URL__;">Delete</a>
HTML
);

/* the list of posts in a thread (including page list)
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by thread.php
  tags:
	&__PAGES__;	a generated list of page links, see TEMPLATE_PAGES_*
	&__POSTS__;	a generated list of posts, see TEMPLATE_POST below
*/
define ('TEMPLATE_THREAD_POSTS', <<<HTML
<h2 id="list">Replies</h2>
<nav class="pages">
	Page &__PAGES__;
</nav>

&__POSTS__;

<nav class="pages">
	Page &__PAGES__;
</nav>
HTML
);

/* a post (appended in sequence)
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	&__POSTS__;	TEMPLATE_THREAD_POSTS
   tags:
	&__ID__;	HTML ID of the post, pointed to by the RSS
	&__TYPE__;	either nothing, TEMPLTE_POST_OP or TEMPLATE_POST_DELETED as appropriate
	&__OP__;	if the post is by the thread’s original poster, TEMPLATE_POST_OP gets inserted here
	&__DELETED__;	if the post is marked as deleted, TEMPLATE_POST_DELETED gets inserted here
	&__DELETE__;	if delete is allowed, TEMPLATE_DELETE is inserted here
	&__DATETIME__;	timestamp in "Sun, 17 Oct 2010 19:41:09 +0000" format for HTML5 `<time>` datetime attribute
	&__TIME__;	Human readable timestamp
	&__NAME__;	the poster’s name
	&__TEXT__;	the post message
*/
define ('TEMPLATE_POST', <<<HTML
<article id="&__ID__;" class="&__TYPE__;">
	<header>&__DELETE__;
		<time datetime="&__DATETIME__;" pubdate>&__TIME__;</time>
		<a href="#&__ID__;">#&__ID__;.</a> <b>&__NAME__;</b>
	</header>
	
	&__TEXT__;
</article>

HTML
);
//if the post is by the thread’s original poster
/* attached to:
	&__TYPE__;	TEMPLATE_POST
   tags:
	none
*/
define ('TEMPLATE_POST_OP', 'op');

//if the post has been deleted
/* attached to:
	&__TYPE__;	TEMPLATE_POST
   tags:
	none
*/
define ('TEMPLATE_POST_DELETED', 'deleted');

/* the reply input form
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by thread.php
   tags:
	&__NAME__;	the value of the input field named 'username', echoed back to maintain form state
	&__PASS__;	the password entered
	&__ERROR__;	a message / error depending on form state, see ERROR_* templates
	&__TEXT__;	the user’s message HTML encoded to go in a `<textarea>`
*/
define ('TEMPLATE_THREAD_FORM', <<<HTML
<form id="reply" method="post" action="#reply" enctype="application/x-www-form-urlencoded;charset=utf-8"><fieldset>
	<legend>Reply</legend>
	
	<label>Name:
		<input id="name" name="username" type="text" size="28" maxlength="18" required autocomplete="on"
		 value="&__NAME__;" />
	</label>
	<label>Password:
		<input name="password" type="password" size="28" maxlength="20" required autocomplete="on"
		 value="&__PASS__;" />
	</label>
	<label class="email">Email: (Leave this as-is, it’s a trap!)
		<input name="email" type="text" value="example@abc.com" required automcomplete="on" />
	</label>
	
	&__ERROR__;
	
	<label>Message:
		<textarea name="text" cols="40" rows="23" maxlength="32768" required autocomplete="off"
		>&__TEXT__;</textarea>
	</label>
	
	<p id="rules">
		<input name="submit" type="submit" value="Reply" />
		
		There’s only 1 rule: don’t be an arse.
		Rule #2 is Kroc makes up the rules.
	</p>
</fieldset></form>
HTML
);
//this is inserted instead of the input form above if `FORUM_ENABLED` is false
/* attached to:
	nothing, inserted directly into the page by thread.php
   tags:
	none
*/
define ('TEMPLATE_THREAD_FORM_DISABLED', <<<HTML
<h1>Reply</h1>
<p class="error">
	Sorry, posting is currently disabled.
</p>
HTML
);

/* the site footer and closing HTML
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by most pages
   tags:
	none
*/
define ('TEMPLATE_FOOTER', <<<HTML

<footer><p>
	<a href="mailto:kroccamen@gmail.com">kroccamen@gmail.com</a> • <a href="http://camendesign.com">camendesign.com</a>
</p><p>
	NoNonsenseForum: <a href="https://github.com/Kroc/NoNonsenseForum">Get the source on GitHub</a>
</p></footer>

</body></html>
HTML
);


/* the deletion page
   ====================================================================================================================== */
/* delete thread
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by delete.php
   tags:
	&__NAME__;	the value of the input field named 'username', echoed back to maintain form state
	&__PASS__;	the password entered
	&__ERROR__;	a message / error depending on form state, see ERROR_* templates
*/
define ('TEMPLATE_DELETE_THREAD', <<<HTML
<form id="delete" method="post" action="#delete" enctype="application/x-www-form-urlencoded;charset=utf-8"><fieldset>
	<legend>Delete Thread &amp; Replies</legend>
	
	<label>Name:
		<input id="name" name="username" type="text" size="28" maxlength="18" required autocomplete="on"
		 value="&__NAME__;" />
	</label>
	<label>Password:
		<input name="password" type="password" size="28" maxlength="20" required autocomplete="on"
		 value="&__PASS__;" />
	</label>
	
	&__ERROR__;
	
	<p>
		<input id="submit" name="submit" type="submit" value="Delete" />
	</p>
</fieldset></form>

<h1>Post</h1>
&__POST__;
HTML
);
/* delete post
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by delete.php
   tags:
	&__NAME__;	the value of the input field named 'username', echoed back to maintain form state
	&__PASS__;	the password entered
	&__ERROR__;	a message / error depending on form state, see ERROR_* templates
*/
define ('TEMPLATE_DELETE_POST', <<<HTML

<form id="delete" method="post" action="#delete" enctype="application/x-www-form-urlencoded;charset=utf-8"><fieldset>
	<legend>Delete Post</legend>
	
	<label>Name:
		<input id="name" name="username" type="text" size="28" maxlength="18" required autocomplete="on"
		 value="&__NAME__;" />
	</label>
	<label>Password:
		<input name="password" type="password" size="28" maxlength="20" required autocomplete="on"
		 value="&__PASS__;" />
	</label>
	
	&__ERROR__;
	
	<p>
		<input id="submit" name="submit" type="submit" value="Delete" />
	</p>
</fieldset></form>

<h1>Post</h1>
&__POST__;
HTML
);

//a different default text for the delete form, and an error message if the user is not authorised to delete a thread/post
/* attached to:
	&__ERROR__;	TEMPLATE_DELETE_THREAD
   tags:
	none
*/
define ('ERROR_DELETE_NONE', '<p>To delete this thread, and all replies to it, you must be either the original poster, or a designated moderator.</p>');
define ('ERROR_DELETE_AUTH', '<p class="error">Name / password mismatch! You must enter the name and password of either the post originator, or a designated moderator.</p>');

//the text left behind when a post is deleted
define ('TEMPLATE_DELETE_USER', '<p>This post was deleted by its owner</p>');
define ('TEMPLATE_DELETE_MOD',  '<p>This post was deleted by a moderator</p>');


/* RSS feeds
   ====================================================================================================================== */
/* new thread RSS feed (replies get inserted to this)
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by index.php
   tags:
	&__URL__;	filename of the thread, sans “.xml” extension
	&__TITLE__;	title of the thread
	&__NAME__;	name of poster
	&__DATE__;	RSS formatted timestamp
	&__TEXT__;	the message, HTML formatted and XML encoded
*/
define ('TEMPLATE_RSS', <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<atom:link href="http://${_SERVER['HTTP_HOST']}&__URL__;.xml" rel="self" type="application/rss+xml" />
<title>&__TITLE__;</title>
<link>http://${_SERVER['HTTP_HOST']}&__URL__;</link>

&__ITEMS__;

</channel>
</rss>
XML
);

/* RSS feed for index pages
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	nothing, inserted directly into the page by shared.php
   tags:
	&__PATH__;	if in a sub-folder, the URL encoded folder name (including ending slash but no prefix slash)
	&__TITLE__;	title of the thread
	&__ITEMS__;	a generated list of RSS items, see `TEMPLATE_RSS_ITEM` below
*/
define ('TEMPLATE_RSS_INDEX', <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<atom:link href="http://${_SERVER['HTTP_HOST']}&__PATH__;index.rss" rel="self" type="application/rss+xml" />
<title>&__TITLE__;</title>
<link>http://${_SERVER['HTTP_HOST']}/</link>
&__ITEMS__;
</channel>
</rss>
XML
);

/* an individual post (appended in sequence)
   ---------------------------------------------------------------------------------------------------------------------- */
/* attached to:
	&__ITEMS__;	TEMPLATE_RSS_INDEX
   tags:
	&__TITLE__;	title of the post (just “Re: Title…”)
	&__URL__;	path & filename of the thread, sans “.xml” extension
	&__NAME__;	name of poster
	&__DATE__;	RSS formatted timestamp
	&__TEXT__;	the message, HTML formatted and XML encoded
*/
define ('TEMPLATE_RSS_ITEM', <<<XML
<item>
	<title>&__TITLE__;</title>
	<link>http://${_SERVER['HTTP_HOST']}&__URL__;</link>
	<author>&__NAME__;</author>
	<pubDate>&__DATE__;</pubDate>
	<description>&__TEXT__;</description>
</item>
XML
);

?>