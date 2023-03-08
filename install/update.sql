INSERT INTO `config` (`name`,`value`) VALUES ('countviewpageswitch',10);
INSERT INTO `config` (`name`,`value`) VALUES ('backup','2023-01-01 00:00:00');
ALTER TABLE `onus` ADD `comments` TEXT NULL DEFAULT NULL AFTER `monitor`;
ALTER TABLE `onus` ADD `apiget` INT(11) NULL DEFAULT NULL AFTER `comments`;
/*add planet*/
update equipment set device = 'switch', phpclass= 'planet2424',work = 'yes' where id = 40;
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'uptime','planet','device','1.3.6.1.2.1.1.3.0','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'listport','planet','port','1.3.6.1.2.1.31.1.1.1.1','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (4,'status','planet','port','1.3.6.1.2.1.2.2.1.8.keyport','global','a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'ifinerrors','planet','port','1.3.6.1.2.1.2.2.1.14.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'ifouterrors','planet','port','1.3.6.1.2.1.2.2.1.20.keyport','global'); 
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (12,'adminstatus','cdata','gpon','1.3.6.1.4.1.17409.2.8.5.1.1.4.keyonu','onu','a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (12,'operstatus','cdata','gpon','1.3.6.1.4.1.17409.2.8.5.1.1.4.keyonu','onu','a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (12,'temp','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.4.1.8.keyonu.0.0','onu','=FUNCT1INT100=');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (12,'reason','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.103.keyonu','onu','a:2:{s:4:"losi";s:4:"err6";s:10:"dying-gasp";s:4:"err1";}');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (12,'time','cdata','gpon','1.3.6.1.4.1.17409.2.8.4.1.1.102.keyonu','onu');
/*add cdata 1608*/
update equipment set device = 'olt', phpclass= 'cdatafd1608',work = 'yes',oidid='12', name='C-DATA', model= 'FD1608SN-2AC', sort = '19', cat = '2', photo = 'cdatafd1608sn.png'  where id = 50;
update oid set result = '=FUNCT1INT10=' where id = 158;
ALTER TABLE `pingstats` CHANGE `system` `system` INT(11) NULL DEFAULT NULL;


















