@echo off
set database=practicalagile
if "%database%"=="" (
  echo Please set database Variable
  pause
  exit
)

if NOT exist ..\..\core\mysql\bin\mysql.exe (set binpath=..\..\usr\local\mysql\bin) else (set binpath=..\..\core\mysql\bin)
set mysqlport=3311
set usr=root
set pwd=root

echo.
echo #######################################################
echo #                                                     #
echo #          This will DROP and RE-CREATE the           #
echo #   MYSql database %database% and initial data    #
echo #       based on _dbstructure.txt and _data.sql       #
echo #                                                     #
echo #######################################################
echo.
set /p id="Enter 'yes' to Continue: "
if /I NOT %id%==YES goto :endit

echo.
time /T 
echo Creating Database
echo SET GLOBAL sql_mode='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'; > database.txt
echo DROP database IF EXISTS %database%; >> database.txt
echo CREATE database %database%; >> database.txt
%binpath%\mysql  --user=%usr% --password=%pwd% --port=%mysqlport% < database.txt 
del /q database.txt
echo Creating Database Structure
%binpath%\mysql  --user=%usr% --password=%pwd% --port=%mysqlport% %database%  < _dbstructure.txt 

echo Importing data
time /T 
%binpath%\mysql  --user=%usr% --password=%pwd% --port=%mysqlport% %database% < _data.sql 

echo Importing Reports
time /T 
%binpath%\mysql  --user=%usr% --password=%pwd% --port=%mysqlport% %database% < _queries.sql 

echo.
echo #######################################
echo #                                     #
echo #       Initial Setup Complete        #
echo #                                     #
echo #######################################
echo.
goto :last 

:endit
echo.
echo #######################################
echo #                                     #
echo #          Setup CANCELLED            #
echo #      Initial Data NOT set up        #
echo #                                     #
echo #######################################
:last
time /T 
pause

