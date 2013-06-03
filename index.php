<?php
# nsupdate-web
# Updates BIND records via web URL for uses such as DIY dynamic DNS
#
# Original by (c) Karl-Martin Skontorp <kms@skontorp.net> ~ http://22pf.org/
# https://github.com/kms/nsupdate-web
# Licensed under the GNU GPL 2.0 or later.
#
# Forked and modified by Pat O'Brien
# https://github.com/drsprite/nsupdate-web
#
######################################################################

header("Content-Type: text/plain");

// Config
$nsupdate = '/usr/bin/nsupdate -v -k /path/to/your/top/secret/nsupdate/key';
$cacheDir = 'cache';

// GET/POST parameters within the URL
$config['hostname'] = $_REQUEST['hostname'];
$config['ip'] = $_REQUEST['ip'];
// Deprecated for my needs: 5/31/2013
	// The key file is currently defined in $nsupdate
		//$config['key'] = $_REQUEST['key'];
	// TTL is no longer needed since it's hard coded in $nsupdateCommands
		//$config['ttl'] = $_REQUEST['ttl'];

// If no IP is defined in URL, then we'll use the remote host address as the IP
if (empty($config['ip'])) {
    $config['ip'] = $_SERVER['REMOTE_ADDR'];
}

// Send an error if there is a parameter missing. 
// The below items within the array are required.
$error = false;
$error |= empty($config['hostname']);
$error |= empty($config['ip']);
// Deprecated for my needs: 5/31/2013
	// TTL is no longer needed since it's hard coded in $nsupdateCommands
		//$error |= empty($config['ttl']);
	// Key is currently not required. 
		//$error |= empty($config['key']);

// Define a debug mode callable from the URL, if needed. 
if ($_REQUEST['debug'] == 'true' || $error) {
    echo "nsupdate web-interface\n";
    echo "----------------------\n\n";
    echo "Error: Missing parameters\n\n";
    print_r($config);
    exit;
}

// Handle cache of old/current IP address.
$cacheFile = $cacheDir . '/' . basename($config['hostname']);

if (is_readable($cacheFile)) {
    $config['old_ip'] = trim(file_get_contents($cacheFile));
}

// Exit now unless IP address is new.
if ($config['old_ip'] == $config['ip']) {
	echo "nsupdate web-interface\n";
	echo "----------------------\n\n";
	echo date("D M j G:i:s T Y") . "\n\n";
	echo "Previous IP: " . $config['old_ip'] . "\n";
	echo "New IP:      " . $config['ip'] . "\n";
	echo "\nError: DNS not updated!\n\n";
	echo "Your IP hasn't changed - exiting.\n\n";
    exit;
}

// Original set of commands for nsupdate. I found this didn't work for me,
// however I am keeping them here for future reference. 
//$nsupdateCommands =  'key ' . $config['hostname'] 
//    . ' ' . $config['key'] .  "\n";
//$nsupdateCommands .= 'key ' . $config['key'] . "\n";
//$nsupdateCommands .= 'update delete ' . $config['hostname'] . "\n";
//$nsupdateCommands .= 'update add ' . $config['hostname'] . ' ' 
//    .  $config['ttl'] . ' A ' . $config['ip'] . "\n";
//$nsupdateCommands .= "send\n\n";

// Setup the nsupdate commands variable.
$nsupdateCommands = "zone yourdomain.com";
$nsupdateCommands .= "\n";
$nsupdateCommands .= "update delete " . $config['hostname'] . ".yourdomain.com. A";
$nsupdateCommands .= "\n";
$nsupdateCommands .= "update add " . $config['hostname'] . ".yourdomain.com. 1 A " . $config['ip'];
$nsupdateCommands .= "\n";
$nsupdateCommands .= "send";
$nsupdateCommands .= "\n";

// Prepare to execute nsupdate binary.
$descriptors = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w")
);
$returnValue = 127;
$errors = '';

// Execute nsupdate and print status info.
$process = proc_open($nsupdate, $descriptors, $pipes);

echo "nsupdate web-interface\n";
echo "----------------------\n\n";
echo date("D M j G:i:s T Y") . "\n\n";
echo "Previous IP: " . $config['old_ip'] . "\n";
echo "New IP:      " . $config['ip'] . "\n";

if (is_resource($process)) {
    fwrite($pipes[0], $nsupdateCommands);
    fclose($pipes[0]);

    while ($s = fgets($pipes[1], 1024)) {
        $errors .= 'STDOUT: ' . $s;
    }
    fclose($pipes[1]);

    while ($s = fgets($pipes[2], 1024)) {
        $errors .= 'STDERR: ' . $s;
    }
    fclose($pipes[2]);

    $returnValue = proc_close($process);

    $errors .= 'RETURN: ' . $returnValue . "\n";
}

// Output errors if unsuccessfull, else update cache.
if ($returnValue != 0) {
    echo "\nError: DNS not updated!\n\n";
    echo $errors;
} else {
    echo "\nDNS updated!\n";

    // Update cache.
    if (is_writable($cacheFile)
        || (!file_exists($cacheFile) && is_writeable($cacheDir))) {
        $f = fopen($cacheFile, 'w');
        fwrite($f, $config['ip']);
        fclose($f);
        echo "Cache updated!\n";
    } else {
        echo "Error: Cache not updated!\n";
    }
}

?>