
DROP TABLE IF EXISTS `audit`;
CREATE TABLE `audit` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `AID` INT KEY DEFAULT '0',
  `PID` INT KEY DEFAULT NULL,
  `User` TEXT(64) NOT NULL,
  `Action` TEXT(64) DEFAULT '',
  `From` longtext NOT NULL,
  `To` longtext NOT NULL,
  `When` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Parent_ID` INT NOT NULL,
  `User_Name` TEXT KEY DEFAULT NULL,
  `Comment_Object_ID` INT NOT NULL DEFAULT '0',
  `Story_AID` INT KEY DEFAULT NULL,
  `Comment_Text` text,
  `Comment_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  );

DROP TABLE IF EXISTS `dbver`;
CREATE TABLE `dbver` (
  `ID` INTEGER DEFAULT NULL,
  `CurrVer` text,
  `appver` tinytext
)  ;

DROP TABLE IF EXISTS `hint`;
CREATE TABLE `hint` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Hint_Text` text NOT NULL
) ;

DROP TABLE IF EXISTS `iteration`;
CREATE TABLE `iteration` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Locked` tinyint(1) DEFAULT '0',
  `Project_ID` INT KEY DEFAULT NULL,
  `Name` text C NOT NULL,
  `Points_Object_ID` bigint(20) DEFAULT NULL,
  `Comment_Object_ID` INT unsigned DEFAULT NULL,
  `Objective` text,
  `Start_Date` date NOT NULL,
  `End_Date` date NOT NULL
); 

DROP TABLE IF EXISTS `points_log`;
CREATE TABLE `points_log` (
  `ID`  INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INT KEY NOT NULL,
  `Points_Date` datetime KEY NOT NULL,
  `Object_ID` INT KEY NOT NULL,
  `Status` char(16) NOT NULL,
  `Story_Count` INT NOT NULL DEFAULT '0',
  `Points_Claimed` decimal(11,1) DEFAULT NULL
) ;

DROP TABLE IF EXISTS `project`;
CREATE TABLE `project` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Points_Object_ID` INT KEY UNIQUE DEFAULT NULL,
  `Comment_Object_ID` INT KEY UNIQUE DEFAULT NULL,
  `Project_Size_ID` INT NOT NULL,
  `Name` TEXT(50) DEFAULT NULL,
  `Desc` TEXT(255)  DEFAULT NULL,
  `As_A` tinyint(1) NOT NULL DEFAULT '0',
  `Col_2` tinyint(1) DEFAULT NULL,
  `Desc_1` char(12) DEFAULT 'So That',
  `Desc_2` char(12) DEFAULT 'I Need',
  `Acceptance` tinyint(1) DEFAULT '0',
  `Enable_Tasks` tinyint(1) DEFAULT '0',
  `Backlog_ID` INT NOT NULL,
  `Velocity` INT NOT NULL DEFAULT '0',
  `Average_Size` INT unsigned DEFAULT '0',
  `Category` TEXT(20) DEFAULT NULL,
  `Archived` tinyint(1) DEFAULT NULL
)   ;

DROP TABLE IF EXISTS `queries`;
CREATE TABLE `queries` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Qseq` int(10) DEFAULT '0',
  `Desc` text NOT NULL,
  `QSQL` text NOT NULL,
  `Qorder` text NOT NULL,
  `External` tinyint(1) NOT NULL
)  ;

DROP TABLE IF EXISTS `release_details`;
CREATE TABLE `release_details` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Locked` tinyint(1) DEFAULT '0',
  `Start` date DEFAULT NULL,
  `End` date DEFAULT NULL,
  `Name` TEXT(255) DEFAULT NULL,
  `Points_Object_ID` INT KEY DEFAULT NULL,
  `Comment_Object_ID` INT KEY DEFAULT NULL
)  ;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `sessionid` INT NOT NULL PRIMARY KEY,
  `sessiondata` text
)  ;


DROP TABLE IF EXISTS `size_type`;
CREATE TABLE `size_type` (
  `ID` INTEGER NOT NULL PRIMARY KEY, 
  `Desc` text NOT NULL
)   ;

DROP TABLE IF EXISTS `size`;
CREATE TABLE `size` (
  `ID` INTEGER NOT NULL PRIMARY KEY, 
  `Type` INT KEY NOT NULL,
  `Order` INT DEFAULT NULL,
  `Value` char(4) ,
  CONSTRAINT `size_ibfk_1` FOREIGN KEY (`Type`) REFERENCES `size_type` (`ID`) ON DELETE CASCADE
) ;


DROP TABLE IF EXISTS `story`;
CREATE TABLE `story` (
  `AID` INTEGER NOT NULL PRIMARY KEY ,
  `ID` INT  KEY NOT NULL,
  `Project_ID` INT KEY NOT NULL,
  `Release_ID` INT KEY  DEFAULT NULL,
  `Iteration_ID` INT KEY NOT NULL,
  `Parent_Story_ID` INT KEY NOT NULL DEFAULT '0',
  `Type` text,
  `Created_By_ID` INT DEFAULT NULL,
  `Owner_ID` INT DEFAULT NULL,
  `Created_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` char(16) DEFAULT NULL,
  `Children_Status` TEXT(200) DEFAULT NULL,
  `Epic_Rank` INT DEFAULT NULL,
  `Iteration_Rank` INT DEFAULT NULL,
  `Size` char(4) DEFAULT NULL,
  `Blocked` tinyint(1) DEFAULT NULL,
  `Summary` text,
  `Col_1` longtext,
  `As_A` text,
  `Col_2` longtext,
  `Acceptance` longtext,
  `Tags` text,
  CONSTRAINT `story_ibfk_1` FOREIGN KEY (`Iteration_ID`) REFERENCES `iteration` (`ID`),
  CONSTRAINT `story_ibfk_3` FOREIGN KEY (`Project_ID`) REFERENCES `project` (`ID`)
)  ;

DROP TABLE IF EXISTS `story_status`;
CREATE TABLE `story_status` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Project_ID` INT KEY NOT NULL,
  `Desc` char(16) DEFAULT NULL,
  `Policy` TEXT(128) DEFAULT NULL,
  `Order` INT DEFAULT NULL,
  `RGB` char(6) NOT NULL DEFAULT 'FFFFFF'
) ;

DROP TABLE IF EXISTS `story_type`;
CREATE TABLE `story_type` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Project_ID` INT KEY NOT NULL,
  `Desc` char(10) DEFAULT NULL,
  `Order` INT NOT NULL
)  ;

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Project_ID` INT unsigned DEFAULT NULL,
  `Desc` text
)  ;

DROP TABLE IF EXISTS `task`;
CREATE TABLE `task` (
  `ID` INTEGER NOT NULL PRIMARY KEY ,
  `Story_AID` INT KEY DEFAULT NULL,
  `User_ID` INT KEY DEFAULT NULL,
  `Rank` INT DEFAULT NULL,
  `Desc` text,
  `Done` tinyint(1) DEFAULT NULL,
  `Expected_Hours` INT DEFAULT '0',
  `Actual_Hours` INT DEFAULT '0',
  `Task_Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
)  ;

DROP TABLE IF EXISTS `upload`;
CREATE TABLE `upload` (
  `AID` INTEGER NOT NULL PRIMARY KEY,
  `Name` binary KEY UNIQUE NOT NULL,
  `Desc` TEXT(128) NOT NULL,
  `Type` TEXT(32) NOT NULL,
  `Size` INT NOT NULL
)  ;

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `ID` INTEGER NOT NULL PRIMARY KEY,
  `Initials` char(4) DEFAULT NULL,
  `Password` TEXT(64) DEFAULT NULL,
  `Friendly_Name` TEXT(64) DEFAULT NULL,
  `EMail` TEXT(64) DEFAULT NULL,
  `Admin_User` tinyint(1) NOT NULL DEFAULT '0',
  `Disabled_User` tinyint(1) DEFAULT '0'
)   ;

DROP TABLE IF EXISTS `user_project`;
CREATE TABLE `user_project` (
  `Project_ID` INTEGER KEY DEFAULT NULL,
  `User_ID` INT KEY DEFAULT NULL,
  `Readonly` tinyint(1) NOT NULL,
  `Project_Admin` tinyint(1) NOT NULL DEFAULT '0'
)  ;


