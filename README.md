nsupdate-web
=============

This PHP script will allow you to update your dynamic BIND zones directly from a URL POST/GET. This script grabs variables placed into the URL, and then passes them onto nsupdate which will then update your dyanmic zone. 

Currently this project is a fork and includes updates specific to my setup - however I have generalized here for public use. 


### INSTALLATION

Copy this PHP script to the web accessible directory of your choosing that is on the same server that your dynamic DNS zones are on. 

Simply point your browser to the directory you dropped it into with a few extra parameters. 

Here's an example that will update the test subdomain to 127.0.0.1: 

    http://yourdomain.com/dyndns/index.php?hostname=test&ip=127.0.0.1
	

### DEBUG INFO

If for some reason this isn't working, you can check the variables being passed to the PHP array by adding this to your URL
    &debug=true

### AVAILABILITY

The fork can be found at https://github.com/drsprite/nsupdate-web