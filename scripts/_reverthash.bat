@echo off
rem place in www\pa\scripts\

setlocal EnableExtensions
setlocal EnableDelayedExpansion

del /Q sedin.txt

del /Q *-hash*.js

for %%i in (*.js) do (
	echo %%i
	set filename=%%i
	set old=!filename!
	set new=.

	: get filename without .js
	set filenametrunc=!filename:~0,-3!

	echo s/!filenametrunc!-hash.................................js/!old!/ >> sedin.txt


)
	:forloop thru files tochange
	for /R ..\ %%j in (*.php) do (
		set filename=%%j
		sed -f sedin.txt "!filename!" > 1.txt
		del/q "!filename!"
		copy 1.txt "!filename!"
	)


del /q 1.txt
del /q sedin.txt
