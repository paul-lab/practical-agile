practical-agile
===============

Practical Agile, a free simple easy to use Scrum tool

Take a look at http://practicalagile.co.uk for more scrum/agile stuff

If you want to download an archive (windows only) that includes this application as well as minimal versions of MYSQL and Apache then go to http://practicalagile.co.uk/content/free-scrum-toolscrum-software. You will also find an up-to-date archive of the application and all files needed to set up and initially populate the database.

Practical Agile Scrum Tool


Create and manage Releases, Projects, Iterations, Stories, Story Tasks and Story Comments using a tool that is simple, easy to use and only does what it needs to do.


Probably the most useful feature, is that you can have a hierarchy of Stories. This is something that is normally only included in scrum tools at a cost. The hierarchy is easily accessible from most places and visible for each story that is part of a tree. This means Stories can have children (and obviously parents) to an almost infinite level. This helps with breaking larger pieces of work down into manageable addressable chunks making lide easier for both the team and Product owner. Work can then be addressed in a single iteration while not losing track of the ultimate business value goal or purpose of the work. 


Key Features
 
*  Free & Simple to Use
*  Releases (That can be made up of multiple projects)
*  A Story Hierarchy 
*  Automatic calculation of epic/parent size
*  A Story can have Tasks, Comments and Tags
*  Drag and Drop Epic, backlog and Iteration ordering
*  Multiple projects 
*  Users allocated per project (and Admins)
*  Multiple Views (List, Tree,  Board)
*  Burn-down/up and graph
*  Project completion prediction
*  Customisable Story Status
*  Customisable Story Size
*  Customisable Story fields
*  Import and Export of Stories
*  Easy to install
 


Requirements 
 
*  MySQL '* (using  v 5.0.67 & 5.5.?)
*  PHP* (using  5.4.7) incl GD2 & Mysqli
*  Web server'* (using Apache 2.2.9 & 2.4.3)
*  Ckeditor '+ 
*  Dynatree '+ 
*  JQuery+ & JqueryUI'+ 
*  jqplot +
 
  '* Included in full install
  '+ Included in both full and small install



Quick Install (Windows only)
* Download and extract  the PA-Full- Install  file into a new directory e.g. PracticalAgile  on the server you want to use (a local PC is fine for testing)
*  Browse  to the directory containing the extracted files 
*  Open and examine _Start.bat and _Stop.bat
*  Run _start.bat and make sure both Apache and Mysql start up
*  Navigate to Navigate to http://127.0.0.1:8088
*  Log in as admin : admin  (Yes I know that is not an e-mail address)
*  Browse the template and/or create your first project.
*  To resize your icons use _icon_resize.bat in the pa/images  folder


Slightly Slower Install 
*	Grab a XAMPP package that include the above requirements somewhere. (xampp-portable-lite-win32-1.8.1-VC9) from PortableApps work well for me)
*	Install it and makes sure that it works.
*	Download and extract  the PA-Site-Only installation file somewhere.
*	Drop the entire pa folder into your document root
*	Edit pa/include/dbconfig.inc.php and make sure that the server, port, user and password are correct.
*	Make sure that MySQL is running
*	Edit the file _setupdatabase.bat and correct any paths, ports, usernames and passwords.
*	Save and Execute the batch file
*	Stop and restart both MySQL and Apache
*	Navigate to http://<yourwebserver>/pa
*	Log in as admin : admin 
*	Change the admin password  and e-mail if you wish
*	Create your first project
*	To resize your icons use _icon_resize.bat in the pa/images  folder
