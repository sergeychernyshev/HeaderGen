<?php

$pathinfo = $_SERVER['PATH_INFO'];

$parts = explode('/', $pathinfo);

$contenttype = 'text/plain';
$file = null;
$delay = 0;
$expires = null;
$cached = false;

$filemap = array(
	'png'	=> array('image/png',			'image.png'),
	'jpg'	=> array('image/jpeg',			'image.jpg'),
	'ico'	=> array('image/vnd.microsoft.icon',	'image.ico'),
	'gif'	=> array('image/gif',			'image.gif'),
	'css'	=> array('text/css',			'stylesheet.css'),
	'js'	=> array('text/javascript',		'script.js')
);

foreach ($parts as $part)
{
	foreach ($filemap as $match => $config)
	{
		if ($part == $match)
		{
			$contenttype = $config[0];
			$file = $config[1];
		}
	}

	if (preg_match('/^(\d+)s$/', $part, $matches)) {
		$delay = $matches[1];
	}

	if ($part == 'expires') {
		header('Expires: '.gmdate('r', time() + 31536000));
		header('Cache-control: max-age=31536000');
	}

	if ($part == 'lastmod') {
		if (array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER)) {
			header('HTTP/1.1 304 Not modified');
			$delay = 0;
			$cached = true;
		}

		header('Last-Modified: Sun, 28 Feb 2010 06:28:48 GMT');
	}
}

header('Content-type: '.$contenttype);

if ($delay > 0)
{
	sleep($delay);
}

if (!$cached && !is_null($file) && file_exists($file)) {
	header('Content-length: '.filesize($file));
	readfile($file);
}
