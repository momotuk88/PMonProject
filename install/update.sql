ALTER TABLE `pingstats` CHANGE `system` `system` INT(11) NULL DEFAULT NULL;
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('3','status','cdata','port','1.3.6.1.2.1.2.2.1.8.keyport','monitor');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('3','ifinerrors','cdata','port','1.3.6.1.2.1.2.2.1.14.keyport','global');
INSERT INTO `oid` (`oidid`,`types`,`model`,`pon`,`oid`,`inf`) VALUES ('3','ifouterrors','cdata','port','1.3.6.1.2.1.2.2.1.20.keyport','global');




















