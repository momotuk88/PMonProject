<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
{html}
{ajax}
{css}
<link rel="shortcut icon" href="../style/favicon.ico" />
<link rel="icon" type="image/png" href="../style/favicon-32x32.png" sizes="32x32" />
<meta name="generator" content="PMon4">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
{var}
<div class="wrap">
<div class="wrap-center wrap-container">
<div class="cols fx-row">
<main class="col-middle fx-1 fx-col color1">
<div class="main-head">{head}</div>
<div class="mmain">{content}</div>
</main>
<aside class="col-left fx-first color2"><div class="menu-left sidebar">{menu}</div></aside>
{block-right}
</div>
</div>
{lang}
</div>
<div id="ajax"></div>
<div id="loading" style="display:none"><div class="loading"> LOAD... </div></div>
</body>
</html>
{debug}