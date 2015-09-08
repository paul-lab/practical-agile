@echo off
SetLocal EnableDelayedExpansion

echo  select default Icon file size
echo.
echo  16  
echo  24  
echo  32 
echo.
Set choice=0
set /P choice=Default Size:

if '%choice%'=='16' (
	set small=16
	set large=24
	goto ValidChoice
)
if '%choice%'=='24' (
	set small=16
	set large=32
	goto ValidChoice
)
if '%choice%'=='32' (
	set small=24
	set large=32
	goto ValidChoice
)

goto endit


:ValidChoice

for %%i in (*%choice%.png) do (
	set file=%%i
	set short=!file:~0,-6%!
	copy /y !short!%small%.png  !short!-small.png > NUL
	copy /y !short!%large%.png  !short!-large.png > NUL
	copy /y %%i !short!.png > NUL
)


goto exit

:endit
echo.  
echo  Bad Size please enter 16 or 24 or 32
echo.

:exit
echo.  
echo  Done!
echo.
pause