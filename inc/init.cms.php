<?php
/*
=====================================================
 PMonProject - PON Device Management UA
-----------------------------------------------------
 Copyright (c) 2023 
 -----------------------------------------------------
 Developer @momotuk88  
=====================================================
 This code is protected by copyright
=====================================================
*/
goto ZZ34n; JtEQh: $tpl->set("\173\150\145\x61\x64\x7d", isset($htmlhead) ? $htmlhead : ''); goto nwOHs; O20Xi: $tpl->set("\x7b\164\160\154\x7d", "\57\x73\164\x79\154\145\x2f"); goto VhAys; M891G: $tpl->set("\x7b\x68\x74\155\154\x7d", $html); goto yu6Oi; yu6Oi: $tpl->set("\173\141\152\141\170\x7d", $ajax); goto JtEQh; c9MIj: $tpl->set("\173\155\x65\156\x75\x7d", !isset($tpl->result["\155\145\156\165"]) ? '' : $tpl->result["\155\145\156\x75"]); goto zFtyd; VhAys: $tpl->set("\173\x66\157\154\x64\145\x72\175", $tpl->folder); goto HWe27; wD2c7: $tpl->compile("\155\141\151\156"); goto cHiIH; bhFOu: $tpl->load_template("\150\x74\155\154\x2e\x74\160\x6c"); goto M891G; ZZ34n: if (!defined("\x50\117\116\x4d\x4f\x4e\111\x54\x4f\122")) { die("\x48\x61\x63\x6b\151\x6e\147\x20\x61\164\x74\x65\x6d\x70\164\41"); } goto JjjyV; JjjyV: if (!empty($USER["\151\x64"])) { require ENGINE_DIR . "\x6d\145\x6e\x75\x2e\160\150\x70"; } goto bhFOu; zFtyd: $tpl->set("\x7b\142\154\157\x63\153\x2d\162\x69\147\150\x74\x7d", !isset($tpl->result["\x62\x6c\157\143\153\x2d\162\151\147\x68\164"]) ? '' : $tpl->result["\142\154\x6f\143\x6b\55\x72\x69\x67\x68\164"]); goto wD2c7; H8Sa9: $tpl->global_clear(); goto u9HuB; cHiIH: echo $tpl->result["\155\141\151\x6e"]; goto H8Sa9; rlPBy: $tpl->set("\173\x64\x65\x62\165\147\175", $config["\x64\x65\142\165\147\155\x79\163\x71\x6c"] == "\x79\x65\x73" && !empty($queryList["\154\151\x73\x74"]) ? $queryList["\154\151\x73\164"] : ''); goto c9MIj; Y6ma3: if ($config["\144\145\x62\165\x67\155\x79\163\161\x6c"] == "\171\x65\x73") { $queryList = $db->queryListdebug($db->query_list); } goto rlPBy; nwOHs: $tpl->set("\173\x76\x61\x72\x7d", $var); goto rq1WQ; HWe27: $tpl->set("\x7b\143\157\x6e\164\x65\156\x74\175", (isset($licensekey) ? $licensekey : '') . $tpl->result["\143\x6f\156\x74\x65\156\164"]); goto Y6ma3; rq1WQ: $tpl->set("\x7b\143\x73\163\x7d", $css); goto O20Xi; u9HuB: ?>
