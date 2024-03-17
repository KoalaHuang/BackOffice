ALTER TABLE `ta_calendar` ADD `c_employeetype` CHAR(1) NOT NULL AFTER `c_totalmins`, ADD `c_before5` SMALLINT NOT NULL AFTER `c_employeetype`, ADD `c_after5` SMALLINT NOT NULL AFTER `c_before5`;

ALTER TABLE `t_calendar` ADD `c_employeetype` CHAR(1) NOT NULL AFTER `c_totalmins`, ADD `c_before5` SMALLINT NOT NULL AFTER `c_employeetype`, ADD `c_after5` SMALLINT NOT NULL AFTER `c_before5`;

UPDATE `t_calendar` SET `c_employeetype`='P' WHERE c_id in (SELECT t_user.c_id from t_user WHERE t_user.c_employee='P');

UPDATE `t_calendar` SET `c_employeetype`='F' WHERE c_id in (SELECT t_user.c_id from t_user WHERE t_user.c_employee='F');

UPDATE `t_calendar` SET `c_employeetype`='P' WHERE `c_employeetype`='';

UPDATE `t_calendar` SET `c_before5`=300,`c_after5`=240 WHERE `c_employeetype`='P' and `c_fullday`=1;

UPDATE `t_calendar` SET `c_before5`=hour(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`))*60 + minute(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`)) WHERE `c_employeetype`='P' AND `c_fullday`=0 AND (timediff(`t_calendar`.`c_timeend`,'17:00')<0);

UPDATE `t_calendar` SET `c_before5`=(hour(timediff('17:00',`c_timestart`))*60 + MINUTE(timediff('17:00',`c_timestart`))) WHERE `c_employeetype`='P' AND `c_fullday`=0 AND timediff(`c_timeend`,'17:00')>=0 AND timediff(`c_timestart`,'17:00')<0;

UPDATE `t_calendar` SET `c_after5`=(hour(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`)))*60 + MINUTE(timediff(`t_calendar`.`c_timeend`,`t_calendar`.`c_timestart`)) WHERE `c_employeetype`='P' AND `c_fullday`=0 AND timediff(`t_calendar`.`c_timestart`,'17:00')>0;

UPDATE `t_calendar` SET `c_after5`=(hour(timediff(`t_calendar`.`c_timeend`,'17:00')))*60 + MINUTE(timediff(`t_calendar`.`c_timeend`,'17:00')) WHERE `c_employeetype`='P' AND `c_fullday`=0 AND timediff(`t_calendar`.`c_timestart`,'17:00')<=0 AND timediff(`t_calendar`.`c_timeend`,'17:00')>0 

UPDATE `t_calendar` SET `c_before5`=`c_before5`-60 WHERE c_employeetype='P' AND `c_after5` + `c_before5` >=600

