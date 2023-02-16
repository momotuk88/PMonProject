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

/*cdata*/
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
/*huawei*/
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
























