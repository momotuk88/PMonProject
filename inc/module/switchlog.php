<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$code='

<STYLE>
#cross-box{}
#cross {
    display: block;
    background: #eee;
    padding: 10px;
}
.optical-socket {
    width: 21px;
    height: 26px;
    background: #5e8ed9;
    margin: 0px 7px 10px 0px;
}
.optical-socket .empty {
    position: relative;
    background: #222;
    display: block;
    height: 17px;
    margin: 4px 4px 4px 3px;
    width: 15px;
}
</STYLE>


<div id="cross-box">
<div id="cross">
<div class="optical-socket" id="opt-1"><span class="empty"></span></div>
<div class="optical-socket" id="opt-2"><span class="empty"></span></div>
<div class="optical-socket" id="opt-3"><span class="empty"></span></div>
<div class="optical-socket" id="opt-4"><span class="empty"></span></div>
<div class="optical-socket" id="opt-5"><span class="empty"></span></div>
<div class="optical-socket" id="opt-6"><span class="empty"></span></div>
<div class="optical-socket" id="opt-7"><span class="empty"></span></div>
<div class="optical-socket" id="opt-8"><span class="empty"></span></div>
<div class="optical-socket" id="opt-9"><span class="empty"></span></div>
<div class="optical-socket" id="opt-10"><span class="empty"></span></div>
<div class="optical-socket" id="opt-11"><span class="empty"></span></div>
<div class="optical-socket" id="opt-12"><span class="empty"></span></div>
<div class="optical-socket" id="opt-13"><span class="empty"></span></div>
<div class="optical-socket" id="opt-14"><span class="empty"></span></div>
<div class="optical-socket" id="opt-15"><span class="empty"></span></div>
<div class="optical-socket" id="opt-16"><span class="empty"></span></div>
</div>
</div>








';
$metatags = array('title'=>$lang['log'].' '.$dataSwitch['place'],'description'=>$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model'],'page'=>'switchlog');
$tpl->load_template('log/page.tpl');
$tpl->set('{result}',$code);
$tpl->set('{url}','/?do=detail&act='.$dataSwitch['device'].'&id='.$dataSwitch['id']);
$tpl->set('{pager}',$pagertop);
$tpl->set('{logdevice}',$dataSwitch['place'].' '.$dataSwitch['inf'].' '.$dataSwitch['model']);
$tpl->set('{logname}',$lang['log']);
$tpl->compile('content');
$tpl->clear();	
?>