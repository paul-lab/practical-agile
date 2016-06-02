Practical Agile Scrum Tool

Create and manage Releases, Projects, Epics, Iterations, Stories, Story Tasks and Story Comments using a tool that is simple, easy to use and only does what it needs to do.

The most useful feature is that you can have a hierarchy of Stories, something that is normally only included in scrum tools at a cost. The hierarchy is easily accessible from most places and visible for each story that is part of a tree. This means Stories can have children (and obviously parents) to an almost infinite level. This helps with breaking larger pieces of work down into manageable addressable chunks. Work can then be addressed in a single iteration while not losing track of the ultimate business value goal, and purpose of the work. 

Key Features.
 
•	Free & Simple to Use
•	A Story Hierarchy 
•	Automatic calculation of epic/parent size
•	Story Tasks, Comments and Tags
•	Drag and Drop Story & Epic ordering
•	Multiple projects 
•	Users allocated per project
•	Multiple Views (List, Tree/Hierarchy ,  Scrum Board)
•	Burn-down/up and graph
•	Customisable Story Status for each project
•	Customisable Story Size  for each project
•	Customisable Story fields for each project
•	Bulk Import and Export of Stories
•	Easy to install
•	Free & Simple to Use.
 
 
Requirements 

(Included in the full install)
•	MySQL* 5+ (using 5.5)
•	PHP* 5+ (using  5.6) incl Mysqli & SQLite 3.8.3
•	Web server* 2.2 (using Apache 2.4)

(Included in both installs)
•	JHtml+ 
•	Fancytree+ 
•	JQuery+ & JqueryUI+ 
•	jqplot 
 

* Included in full install
+ Included in both full and small install


Quick Install (Windows only)
1.	Download and extract  the ‘PA-Full- Install’  file into a new directory ‘e.g. PracticalAgile ’ on the server you want to use (a local PC is fine for testing)
2.	Browse  to the directory containing the extracted files 
3.	Open and examine _Start.bat and _Stop.bat
4.	Run _start.bat and make sure both Apache and Mysql start up
5.	Navigate to Navigate to http://127.0.0.1:8088
6.	Log in as admin : admin  (Yes I know that is not an e-mail address)
7. 	Change the admin password  and e-mail if you wish
8.	Browse the template and/or create your first project.
9.	To resize your icons use ‘ _icon_resize.bat’ in the pa/images  folder

Slightly Slower Install 
1.	Grab a XAMPP package that include the above requirements somewhere. (‘xampp-portable-lite-win32-1.8.1-VC9’ from PortableApps work well for me)
2.	Install it and makes sure that it works.
3.	Download and extract  the ‘PA-Site-Only’ installation file somewhere.
4.	Drop the entire ‘pa’ folder into your document root
5.	Edit pa/include/dbconfig.inc.php and make sure that the server, port, user and password are correct.
6.	Make sure that MySQL is running
7.	Edit the file ‘_setupdatabase.bat’ and correct any paths, ports, usernames and passwords.
8.	Save and Execute the batch file
9.	Stop and restart both MySQL and Apache
10.	Navigate to http://<yourwebserver>/pa
11.	Log in as admin : admin 
12.	Change the admin password  and e-mail if you wish
13.	Create your first project
14.	To resize your icons use ‘ _icon_resize.bat’ in the pa/images  folder.
