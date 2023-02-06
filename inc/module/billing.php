<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$metatags = array('title'=>$lang['pt_apibilling'],'description'=>$lang['pd_apibilling'],'page'=>'apibilling');
$select_list_billing = '<select class="select_billing" name="billingtype">';
$select_list_billing .= '<option value="mikbill" '.($config['billingtype']=='mikbill'?'selected="selected"':''). '>MikBill</option>';
$select_list_billing .= '<option value="abills" '.($config['billingtype']=='abills'?'selected="selected"':''). '>ABillS</option>';
$select_list_billing .= '<option value="userside" '.($config['billingtype']=='userside'?'selected="selected"':''). '>UserSide</option>';
$select_list_billing .= '<option value="nodeny" '.($config['billingtype']=='nodeny'?'selected="selected"':''). '>NoDeny Plus</option>';
$select_list_billing .= '</select>';
$select_status_billing = '<select class="select_billing" name="billing">';
$select_status_billing .= '<option value="on" '.($config['billing']=='on'?'selected="selected"':''). '>'.($config['billing']=='on'?$lang['ons']:$lang['on']).'</option>';
$select_status_billing .= '<option value="off" '.($config['billing']=='off'?'selected="selected"':''). '>'.($config['billing']=='off'?$lang['offs']:$lang['off']).'</option>';
$select_status_billing .= '</select>';
$tpl->load_template('billing/page.tpl');
$tpl->set('{select_list_billing}',$select_list_billing);
$tpl->set('{select_status_billing}',$select_status_billing);
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