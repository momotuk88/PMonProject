<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['pt_apibilling'],'description'=>$lang['pd_apibilling'],'page'=>'apibilling');
$selbill = '<select class="select_billing" name="billingtype">';
$selbill .= '<option value="mikbill" '.($config['billingtype']=='mikbill'?'selected="selected"':''). '>MikBill</option>';
$selbill .= '<option value="abills" '.($config['billingtype']=='abills'?'selected="selected"':''). '>ABillS</option>';
$selbill .= '<option value="userside" '.($config['billingtype']=='userside'?'selected="selected"':''). '>UserSide</option>';
$selbill .= '<option value="nodeny" '.($config['billingtype']=='nodeny'?'selected="selected"':''). '>NoDeny Plus</option>';
$selbill .= '</select>';
$selstbill = '<select class="select_billing" name="billing">';
$selstbill .= '<option value="on" '.($config['billing']=='on'?'selected="selected"':''). '>'.($config['billing']=='on'?$lang['ons']:$lang['on']).'</option>';
$selstbill .= '<option value="off" '.($config['billing']=='off'?'selected="selected"':''). '>'.($config['billing']=='off'?$lang['offs']:$lang['off']).'</option>';
$selstbill .= '</select>';
$tpl->load_template('billing/page.tpl');
$tpl->set('{selbill}',$selbill);
$tpl->set('{selstbill}',$selstbill);
$tpl->set('{billingapikey}',$config['billingapikey']);
$tpl->set('{descrstatus}',$lang['apibilling_statusdescr']);
$tpl->set('{status}',$lang['apibilling_status']);
$tpl->set('{setup}',$lang['apibilling_setup']);
$tpl->set('{type}',$lang['apibilling_type']);
$tpl->set('{typedescr}',$lang['apibilling_typedescr']);
$tpl->set('{savesetup}',$lang['savesetup']);
$tpl->set('{keyapi}',$lang['apibilling_keyapi']);
$tpl->set('{keyapidescr}',$lang['apibilling_keyapidescr']);
$tpl->set('{urlapi}',$lang['apibilling_urlapi']);
$tpl->set('{urlapidescr}',$lang['apibilling_urlapidescr']);
$tpl->set('{billingurl}',$config['billingurl']);
$tpl->set('{alluid}',$lang['apibilling_alluid']);
$tpl->set('{alluiddescr}',$lang['apibilling_alluiddescr']);
$tpl->set('{result}',$tpl->result['checker']);
$tpl->compile('content');
$tpl->clear();
?>