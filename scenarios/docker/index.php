<?php
// Enable better php debugging
ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);
error_reporting(E_ALL);

// Credit
$author_name = 'AlphaMusk';
$author_version = 'v1.3';
$author_email = 'alphamusk@networkpulse.com';
$author_project = 'AWS Metadata PHP Page';

// Dont use dashes - in declaring variables, break them, no idea.
// Be sure to end all http URLs with /, or some wont render data.
// We build array and the order below is how it builds the table
$curl_cmd = 'curl --connect-timeout 1';
$meta_host = '169.254.169.254';
$meta_data['ami-id'] = $ami_id = exec($curl_cmd." http://".$meta_host."/latest/meta-data/ami-id/");
$meta_data['instance-id'] = $instance_id = exec($curl_cmd." http://".$meta_host."/latest/meta-data/instance-id/");
$meta_data['availability-zone'] = $reg_az = exec($curl_cmd." http://".$meta_host."/latest/meta-data/placement/availability-zone/");
$meta_data['public-hostname'] = $public_hostname = exec($curl_cmd." http://".$meta_host."/latest/meta-data/public-hostname/");
$meta_data['public-ipv4'] = $public_ipv4 = exec($curl_cmd." http://".$meta_host."/latest/meta-data/public-ipv4/");
$meta_data['local-hostname'] = $local_hostname = exec($curl_cmd." http://".$meta_host."/latest/meta-data/local-hostname/");
$meta_data['local-ipv4'] = $local_ipv4 = exec($curl_cmd." http://".$meta_host."/latest/meta-data/local-ipv4/");
$git_url = 'https://github.com/alphamusk/aws-metadata-php-page';
$S3_url = "https://s3-us-west-1.amazonaws.com/networkpulse/com/public/images";
$S3_image = "$S3_url.aws.png";
$server_name = $_SERVER['SERVER_NAME'];
$server_ip = $meta_data['public-ipv4'];
$server_software = $_SERVER['SERVER_SOFTWARE'];
$client_ip = $_SERVER['REMOTE_ADDR'];
$client_agent = $_SERVER['HTTP_USER_AGENT'];
$page_title =  'AWS Cloud - ' . $server_name;
$php_self = $_SERVER['SCRIPT_NAME'];
$hostname = exec('hostname');


/** Check for page refresh, defaults to 5 mins **/
if (empty($_GET['refresh'])) {
	 $page_refresh = 300;
   } else {
	 $page_refresh = $_GET['refresh'];
}

/** find the availability zone **/
 function findAZ ($az) {
	// check if the value is null/empty
	if (empty($az) || !isset($az)) {
	return 'Error: unknown az';
	}
	$az = strtolower($az);
	return $az;		
 } //end function
 
 /** find the region **/
 function findRegion ($region) {
 	// check if the value is null/empty
	if (empty($region) || !isset($region)) {
	return 'Error: unknown region';
	}
	$region = substr($region, 0,-1);
	$region = strtoupper($region);
	return $region;
 } //end function
 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
	<title><?php echo $author_project.' '.$author_version; ?></title>
	<meta http-equiv="refresh" content="<?php echo $page_refresh; ?>" />
	<meta http-equiv="Content-Language" content="en-us" />
	
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	
	<meta name="description" content="Description" />
	<meta name="keywords" content="Keywords" />
	
	<meta name="author" content="<?php echo $author_name; ?>" />
	
	<style type="text/css" media="all">@import "master.css";</style>
</head>

<body class="about">
<div id="page-container">
	<div id="header">
		<div id="logo">
			<h1>Amazon Web Services</h1>
		</div>
	</div>
	<div id="main-nav">
		<div id="links">
			<ul>
				<li><span>Refresh</span></li>
				<li><a href="<?php echo $php_self.'?refresh=2'; ?>">2s</a></li>
				<li><a href="<?php echo $php_self.'?refresh=5'; ?>">5s</a></li>
				<li><a href="<?php echo $php_self.'?refresh=30'; ?>">30s</a></li>
				<li><a href="<?php echo $php_self.'?refresh=60'; ?>">1m</a></li>
				<li><a href="<?php echo $php_self.'?refresh=300'; ?>">5m</a></li>
			</ul>
		</div>
	</div>
	<div id="sidebar-a">
		<div class="padding">
			<h2>AWS  - Region</h2>
			<p><?php echo findRegion($meta_data['availability-zone']); ?></p><br>
			<h3>Availability Zone</h3>
			<p><?php echo findAZ($meta_data['availability-zone']); ?></p><br>
			<h3>Information</h3>
			<p>Server: <?php echo $server_software.'<br>Public IP: ';?><a href="http://<?php echo $server_ip; ?>"><?php echo $server_ip; ?></a></p>
			<p>Client: <?php echo $client_agent.'<br>IP: '.$client_ip; ?></p>
		</div>
	</div> <!-- End sidebar-a -->
	
	<div id="content">
		<div class="padding">
			<h2>EC2 Metadata</h2>
			<?php
			    //metadata table
			    echo '<table border="0" bgcolor="#ffffff" cellpadding="5" cellspacing="0" width="100%">';
			    echo '<tr><th align="left">Metadata</th><th align="left">Value</th></tr>';
			    foreach($meta_data as $x=>$x_value) {
			       echo '<tr>';
			    	echo '<td nowrap><span class="key">'. $x . '</span></td>';
			            echo '<td no wrap><span class="value">'. $x_value . '</span></td>';
			       echo '</tr>';
                }
                echo '<tr><td> Container ID:</td><td>' . $hostname . '</td></td></tr>';
			    echo '</table>';
		    	?>
		</div>
	</div> <!-- End Content -->
	
	<div id="footer">
		<div id="altnav">
			<a href="<?php echo $git_url; ?>/blob/master/README.md">Readme</a> | 
			<a href="<?php echo $git_url; ?>">Source</a> | 
			<a href="mailto:<?php echo $author_email; ?>?subject=<?php echo $author_project;?>">Contact</a> | 
			<a href="<?php echo $git_url; ?>/blob/master/LICENSE">License</a>
		</div>
		<div id="copyleft">Copyleft &copy; <a href="<?php echo $git_url; ?>"><?php echo $author_project.' '.$author_version;?></a><br />
			Powered by <a href="http://www.php.net/">PHP5</a> and <a href="mailto:<?php echo $author_email;?>?subject=<?php echo $author_project;?>"><?php echo $author_name.' Development'; ?></a>
		</div>
	</div> <!-- End Footer -->

</div> <!-- End Page Container -->
</body>
</html>
