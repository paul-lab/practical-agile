@echo off

:reset to unhashed filenames
call_reverthash.bat


setlocal EnableExtensions
setlocal EnableDelayedExpansion

del /Q sedin.txt

for %%i in (*.js) do (
	echo %%i
	set filename=%%i
	set old=!filename!
	set new=.

	: get filename without .js
	set filenametrunc=!filename:~0,-3!

	:create a file containing the hash	
	md5sum !filename! > !filenametrunc!.hash

	set /P bloop=<!filenametrunc!.hash

	: set a var containing only the hash
	set filehash=!bloop:~0,32!

	:tidy up by deleting the file with the hash
	del /q !filenametrunc!.hash

	:if the file is already there, it has not changed
	IF EXIST *-!filehash!.js  ( 
		echo existsnochangessononeedtodoanythng
	
	) ELSE (
		echo new notexist
		IF EXIST !filenametrunc!-*.js (
			echo thereisanoldone
			dir /B !filenametrunc!-hash*.js > oldname.txt
			set /P old=<oldname.txt
			del /q !filenametrunc!-hash*.js
			del /q oldname.txt
		)
		echo weneedanewone
		set new=!filenametrunc!-hash!filehash!.js
		IF EXIST _yuicomp.jar  (
			java -jar _yuicomp.jar !filename! -o !new!
		) ELSE (
			copy !filename!  !new!
		)
		echo updatewhereitisused
		echo s/!old!/!new!/ >> sedin.txt
	)
)

:forloop thru files tochange
for /R ..\ %%i in (*.php) do (
	set filename=%%i
	IF EXIST sedin.txt (
		sed -f sedin.txt "!filename!" > 1.txt
 		del /q "!filename!"
		copy 1.txt "!filename!"
	)

)

del /q 1.txt
del /q sedin.txt

