
DROP TABLE IF EXISTS "audit";
CREATE TABLE `audit` (
  `ID` INTEGER NOT NULL primary key,
  `AID` INTEGER  KEY DEFAULT '0' ,
  `PID` INTEGER  DEFAULT NULL,
  `User` TEXT NOT NULL,
  `Action` TEXT DEFAULT '',
  `From` TEXT NOT NULL,
  `To` NOT NULL,
  `When` DATETIME NOT NULL DEFAULT (datetime('now','localtime'))
);
CREATE INDEX "audit_PID" ON "audit" ("PID");
CREATE INDEX "audit_AID" ON "audit" ("AID");

DROP TABLE IF EXISTS "comment";
CREATE TABLE `comment` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Parent_ID` INTEGER NOT NULL,
  `User_Name` TEXT DEFAULT NULL,
  `Comment_Object_ID` INTEGER NOT NULL DEFAULT '0',
  `Story_AID` INTEGER DEFAULT NULL,
  `Comment_Text` TEXT,
  `Comment_Date` DATETIME NOT NULL DEFAULT (datetime('now','localtime'))
);
CREATE INDEX "User_ID" ON "comment" ("User_Name");
CREATE INDEX "Object_ID" ON "comment" ("Story_AID");

DROP TABLE IF EXISTS "dbver";
CREATE TABLE `dbver` (
  `ID` INTEGER DEFAULT NULL,
  `CurrVer` TEXT,
  `appver` TEXT
);

DELETE FROM "dbver";
INSERT INTO "dbver" ("ID", "CurrVer", "appver") VALUES (1,	'1.17',	'2.56');

DROP TABLE IF EXISTS "hint";
CREATE TABLE "hint" (
  "ID" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "Hint_Text" text NOT NULL
);


DROP TABLE IF EXISTS "iteration";
CREATE TABLE `iteration` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Locked` INTEGER DEFAULT '0',
  `Project_ID` INTEGER DEFAULT NULL,
  `Name` TEXT NOT NULL,
  `Points_Object_ID`  INTEGER DEFAULT NULL,
  `Comment_Object_ID` INTEGER DEFAULT NULL,
  `Objective` TEXT,
  `Start_Date` DATE NOT NULL,
  `End_Date` DATE NOT NULL
);

CREATE INDEX "Project_ID" ON "iteration" ("Project_ID");

DROP TABLE IF EXISTS "points_log";
CREATE TABLE `points_log` (
  `ID`  INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INTEGER NOT NULL,
  `Points_Date` DATETIME NOT NULL DEFAULT (datetime('now','localtime')),
  `Object_ID` INTEGER NOT NULL,
  `Status`  TEXT NOT NULL,
  `Story_Count` INTEGER NOT NULL DEFAULT '0',
  `Points_Claimed` NUMERIC DEFAULT NULL
);

CREATE INDEX "points_log_Points_Date" ON "points_log" ("Points_Date");

CREATE INDEX "points_log_Object_ID" ON "points_log" ("Object_ID");
CREATE INDEX "points_log_Project_ID" ON "points_log" ("Project_ID");



DROP TABLE IF EXISTS "project";
CREATE TABLE `project` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Points_Object_ID`  INTEGER DEFAULT NULL,
  `Comment_Object_ID` INTEGER DEFAULT NULL,
  `Project_Size_ID` INTEGER NOT NULL,
  `Name` TEXT  DEFAULT NULL,
  `Desc` TEXT  DEFAULT NULL,
  `As_A` INTEGER NOT NULL DEFAULT '0',
  `Col_2` INTEGER DEFAULT NULL,
  `Desc_1` TEXT DEFAULT 'So That',
  `Desc_2` TEXT DEFAULT 'I Need',
  `Acceptance` INTEGER DEFAULT '0',
  `Enable_Tasks` INTEGER DEFAULT '0',
  `Backlog_ID` INTEGER NOT NULL,
  `Velocity` INTEGER NOT NULL DEFAULT '0',
  `Average_Size` INTEGER DEFAULT '0',
  `Category` TEXT DEFAULT NULL,
  `Archived` INTEGER DEFAULT 0
);

CREATE UNIQUE INDEX "project_Points_Date" ON "project" ("Points_Object_ID");

DROP TABLE IF EXISTS "queries";
CREATE TABLE `queries` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Qseq` INTEGER DEFAULT '0',
  `Desc` TEXT NOT NULL,
  `QSQL` TEXT NOT NULL,
  `Qorder` TEXT NOT NULL,
  `External` INTEGER NOT NULL
);


DROP TABLE IF EXISTS "release_details";
CREATE TABLE `release_details` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Locked` INTEGER DEFAULT '0',
  `Start` DATE DEFAULT NULL,
  `End` DATE DEFAULT NULL,
  `Name` TEXT  DEFAULT NULL,
  `Points_Object_ID` INTEGER DEFAULT NULL,
  `Comment_Object_ID` INTEGER DEFAULT NULL
);

CREATE INDEX "release_details_Points_Comment_Object_ID" ON "release_details" ("Comment_Object_ID");

CREATE INDEX "release_details_Points_Object_ID" ON "release_details" ("Points_Object_ID");

DROP TABLE IF EXISTS "sessions";
CREATE TABLE `sessions` (
  `sessionid` INTEGER NOT NULL PRIMARY KEY,
  `sessiondata` TEXT
);


DROP TABLE IF EXISTS "size_type";
CREATE TABLE `size_type` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Desc` TEXT NOT NULL
);


DROP TABLE IF EXISTS "size";
CREATE TABLE `size` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Type` INTEGER NOT NULL,
  `Order` INTEGER DEFAULT NULL,
  `Value` TEXT  DEFAULT NULL,
  FOREIGN KEY (`Type`) REFERENCES `size_type`(`ID`) ON DELETE CASCADE
);

CREATE INDEX "isize_Type" ON "size" ("Type");

DROP TABLE IF EXISTS "story";
CREATE TABLE `story` (
  `AID` INTEGER NOT NULL PRIMARY KEY,
  `ID` INTEGER NOT NULL,
  `Project_ID` INTEGER NOT NULL,
  `Release_ID` INTEGER DEFAULT NULL,
  `Iteration_ID` INTEGER NOT NULL,
  `Parent_Story_ID` INTEGER NOT NULL DEFAULT '0',
  `Type` TEXT ,
  `Created_By_ID` INTEGER DEFAULT NULL,
  `Owner_ID` INTEGER DEFAULT NULL,
  `Created_Date` DATETIME NOT NULL DEFAULT (datetime('now','localtime')),
  `Status`  TEXT  DEFAULT NULL,
  `Children_Status` TEXT DEFAULT NULL,
  `Epic_Rank` INTEGER DEFAULT NULL,
  `Iteration_Rank` INTEGER DEFAULT NULL,
  `Size` TEXT DEFAULT NULL,
  `Blocked` INTEGER DEFAULT NULL,
  `Summary` TEXT ,
  `Col_1` longTEXT ,
  `As_A` TEXT ,
  `Col_2` longTEXT ,
  `Acceptance` longTEXT ,
  `Tags` TEXT ,
  FOREIGN KEY (`Iteration_ID`) REFERENCES `iteration` (`ID`),
  FOREIGN KEY (`Project_ID`) REFERENCES `project` (`ID`)
);

CREATE INDEX "story_ID" ON "story" ("ID");
CREATE INDEX "story_Iteration_ID" ON "story" ("Iteration_ID");
CREATE INDEX "story_Parent_Story_ID" ON "story" ("Parent_Story_ID");
CREATE INDEX "story_Release_ID" ON "story" ("Release_ID");
CREATE INDEX "story_Project_ID" ON "story" ("Project_ID");


DROP TABLE IF EXISTS "story_status";
CREATE TABLE `story_status` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INTEGER NOT NULL,
  `Desc`  TEXT  DEFAULT NULL,
  `Policy` TEXT DEFAULT NULL,
  `Order` INTEGER DEFAULT NULL,
  `RGB` char(6)  NOT NULL DEFAULT 'FFFFFF'
);

CREATE INDEX "story_status_Project_ID" ON "story_status" ("Project_ID");


DROP TABLE IF EXISTS "story_type";
CREATE TABLE `story_type` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INTEGER NOT NULL,
  `Desc` TEXT  DEFAULT NULL,
	`Order` INTEGER DEFAULT 0
);


DROP TABLE IF EXISTS "tags";
CREATE TABLE `tags` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INTEGER DEFAULT NULL,
  `Desc` TEXT
);

DELETE FROM "tags";
INSERT INTO "tags" ("ID", "Project_ID", "Desc") VALUES (1,	1,	'test,A tag with spaces,TEST,Test,tag2,tag1,tag3');

DROP TABLE IF EXISTS "task";
CREATE TABLE `task` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Story_AID` INTEGER DEFAULT NULL,
  `User_ID` INTEGER DEFAULT NULL,
  `Rank` INTEGER DEFAULT NULL,
  `Desc` TEXT ,
  `Done` INTEGER DEFAULT NULL,
  `Expected_Hours` INTEGER DEFAULT '0',
  `Actual_Hours` INTEGER DEFAULT '0',
  `Task_Date` DATETIME NOT NULL DEFAULT (datetime('now','localtime')) 
);

CREATE INDEX "task_User_ID" ON "task" ("User_ID");
CREATE INDEX "task_Story_AID" ON "task" ("Story_AID");


DROP TABLE IF EXISTS "upload";
CREATE TABLE `upload` (
  `AID` INTEGER NOT NULL,
  `Name` BLOB NOT NULL UNIQUE PRIMARY KEY,
  `Desc` TEXT NOT NULL,
  `Type` TEXT NOT NULL,
  `Size` INTEGER NOT NULL
);


DROP TABLE IF EXISTS "user";
CREATE TABLE `user` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Initials` TEXT  DEFAULT NULL,
  `Password` TEXT  DEFAULT NULL,
  `Friendly_Name` TEXT  DEFAULT NULL,
  `EMail` TEXT  DEFAULT NULL,
  `Admin_User` INTEGER NOT NULL DEFAULT '0',
  `Disabled_User` INTEGER DEFAULT '0'
);


DROP TABLE IF EXISTS "user_project";
CREATE TABLE `user_project` (
  `Project_ID` INTEGER DEFAULT NULL,
  `User_ID` INTEGER DEFAULT NULL,
  `Readonly` INTEGER NOT NULL DEFAULT '0',
  `Project_Admin` INTEGER NOT NULL DEFAULT '0'
);

CREATE INDEX "user_project_User_ID" ON "user_project" ("User_ID");
CREATE INDEX "user_project_Project_ID" ON "user_project" ("Project_ID");
