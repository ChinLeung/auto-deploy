<?php
    // Attempt to retrieve the config
    if(!file_exists('autodeploy.config.json'))
        exit('Unable to load configuration file.');

    // Attempt to parse the config file
    $config = json_decode(file_get_contents('autodeploy.config.json'), true);
    if(!$config || !isset($config['host']))
        exit('Invalid configuration file.');

    // If it contains a letter, suppose it's a domain
    if(preg_match('/[a-z]/i', $config['host']))
        $host = gethostbyname($config['host']);
    else
        $host = $config['host'];

    // Set the default project name
    if(!isset($config['project-name']))
        $config['project-name'] = "Auto Puller";

    // Set the default log access
    if(!isset($config['log-access']) || !is_bool($config['log-access']))
        $config['log-access'] = true;

    // Set the default log file
    if(!isset($config['log-file']) || !is_string($config['log-file']))
        $config['log-file'] = "autodeploy.log";

    // Set the default pull branch
    if(!isset($config['pull-branch']))
        $config['pull-branch'] = "refs/heads/master";

	$ip = $_SERVER['REMOTE_ADDR'];
	$valid = $host == $ip;
	$time = date('F jS, Y \a\t H:i:s a', time());

	echo "<span class=\"command\">Host</span>: " . $ip . "<br>";
	echo "<span class=\"command\">Time</span>: " . $time . "<br>";
	echo "<span class=\"command\">Status</span>: " . ($valid ? "<span style=\"color:green;\">Valid</span>" : "<span style=\"color:red;\">Permission Denied</span>");

    // Only log it if the config is on
    if($config['log-access'])
        shell_exec("printf '[$time] \t " . ($valid ? 'Server' : 'Guest\t') ." \t $ip \t\t " . ($valid ? 'Valid' : 'Permission Denied') . "\r\n' >> " . $config['log-file']);

	if($valid){
		$requestBody = json_decode(file_get_contents('php://input'));

		if(isset($requestBody->ref) && $requestBody->ref == $config['pull-branch']){
			$commands = array(
				'git pull -f',
				'git checkout -f'
			);

			foreach($commands as $command){
				$startTime = time();
				$c = shell_exec($command);
				$output .= '<div>';
				$output .= '<span class="dollar">$</span> <span class="command">' . $command . ' (' . number_format(((time() - $startTime) / 1000), 2)  .  'ms)</span><br>';
				$output .= $c . "<br>";
				$output .= '</div>';
			}
		}
	}
?>

<!DOCTYPE HTML><html><head><meta charset="utf-8"><title><?php echo $config['project-name']; ?></title><style>html{overflow:hidden;}body{background-color:#000;color:#FFF;font-family:monospace;font-weight:bold;margin:20px auto;max-height:500px;position:relative;top:30%;white-space:pre;width:500px;}.command{color: #729FCF;}div{margin:10px 0px;}.dollar{color:#6BE234;}</style></head><body><?php echo $valid ? $output : ""; ?></body></html>
