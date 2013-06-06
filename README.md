nsupdate-web
=============

(c) Pat O'Brien <drsprite@github.com> - http://github.com/drsprite
Licensed under the GNU GPL 2.0 or later.

This PHP script will allow you to update your dynamic BIND zones directly 
from a URL POST/GET. This script grabs variables placed into the URL, and 
then passes them onto nsupdate which will then update your dynamic zone. 

This PHP script also uses a cache file for each hostname to detect IP changes 
before calling nsupdate. This helps to reduce unnecessary IP changes to DNS. 

I've forked this project and set it up for my own personal use, however I have
generalized it for public use. Just need to change the yourdomain.com values to
your domain. I'm also not a master at PHP & MySQL, so if you see any errors, 
please let me know! 


### INSTALLATION

There are two types of ways to install this script. 

1. Using a flatfile to keep track of IP history
2. Using MySQL to keep track of IP history, as well as a way to get domain status.

Option 1:
1. Copy index_flatfile.php to the web accessible directory of your choosing that is on 
   the same server that your dynamic DNS zones are on.
2. Create a subdirectory called <b>cache</b>, and give the directory write permissions.


Option 2:
1. Copy index_mysql.php to the web accessible directory of your choosing that is on the
same server that your dynamic DNS zones are on.
2. Copy config.php, styles.css to the same directory.
3. Edit the values within config.php to match your MySQL server, username, password and database.
4. Run the dyndns.sql script to create the table that will be used.

Once you have either option installed, simply point your browser to the directory you dropped 
it into with a few extra parameters. 

Here's an example that will update the test subdomain to 127.0.0.1: 

    http://yourdomain.com/dyndns/index.php?hostname=test&ip=127.0.0.1
	

MySQL Version: to get a status on the dynamic domains that are currently configured for your domain:

    http://yourdomain.com/dyndns/index.php?status
	
Note: the status just polls the MySQL database. If you have dynamic zones that have been set 
prior to using this script, they will not show.
	
	
### DEBUG INFO

If for some reason this isn't working, you can check the variables being passed to the 
PHP array by adding this to your URL

    http://yourdomain.com/dyndns/index.php?debug

### AVAILABILITY

The fork can be found at https://github.com/drsprite/nsupdate-web


### CREDITS 
Original project by (c) Karl-Martin Skontorp <kms@skontorp.net>
https://github.com/kms/nsupdate-web