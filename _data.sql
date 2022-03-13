-- phpMiniAdmin dump 1.9.150108
-- Database: practicalagile

/*!40030 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40000 ALTER TABLE `audit` DISABLE KEYS */;
INSERT INTO `audit` VALUES 
('1','0','0','admin','Unsuccessful Login','','','2015-10-27 16:35:29'),('2','0','0','admin','Login','','','2015-10-27 16:35:39');
/*!40000 ALTER TABLE `audit` ENABLE KEYS */;

/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES 
('1','0','Admin','0','3','Probably an idea to use the Google graphs as everything is already there','2000-01-22 16:32:22'),
('2','1','Admin','0','3','The stacked area graph looks like the best fit here but it does need 2 dates before showing anything','2000-01-22 16:32:58'),
('27','0','Admin (Do Not Delete)','1155508250',NULL,'backlog comment','2000-12-01 12:58:14'),
('44','0','Admin (Do Not Delete)','1117976109',NULL,'comment','2000-12-01 15:14:17'),
('45','44','Admin (Do Not Delete)','1117976109',NULL,'do','2000-12-01 15:15:19'),
('46','44','Admin (Do Not Delete)','1117976109',NULL,'stop','2000-12-01 15:15:24'),
('47','44','Admin (Do Not Delete)','1117976109',NULL,'keep','2000-12-01 15:15:30');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;

/*!40000 ALTER TABLE `dbver` DISABLE KEYS */;
INSERT INTO `dbver` VALUES ('1','1.18','2.57');
/*!40000 ALTER TABLE `dbver` ENABLE KEYS */;

/*!40000 ALTER TABLE `hint` DISABLE KEYS */;
INSERT INTO `hint` VALUES ('1','Get your Administrator to Import hints and tips (Check the release notes).');
/*!40000 ALTER TABLE `hint` ENABLE KEYS */;

/*!40000 ALTER TABLE `iteration` DISABLE KEYS */;
INSERT INTO `iteration` VALUES 
('1','0','1','Backlog','2','1155508250','','2012-12-31','2199-12-31'),
('2','0','1','Iteration 1','366635646','381202127','','2000-01-08','2000-01-01'),
('3','0','1','Iteration 2','388619810','7967016','','2000-01-22','2000-01-01'),
('4','0','1','Iteration 3','1182884766','1117976109','','2000-01-07','2000-01-01'),
('5','0','1','iteration next','1435225184','1455738537','no comment id','2000-12-01','2000-12-08');
/*!40000 ALTER TABLE `iteration` ENABLE KEYS */;

/*!40000 ALTER TABLE `points_log` DISABLE KEYS */;
INSERT INTO `points_log` VALUES 
('1','1','2000-01-01 00:00:00','1','Todo','3','11'),
('2','1','2000-01-02 00:00:00','1','Todo','3','10'),
('3','1','2000-01-03 00:00:00','1','Todo','3','16'),
('4','1','2000-01-04 00:00:00','1','Todo','12','46'),
('5','1','2000-01-05 00:00:00','1','Todo','11','43'),
('6','1','2000-01-07 00:00:00','1','Todo','15','58'),
('7','1','2000-01-09 00:00:00','1','Doing','2','4'),
('8','1','2000-01-09 00:00:00','1','Todo','14','55'),
('9','1','2000-01-17 00:00:00','1','Todo','13','52'),
('10','1','2000-01-17 00:00:00','1','Doing','2','4'),
('11','1','2000-01-17 00:00:00','1','Done','1','3'),
('12','1','2000-01-22 00:00:00','1','Todo','12','51'),
('13','1','2000-01-22 00:00:00','1','Done','3','7'),
('14','1','2000-01-23 00:00:00','1','Doing','2','6'),
('15','1','2000-01-23 00:00:00','1','Todo','10','48'),
('16','1','2000-01-23 00:00:00','1','Done','3','7'),
('17','1','2000-01-28 00:00:00','1','Doing','2','6'),
('18','1','2000-01-28 00:00:00','1','OK to Review','1','3'),
('19','1','2000-01-28 00:00:00','1','Done','4','10'),
('86','1','2000-01-28 00:00:00','1','Todo','8','42'),
('20','1','2000-02-03 00:00:00','1','OK to Review','1','3'),
('21','1','2000-02-03 00:00:00','1','Done','5','13'),
('87','1','2000-02-03 00:00:00','1','Todo','8','42'),
('22','1','2000-02-05 00:00:00','1','Done','6','16'),
('23','1','2000-02-05 00:00:00','1','Todo','10','45'),
('24','1','2000-02-06 00:00:00','1','Doing','2','5'),
('25','1','2000-02-06 00:00:00','1','Todo','9','45'),
('26','1','2000-02-06 00:00:00','1','Done','6','16'),
('27','1','2000-02-13 00:00:00','1','Todo','7','39'),
('28','1','2000-02-13 00:00:00','1','Doing','2','10'),
('29','1','2000-02-13 00:00:00','1','OK to Review','1','1'),
('30','1','2000-02-13 00:00:00','1','Done','6','16'),
('31','1','2000-02-16 00:00:00','1','Todo','7','39'),
('32','1','2000-02-16 00:00:00','1','Doing','2','10'),
('33','1','2000-02-16 00:00:00','1','Done','7','17'),
('34','1','2000-02-18 00:00:00','1','Todo','6','38'),
('35','1','2000-02-18 00:00:00','1','OK to Review','1','5'),
('36','1','2000-02-18 00:00:00','1','Done','2','22'),
('37','1','2000-02-19 00:00:00','1','Todo','7','28'),
('38','1','2000-02-19 00:00:00','1','Done','3','32'),
('39','1','2000-01-01 00:00:00','2','Todo','3','11'),
('40','1','2000-01-02 00:00:00','2','Todo','3','10'),
('41','1','2000-01-03 00:00:00','2','Todo','3','16'),
('42','1','2000-01-04 00:00:00','2','Todo','12','46'),
('43','1','2000-01-05 00:00:00','2','Todo','11','43'),
('44','1','2000-01-07 00:00:00','2','Todo','15','58'),
('45','1','2000-01-09 00:00:00','2','Todo','14','55'),
('46','1','2000-01-17 00:00:00','2','Todo','13','52'),
('47','1','2000-01-22 00:00:00','2','Todo','12','51'),
('48','1','2000-01-09 00:00:00','2','Todo','14','55'),
('49','1','2000-01-17 00:00:00','2','Todo','13','52'),
('50','1','2000-01-22 00:00:00','2','Todo','12','51'),
('51','1','2000-01-23 00:00:00','2','Todo','9','45'),
('52','1','2000-01-28 00:00:00','2','Todo','9','40'),
('53','1','2000-02-03 00:00:00','2','Todo','8','38'),
('54','1','2000-02-05 00:00:00','2','Todo','10','45'),
('55','1','2000-02-06 00:00:00','2','Todo','9','45'),
('56','1','2000-02-13 00:00:00','2','Todo','7','35'),
('57','1','2000-02-16 00:00:00','2','Todo','7','33'),
('58','1','2000-02-18 00:00:00','2','Todo','6','33'),
('59','1','2000-02-19 00:00:00','2','Todo','7','28'),
('60','1','2000-01-09 00:00:00','366635646','Doing','2','4'),
('61','1','2000-01-09 00:00:00','366635646','Todo','1','3'),
('62','1','2000-01-17 00:00:00','366635646','Doing','2','4'),
('63','1','2000-01-17 00:00:00','366635646','Done','1','3'),
('64','1','2000-01-22 00:00:00','366635646','Done','3','7'),
('65','1','2000-01-23 00:00:00','388619810','Doing','2','6'),
('66','1','2000-01-23 00:00:00','388619810','Todo','1','3'),
('67','1','2000-01-28 00:00:00','388619810','Doing','2','3'),
('68','1','2000-01-28 00:00:00','388619810','OK to Review','1','3'),
('69','1','2000-01-28 00:00:00','388619810','Done','1','3'),
('70','1','2000-02-03 00:00:00','388619810','OK to Review','1','3'),
('71','1','2000-02-03 00:00:00','388619810','Done','2','6'),
('72','1','2000-02-05 00:00:00','388619810','Done','3','9'),
('73','1','2000-02-06 00:00:00','1182884766','Doing','2','6'),
('74','1','2000-02-06 00:00:00','1182884766','Todo','1','5'),
('75','1','2000-02-13 00:00:00','1182884766','Doing','2','5'),
('76','1','2000-02-13 00:00:00','1182884766','Todo','1','5'),
('77','1','2000-02-13 00:00:00','1182884766','Done','1','1');
/*!40000 ALTER TABLE `points_log` ENABLE KEYS */;

/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES 
('1','1',NULL,'2','Template Project','Template Project used as source for \r\n\'Story Type\' as well as \'Story Status\' and a few other bits & bobs\r\n== Do NOT Delete Me ==','1','0','So That','I Need','0','0','1','6','0','_ Template','1','5');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;

/*!40000 ALTER TABLE `release_details` DISABLE KEYS */;
INSERT INTO `release_details` VALUES 
('2','0','2000-01-01','2000-10-04','One release','926580713','0'),
('3','0','2000-01-01','2000-10-01','Another release','1823697149','0');
/*!40000 ALTER TABLE `release_details` ENABLE KEYS */;

/*!40000 ALTER TABLE `size` DISABLE KEYS */;
INSERT INTO `size` VALUES 
('1','1','0','?'),('2','1','1','1'),('3','1','2','2'),('4','1','3','3'),('5','1','4','5'),('6','1','5','8'),('7','1','6','13'),('8','1','7','21'),('9','1','8','34'),('10','1','9','55'),('11','1','10','89'),('12','1','11','114'),('13','1','999','Inf'),('25','2','0','?'),('26','2','1','0'),('27','2','2','1'),('28','2','3','2'),('29','2','4','3'),('30','2','5','5'),('31','2','6','8'),('32','2','7','13'),('33','2','8','20'),('34','2','9','40'),('35','2','10','100'),('36','2','999','Inf'),('37','3','0','?'),('38','3','1','0'),('39','3','2','1'),('40','3','3','2'),('41','3','4','3'),('42','3','5','4'),('43','3','6','5'),('44','3','999','Inf'),('45','4','0','?'),('46','4','1','0'),('47','4','2','1'),('48','4','3','2'),('49','4','4','3'),('50','4','5','4'),('51','4','6','5'),('52','4','7','6'),('53','4','8','7'),('54','4','9','8'),('55','4','10','9'),('56','4','11','10'),('57','4','12','15'),('58','4','13','25'),('59','4','14','50'),('60','4','15','100'),('61','4','999','Inf'),('62','5','0','?'),('63','5','1','0'),('64','5','2','1'),('65','5','3','2'),('66','5','4','4'),('67','5','5','8'),('68','5','6','16'),('69','5','7','32'),('70','5','8','64'),('71','5','9','128'),('72','5','10','256'),('73','5','999','Inf');
/*!40000 ALTER TABLE `size` ENABLE KEYS */;

/*!40000 ALTER TABLE `size_type` DISABLE KEYS */;
INSERT INTO `size_type` VALUES 
('1','Fibonacci'),
('2','Mod. Fibonacci'),
('3','Simplified '),
('4','Natural'),
('5','Binary');
/*!40000 ALTER TABLE `size_type` ENABLE KEYS */;

/*!40000 ALTER TABLE `story` DISABLE KEYS */;
INSERT INTO `story` VALUES 
('1','1','1','0','1','0','Feature',NULL,'0','2000-01-01 00:00:00','Todo','Todo','30','10','13','0','Export/Import','','','','',''),
('2','2','1','0','2','27','Feature',NULL,'0','2000-01-01 00:00:00','Done','','10','10','3','0','support for multiple projects','','','','',''),
('3','3','1','0','1','26','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','60','10','2','0','track backlog size for each available story status','','','','',''),
('4','4','1','0','1','26','Feature',NULL,'0','2000-01-01 00:00:00','Todo','Doing,Todo,Done','90','10','11','0','Hiearchy of stories ','','','','',''),
('5','5','1','0','2','26','Feature',NULL,'0','2000-01-01 00:00:00','Done','','10','20','1','0','Create and add stories onto  the product/project backlog or specific iteration','','','','',''),
('6','6','1','0','1','26','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','50','20','2','0','create iteration backlog from product backlog ','','','','',''),
('7','7','1','0','1','26','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','70','30','3','0','The ability to search story content for words and terms ','including wildcards','','','',''),
('8','8','1','0','2','26','Feature',NULL,'0','2000-01-01 00:00:00','Done','','20','30','3','0','size / re-size stories','','','','',''),
('9','9','1','0','1','1','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','30','40','5','0','Export search results','','','','',''),
('10','10','1','0','3','26','Feature',NULL,'0','2000-01-01 00:00:00','Done','','40','40','5','0','Viewing stories','I can easily find, review and compare stories.<br />\r\nI need to be able to view all stories associated with a project in a variety of ways<br />\r\n* I can view all stories associated with a project on a single screen(i.e. I don&#39;t have to toggle between iterations and the backlog)<br />\r\n* I can search for stories by size, category, status or any keyword appearing in any text section.','Product Owner','','',''),
('11','11','1','0','1','1','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','10','50','3','0','Import Stories','','','','',''),
('12','12','1','0','1','1','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','40','50','2','0','Export Project','','','','',''),
('13','13','1','0','1','1','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','20','50','3','0','Export Iteration','','','','',''),
('14','14','1','0','4','26','Chore',NULL,'0','2000-01-01 00:00:00','Done','','30','50','1','0','Track story status','support at least todo, doing &amp; done','','','',''),
('15','15','1','0','3','27','Feature',NULL,'0','2000-01-01 00:00:00','Done','','20','60','3','0','support for multiple teams','','','','',''),
('16','16','1','0','1','26','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','80','60','13','0','Track changes to a story (Who, When & What)','','','','',''),
('17','17','1','0','4','4','Feature',NULL,'0','2000-01-01 00:00:00','Doing','','10','70','5','0','Enable A Story hiearchy','I can create a hiearchy of stories and easily break down large features into managable pieces of work.<br />\r\nI Need: to be able to create a hiearchy of stories that can be individually ordered beneath their parent and allow these to be broken down further so that the development team can split then into managable pieces of work that can fit into an iteration wothout losing sight of the business value feature being worked on.<br />\r\n* The ability to size individual stories without children<br />\r\n* Parent Story size is calculated from child stories<br />\r\n* Parent Stories can not be worked on/included in the iteration backlog.<br />\r\n* Parent Story Status can not be edited and is derived from child story status.<br />\r\n* easily view/navigate to a parent from a child and visa versa','Product Owner','','',''),
('18','18','1','0','1','25','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','30','70','0','0','Supportable Solution','in the event of failure FDB will get assistance in getting up and running again<br />\r\nProvide support for the solution','IT support analyst &amp; PMO','','',''),
('19','19','1','0','4','4','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','20','80','5','0','I can break stories down into managable pieces of work (Tasks)','So That: I can break stories down into managable pieces of work<br />\r\nI Need: to be able to create tasks that are assigned to a story and indicate when they have been completed as well as who completed the work<br />\r\n* Tasks linked to stories<br />\r\n* Tasks include a status &#39;Done&#39; , &#39;Not Done&#39; or similar.<br />\r\n* Tasks to indicate who will do/has done the work','Scrum team member','','',''),
('20','20','1','0','1','25','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','10','80','0','0','Access to backend information','We are able to report on all aspects of the project without having to define up front or pay for reports/dashboards to be developed.','ProdMgr/ProjMgr/PO/SM/Resource Manager/PMO/Exec','','',''),
('21','21','1','0','3','4','Feature',NULL,'0','2000-01-01 00:00:00','Done','','30','90','1','0','I can have a reaonable estimate of the work required to be completed.','I can have a reaonable estimate of the work required to be completed.<br />\r\nto be able to indicate the time spent/expected to be spent against a task<br />\r\n* able to indicate Time against an individual task<br />\r\n* able to extract and report on time spent against tasks','Scrum team member','','',''),
('22','22','1','0','1','25','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','50','90','0','0','Provide secure solution','project information, which can be commercially sensitive, is kept secure<br />\r\nUse of SSL, back end security and whatever other measures required','exec/PMO','','',''),
('23','23','1','0','1','25','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','20','100','0','0','The company can if so desired, host the tool on a local Database','The company can manage availability and data security, and are more protected against the risk of an external provider removing the service<br />\r\nAs the company become increasingly reliant on this tool, it is important that we at least have the option of hosting it on local hardware.<br />\r\nService is successfully deployed locally','Scrum Team','','',''),
('24','24','1','0','1','25','Feature',NULL,'0','2000-01-01 00:00:00','Todo','','40','110','0','0','not too complicated!','It can be used by all staff and administrated by project managers without specialist knowledge.<br />\r\ndo not want a solution that requires extensive or specialist understanding to administer or use','PMO','','',''),
('25','25','1','0','1','0','Feature',NULL,'0','2000-01-01 00:00:00','Todo','Todo','40','11100','0','0','Non Functional Requirements','','','','',''),
('26','26','1','0','1','0','Feature',NULL,'0','2000-01-01 00:00:00','Todo','Todo,Doing,Done','20','11200','41','0','Stories','','','','',''),
('27','27','1','0','1','0','Feature',NULL,'0','2000-01-01 00:00:00','Todo','Done','10','11200','6','0','Projects','','','','','');
/*!40000 ALTER TABLE `story` ENABLE KEYS */;

/*!40000 ALTER TABLE `story_status` DISABLE KEYS */;
INSERT INTO `story_status` VALUES 
('1','1','Todo','Card created, No work started (possibly Sized)','1','D0D0D0'),
('2','1','','Sized Understood and contains acceptance criteria','2','A0A0A0'),
('3','1','',NULL,'3','66CCFF'),
('4','1','Doing','Tasked out, Development and unit test work in progress','4','3399FF'),
('5','1','',NULL,'5','0066FF'),
('6','1','',NULL,'6','A066FF'),
('7','1','',NULL,'7','FFFF54'),
('8','1','OK to Review','All unit and system tests passing, ready for review by PO/Truth','8','FF7F05'),
('9','1','','Work rejected. Review, revise & go back to in progress','9','FF0000'),
('10','1','Done','Accepted as per the current Definition of Done','10','A0D050');
/*!40000 ALTER TABLE `story_status` ENABLE KEYS */;

/*!40000 ALTER TABLE `story_type` DISABLE KEYS */;
INSERT INTO `story_type` VALUES ('1','1','Feature','1'),('2','1','Chore','2'),('3','1','Bug','3'),('4','1','Debt','4');
/*!40000 ALTER TABLE `story_type` ENABLE KEYS */;

/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES 
('1','3','0','100','Database Changes to support points per status per change at iteration and project level','0','4','0','2000-01-22 16:30:21'),
('2','3','0','200','Add graph of points by date for each iteration (incl Backlog) and Project','0','6','0','2000-01-22 16:30:00'),
('3','3','0','400','Use either of the images as a handle to drag and drop task order','0','6','0','2000-10-02 12:31:00'),
('5','6','0','30000','task','0','0','0','2000-10-02 12:12:29'),
('12','7','0','30000','xcv','0','0','0','2000-10-02 12:29:06'),
('13','11','0','30000','xzcv','0','0','0','2000-10-02 12:29:53'),
('14','3','0','300','wert','0','0','0','2000-10-02 12:31:00');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;

/*!40000 ALTER TABLE `upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `upload` ENABLE KEYS */;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES 
('1','ADMI','0e63cca22b2570d0a7a17b4b58f369a7','Admin (Do Not Delete)','admin','1','0');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40000 ALTER TABLE `user_project` DISABLE KEYS */;
INSERT INTO `user_project` VALUES 
('1','1','0','0');
/*!40000 ALTER TABLE `user_project` ENABLE KEYS */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

/* A Hack to set reasonable dates and points values for the backlog and sprints in the template project
Note, that this is a bodge and not everything will align! */

/* find a reasonable start date so that the current sprint ends next Tuesday */;
SELECT 
@start := DATE_SUB(DATE(NOW()) - INTERVAL WEEKDAY(NOW()) + 5 DAY, INTERVAL 6 WEEK) start, 
@end := DATE_ADD(@start, INTERVAL 13 DAY) end,
@offset := DATEDIFF(@start,'2000-01-01') offset;

/* fix the backlog dates */;
UPDATE `iteration` SET 
Start_Date = '1000-01-01 00:00:00',
End_Date =  '2999-12-31 00:00:00'
WHERE ID = 1;

/* set up the stories */;
UPDATE `story` SET 
Created_Date = DATE_ADD( @start, INTERVAL ROUND((RAND() * 21)+1) day);

/* set up the sprints */;
UPDATE `iteration` SET 
Start_Date = DATE_ADD( @start, INTERVAL iteration.ID*2-2 WEEK),
End_Date =  DATE_ADD( @end, INTERVAL iteration.ID*2-2 WEEK)
WHERE ID < 6 AND ID > 1;

UPDATE `points_log` SET 
Points_Date =  DATE_ADD(Points_Date, INTERVAL @offset DAY)
WHERE 1=1;
-- phpMiniAdmin dump end
