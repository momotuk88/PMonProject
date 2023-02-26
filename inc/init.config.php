<?php
if(!defined('PONMONITOR')){
	die('Hacking attempt system!');
}
if(!defined('CONFIG')){
	die('Hacking attempt config!');
}
$config = $db->config();
$allowed_types = array('image/pjpeg' => 'jpg','image/jpeg' => 'jpg','image/jpg' => 'jpg','image/png' => 'png');
$colorfiber['1'] = '#87ff00';
$colorfiber['2'] = '#f47d7d';
$colorfiber['4'] = '#6fb9e7';
$colorfiber['8'] = '#ffbb00';
$colorfiber['12'] = '#fff700';
$colorfiber['16'] = '#36cecb';
$colorfiber['18'] = '#f500ff';
$colorfiber['24'] = 'c908fb';
$colorfiber['48'] = '#fff';
$svg_full_box = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="site-nav-dropdown-icon"><path d="M2.5 0.75H6.5C7.4665 0.75 8.25 1.5335 8.25 2.5V6.5C8.25 7.4665 7.4665 8.25 6.5 8.25H2.5C1.5335 8.25 0.75 7.4665 0.75 6.5V2.5C0.75 1.5335 1.5335 0.75 2.5 0.75ZM13.5 0.75H17.5C18.4665 0.75 19.25 1.5335 19.25 2.5V6.5C19.25 7.4665 18.4665 8.25 17.5 8.25H13.5C12.5335 8.25 11.75 7.4665 11.75 6.5V2.5C11.75 1.5335 12.5335 0.75 13.5 0.75ZM13.5 11.75H17.5C18.4665 11.75 19.25 12.5335 19.25 13.5V17.5C19.25 18.4665 18.4665 19.25 17.5 19.25H13.5C12.5335 19.25 11.75 18.4665 11.75 17.5V13.5C11.75 12.5335 12.5335 11.75 13.5 11.75ZM2.5 11.75H6.5C7.4665 11.75 8.25 12.5335 8.25 13.5V17.5C8.25 18.4665 7.4665 19.25 6.5 19.25H2.5C1.5335 19.25 0.75 18.4665 0.75 17.5V13.5C0.75 12.5335 1.5335 11.75 2.5 11.75Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"></path></svg>';
$svg_min_box = '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="currentColor" class="site-nav-dropdown-icon"><path d="M14.6161 1.44455C15.1043 0.956391 15.8957 0.956391 16.3839 1.44455L19.5659 4.62653C20.054 5.11468 20.054 5.90614 19.5659 6.39429L16.3839 9.57627C15.8957 10.0644 15.1043 10.0644 14.6161 9.57627L11.4341 6.39429C10.946 5.90614 10.946 5.11468 11.4341 4.62653L14.6161 1.44455L14.0963 0.924774L14.6161 1.44455ZM0.75 3C0.75 2.30965 1.30964 1.75 2 1.75H7C7.69036 1.75 8.25 2.30965 8.25 3V8C8.25 8.69036 7.69036 9.25 7 9.25H2C1.30964 9.25 0.75 8.69036 0.75 8V3ZM0.75 14C0.75 13.3096 1.30964 12.75 2 12.75H7C7.69036 12.75 8.25 13.3096 8.25 14V19C8.25 19.6904 7.69036 20.25 7 20.25H2C1.30964 20.25 0.75 19.6904 0.75 19V14ZM11.75 14C11.75 13.3096 12.3096 12.75 13 12.75H18C18.6904 12.75 19.25 13.3096 19.25 14V19C19.25 19.6904 18.6904 20.25 18 20.25H13C12.3096 20.25 11.75 19.6904 11.75 19V14Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"></path></svg>';
$svg_cof = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="site-nav-dropdown-icon"><g clip-path="url(#clip0_11881_16619)"><path d="M8 15L6 19H14L12 15H8Z" fill-opacity="0.22" stroke="#b3c4da" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><rect x="0.75" y="3.75" width="18.5" height="11.5" rx="1.25" fill="#b3c4da" fill-opacity="0.22" stroke="#b3c4da" stroke-width="1.5"></rect></g></svg>';
$svg_work = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" class="site-nav-dropdown-icon"><g clip-path="url(#clip0_11881_16619)"><path d="M8 15L6 19H14L12 15H8Z" fill-opacity="0.22" stroke="#7ad070" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><rect x="0.75" y="3.75" width="18.5" height="11.5" rx="1.25" fill="#7ad070" fill-opacity="0.22" stroke="#7ad070" stroke-width="1.5"></rect></g></svg>';
