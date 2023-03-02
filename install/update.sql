DROP TABLE `unit`, `unitbasket`, `unitdevice`, `unitfiber`, `unitfibermap`, `unitmafta`, `unitponbox`, `unitponboxont`, `unitpontree`;
INSERT INTO `config` (`name`,`value`) VALUES ('countviewpageswitch',10);
/*add planet*/
update equipment set device = 'switch', phpclass= 'planet2424',work = 'yes' where id = 40;
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'uptime','planet','device','1.3.6.1.2.1.1.3.0','health');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'listport','planet','port','1.3.6.1.2.1.31.1.1.1.1','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`,`result`) VALUES (4,'status','planet','port','1.3.6.1.2.1.2.2.1.8.keyport','global','a:2:{i:1;s:2:\"up\";i:2;s:4:\"down\";}');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'ifinerrors','planet','port','1.3.6.1.2.1.2.2.1.14.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES (4,'ifouterrors','planet','port','1.3.6.1.2.1.2.2.1.20.keyport','global'); 
/*add cdata 1608*/
update equipment set device = 'olt', phpclass= 'cdatafd1608',work = 'yes',oidid='12', name='C-DATA', model= 'FD1608SN-2AC', sort = '19', cat = '2', photo = 'cdatafd1608sn.png'  where id = 50;
update oid set result = '=FUNCT1INT10=' where id = 158;
ALTER TABLE `pingstats` CHANGE `system` `system` INT(11) NULL DEFAULT NULL;


















