
/*!40000 ALTER TABLE `queries` DISABLE KEYS */;
INSERT INTO `queries` VALUES 
('1','10','My Current Stories','story.Owner_ID={User}  and story.Status<>\'Done\'','order by Iteration_Rank','1'),
('2','20','My \'Done\' Stories','story.Owner_ID={User}  and story.Status=\'Done\' ','Order by Epic_Rank','1'),
('3','30','All Work',' 0 = (select count(story.AID) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),
('4','40','All \'Done\' Work',' story.Status=\'Done\' and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),
('5','50','All Outstanding Work',' story.Status<>\'Done\' and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),
('6','60','Unsized Stories','story.Size=\'?\' or story.Size is NULL or story.Size=\'\'','order by Iteration_Rank','1'),
('7','70','Blocked Work',' story.Blocked<>0 ','Order by Iteration_Rank','1'),
('8','80','Epic Stories',' 0 < (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by story.Epic_Rank','1'),
('9','90','Has Parent','Parent_Story_ID>0','order by story.Epic_Rank','1'),
('10','100','NO Parent','Parent_Story_ID<1','order by story.Epic_Rank','1'),
('11','110','Not in Release','(story.Release_ID is NULL or story.Release_ID=0) and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','Order by story.Epic_Rank','1'),
('100','100','Iteration Hours','select concat( initials,\' - \' ,Friendly_Name) as User ,IF(Done=1,\'Yes\',\'No\') as Done , sum( Expected_Hours) as \'Est. hrs\', sum(Actual_Hours) as \'Act. hrs\' from story left join iteration on story.Iteration_ID = iteration.ID, task left JOIN user on task.User_ID = user.ID where story.Iteration_ID = 1 and task.Story_AID = story.AID group by User_ID, Done, Iteration_ID ','','2'),
('103','5','Project Progress','SELECT story.Type, story.Status, count(story.Type) as \'# Stories\', sum(story.Size) as \'# Points\' FROM story where story.Project_ID={Project} and ( 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID)) group by story.Type, story.Status','','2'),
('110','25','My Tasks','select concat( initials,\' - \' ,Friendly_Name) as User, story.ID as \'Story #\' ,task.Desc as Task,IF(Done=0,\'-\',IF(Done=1,\'In Progress\',\'Done\')) as Status, Expected_Hours as \'Est. hrs\', Actual_Hours as \'Act. hrs\' from story left join iteration on story.Iteration_ID = iteration.ID, task left JOIN user on task.User_ID = user.ID where story.Iteration_ID = {Iteration} and User_ID = {User} and task.Story_AID = story.AID order by story.Iteration_Rank, story.ID, Done','','2'),
('120','120','Iteration Tasks','select concat( initials,\" - \" ,Friendly_Name) as User, story.ID as \'Story #\' ,task.Desc  as Task,IF(Done=0,\'-\',IF(Done=1,\'In Progress\',\'Done\')) as Status,  Expected_Hours as \'Est. hrs\', Actual_Hours as \'Act. hrs\' from story left join iteration on story.Iteration_ID = iteration.ID, task  left JOIN user on task.User_ID = user.ID  where story.Iteration_ID = {Iteration} and task.Story_AID = story.AID order by story.Iteration_Rank, story.ID, User_ID,  Done','','2'),
('130','130','Project Progress','SELECT story.Type, story.Status, count(story.Type) as \'# Stories\', sum(story.Size) as \'# Points\' FROM story where story.Project_ID={Project} and ( 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID)) group by story.Type, story.Status','','2'),('200','500','Audit (recent 200)','SELECT * from audit order by ID desc limit 200','','2'),
('500','500','Audit (recent 200)','SELECT * from audit order by ID desc limit 200','','2'),
('510','510','Project Audit','SELECT * from audit where PID={Project} order by ID desc','','2'),('520','520','Non Project Audit','SELECT * from audit where PID=0 order by ID desc','','2'),
('530','530','Uploaded Files','select concat(\'<a href=story_List.php?searchstring=%23\',story.ID,\'&PID=\',story.Project_ID,\'&Type=search>#\',story.ID,\'</a>\') as \'Story\', story.Summary as \'Summary\', upload.Desc as \'File\' , CONCAT(\'<a href=upload/\',HEX(upload.Name),\'.\',convert(upload.Type using utf8),\'>Link</a>\')as \'Link\' from story inner join `upload` on upload.AID=story.AID  where story.Project_ID=1 order by upload.Desc','','2'),
('550','550','Release plan','(story.Release_ID > 0) and 0 = (select count(*) from story as child where child.Parent_Story_ID=story.AID) ','order by  Release_ID','1');
/*!40000 ALTER TABLE `queries` ENABLE KEYS */;
