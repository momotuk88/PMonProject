<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
if(!empty($USER['id']))	require ENGINE_DIR.'menu.php';
$tpl->load_template('html.tpl');
$tpl->set('{html}',$html);
$tpl->set('{ajax}',$ajax);
$tpl->set('{head}',(isset($htmlhead)?$htmlhead:''));
$tpl->set('{var}',$var);
$tpl->set('{css}',$css);
$tpl->set('{tpl}','/style/');
$tpl->set('{folder}',$tpl->folder);
$tpl->set('{content}',$tpl->result['content']);
if($config['debugmysql']=='yes')
	$queryList = $db->queryListdebug($db->query_list);
$tpl->set('{debug}',($config['debugmysql']=='yes' && !empty($queryList['list'])?$queryList['list']:''));
$tpl->set('{menu}',(!isset($tpl->result['menu'])?'':$tpl->result['menu']));
$tpl->set('{block-right}',(!isset($tpl->result['block-right']) ? '' : $tpl->result['block-right']));
$tpl->compile('main');
echo $tpl->result['main'];
$tpl->global_clear();
?>
