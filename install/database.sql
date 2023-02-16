SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
CREATE TABLE `apikey` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `apikey` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ipaccess` varchar(20) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `types` varchar(10) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `apikey` (`id`, `added`, `apikey`, `ipaccess`, `count`, `types`) VALUES(1, NULL, 'regthy76rtfig8lt8ug', NULL, 10, 'monitor'),(2, NULL, 'rt325ye6irei65e', NULL, NULL, 'ont');
CREATE TABLE `baseip` (
  `id` int(11) NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `ip` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `battery_list` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `types` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `config` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` text DEFAULT NULL,
  `value` text DEFAULT NULL,
  `types` text DEFAULT NULL,
  `update` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `config` (`id`, `name`, `value`, `types`, `update`) VALUES
(1, 'root', '/', 'test', '0000-00-00 00:00:00'),
(2, 'securityipst', NULL, 'ip', '2022-07-27 16:08:49'),
(3, 'countviewpageonu', '40', 'int', '2023-01-31 22:18:30'),
(4, 'url', 'http://1.ua', 'url', '2023-01-31 22:07:21'),
(5, 'skin', 'pmon', 'text', '0000-00-00 00:00:00'),
(6, 'billingapikey', 'keyus', 'text', '0000-00-00 00:00:00'),
(7, 'telegramtoken', 'sam_token', 'text', '2023-01-06 13:54:45'),
(8, 'telegramchatid', 'id_chat', 'text', '2023-01-06 13:54:45'),
(9, 'security', 'off', 'enum', '0000-00-00 00:00:00'),
(10, 'api', 'on', 'enum', '0000-00-00 00:00:00'),
(11, 'map', 'off', 'enum', '2023-01-03 17:14:52'),
(12, 'task', 'on', 'enum', '0000-00-00 00:00:00'),
(13, 'marker', 'on', 'enum', '2022-12-18 15:02:04'),
(14, 'telegram', 'on', 'enum', '2023-01-06 13:54:45'),
(15, 'billing', 'off', 'enum', '0000-00-00 00:00:00'),
(16, 'unit', 'off', 'enum', '2023-01-03 17:14:52'),
(17, 'comment', 'off', 'enum', '2023-01-03 17:15:04'),
(18, 'lon', '', 'int', '0000-00-00 00:00:00'),
(19, 'lan', '', 'int', '0000-00-00 00:00:00'),
(20, 'criticsignal', '2', 'int', '2022-07-27 15:50:42'),
(21, 'countlistsitelog', '20', 'int', '0000-00-00 00:00:00'),
(22, 'critictemp', '70', 'int', '2022-07-27 15:58:53'),
(23, 'criticcpuolt', '20', 'int', '0000-00-00 00:00:00'),
(24, 'root_pmon', '/', 'test', '0000-00-00 00:00:00'),
(25, 'billingurl', 'http://2.test/api.php', 'url', '0000-00-00 00:00:00'),
(26, 'configport', 'off', 'enum', '2022-07-27 16:03:10'),
(27, 'pathwalk', 'snmpwalk', 'text', '0000-00-00 00:00:00'),
(28, 'pathget', 'snmpget', 'text', '0000-00-00 00:00:00'),
(29, 'snmpmode', 'class', 'text', '0000-00-00 00:00:00'),
(30, 'background', 'false', 'text', '0000-00-00 00:00:00'),
(31, 'cachetime', '60', 'int', '0000-00-00 00:00:00'),
(32, 'debug', 'false', 'text', '0000-00-00 00:00:00'),
(33, 'monitorapi', 'http://1.ua/api.php', 'url', '2023-01-31 22:18:30'),
(34, 'sklad', 'on', 'enum', '0000-00-00 00:00:00'),
(35, 'pon', 'on', 'enum', '2023-01-30 12:53:09'),
(36, 'billingtype', 'mikbill', 'enum', '0000-00-00 00:00:00'),
(37, 'tag', 'off', 'enum', '2023-01-28 16:44:30'),
(38, 'comment', 'off', 'enum', '2023-01-03 17:15:04'),
(39, 'currentdevice', '3', 'int', '0000-00-00 00:00:00'),
(40, 'geo_lan', '48.309652', 'text', '0000-00-00 00:00:00'),
(41, 'geo_lon', '25.918261', 'text', '0000-00-00 00:00:00'),
(42, 'onugraph', 'on', 'enum', '0000-00-00 00:00:00'),
(43, 'debugmysql', 'no', 'text', '0000-00-00 00:00:00'),
(44, 'statusport', 'on', 'enum', '0000-00-00 00:00:00'),
(45, 'errorport', 'on', 'enum', '0000-00-00 00:00:00');
CREATE TABLE `connect_port` (
  `id` int(11) NOT NULL,
  `types` varchar(5) DEFAULT NULL,
  `cursfp` int(11) DEFAULT NULL,
  `curp` int(11) NOT NULL,
  `curd` int(11) NOT NULL,
  `connsfp` int(11) DEFAULT NULL,
  `connp` int(11) NOT NULL,
  `connd` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `equipment` (
  `id` int(11) UNSIGNED NOT NULL,
  `cat` int(11) DEFAULT NULL,
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `oidid` int(11) DEFAULT NULL,
  `model` text DEFAULT NULL,
  `device` varchar(100) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `phpclass` text DEFAULT NULL,
  `work` varchar(5) NOT NULL,
  `photo` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `equipment` (`id`, `cat`, `sort`, `oidid`, `model`, `device`, `name`, `phpclass`, `work`, `photo`) VALUES
(1, 1, 1, 1, 'P3310B', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3310B.png'),
(2, 1, 2, 1, 'P3310C', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3310C.png'),
(3, 1, 3, 1, 'P3310D', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3310D.png'),
(4, 1, 4, 1, 'P3608-2TE', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3608-2TE.png'),
(5, 1, 5, 1, 'P3616-2TE', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3608-2TE.png'),
(6, 1, 6, 2, 'GP3600-08', 'olt', 'BDCOM', 'bdcomgpon', 'yes', 'GP3600-08.png'),
(7, 1, 7, 2, 'GP3600-16', 'olt', 'BDCOM', 'bdcomgpon', 'yes', 'GP3600-16.png'),
(8, 1, 8, 1, 'P3608B', 'olt', 'BDCOM', 'bdcomepon', 'yes', 'P3608B.png'),
(9, 4, 9, 3, 'C220', 'olt', 'ZTE', 'zte220_2', 'yes', 'ztec220.png'),
(10, 4, 10, 7, 'C320 (2.1)', 'olt', 'ZTE', 'zte320_2', 'yes', 'ztec320.png'),
(11, 4, 11, 6, 'C610', 'olt', 'ZTE', 'zte6', 'no', NULL),
(12, 4, 12, 7, 'C300 (2.1)', 'olt', 'ZTE', 'zte300_2', 'yes', 'ztec300.png'),
(13, 4, 13, NULL, 'C300', 'olt', 'ZTE', 'zte3', 'no', NULL),
(16, 2, 16, 13, 'FD1104', 'olt', 'C-DATA', 'cdata1108', 'yes', 'cdata1108.png'),
(17, 2, 17, 13, 'FD1108', 'olt', 'C-DATA', 'cdata1108', 'yes', 'cdata1108.png'),
(18, 2, 18, 15, 'FD1216', 'olt', 'C-DATA', 'cdataf1216s', 'yes', 'cdata1208.png'),
(19, 2, 19, 15, 'FD1208', 'olt', 'C-DATA', 'cdata1208sr2dap', 'yes', 'cdata1208.png'),
(25, 5, 25, NULL, 'EL5610-16P', 'olt', 'GCOM', 'gcomepon', 'no', NULL),
(26, 5, 26, NULL, 'EL5610-08P', 'olt', 'GCOM', 'gcomgpon', 'no', NULL),
(30, 3, 30, NULL, '5683', 'olt', 'Huawei', 'huawei', 'no', NULL),
(31, 3, 31, NULL, 'S2326TP', 'switch', 'Huawei ', 'huawei2326switch', 'no', NULL),
(32, 3, 32, 18, 'MA5608T', 'olt', 'Huawei', 'huawei5608t', 'no', 'huawei5608t.png'),
(27, 5, 27, NULL, 'EL5610-04P', 'olt', 'GCOM', 'gcomgpon', 'no', NULL),
(28, 6, 30, 5, 'DGS-1100-06/ME', 'switch', 'Dlink', 'dlinkdgs1106', 'yes', 'dlink1106me.png'),
(40, 10, 40, 4, 'SGSW-24240', 'switchl2', 'PLANET ', 'planet', 'no', 'SGSW-24240.png'),
(50, 20, 1, 50, 'Ip7', 'ups', 'Equicom', 'equicomip7', 'no', 'equicomip7.png'),
(20, 2, 20, 15, 'FD1216', 'olt', 'C-DATA', 'cdataf1216s', 'no', 'cdata1216s.png');
CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `historysignal` (
  `id` int(11) NOT NULL,
  `device` int(11) UNSIGNED DEFAULT NULL,
  `onu` int(11) DEFAULT NULL,
  `signal` varchar(16) DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `photo` varchar(150) DEFAULT NULL,
  `lan` text DEFAULT NULL,
  `lon` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `log` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `userid` int(11) NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `progress` enum('user','system','switch','telegram','config') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `monitor` (
  `id` int(11) NOT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `name` varchar(500) DEFAULT NULL,
  `status` enum('none','up','down') NOT NULL DEFAULT 'none',
  `types` enum('olt','switch','onu','none','port') DEFAULT NULL,
  `time_online` datetime DEFAULT NULL,
  `time_offline` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `checker` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `monitoronu` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `monitor` datetime DEFAULT NULL,
  `idonu` int(11) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `oid` (
  `id` int(11) UNSIGNED NOT NULL,
  `oidid` int(11) DEFAULT NULL,
  `types` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `pon` varchar(10) DEFAULT NULL,
  `oid` text DEFAULT NULL,
  `format` varchar(50) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `inf` varchar(10) DEFAULT NULL,
  `result` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `oid` (`id`, `oidid`, `types`, `model`, `pon`, `oid`, `format`, `descr`, `inf`, `result`) VALUES
(1, 1, 'uptime', 'bdcom', 'device', '1.3.6.1.2.1.1.3.0', 'integer', 'oid_uptime', 'health', NULL),
(2, 1, 'cpu', 'bdcom', 'device', '1.3.6.1.4.1.3320.9.109.1.1.1.1.3.1', 'integer', 'oid_cpu', 'health', NULL),
(3, 1, 'temp', 'bdcom', 'device', '1.3.6.1.4.1.3320.9.181.1.1.7.1', 'integer', 'oid_temp', 'health', NULL),
(4, 1, 'ifinerrors', 'bdcom', 'port', '1.3.6.1.2.1.2.2.1.14.keyport', 'integer', 'oid_ifinerrors', 'monitor', NULL),
(5, 1, 'ifouterrors', 'bdcom', 'port', '1.3.6.1.2.1.2.2.1.20.keyport', 'integer', 'oid_ifouterrors', 'monitor', NULL),
(6, 3, 'listmac', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.2.1.1.6', 'hex', 'oid_list_mac', 'onu', NULL),
(7, 3, 'mac', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.2.1.1.6.keyonu', 'integer', 'oid_epon_mac', 'onu', NULL),
(8, 3, 'eth', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.5.1.2.keyonu.1', 'integer', 'oid_epon_wan', 'onu', NULL),
(9, 3, 'status', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.2.1.1.1.keyonu', 'integer', 'oid_epon_status', 'onu', NULL),
(10, 3, 'reason', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.7.4.1.17.keyonu', 'integer', 'oid_epon_reason', 'onu', NULL),
(11, 3, 'rx', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.29.1.5.keyonu', 'string', 'oid_epon_rx', 'onu', NULL),
(12, 3, 'tx', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.29.1.4.keyonu', 'string', 'oid_epon_tx', 'onu', NULL),
(13, 3, 'dist', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.2.1.1.10.keyonu', 'integer', 'oid_epon_dist', 'onu', NULL),
(14, 3, 'model', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.1.1.5.keyonu', 'string', 'oid_epon_model', 'onu', NULL),
(15, 3, 'device', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.1.1.2.keyonu', 'string', 'oid_epon_device', 'onu', NULL),
(16, 3, 'cpu', 'zte', 'device', '1.3.6.1.4.1.3902.1015.2.1.1.3.1.9.1.1.1', 'integer', 'oid_cpu', 'health', NULL),
(17, 3, 'temp', 'zte', 'device', '1.3.6.1.4.1.3902.1015.2.1.3.2.0', 'integer', 'oid_temp', 'health', NULL),
(18, 3, 'uptime', 'zte', 'device', '1.3.6.1.2.1.1.3.0', 'string', 'oid_uptime', 'health', NULL),
(21, 3, 'volt', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.29.1.2.keyonu', 'string', 'oid_epon_volt', 'onu', NULL),
(20, 3, 'bias', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.29.1.3.keyonu', 'string', 'oid_epon_bias_curent', 'onu', NULL),
(19, 3, 'listport', 'zte', 'port', '1.3.6.1.2.1.31.1.1.1.1', 'string', 'oid_list_port', 'global', NULL),
(22, 3, 'temp', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.29.1.1.keyonu', 'string', 'oid_epon_volt', 'onu', NULL),
(23, 3, 'offline', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.7.4.1.14.keyonu', 'string', 'oid_epon_time_offline', 'onu', NULL),
(24, 3, 'config', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.7.4.1.5.keyonu', 'string', 'oid_epon_vendor', 'onu', NULL),
(25, 3, 'status', 'zte', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'integer', 'oid_olt_port_status', 'monitor', ''),
(26, 3, 'vlanmode', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.10.1.1.1.keyonu.1', 'string', 'oid_epon_lan_mode', 'onu', NULL),
(29, 4, 'status', 'planet', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'integer', 'oid_olt_port_status', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(30, 5, 'status', 'dlink', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'integer', 'oid_olt_port_status', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(31, 6, 'status', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.2.keyport.keyonu', 'integer', 'oid_gpon_status', 'onu', NULL),
(32, 6, 'dist', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.10.1.2.keyport.keyonu', 'string', 'oid_gpon_dist', 'onu', NULL),
(33, 6, 'name', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.3.1.2.keyport.keyonu', 'string', 'oid_gpon_name', 'onu', NULL),
(34, 6, 'descr', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.3.1.3.keyport.keyonu', 'string', 'oid_gpon_descr', 'onu', NULL),
(35, 6, 'reason', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.7.keyport.keyonu', 'integer', 'oid_gpon_reason', 'onu', NULL),
(36, 6, 'timeup', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.5.keyport.keyonu', 'string', 'oid_gpon_timeup', 'onu', NULL),
(37, 6, 'timedown', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.6.keyport.keyonu', 'string', 'oid_gpon_timedown', 'onu', NULL),
(38, 6, 'rxolt', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.1.2.4.2.1.2.keyport.keyonu', 'string', 'oid_gpon_rxolt', 'onu', NULL),
(39, 6, 'rx', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.20.2.2.2.1.10.keyport.keyonu.1', 'string', 'oid_gpon_rx', 'onu', NULL),
(40, 6, 'tx', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.20.2.2.2.1.14.keyport.keyonu.1', 'string', '', 'onu', NULL),
(41, 6, 'eth', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.20.2.3.2.1.6.keyport.keyonu.1', 'string', '', 'onu', NULL),
(42, 6, 'model', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.20.2.1.2.1.8.keyport.keyonu', 'string', 'oid_gpon_model', 'onu', NULL),
(43, 6, 'oper', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.3.keyport.keyonu', 'integer', 'oid_gpon_operstatus', 'onu', NULL),
(44, 6, 'countmac', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.20.2.4.15.1.10.keyport.keyonu.1', 'string', 'oid_gpon_count_mac', 'onu', NULL),
(45, 6, 'typereg', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.3.keyport.keyonu', 'string', 'oid_gpon_type_reg', 'onu', NULL),
(46, 6, 'admin', 'zte', 'gpon', '1.3.6.1.4.1.3902.1082.500.10.2.3.8.1.1.keyport.keyonu', 'integer', 'oid_gpon_admin_status', 'onu', NULL),
(47, 1, 'listname', 'bdcom', 'epon', '1.3.6.1.2.1.2.2.1.2', 'string', 'oid_epon_all_name', 'onu', NULL),
(48, 1, 'mac', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.1.1.3.keyonu', 'hex-string', 'oid_epon_mac', 'onu', 'macbdcom'),
(49, 1, 'dist', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.1.1.27.keyonu', 'string', 'oid_epon_dist', 'onu', NULL),
(50, 1, 'status', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.1.1.26.keyonu', 'integer', 'oid_epon_status', 'onu', NULL),
(51, 1, 'rx', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.5.1.5.keyonu', 'integer', 'oid_epon_rx', 'onu', 'rxbdcom'),
(52, 1, 'temp', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.5.1.2.keyonu', 'integer', 'oid_epon_temp', 'onu', 'tempbdcom'),
(53, 1, 'tx', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.5.1.6.keyonu', 'string', 'oid_epon_tx', 'onu', 'rxbdcom'),
(54, 1, 'inface', 'bdcom', 'epon', '1.3.6.1.2.1.2.2.1.2.keyonu', 'string', 'oid_epon_inface', 'onu', NULL),
(55, 1, 'vendor', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.1.1.1.keyonu', 'string', 'oid_epon_vendor', 'onu', NULL),
(56, 1, 'model', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.10.1.1.2.keyonu', 'string', 'oid_epon_model', 'onu', NULL),
(57, 1, 'eth', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.12.1.1.8.keyonu.1', '', 'oid_epon_eth', 'onu', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(58, 1, 'pvid', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.12.1.1.3.keyonu.1', 'integer', 'oid_epon_volt', 'onu', 'voltbdcom'),
(59, 1, 'rxolt', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.108.1.3.keyonu', 'string', 'oid_epon_rxolt', 'onu', 'rxbdcom'),
(60, 1, 'listport', 'bdcom', 'epon', '1.3.6.1.4.1.3320.101.107.1.1', 'string', 'oid_epon_list_port', 'global', NULL),
(61, 1, 'status', 'bdcom', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'integer', 'oid_epon_port_satus', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(62, 1, 'temp', 'bdcom', 'device', '1.3.6.1.4.1.3320.9.181.1.1.7.1', 'integer', 'oid_epon_switch_temp', 'health', NULL),
(63, 50, 'status', 'equicom', 'device', '1.3.6.1.4.1.35160.1.26.0', 'integer', 'oid_status_220', 'health', 'a:2:{i:1;s:2:\"up\";i:0;s:4:\"down\";}'),
(64, 50, 'volt', 'equicom', 'device', '1.3.6.1.4.1.35160.1.16.1.13.3', 'string', 'oid_volt_battery', 'health', '=FUNCT1INT10='),
(65, 2, 'sn', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.1.1.4.keyonu', 'hex', 'oid_gpon_sn', 'onu', NULL),
(66, 2, 'listname', 'bdcom', 'gpon', '1.3.6.1.2.1.2.2.1.2', 'hex', 'oid_gpon_list_inface', 'onu', NULL),
(67, 2, 'dist', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.1.1.33.keyonu', 'integer', 'oid_gpon_onu_dist', 'onu', '=FUNCT1INT10='),
(68, 2, 'status', 'bdcom', 'gpon', '1.3.6.1.2.1.2.2.1.8.keyonu', 'integer', 'oid_gpon_onu_status', 'onu', NULL),
(69, 2, 'rx', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.4.1.2.keyonu', 'auto', '', 'onu', '=FUNCT1INT10='),
(70, 2, 'tx', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.4.1.3.keyonu', 'auto', '', 'onu', '=FUNCT1INT10='),
(71, 2, 'reason', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.1.1.35.keyonu', 'auto', '', 'onu', NULL),
(72, 2, 'admin', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.4.1.1.3.keyonu.1', 'auto', '', 'onu', NULL),
(73, 2, 'vendor', 'bdcom', 'gpon', '1.3.6.1.4.1.3320.10.3.1.1.2.keyonu', 'string', '', 'onu', NULL),
(74, 2, 'uptime', 'bdcom', 'gpon', '1.3.6.1.2.1.2.2.1.9.keyonu', 'auto', '', 'onu', NULL),
(75, 2, 'name', 'bdcom', 'gpon', '1.3.6.1.2.1.31.1.1.1.18.keyonu', 'auto', '', 'onu', NULL),
(76, 2, 'eth', 'bdcom', 'gpon', '1.3.6.1.2.1.2.2.1.8.keyonu', 'auto', '', 'onu', NULL),
(77, 2, 'ifinerrors', 'bdcom', 'port', '1.3.6.1.2.1.2.2.1.14.keyport', 'integer', 'oid_ifinerrors', 'monitor', 'monitor'),
(78, 2, 'status', 'bdcom', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'integer', 'oid_gpon_port_satus', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(79, 5, 'listport', 'dlink', 'port', '1.3.6.1.2.1.31.1.1.1.1', 'auto', 'oid_get_list_port', 'global', NULL),
(80, 5, 'status', 'dlink', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'auto', '', 'global', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(81, 5, 'ifinerrors', 'dlink', 'port', '1.3.6.1.2.1.2.2.1.14.keyport', 'auto', '', 'global', NULL),
(82, 5, 'ifouterrors', 'dlink', 'port', '1.3.6.1.2.1.2.2.1.20.keyport', 'auto', '', 'global', NULL),
(83, 5, 'uptime', 'dlink', 'device', '1.3.6.1.2.1.1.3.0', 'auto', '', 'health', NULL),
(84, 2, 'cpu', 'bdcom', 'device', '1.3.6.1.2.1.25.3.3.1.2.1', 'auto', 'oid_cpu', 'health', NULL),
(85, 2, 'uptime', 'bdcom', 'device', '1.3.6.1.2.1.1.3.0', 'auto', '', 'health', NULL),
(86, 2, 'firmware', 'bdcom', 'device', '1.3.6.1.2.1.1.1.0', 'auto', '', 'health', NULL),
(87, 15, 'rx', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.2.1.4.keyonu.0.0', 'auto', '', 'onu', '=FUNCT1INT100='),
(88, 15, 'dist', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.15.keyonu', 'auto', '', 'onu', NULL),
(89, 15, 'name', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.2.keyonu', 'auto', '', 'onu', NULL),
(90, 15, 'listname', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.7', 'auto', '', 'onu', NULL),
(91, 15, 'status', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.8.keyonu', 'auto', '', 'onu', NULL),
(92, 15, 'listporteth', 'c-data', 'port', '1.3.6.1.4.1.17409.2.3.2.1.1.4.1.0', 'auto', '', 'global', NULL),
(93, 15, 'listportpon', 'c-data', 'port', '1.3.6.1.4.1.17409.2.3.3.1.1.21.1.0', 'auto', '', 'global', NULL),
(94, 15, 'tx', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.2.1.5.keyonu.0.0', 'auto', '', 'onu', '=FUNCT1INT100='),
(95, 15, 'vendor', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.26.keyonu', 'auto', '', 'onu', NULL),
(96, 15, 'model', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.4.1.1.25.keyonu', 'auto', '', 'onu', NULL),
(97, 15, 'eth', 'c-data', 'epon', '1.3.6.1.4.1.17409.2.3.5.1.1.5.keyonu.0.1', 'auto', '', 'onu', NULL),
(98, 15, 'temp', 'c-data', 'device', '1.3.6.1.4.1.34592.1.3.100.1.8.6.0', 'auto', '', 'health', '=FUNCT1INT10='),
(99, 15, 'uptime', 'c-data', 'device', '1.3.6.1.4.1.17409.2.3.1.2.1.1.5.1', 'auto', '', 'health', NULL),
(100, 15, 'cpu', 'c-data', 'device', '1.3.6.1.4.1.34592.1.3.100.1.8.1.0', 'auto', '', 'health', NULL),
(101, 15, 'name', 'c-data', 'device', '1.3.6.1.4.1.17409.2.3.1.2.1.1.2.1', 'auto', '', 'health', NULL),
(102, 15, 'reason', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.100.12.3.1.1.7.keyonu', 'auto', '', 'onu', 'a:2:{s:4:\"losi\";s:4:\"err6\";s:10:\"dying-gasp\";s:4:\"err1\";}'),
(103, 15, 'status', 'c-data', 'portsfp', '1.3.6.1.4.1.17409.2.3.2.1.1.6.1.0.keyport', 'auto', '', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(104, 15, 'status', 'c-data', 'portepon', '1.3.6.1.4.1.17409.2.3.3.1.1.5.1.0.keyport', 'auto', '', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(105, 13, 'rx', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.36.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(106, 13, 'tx', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.37.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(107, 13, 'reason', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.45.1.keyport.keyonu', 'auto', '', 'onu', 'a:2:{s:4:\"losi\";s:4:\"err6\";s:10:\"dying-gasp\";s:4:\"err1\";}'),
(108, 13, 'status', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.11.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(109, 13, 'dist', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.13.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(110, 13, 'model', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.6.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(111, 13, 'vendor', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.5.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(112, 13, 'eth', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.3.1.3.1.keyport.keyonu.1', 'auto', '', 'onu', NULL),
(113, 13, 'uptime', 'c-data', 'device', '1.3.6.1.4.1.34592.1.3.1.5.2.1.1.8.1', 'auto', '', 'health', NULL),
(114, 13, 'cpu', 'c-data', 'device', '1.3.6.1.4.1.34592.1.3.1.1.8.0', 'auto', '', 'health', NULL),
(115, 13, 'temp', 'c-data', 'device', '1.3.6.1.4.1.34592.1.3.1.3.4.0', 'auto', '', 'health', NULL),
(116, 13, 'listmac', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.4.1.1.7', 'auto', '', 'onu', NULL),
(117, 13, 'listport', 'c-data', 'port', '1.3.6.1.2.1.2.2.1.2', 'auto', '', 'global', NULL),
(118, 13, 'temp', 'c-data', 'epon', '1.3.6.1.4.1.34592.1.3.3.4.5.1.1.1.keyport.keyonu', 'auto', '', 'onu', '=FUNCT1INT100='),
(119, 7, 'listport', 'zte', 'port', '1.3.6.1.2.1.31.1.1.1.1', 'auto', '', 'global', NULL),
(120, 7, 'sn', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.5.keyport.keyonu', 'auto', '', 'onu', NULL),
(121, 7, 'listsn', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.5', 'auto', '', 'onu', NULL),
(122, 7, 'dist', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.11.4.1.2.keyport.keyonu', 'auto', '', 'onu', NULL),
(123, 7, 'name', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.2.keyport.keyonu', 'auto', '', 'onu', NULL),
(124, 7, 'note', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.3.keyport.keyonu', 'auto', '', 'onu', NULL),
(125, 7, 'eth', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.keyport.keyonu.1', 'auto', '', 'onu', NULL),
(126, 7, 'status', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.2.1.4.keyport.keyonu', 'auto', '', 'onu', NULL),
(127, 7, 'rx', 'zte', 'gpon', '1.3.6.1.4.1.3902.1015.1010.11.2.1.2.keyport.keyonu', 'auto', '', 'onu', NULL),
(128, 7, 'tx', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.50.12.1.1.14.keyport.keyonu.1', 'auto', '', 'onu', NULL),
(129, 7, 'reason', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.2.1.7.keyport.keyonu', 'auto', '', 'onu', NULL),
(130, 7, 'model', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.50.11.2.1.17.keyport.keyonu', 'auto', '', 'onu', NULL),
(131, 7, 'vendor', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(132, 7, 'uptime', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.2.1.5.keyport.keyonu', 'auto', '', 'onu', NULL),
(133, 7, 'uptime', 'zte', 'device', '1.3.6.1.2.1.1.3.0', 'auto', '', 'health', NULL),
(134, 7, 'name', 'zte', 'device', '1.3.6.1.2.1.1.5.0', 'auto', '', 'health', NULL),
(135, 7, 'typereg', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.12.keyport.keyonu', 'auto', '', 'onu', NULL),
(136, 7, 'status', 'zte', 'port', '1.3.6.1.2.1.2.2.1.8.keyport', 'auto', '', 'monitor', 'a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}'),
(137, 7, 'ifouterrors', 'zte', 'port', '1.3.6.1.2.1.2.2.1.20.keyport', 'auto', '', 'monitor', NULL),
(138, 7, 'ifinerrors', 'zte', 'port', '1.3.6.1.2.1.2.2.1.14.keyport', 'auto', '', 'monitor', NULL),
(139, 7, 'config', 'zte', 'gpon', '1.3.6.1.4.1.3902.1012.3.28.1.1.1.keyport.keyonu', 'auto', '', 'onu', NULL),
(140, 7, 'mngtvlan', 'zte', 'gpon', '1.3.6.1.4.1.3902.1015.1010.5.9.1.4.keyport.keyonu', 'auto', '', 'onu', NULL),
(141, 3, 'vendor', 'zte', 'epon', '1.3.6.1.4.1.3902.1015.1010.1.1.1.1.1.6.keyonu', 'auto', '', 'onu', NULL);
CREATE TABLE `onus` (
  `idonu` int(11) NOT NULL,
  `olt` int(11) UNSIGNED DEFAULT NULL,
  `sw_shelf` int(11) DEFAULT NULL,
  `sw_slot` int(11) DEFAULT NULL,
  `sw_port` int(11) DEFAULT NULL,
  `portolt` int(11) UNSIGNED DEFAULT NULL,
  `sw_ont` int(11) DEFAULT NULL,
  `keyonu` int(11) UNSIGNED DEFAULT NULL,
  `zte_idport` int(11) DEFAULT NULL,
  `status` int(11) UNSIGNED DEFAULT NULL,
  `wan` varchar(10) DEFAULT NULL,
  `inface` varchar(30) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `mac` varchar(20) DEFAULT NULL,
  `name` varchar(300) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `sn` varchar(20) DEFAULT NULL,
  `rx` varchar(7) DEFAULT '0',
  `lastrx` varchar(7) DEFAULT NULL,
  `tx` varchar(7) DEFAULT NULL,
  `reason` varchar(10) DEFAULT NULL,
  `dist` int(11) DEFAULT NULL,
  `rating` int(11) UNSIGNED DEFAULT NULL,
  `updates` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `offline` datetime DEFAULT NULL,
  `online` datetime DEFAULT NULL,
  `changerx` datetime DEFAULT NULL,
  `rxstatus` varchar(7) DEFAULT NULL,
  `cron` int(11) DEFAULT NULL,
  `tag` varchar(300) DEFAULT NULL,
  `vendor` varchar(50) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `inspector` int(11) NOT NULL DEFAULT 1,
  `monitor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `onusold` (
  `idonu` int(11) NOT NULL,
  `inspector` int(11) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `info` varchar(20) DEFAULT NULL,
  `olt` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `inface` text DEFAULT NULL,
  `portolt` text DEFAULT NULL,
  `keyolt` int(11) DEFAULT NULL,
  `portoltzte` text DEFAULT NULL,
  `mac` text DEFAULT NULL,
  `sn` text DEFAULT NULL,
  `dist` int(11) DEFAULT NULL,
  `rx` decimal(10,2) DEFAULT 0.00,
  `lastrx` text DEFAULT NULL,
  `tx` decimal(10,2) DEFAULT 0.00,
  `added` datetime DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  `offline` datetime DEFAULT NULL,
  `online` datetime DEFAULT NULL,
  `time` text DEFAULT NULL,
  `changerx` datetime DEFAULT NULL,
  `lan` text DEFAULT NULL,
  `lon` text DEFAULT NULL,
  `vendor` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `rxstatus` text DEFAULT NULL,
  `tag` varchar(200) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `onus_comm` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `userid` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `idonu` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `comm` varchar(255) NOT NULL DEFAULT '',
  `ip` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `onus_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `descr` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `onuid` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `olt` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `pmonstats` (
  `id` int(10) UNSIGNED NOT NULL,
  `datetime` datetime DEFAULT NULL,
  `online` int(11) DEFAULT NULL,
  `offline` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `sessions` (
  `sid` varchar(32) NOT NULL DEFAULT '',
  `uid` int(10) NOT NULL DEFAULT 0,
  `username` varchar(40) NOT NULL DEFAULT '',
  `class` tinyint(4) NOT NULL DEFAULT 0,
  `ip` varchar(40) NOT NULL DEFAULT '',
  `time` bigint(30) NOT NULL DEFAULT 0,
  `url` varchar(150) NOT NULL DEFAULT '',
  `useragent` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sfp`
--

CREATE TABLE `sfp` (
  `id` int(11) UNSIGNED NOT NULL,
  `model` varchar(500) DEFAULT NULL,
  `cat` int(11) DEFAULT NULL,
  `sort` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `types` text DEFAULT NULL,
  `wavelength` varchar(100) DEFAULT NULL,
  `connector` text DEFAULT NULL,
  `dist` text DEFAULT NULL,
  `speed` varchar(50) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `sfp` (`id`, `model`, `cat`, `sort`, `types`, `wavelength`, `connector`, `dist`, `speed`) VALUES
(1, 'SFP Alistar Модуль SFP 1000BASE-BX 1SM WDM LC 3KM', NULL, 0, 'sm', '1310', 'lc', '3', '1'),
(2, 'SFP Alistar Модуль SFP 1000BASE-BX 1SM WDM LC 3KM', NULL, 0, 'sm', '1550', 'lc', '3', '1');
CREATE TABLE `sklad_battery` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `install` enum('yes','no','removed') NOT NULL DEFAULT 'no',
  `photo` text DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  `types` text DEFAULT NULL,
  `model` text DEFAULT NULL,
  `amper` text DEFAULT NULL,
  `volt` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `sn` text DEFAULT NULL,
  `upsid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `sklad_device` (
  `id` int(11) NOT NULL,
  `model` varchar(100) DEFAULT NULL,
  `install` enum('yes','no') NOT NULL DEFAULT 'no',
  `mac` varchar(100) DEFAULT NULL,
  `sn` varchar(200) DEFAULT NULL,
  `device` enum('olt','switch','switchl2','switchl3','router','antena','ups','battery','other') DEFAULT NULL,
  `ip` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `sklad_install` (
  `id` int(11) NOT NULL,
  `skladdeviceid` int(11) DEFAULT NULL,
  `sklad` enum('ups','switch','battery','device') DEFAULT NULL,
  `note` text DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `connect` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `sklad_switch` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `port` int(11) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `install` enum('yes','no') DEFAULT 'no',
  `sn` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `sklad_ups` (
  `id` int(10) UNSIGNED NOT NULL,
  `added` datetime DEFAULT NULL,
  `install` enum('yes','no') NOT NULL,
  `photo` text DEFAULT NULL,
  `update` datetime DEFAULT NULL,
  `model` text DEFAULT NULL,
  `power` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `sn` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `swcron` (
  `id` int(11) NOT NULL,
  `status` enum('yes','no','go') NOT NULL DEFAULT 'no',
  `oltid` int(11) NOT NULL DEFAULT 0,
  `priority` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `switch` (
  `id` int(11) NOT NULL,
  `monitor` enum('yes','no') DEFAULT NULL,
  `status` enum('yes','no','go') NOT NULL DEFAULT 'no',
  `gallery` enum('yes','no') NOT NULL DEFAULT 'no',
  `connect` enum('yes','no') NOT NULL DEFAULT 'no',
  `jobid` int(11) NOT NULL DEFAULT 0,
  `typecheck` varchar(10) DEFAULT NULL,
  `oidid` int(11) DEFAULT NULL,
  `inf` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `netip` varchar(50) DEFAULT NULL,
  `mac` varchar(100) DEFAULT NULL,
  `sn` varchar(200) DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `snmpro` varchar(50) DEFAULT NULL,
  `snmprw` varchar(50) DEFAULT NULL,
  `countonu` int(11) UNSIGNED DEFAULT NULL,
  `device` enum('olt','switch','switchl2','switchl3','router','antena','ups','battery','other') DEFAULT NULL,
  `name` text DEFAULT NULL,
  `firmware` text DEFAULT NULL,
  `olt_descr` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `uptime` varchar(200) DEFAULT NULL,
  `timecheck` int(11) DEFAULT NULL,
  `timechecklast` int(11) DEFAULT NULL,
  `place` varchar(200) DEFAULT NULL,
  `updates` datetime DEFAULT NULL,
  `updates_rx` datetime DEFAULT NULL,
  `updates_port` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `offonu` int(11) DEFAULT NULL,
  `ononu` int(11) DEFAULT NULL,
  `losonu` int(11) DEFAULT NULL,
  `maxonu` int(11) DEFAULT NULL,
  `allonu` int(11) DEFAULT NULL,
  `todayonu` int(11) NOT NULL DEFAULT 0,
  `img` varchar(100) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `skladid` int(11) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `location` int(11) DEFAULT NULL,
  `locationname` text DEFAULT NULL,
  `groups` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `switch_log` (
  `id` int(11) NOT NULL,
  `deviceid` int(11) NOT NULL DEFAULT 0,
  `types` enum('cron','system','user','switch') DEFAULT 'system',
  `added` datetime DEFAULT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `switch_photo` (
  `id` int(11) NOT NULL,
  `deviceid` int(11) NOT NULL DEFAULT 0,
  `name` varchar(300) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `photo` varchar(300) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `switch_pon` (
  `id` int(10) UNSIGNED NOT NULL,
  `sort` int(11) DEFAULT NULL,
  `oltid` text DEFAULT NULL,
  `pon` text DEFAULT NULL,
  `card` varchar(15) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `sfpid` varchar(40) DEFAULT NULL,
  `idportolt` bigint(22) DEFAULT NULL,
  `support` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `online` int(11) DEFAULT NULL,
  `offline` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `switch_port` (
  `id` int(11) NOT NULL,
  `monitor` enum('yes','no') NOT NULL DEFAULT 'no',
  `sms` enum('yes','no') NOT NULL DEFAULT 'no',
  `log` enum('yes','no') NOT NULL DEFAULT 'no',
  `error` enum('yes','no') NOT NULL DEFAULT 'no',
  `deviceid` int(11) NOT NULL,
  `llid` int(11) DEFAULT NULL,
  `operstatus` enum('up','down','none') DEFAULT NULL,
  `nameport` varchar(100) DEFAULT NULL,
  `descrport` varchar(200) DEFAULT NULL,
  `typeport` varchar(50) DEFAULT NULL,
  `speedport` varchar(10) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updates` datetime DEFAULT NULL,
  `timedown` datetime DEFAULT NULL,
  `timeup` datetime DEFAULT NULL,
  `information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `switch_port_err` (
  `id` int(11) UNSIGNED NOT NULL,
  `llid` int(11) DEFAULT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `status_outerror` varchar(5) DEFAULT 'no',
  `status_inerror` varchar(5) DEFAULT 'no',
  `inerror` int(11) DEFAULT 0,
  `newin` int(11) NOT NULL DEFAULT 0,
  `outerror` bigint(22) DEFAULT 0,
  `newout` int(11) NOT NULL DEFAULT 0,
  `added` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `swlogport` (
  `id` int(11) NOT NULL,
  `deviceid` int(11) NOT NULL DEFAULT 0,
  `portid` int(11) NOT NULL DEFAULT 0,
  `status` enum('up','down') DEFAULT 'down',
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `task_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `taskid` int(11) DEFAULT NULL,
  `username` text DEFAULT NULL,
  `class` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `unit` (
  `id` int(11) NOT NULL,
  `port` int(11) DEFAULT NULL,
  `location` int(11) UNSIGNED DEFAULT NULL,
  `locationname` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `lan` text DEFAULT NULL,
  `lon` text DEFAULT NULL,
  `logo` varchar(20) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitbasket` (
  `id` int(11) NOT NULL,
  `ponboxid` int(11) DEFAULT NULL,
  `spliter` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitdevice` (
  `id` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `deviceid` int(11) UNSIGNED DEFAULT NULL,
  `unitid` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitfiber` (
  `id` int(11) NOT NULL,
  `typesfiber` enum('vol1','vol2','vol4','vol8','vol12','vol18','vol24','vol48') DEFAULT NULL,
  `colorfiber` int(11) DEFAULT NULL,
  `treeid` int(11) DEFAULT NULL,
  `unitid` int(11) DEFAULT NULL,
  `locationid` int(11) DEFAULT NULL,
  `getconnect` int(11) DEFAULT NULL,
  `getconnectid` int(11) DEFAULT NULL,
  `nextconnect` int(11) DEFAULT NULL,
  `nextconnectid` int(11) DEFAULT NULL,
  `metr` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitfibermap` (
  `id` int(11) NOT NULL,
  `fiberid` int(11) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `geo` text DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitmafta` (
  `id` int(11) NOT NULL,
  `locationid` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `lan` varchar(50) DEFAULT NULL,
  `lon` varchar(50) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitponbox` (
  `id` int(11) NOT NULL,
  `sort` int(11) UNSIGNED DEFAULT NULL,
  `types` varchar(50) DEFAULT NULL,
  `deviceid` int(11) DEFAULT NULL,
  `portid` int(11) UNSIGNED DEFAULT NULL,
  `unitid` int(11) DEFAULT NULL,
  `treeid` int(11) DEFAULT NULL,
  `locationid` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `note` text DEFAULT NULL,
  `lan` text DEFAULT NULL,
  `lon` text DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updates` timestamp NULL DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `offline` int(11) DEFAULT NULL,
  `online` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitponboxont` (
  `id` int(11) NOT NULL,
  `ponboxid` int(11) UNSIGNED DEFAULT NULL,
  `onuid` int(11) UNSIGNED DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updates` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `unitpontree` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `sort` int(11) NOT NULL,
  `deviceid` int(11) UNSIGNED DEFAULT NULL,
  `portid` int(11) UNSIGNED DEFAULT NULL,
  `unitid` int(11) DEFAULT NULL,
  `added` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `class` int(11) NOT NULL DEFAULT 1,
  `ip` varchar(200) DEFAULT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(200) NOT NULL,
  `added` datetime NOT NULL DEFAULT current_timestamp(),
  `lastactivity` datetime DEFAULT NULL,
  `port` text DEFAULT NULL,
  `setip` varchar(50) DEFAULT NULL,
  `onlyip` enum('on','off') NOT NULL DEFAULT 'off',
  `url` varchar(200) DEFAULT NULL,
  `hideonu` enum('yes','no') DEFAULT 'no'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
INSERT INTO `users` (`id`, `username`, `name`, `class`, `ip`, `password`, `email`, `added`, `lastactivity`, `port`, `setip`, `onlyip`, `url`, `hideonu`) VALUES
(1, 'user', 'alex', 7, '127.0.0.1', '20ccbe71c69cb25e4e0095483cb63bd394a12b23', 'user@email.com', '2022-07-24 17:01:20', '2023-02-05 17:53:23', 'a:1:i:85s:4:show', '127.0.0.2', 'off', '/', 'no'),
(7, 'user1', 'alex', 1, '127.0.0.1', '20ccbe71c69cb25e4e0095483cb63bd394a12b23', 'user@email.com', '2023-01-09 17:57:47', '2023-01-18 22:45:16', NULL, NULL, 'off', '/?do=exit', NULL);

ALTER TABLE `apikey`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `baseip`
--
ALTER TABLE `baseip`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `battery_list`
--
ALTER TABLE `battery_list`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `connect_port`
--
ALTER TABLE `connect_port`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `historysignal`
--
ALTER TABLE `historysignal`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added` (`added`);

--
-- Индексы таблицы `monitor`
--
ALTER TABLE `monitor`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `monitoronu`
--
ALTER TABLE `monitoronu`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `oid`
--
ALTER TABLE `oid`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `onus`
--
ALTER TABLE `onus`
  ADD PRIMARY KEY (`idonu`);

--
-- Индексы таблицы `onusold`
--
ALTER TABLE `onusold`
  ADD PRIMARY KEY (`idonu`);

--
-- Индексы таблицы `onus_comm`
--
ALTER TABLE `onus_comm`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `onus_log`
--
ALTER TABLE `onus_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added` (`added`);

--
-- Индексы таблицы `pmonstats`
--
ALTER TABLE `pmonstats`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sfp`
--
ALTER TABLE `sfp`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sklad_battery`
--
ALTER TABLE `sklad_battery`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sklad_device`
--
ALTER TABLE `sklad_device`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sklad_install`
--
ALTER TABLE `sklad_install`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sklad_switch`
--
ALTER TABLE `sklad_switch`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sklad_ups`
--
ALTER TABLE `sklad_ups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `swcron`
--
ALTER TABLE `swcron`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch`
--
ALTER TABLE `switch`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch_log`
--
ALTER TABLE `switch_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch_photo`
--
ALTER TABLE `switch_photo`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch_pon`
--
ALTER TABLE `switch_pon`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch_port`
--
ALTER TABLE `switch_port`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `switch_port_err`
--
ALTER TABLE `switch_port_err`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `swlogport`
--
ALTER TABLE `swlogport`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `task_users`
--
ALTER TABLE `task_users`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitbasket`
--
ALTER TABLE `unitbasket`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitdevice`
--
ALTER TABLE `unitdevice`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitfiber`
--
ALTER TABLE `unitfiber`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitfibermap`
--
ALTER TABLE `unitfibermap`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitmafta`
--
ALTER TABLE `unitmafta`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitponbox`
--
ALTER TABLE `unitponbox`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitponboxont`
--
ALTER TABLE `unitponboxont`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `unitpontree`
--
ALTER TABLE `unitpontree`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `apikey`
--
ALTER TABLE `apikey`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `baseip`
--
ALTER TABLE `baseip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `battery_list`
--
ALTER TABLE `battery_list`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT для таблицы `connect_port`
--
ALTER TABLE `connect_port`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `historysignal`
--
ALTER TABLE `historysignal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `monitor`
--
ALTER TABLE `monitor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `monitoronu`
--
ALTER TABLE `monitoronu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `oid`
--
ALTER TABLE `oid`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT для таблицы `onus`
--
ALTER TABLE `onus`
  MODIFY `idonu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `onusold`
--
ALTER TABLE `onusold`
  MODIFY `idonu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `onus_comm`
--
ALTER TABLE `onus_comm`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `onus_log`
--
ALTER TABLE `onus_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `pmonstats`
--
ALTER TABLE `pmonstats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sfp`
--
ALTER TABLE `sfp`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `sklad_battery`
--
ALTER TABLE `sklad_battery`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sklad_device`
--
ALTER TABLE `sklad_device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `sklad_install`
--
ALTER TABLE `sklad_install`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sklad_switch`
--
ALTER TABLE `sklad_switch`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sklad_ups`
--
ALTER TABLE `sklad_ups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `swcron`
--
ALTER TABLE `swcron`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `switch`
--
ALTER TABLE `switch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `switch_log`
--
ALTER TABLE `switch_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `switch_photo`
--
ALTER TABLE `switch_photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `switch_pon`
--
ALTER TABLE `switch_pon`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `switch_port`
--
ALTER TABLE `switch_port`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `switch_port_err`
--
ALTER TABLE `switch_port_err`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `swlogport`
--
ALTER TABLE `swlogport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `task_users`
--
ALTER TABLE `task_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unit`
--
ALTER TABLE `unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitbasket`
--
ALTER TABLE `unitbasket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitdevice`
--
ALTER TABLE `unitdevice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitfiber`
--
ALTER TABLE `unitfiber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitfibermap`
--
ALTER TABLE `unitfibermap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitmafta`
--
ALTER TABLE `unitmafta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitponbox`
--
ALTER TABLE `unitponbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitponboxont`
--
ALTER TABLE `unitponboxont`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `unitpontree`
--
ALTER TABLE `unitpontree`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

ALTER TABLE `switch_port_err` CHANGE `inerror` `inerror` BIGINT(22) NULL DEFAULT NULL;
ALTER TABLE `onus` CHANGE `sn` `sn` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `switch_port` CHANGE `llid` `llid` BIGINT(22) NULL DEFAULT NULL;
ALTER TABLE `switch_port_err` CHANGE `llid` `llid` BIGINT(22) NULL DEFAULT NULL;
ALTER TABLE `onus` CHANGE `portolt` `portolt` BIGINT(22) NULL DEFAULT NULL;
ALTER TABLE `onus` CHANGE `zte_idport` `zte_idport` BIGINT(22) NULL DEFAULT NULL;
ALTER TABLE `users` CHANGE `hideonu` `hideonu` ENUM('yes','no') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'no';
UPDATE equipment set phpclass = 'huawei5608t', work = 'yes', oidid = '14', photo = 'huawei5608t.png'  where id = 32;
INSERT INTO `config` (`name`,`value`) VALUES ('viewipswitch','on');
INSERT INTO `config` (`name`,`value`) VALUES ('badsignalstart','28');
INSERT INTO `config` (`name`,`value`) VALUES ('badsignalend','40');
ALTER TABLE `pmonstats` ADD `badsignal` INT(11) NULL DEFAULT NULL AFTER `datetime`;
ALTER TABLE `pmonstats` ADD `countonu` INT(11) NULL DEFAULT NULL AFTER `badsignal`;
ALTER TABLE `users` ADD `viewlist` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `hideonu`;
UPDATE `equipment` SET `model` = 'FD1616SN',`oidid` = '12',`phpclass` = 'cdataf1616sn',`work` = 'yes',`photo` = 'cdataf1616sn.png' WHERE `id` = 20;
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','eth','cdata','gpon','3.6.1.4.1.34592.1.5.1.1.2.19.1.1.1.1.0.8keyonu.1','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','status','cdata','port','1.3.6.1.2.1.2.2.1.8.keyport','monitor');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','ifinerrors','cdata','port','1.3.6.1.2.1.2.2.1.14.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','ifouterrors','cdata','port','1.3.6.1.2.1.2.2.1.20.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','listport','cdata','port','1.3.6.1.2.1.31.1.1.1.1','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','model','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.5.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','vendor','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.6.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','rx','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.4.1.4.keyonu.0.0','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','tx','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.4.1.5.keyonu.0.0','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','dist','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.9.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','reason','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.103.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','listsn','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.3','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','status','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.7.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','name','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.2.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','uptime','cdata','device','1.3.6.1.4.1.17409.2.3.1.2.1.1.5.1','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','cpu','cdata','device','1.3.6.1.4.1.34592.1.3.100.1.8.1.0','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('12','temp','cdata','device','1.3.6.1.4.1.34592.1.3.100.1.8.6.0','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','dist','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.46.1.20.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','status','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','listsn','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','rx','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','tx','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.51.1.3.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','reason','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','bias','huawei','gpon','1.3.6.1.4.1.2011.6.158.1.1.1.2.1.12.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','name','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','eth','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.62.1.21.keyport.keyonu.1','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','linepro','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','service','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','temp','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.51.1.1.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','macport','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.46.1.21.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','listport','huawei','port','1.3.6.1.2.1.31.1.1.1.1','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','status','huawei','port','1.3.6.1.2.1.2.2.1.8.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','countmacport','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.46.1.21.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','uservlan','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.keyport.keyonu.1','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','model','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','onuerror','huawei','gpon','1.3.6.1.4.1.2011.6.128.1.1.4.27.1.2.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','ifinerrors','huawei','port','1.3.6.1.2.1.2.2.1.14.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','ifouterrors','huawei','port','1.3.6.1.2.1.2.2.1.20.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','oltrx','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.104.1.1.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','temp','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.104.1.2.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','reason','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.103.1.8.keyport.keyonu.9','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','name','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','tx','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.104.1.4.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','rx','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','eth','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.81.1.31.keyport.keyonu.1','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','dist','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.57.1.19.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','status','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.57.1.18.keyport.keyonu','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','listmac','huawei','epon','1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3','onu');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','uptime','huawei','device','1.3.6.1.2.1.1.3.0','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('14','status','huawei','port','1.3.6.1.2.1.2.2.1.8.keyport','monitor');
























