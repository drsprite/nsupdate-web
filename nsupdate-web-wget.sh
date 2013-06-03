#!/bin/sh
# nsupdate-web
# Updates BIND records.
#
# (c) Karl-Martin Skontorp <kms@skontorp.net> ~ http://22pf.org/
# Licensed under the GNU GPL 2.0 or later.
#
# Forked and modified by Pat O'Brien
# https://github.com/drsprite/nsupdate-web
#
######################################################################


# Config
WGET=/usr/bin/wget

URL="http://yourdomain.com/nsupdate-web/"
HOSTNAME="test"
IP="" # Default
# TTL and KEY are not needed currently as I have deprecated them in the index.php file. 
#TTL="30" # Seconds TTL
#KEY="xxx" # Remember to URL encode this!

# Execute
PARAMS="?hostname=$HOSTNAME&ip=$IP"

$WGET -q -O - $URL$PARAMS
