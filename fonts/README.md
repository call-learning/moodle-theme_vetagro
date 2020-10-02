About the fonts
==

All fonts here are from Google fonts (https://fonts.google.com/).
They have been converted to various formats to stay compatible with different
web browsers by webify (https://github.com/ananthakumaran/webify) and the tool
woff2_compress from debian package 'woff2'.
There seems to be an alternative to webify as woff-tools package from Debian.

Mainly:
    
    for i in `ls *.ttf`; do webify $i; done
    for i in `ls *.ttf`; do sfnt2woff $i; done
    

