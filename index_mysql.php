<?php
# nsupdate-web
# Updates BIND records via web URL for uses such as DIY dynamic DNS
#
# (c) 2013 Pat O'Brien - drsprite@github.com
# https://github.com/drsprite/nsupdate-web
# Licensed under the GNU GPL 2.0 or later.
#
######################################################################

// Setup MySQL.
require("config.php");
if (!mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS)) {
	die("Unable to connect to database.");
}
if (!mysql_select_db(MYSQL_DB)) {
	die("Unable to select database.");
}


//header("Content-Type: text/plain");
header("Content-Type: text/html");

// Setup global HTML & CSS
echo "<html>";
echo "<head>";
echo "<title>Dynamic DNS Web Updater</title>";
echo '<link rel="stylesheet" href="styles.css" type="text/css">';
echo "</head>";
echo "<body>";
// Using a global <pre> so that all existing \n newlines will continue to work.
echo "<pre>";

// Config
$nsupdate = '/usr/bin/nsupdate -v -k /path/to/your/top/secret/nsupdate/key';
$cacheDir = 'cache';

// Show all items in the database if &status is in the URL.
if (isset($_GET['status'])) {
    echo "nsupdate web interface\n";
    echo "----------------------\n\n";
	echo date("D M j G:i:s T Y") . "\n\n";
    echo "Current defined dynamic zones:\n\n";
	
	// Retrieve all items from the database.
	$sql=mysql_query("SELECT * FROM domains");
	// Build the array from MySQL results.
	while($result[]=mysql_fetch_array($sql));
	// Delete last empty array object since it's always empty.
	array_pop($result);
	
	echo "<table>";
		echo '<thead>';
			echo '<tr>';
				echo '<th>ID</th>';
				echo '<th>Hostname</th>';
				echo '<th>Current IP</th>';
				echo '<th>Updated From</th>';
				echo '<th>Update Time</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			foreach ($result as $r) {
				$id = $r['id'];
				$hostname = $r['hostname'];
				$current = $r['ip'];
				$updateSource = $r['updateSource'];
				$updateTime = $r['updateTime'];
				echo '<tr><td class="id">'.$id.'</td><td class="hostname">'.$hostname.'</td><td class="ip">'.$current.'</td><td class="updateSource">'.$updateSource.'</td><td class="updateTime">'.$updateTime.'</td></tr>';
			}
		echo '</tbody>';
	echo '</table>';
    exit;
}

// GET/POST parameters within the URL
$config['hostname'] = $_REQUEST['hostname'];
$config['ip'] = $_REQUEST['ip'];
// Deprecated for my needs: 5/31/2013
	// The key file is currently defined in $nsupdate
		//$config['key'] = $_REQUEST['key'];
	// TTL is no longer needed since it's hard coded in $nsupdateCommands
		//$config['ttl'] = $_REQUEST['ttl'];

// If no IP is defined in URL, then we'll use the remote host address as the IP.
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
if (isset($_GET['debug']) || $error) {
    echo "nsupdate web interface\n";
    echo "----------------------\n\n";
    echo "Error: Missing parameters\n\n";
    print_r($config);
    exit;
}

// Retrieve information from MySQL for the hostname to be updated.
$hostname = $config['hostname'] . ".yourdomain.com";
$sql = mysql_query("SELECT * FROM domains WHERE hostname='$hostname'");
$result = mysql_fetch_object($sql);

// Needed for later MySQL updates
$updateID = $result->id;
$updateIP = $config['ip'];
$ip = $result->ip;
$updateSource = $_SERVER['REMOTE_ADDR'];
$updateTime = date('Y-m-d H:i:s');

// Handle cache of old/current IP address.
$config['old_ip'] = $result->ip;

// Exit now unless IP address is new.
if ($config['ip'] == $result->ip) {
	echo "nsupdate web interface\n";
	echo "----------------------\n\n";
	echo date("D M j G:i:s T Y") . "\n\n";
	echo "Current Defined IP:  " . $config['old_ip'] . "\n";
	echo "New IP:              " . $config['ip'] . "\n";
	echo "\nError: DNS not updated!\n\n";
	echo "Your IP hasn't changed - exiting.\n\n";
    exit;
}

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

echo "nsupdate web interface\n";
echo "----------------------\n\n";
echo date("D M j G:i:s T Y") . "\n\n";
echo "Previous IP:  " . $config['old_ip'] . "\n";
echo "New IP:       " . $config['ip'] . "\n";

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

    // Update MySQL cache.
	$sql = "REPLACE INTO domains (id, hostname, ip, updateSource, updateTime) VALUES ('$updateID','$hostname','$updateIP','$updateSource','$updateTime')"; 
	$result = mysql_query($sql);
	if ($result) { 
		echo "\nMySQL cache updated!\n";
    } else {
        echo "\nError: MySQL cache not updated!\n";
    }
}

// Ending HTML
echo "</pre>";
echo "</body>";
echo "</html>";

?>