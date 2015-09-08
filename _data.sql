-- phpMiniAdmin dump 1.4.080217
-- Datetime: 2013-09-27 10:00:09
-- Host: 127.0.0.1
-- Database: practicalagile

/*!40030 SET max_allowed_packet=838860 */;

/*!40000 ALTER TABLE `audit` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit` ENABLE KEYS */;

/*!40000 ALTER TABLE `comment` DISABLE KEYS */;
INSERT INTO `comment` VALUES ('1','0','Admin','3','Probably an idea to use the Google graphs as everything is already there','2013-05-22 16:32:22'),('2','1','Admin','3','The stacked area graph looks like the best fit here but it does need 2 dates before showing anything','2013-05-22 16:32:58');
/*!40000 ALTER TABLE `comment` ENABLE KEYS */;

/*!40000 ALTER TABLE `iteration` DISABLE KEYS */;
INSERT INTO `iteration` VALUES ('1','1','Backlog','2','1155508250','','2012-12-31','2199-12-31'),('2','1','Iteration 1','366635646','0','','2013-04-08','2013-04-19'),('3','1','Iteration 2','388619810','0','','2013-04-22','2013-05-03'),('4','1','Iteration 3','1182884766','0','','2013-05-07','2013-05-17');
/*!40000 ALTER TABLE `iteration` ENABLE KEYS */;

/*!40000 ALTER TABLE `points_log` DISABLE KEYS */;
INSERT INTO `points_log` VALUES ('1','1','2013-04-10 00:00:00','1','Todo','3','11'),('2','1','2013-04-15 00:00:00','1','Todo','3','10'),('3','1','2013-04-21 00:00:00','1','Todo','3','16'),('4','1','2013-05-10 00:00:00','1','Todo','12','46'),('5','1','2013-05-13 00:00:00','1','Todo','11','43'),('6','1','2013-05-14 00:00:00','1','Todo','7','21'),('7','1','2013-05-15 00:00:00','1182884766','Doing','1','1'),('8','1','2013-05-15 00:00:00','1182884766','Todo','3','10'),('9','1','2013-05-15 00:00:00','1','Doing','1','1'),('10','1','2013-05-15 00:00:00','1','Done','5','13'),('11','1','2013-05-15 00:00:00','1','Todo','12','33'),('12','1','2013-05-16 00:00:00','1182884766','Doing','1','5'),('13','1','2013-05-16 00:00:00','1182884766','Done','1','1'),('14','1','2013-05-16 00:00:00','1182884766','Todo','1','5'),('15','1','2013-05-16 00:00:00','1','Doing','1','5'),('16','1','2013-05-16 00:00:00','1','Done','6','14'),('17','1','2013-05-16 00:00:00','1','Todo','11','28'),('18','1','2013-05-17 00:00:00','1182884766','Doing','1','5'),('19','1','2013-05-17 00:00:00','1182884766','Done','1','1'),('20','1','2013-05-17 00:00:00','1182884766','Todo','1','5'),('21','1','2013-04-19 00:00:00','366635646','Done','3','7'),('22','1','2199-12-31 00:00:00','2','Todo','13','33'),('23','1','2013-05-03 00:00:00','388619810','Done','3','9'),('24','1','2013-05-20 00:00:00','1','Doing','1','5'),('25','1','2013-05-20 00:00:00','1','Done','7','17'),('26','1','2013-05-20 00:00:00','1','Todo','14','38');
/*!40000 ALTER TABLE `points_log` ENABLE KEYS */;

/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES ('1','1',NULL,'2','Template Project','Template Project used as source for \r\n\'Story Type\' as well as \'Story Status\' and a few other bits & bobs\r\n== Do NOT Delete Me ==','1','0','0','1','6','_ Template','1');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;

/*!40000 ALTER TABLE `queries` DISABLE KEYS */;
INSERT INTO `queries` VALUES ('0','Replaceable Params','{User} {Project} {Iteration} {Backlog} ','','0'),('1','My Current Stories','story.Owner_ID={User}  and story.Status<>\'Done\'','order by Iteration_Rank','1'),('2','My \'Done\' Stories','story.Owner_ID={User}  and story.Status=\'Done\' ','Order by Epic_Rank','1'),('3','All Work',' 0 = (select count(story.AID) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),('4','All \'Done\' Work',' story.Status=\'Done\' and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),('5','All Outstanding Work',' story.Status<>\'Done\' and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),('6','Unsized Stories','story.Size=\'?\' or story.Size is NULL or story.Size=\'\'','order by Iteration_Rank','1'),('7','Blocked Work',' story.Blocked<>0 ','Order by Iteration_Rank','1'),('8','Has Children',' 0 < (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),('9','Has Parent','Parent_Story_ID>0','order by story.Epic_Rank','1'),('10','NO Parent','Parent_Story_ID<1','order by story.Epic_Rank','1'),('11','Not in Release','(story.Release_ID is NULL or story.Release_ID=0) and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','Order by story.Epic_Rank','1');
/*!40000 ALTER TABLE `queries` ENABLE KEYS */;

/*!40000 ALTER TABLE `release_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `release_details` ENABLE KEYS */;

/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

/*!40000 ALTER TABLE `size_type` DISABLE KEYS */;
INSERT INTO `size_type` VALUES ('1','Fibonacci'),('2','Mod. Fibonacci'),('3','Simplified '),('4','Natural'),('5','Binary');
/*!40000 ALTER TABLE `size_type` ENABLE KEYS */;


/*!40000 ALTER TABLE `size` DISABLE KEYS */;
INSERT INTO `size` VALUES 
('1','1','0','?'),
('2','1','1','0'),
('3','1','2','0.5'),
('4','1','3','1'),
('5','1','4','2'),
('6','1','5','3'),
('7','1','6','5'),
('8','1','7','8'),
('9','1','8','13'),
('10','1','9','21'),
('11','1','10','34'),
('12','1','12','55'),
('13','1','13','89'),
('14','1','13','114'),
('15','1','999','Inf'),
('16','2','0','?'),
('17','2','1','0'),
('18','2','2','0.5'),
('19','2','3','1'),
('20','2','4','2'),
('21','2','5','3'),
('22','2','6','5'),
('23','2','7','8'),
('24','2','8','13'),
('25','2','9','20'),
('26','2','10','40'),
('27','2','11','100'),
('28','2','999','Inf'),
('37','3','0','?'),
('38','3','1','0'),
('39','3','2','1'),
('40','3','3','2'),
('41','3','4','3'),
('42','3','5','4'),
('43','3','6','5'),
('44','3','999','Inf'),
('45','4','0','?'),
('46','4','1','0'),
('47','4','2','1'),
('48','4','3','2'),
('49','4','4','3'),
('50','4','5','4'),
('51','4','6','5'),
('52','4','7','6'),
('53','4','8','7'),
('54','4','9','8'),
('55','4','10','9'),
('56','4','11','10'),
('57','4','12','15'),
('58','4','13','25'),
('59','4','14','50'),
('60','4','15','100'),
('61','4','999','Inf'),
('62','5','0','?'),
('63','5','1','0'),
('64','5','2','1'),
('65','5','3','2'),
('66','5','4','4'),
('67','5','5','8'),
('68','5','6','16'),
('69','5','7','32'),
('70','5','8','64'),
('71','5','9','128'),
('72','5','10','256'),
('73','5','999','Inf');
/*!40000 ALTER TABLE `size` ENABLE KEYS */;

/*!40000 ALTER TABLE `story` DISABLE KEYS */;
INSERT INTO `story` VALUES 
('1','1','1','0','1','0','Feature',NULL,'0','0000-00-00 00:00:00','Todo','Todo','30','10','13','0','Export/Import','','','','',''),
('2','2','1','0','2','27','Feature',NULL,'0','0000-00-00 00:00:00','Done','','10','10','3','0','support for multiple projects','','','','',''),
('3','3','1','0','1','26','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','60','10','2','0','track backlog size for each available story status','','','','',''),
('4','4','1','0','1','26','Feature',NULL,'0','0000-00-00 00:00:00','Todo','Doing,Todo,Done','90','10','11','0','Hiearchy of stories ','','','','',''),
('5','5','1','0','2','26','Feature',NULL,'0','0000-00-00 00:00:00','Done','','10','20','1','0','Create and add stories onto  the product/project backlog or specific iteration','','','','',''),
('6','6','1','0','1','26','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','50','20','2','0','create iteration backlog from product backlog ','','','','',''),
('7','7','1','0','1','26','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','70','30','3','0','The ability to search story content for words and terms ','including wildcards','','','',''),
('8','8','1','0','2','26','Feature',NULL,'0','0000-00-00 00:00:00','Done','','20','30','3','0','size / re-size stories','','','','',''),
('9','9','1','0','1','1','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','30','40','5','0','Export search results','','','','',''),
('10','10','1','0','3','26','Feature',NULL,'0','0000-00-00 00:00:00','Done','','40','40','5','0','Viewing stories','I can easily find, review and compare stories.<br />\r\nI need to be able to view all stories associated with a project in a variety of ways<br />\r\n* I can view all stories associated with a project on a single screen(i.e. I don&#39;t have to toggle between iterations and the backlog)<br />\r\n* I can search for stories by size, category, status or any keyword appearing in any text section.','Product Owner','','',''),
('11','11','1','0','1','1','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','10','50','3','0','Import Stories','','','','',''),
('12','12','1','0','1','1','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','40','50','2','0','Export Project','','','','',''),
('13','13','1','0','1','1','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','20','50','3','0','Export Iteration','','','','',''),
('14','14','1','0','4','26','Chore',NULL,'0','0000-00-00 00:00:00','Done','','30','50','1','0','Track story status','support at least todo, doing &amp; done','','','',''),
('15','15','1','0','3','27','Feature',NULL,'0','0000-00-00 00:00:00','Done','','20','60','3','0','support for multiple teams','','','','',''),
('16','16','1','0','1','26','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','80','60','13','0','Track changes to a story (Who, When & What)','','','','',''),
('17','17','1','0','4','4','Feature',NULL,'0','0000-00-00 00:00:00','Doing','','10','70','5','0','Enable A Story hiearchy','I can create a hiearchy of stories and easily break down large features into managable pieces of work.<br />\r\nI Need: to be able to create a hiearchy of stories that can be individually ordered beneath their parent and allow these to be broken down further so that the development team can split then into managable pieces of work that can fit into an iteration without losing sight of the business value feature being worked on.<br />\r\n* The ability to size individual stories without children<br />\r\n* Parent Story size is calculated from child stories<br />\r\n* Parent Stories can not be worked on/included in the iteration backlog.<br />\r\n* Parent Story Status can not be edited and is derived from child story status.<br />\r\n* easily view/navigate to a parent from a child and visa versa','Product Owner','','',''),
('18','18','1','0','1','25','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','30','70','0','0','Supportable Solution','in the event of failure FDB will get assistance in getting up and running again<br />\r\nProvide support for the solution','IT support analyst &amp; PMO','','',''),
('19','19','1','0','4','4','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','20','80','5','0','I can break stories down into managable pieces of work (Tasks)','So That: I can break stories down into managable pieces of work<br />\r\nI Need: to be able to create tasks that are assigned to a story and indicate when they have been completed as well as who completed the work<br />\r\n* Tasks linked to stories<br />\r\n* Tasks include a status &#39;Done&#39; , &#39;Not Done&#39; or similar.<br />\r\n* Tasks to indicate who will do/has done the work','Scrum team member','','',''),
('20','20','1','0','1','25','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','10','80','?','0','Access to backend information','We are able to report on all aspects of the project without having to define up front or pay for reports/dashboards to be developed.','ProdMgr/ProjMgr/PO/SM/Resource Manager/PMO/Exec','','',''),
('21','21','1','0','3','4','Feature',NULL,'0','0000-00-00 00:00:00','Done','','30','90','1','0','I can have a reaonable estimate of the work required to be completed.','I can have a reaonable estimate of the work required to be completed.<br />\r\nto be able to indicate the time spent/expected to be spent against a task<br />\r\n* able to indicate Time against an individual task<br />\r\n* able to extract and report on time spent against tasks','Scrum team member','','',''),
('22','22','1','0','1','25','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','50','90','0','0','Provide secure solution','project information, which can be commercially sensitive, is kept secure<br />\r\nUse of SSL, back end security and whatever other measures required','exec/PMO','','',''),
('23','23','1','0','1','25','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','20','100','0','0','The company can if so desired, host the tool on a local Database','The company can manage availability and data security, and are more protected against the risk of an external provider removing the service<br />\r\nAs the company become increasingly reliant on this tool, it is important that we at least have the option of hosting it on local hardware.<br />\r\nService is successfully deployed locally','Scrum Team','','',''),
('24','24','1','0','1','25','Feature',NULL,'0','0000-00-00 00:00:00','Todo','','40','110','0','0','not too complicated!','It can be used by all staff and administrated by project managers without specialist knowledge.<br />\r\ndo not want a solution that requires extensive or specialist understanding to administer or use','PMO','','',''),
('25','25','1','0','1','0','Feature',NULL,'0','0000-00-00 00:00:00','Todo','Todo','40','11100','0','0','Non Functional Requirements','','','','',''),
('26','26','1','0','1','0','Feature',NULL,'0','0000-00-00 00:00:00','Todo','Todo,Doing,Done','20','11200','41','0','Stories','','','','',''),
('27','27','1','0','1','0','Feature',NULL,'0','0000-00-00 00:00:00','Todo','Done','10','11200','6','0','Projects','','','','','');
/*!40000 ALTER TABLE `story` ENABLE KEYS */;

/*!40000 ALTER TABLE `story_status` DISABLE KEYS */;
INSERT INTO `story_status` VALUES ('1','1','Todo','1','D0D0D0'),('2','1','','2','A0A0A0'),('3','1','','3','66CCFF'),('4','1','Doing','4','3399FF'),('5','1','','5','0066FF'),('6','1','','6','A066FF'),('7','1','','7','FFFF54'),('8','1','OK to Review','8','FF7F05'),('9','1','','9','FF0000'),('10','1','Done','10','A0D050');
/*!40000 ALTER TABLE `story_status` ENABLE KEYS */;

/*!40000 ALTER TABLE `story_type` DISABLE KEYS */;
INSERT INTO `story_type` VALUES ('1','1','Feature','1'),('2','1','Chore','2'),('3','1','Bug','3'),('4','1','Debt','4');
/*!40000 ALTER TABLE `story_type` ENABLE KEYS */;

/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;

/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES ('1','3','0','100','Database Changes to support points per status per change at iteration and project level','0','4','0','2013-05-22 16:30:21'),('2','3','0','200','Add graph of points by date for each iteration (incl Backlog) and Project','0','6','0','2013-05-22 16:30:00'),('3','3','0','300','Use either of the images as a handle to drag and drop task order','0','6','0','2013-05-22 16:40:00');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('1','ADMI','21232f297a57a5a743894a0e4a801fc3','Admin (Do Not Delete)','admin','1'),('2','U2','7e58d63b60197ceb55a1c487989a3720','User2','user2','0'),('3','U3','92877af70a45fd6a2ed7fe81e1236b78','User3','user3@here.com','0');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

/*!40000 ALTER TABLE `user_project` DISABLE KEYS */;
INSERT INTO `user_project` VALUES ('1','1','0','0');
/*!40000 ALTER TABLE `user_project` ENABLE KEYS */;


-- phpMiniAdmin dump end

