<?php

$php_ok = (function_exists('version_compare') && version_compare(phpversion(), '4.3.0', '>='));
$xml_ok = extension_loaded('xml');
$pcre_ok = extension_loaded('pcre');
$curl_ok = function_exists('curl_exec');
$zlib_ok = extension_loaded('zlib');
$mbstring_ok = extension_loaded('mbstring');
$iconv_ok = extension_loaded('iconv');

require_once( dirname(__FILE__) . '/../../../wp-load.php' );

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
<title>AutoBlogged Diagnostic Test</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />

<style type="text/css">


table#chart {
	border-collapse:collapse;
}

table#chart th {
	text-align:left;
	background-color:#eee;
	padding:2px 3px;
	border:1px solid #fff;
}

table#chart td {
	text-align:left;
	padding:2px 3px;
	border:1px solid #eee;
}

table#chart tr.enabled td {
		background-color:#EAFFEA;

}

table#chart tr.disabled td, 
table#chart tr.disabled td a {
	background-color:#fcc;
	color:#c00;
}

table#chart tr.disabled td a {
	text-decoration:underline;
}

div.chunk {
	margin:20px 0 0 0;
	padding:0 0 10px 0;
	border-bottom:1px solid #ccc;
}

.footnote,
.footnote a {
	font:10px/12px verdana, sans-serif;
	color:#aaa;
}

.footnote em {
	background-color:transparent;
	font-style:italic;
}
</style>
</head>
<body>

<?PHP 
  if ($_GET['type'] == 'info') {
  	phpinfo();
  } else { 
  	?>
<div id="site">
	<div id="content">

		<div class="chunk">
			<h2>AutoBlogged Diagnostic Tests</h2><br /><br />
			<table cellpadding="0" cellspacing="0" border="0" width="100%" id="chart">
				<thead>
					<tr>
						<th>Component</th>
						<th>Results</th>
					</tr>
				</thead>
				<tbody>
					
					
					
					<!-- PHP Version -->
					<tr class="<?php echo ($php_ok) ? 'enabled' : 'disabled'; ?>">
						<td>PHP version</td>
						<td>v<?php echo phpversion(); ?>: <?php echo ($php_ok) ? ' passed' : 'Requires v4.3.0 or higher.'; ?></td>
					</tr>
					
					
					<!-- WordPress Version -->
					<?PHP 
					require (ABSPATH . WPINC . '/version.php');
					$wp_ok = version_compare($wp_version, '2.5', '>=');
					?>
					
					<tr class="<?php echo ($wp_ok) ? 'enabled' : 'disabled'; ?>">
						<td>WordPress version</td>
						<td>v<?php echo $wp_version; ?>: <?php echo ($wp_ok) ? ' passed' : 'Requires v2.5.0 or higher.'; ?></td>
					</tr>
					
					
					<!-- MySQL Version -->
					<?PHP 
					$MySQL = mysql_get_server_info();
					$mysql_ok = version_compare($MySQL, '4.0.0', '>=');
					?>
					<tr class="<?php echo ($mysql_ok) ? 'enabled' : 'disabled'; ?>">
						<td>MySQL version</td>
						<td>v<?php echo $MySQL; ?>: <?php echo ($mysql_ok) ? ' passed' : 'Requires v4.0.0 or higher.'; ?></td>
					</tr>

					
					<tr class="<?php echo ($xml_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/xml">XML</a> extension</td>
						<td><?php echo ($xml_ok) ? 'Enabled' : 'XML extension is not enabled'; ?></td>
					</tr>
					
					
					<tr class="<?php echo ($pcre_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/pcre">PCRE</a> extension</td>
						<td><?php echo ($pcre_ok) ? 'Enabled' : 'PCRE extension is not enabled'; ?></td>
					</tr>
					
				<tr class="<?php echo ($zlib_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/zlib">Zlib</a> extension</td>
						<td><?php echo ($zlib_ok) ? 'Enabled' : 'Disabled'; ?></td>
					</tr>
					<tr class="<?php echo ($mbstring_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/mbstring">mbstring</a> extension</td>
						<td><?php echo ($mbstring_ok) ? 'Enabled' : 'Disabled'; ?></td>
					</tr>
					<tr class="<?php echo ($iconv_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/iconv">iconv</a> extension</td>
						<td><?php echo ($iconv_ok) ? 'Enabled' : 'Disabled'; ?></td>
					</tr>
					
					
					<?PHP
					$dnstest1 = gethostbynamel('nick-parker.com.');
					echo '<!--';
					print_r($dnstest1);
					echo '-->';
					$dnstest2 = gethostbynamel('google.com.');
					$dns_ok  = (is_array($dnstest1) || is_array($dnstest2))
					?>
					
					<tr class="<?php echo ($dns_ok) ? 'enabled' : 'disabled'; ?>">
						<td>DNS test</td>
						<td><?php echo ($dns_ok) ? 'Passed' : 'DNS lookups failed'; ?></td>
					</tr>


					<?PHP
					$safe_mode_check = ini_get('safe_mode');
					if ($safe_mode_check) {
						$safe_mode = "Enabled";
					} else {
						$safe_mode = "Disabled";
					}
					
					$openbasedir_check = ini_get('open_basedir');
					if (strlen($openbasedir_check) == 0) {
						$openbasedir = "Null";
					} else {
						$openbasedir = $openbasedir_check;
					}
					?>
					<tr class="<?php echo ($curl_ok) ? 'enabled' : 'disabled'; ?>">
						<td><a href="http://php.net/curl">cURL</a> extension</td>
						<td>
							<?php echo (extension_loaded('curl')) ? 'cURL Installed' : 'cURL extension is not enabled' ?><br/>
							safe_mode: <?php echo $safe_mode ?><br />
							open_basedir: <?php echo $openbasedir ?><br />
							</td>
					</tr>

						<?PHP
							$curltest = http_fetch("http://nick-parker.com/curltest.txt");
							if ($curltest['contents'] <> '-autoblogged-') {
								$curltest_ok = false;
								$curl_error = '('.$curltest['http_code'].') '.$curltest['error'];
							} else {
								$curltest_ok = true;
							}
							?>		
					
						<tr class="<?php echo ($curltest_ok) ? 'enabled' : 'disabled'; ?>">
						<td>HTTP retrieval test</td>
						<td>
							<?php echo (extension_loaded('curl')) ? 'Passed' : $curl_error ?><br/>
							</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?PHP } ?>
</body>
</html>

<?PHP
function http_fetch($url, $timeout = 15)
	{
		$result = array();
		// Initialize curl
		$ch = curl_init();
		if (!$ch) {
			$result['error'] = "AutoBlogged requires the cURL library.";
			return $result;
		}

		//CURLOPT_AUTOREFERER
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_GET, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		
	  //$follow_location = (ini_get('open_basedir') == true || ini_get('safe_mode') == true);
		$follow_location = true;
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		// Grab the URL contents
		$result['contents'] = curl_exec($ch);
		$result['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result['error'] = curl_error($ch);

		curl_close($ch);
		if ($fp) fclose($fp);
		return $result;
	}
	?>