<?php
// TODO: Refactor this!
// Use in the "Post-Receive URLs" section of your GitHub repo.

/* security */
$access_token = 'LVx2AjJHKGy5ObuKsBCjEIbH9Nzp9Khu2jI9J20Y';
$access_ip = array('13.56.215.193');

/* get user token and ip address */
$client_token = $_ENV['HTTP_X_GITLAB_TOKEN'];
$client_ip = $_SERVER['REMOTE_ADDR'];

/* create open log */
$fs = fopen('../webhook.log', 'a');

fwrite($fs, 'Request on ['.date("Y-m-d H:i:s").'] from ['.$client_ip.']'.PHP_EOL);

/* test token */
if ($client_token !== $access_token)
{
    echo "error 403";
    fwrite($fs, "Invalid token [{$client_token}]".PHP_EOL);
    exit(0);
}


/* test ip */
if ( ! in_array($client_ip, $access_ip))
{
    echo "error 503";
    fwrite($fs, "Invalid ip [{$client_ip}]".PHP_EOL);
    exit(0);
}

/* get json data */
$json = file_get_contents('php://input');
$data = json_decode($json, true);
//fwrite($fs, "JSON [{$json}]".PHP_EOL);
/* get branch */
$branch = $data["ref"];
$cwd = getcwd();
fwrite($fs, '======================================================================='.PHP_EOL);
/* if you need get full json input */
//fwrite($fs, 'DATA: '.print_r($data, true).PHP_EOL);
/* branch filter */
if ($branch === 'refs/heads/master')
	{
	/* if master branch*/
	fwrite($fs, 'BRANCH: '.print_r($branch, true).PHP_EOL);
	fwrite($fs, '======================================================================='.PHP_EOL);
	$fs and fclose($fs);
	/* then pull master */
	shell_exec( "cd $cwd && git reset --hard HEAD && git pull origin master && chmod -R 755 *" );
	} 
else 
	{
	/* if dev branch */
	fwrite($fs, 'BRANCH: '.print_r($branch, true).PHP_EOL);
	fwrite($fs, '======================================================================='.PHP_EOL);
	$fs and fclose($fs);
	/* pull dev branch */
	shell_exec( "cd $cwd && git reset --hard HEAD && git pull origin dev && chmod -R 755 *" );
	}

/*
if ( $_POST['payload'] ) {
  $cwd = getcwd();
  shell_exec( "cd $cwd && git reset --hard HEAD && git pull && chmod -R 755 *" );
}
*/
?>hi