echo off
set database=practicalagile
if "%database%"=="" (
  echo Please set database Variable
  pause
  exit
)
set binpath=..\..\usr\local\mysql\bin
set mysqlport=3311

echo.
echo Creating database %database% based on _dbstructure.txt
echo.
echo ^^C to abort
echo.
pause

time /T 
echo CREATE database %database%; > database.txt
%binpath%\mysql  --user=root --password=root --port=%mysqlport% < database.txt
%binpath%\mysql  --user=root --password=root --port=%mysqlport% %database%  < _dbstructure.txt
del/q database.txt

echo Importing data
time /T 
%binpath%\mysql  --user=root --password=root --port=%mysqlport% %database% < _data.sql
time /T 

echo Importing Reports
time /T 
%binpath%\mysql  --user=root --password=root --port=%mysqlport% %database% < _queries.sql
time /T 

pause

