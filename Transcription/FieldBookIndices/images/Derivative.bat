@echo off
setlocal enabledelayedexpansion
set "CMD_STR= FORFILES /P "..\ImageMagick" /S /M *.tif /C "cmd /c echo @path""
set CONCAT_STR=
for /f %%i in ('%CMD_STR%') do set "CONCAT_STR=!CONCAT_STR! %%i"
convert -quality 20 -compress jpeg !CONCAT_STR! -append Output/Append_Relative.pdf
