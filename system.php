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
goto dYL7Z; dYL7Z: $olt = isset($_POST["\x6f\154\x74"]) ? (int) $_POST["\157\x6c\164"] : null; goto ap3Iw; ap3Iw: $jobid = isset($_POST["\152\x6f\142\151\144"]) ? (int) $_POST["\x6a\157\x62\x69\x64"] : null; goto JPYfa; JPYfa: if (isset($_POST["\157\154\x74"])) { if ($olt && $jobid) { exec("\160\150\x70\x20\155\x6f\x6e\151\x74\x6f\162\56\160\150\160\x20\x2d\163\42" . $olt . "\x22\x20\x2d\152\x22" . $jobid . "\x22\x20\x3e\76\x20\x2f\x64\145\x76\x2f\x6e\x75\154\x6c\40\x32\x3e\46\x31"); } } goto H568z; H568z: 
?>