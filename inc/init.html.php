<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$html = <<<HTML
<title>{$metatags['title']}</title>
<meta name="description" content="{$metatags['description']}" />
HTML;
$var = <<<HTML
<script type="text/javascript">
var root   = "/";
var author  = "@momotuk88";
var version  = "pmon4";
</script>
HTML;
$ajax = <<<HTML
<script src="../style/js/jquery-3.6.0.min.js"></script>
<script src="../style/js/jquery.cookies.js"></script>
<script src="../style/js/script.js"></script>
HTML;
$css = <<<HTML
<link href="../style/css/styles.css" type="text/css" rel="stylesheet" />
<link href="../style/css/engine.css" type="text/css" rel="stylesheet" />
<link href="../style/css/nav.css" type="text/css" rel="stylesheet" />
<link href="../style/css/font.css" type="text/css" rel="stylesheet" />
<link href="../style/rounded/css/uicons-regular-rounded.css" rel="stylesheet">
HTML;
?>
