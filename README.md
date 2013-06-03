nsupdate-web
=============

(c) Pat O'Brien <drsprite@github.com> - http://github.com/drsprite
https://github.com/drsprite/nsupdate-web
Licensed under the GNU GPL 2.0 or later.

This PHP script will allow you to update your dynamic BIND zones directly 
from a URL POST/GET. This script grabs variables placed into the URL, and 
then passes them onto nsupdate which will then update your dynamic zone. 

This PHP script also uses a cache file for each hostname to detect IP changes 
before calling nsupdate. This helps to reduce unnecessary IP changes to DNS. 

Currently this project is a fork and includes updates specific to my setup - 
however I have generalized here for public use. 


### INSTALLATION

Copy this PHP script to the web accessible directory of your choosing that is on 
the same server that your dynamic DNS zones are on. 

Create a subdirectory called <b>cache</b>, and give the directory write permissions. 

Simply point your browser to the directory you dropped it into with a few extra 
parameters. Here's an example that will update the test subdomain to 127.0.0.1: 

    http://yourdomain.com/dyndns/index.php?hostname=test&ip=127.0.0.1
	

### DEBUG INFO

If for some reason this isn't working, you can check the variables being passed to the 
PHP array by adding this to your URL

    &debug=true

### AVAILABILITY

The fork can be found at https://github.com/drsprite/nsupdate-web


### CREDITS 
Original project by (c) Karl-Martin Skontorp <kms@skontorp.net> ~ http://22pf.org/
https://github.com/kms/nsupdate-web