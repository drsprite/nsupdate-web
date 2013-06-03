# nsupdate-web-powershell
# Creates a simple call to the nsupdate-web update URL to dynamically update 
# the DNS for the host that called this script. 
#
# (c) Pat O'Brien <drsprite@github.com> ~ http://github.com/drsprite
# https://github.com/drsprite/nsupdate-web
# Licensed under the GNU GPL 2.0 or later.
#
######################################################################

$update = (new-object net.webclient).DownloadString("http://yourdomain.com/dyndns/index.php?hostname=test")