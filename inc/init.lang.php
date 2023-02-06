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
goto Axdt1; Axdt1: if (!defined("\120\117\x4e\x4d\x4f\116\111\x54\117\x52")) { die("\110\x61\x63\x6b\x69\156\x67\x20\x61\164\164\x65\155\160\x74\41"); } goto v1512; Xx2uK: class lang implements arrayaccess { private $lang_system = array(); public function __construct() { global $nova_ukraine; require ENGINE_DIR . "\x2f\x6c\141\156\x67\57\x75\x61\56\x70\x68\160"; $this->lang_system = $nova_ukraine; } public function offsetSet($offset, $value) { $this->lang_system[$offset] = $value; } public function offsetExists($offset) { return isset($this->lang_system[$offset]); } public function offsetUnset($offset) { unset($this->lang_system[$offset]); } public function offsetGet($offset) { return isset($this->lang_system[$offset]) ? $this->lang_system[$offset] : "\x4e\x4f\x5f\x4c\101\116\107\x5f" . strtoupper($offset); } } goto T3ssA; v1512: define("\114\x41\116\x47", true); goto Xx2uK; T3ssA: $lang = new lang(); goto BBTow; BBTow: 
?>