<?php
// script to create an rss feed from New York State Young Democrats' blog page with enclosures for podcast
include_once('simple_html_dom.php');
$target_url = "http://www.nysyd.org/blog.php";
function remote_file_size($url){ // found via Google
	$head = ""; 
	$url_p = parse_url($url); 
	$host = $url_p["host"]; 
	if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$host)){
		// a domain name was given, not an IP
		$ip=gethostbyname($host);
		if(!preg_match("/[0-9]*\.[0-9]*\.[0-9]*\.[0-9]*/",$ip)){
			//domain could not be resolved
			return -1;
		}
	}
	$port = intval($url_p["port"]); 
	if(!$port) $port=80;
	$path = $url_p["path"]; 
	//echo "Getting " . $host . ":" . $port . $path . " ...";
	$fp = fsockopen($host, $port, $errno, $errstr, 20); 
	if(!$fp) { 
		return false; 
		} else { 
		fputs($fp, "HEAD "  . $url  . " HTTP/1.1\r\n"); 
		fputs($fp, "HOST: " . $host . "\r\n"); 
		fputs($fp, "User-Agent: http://www.example.com/my_application\r\n");
		fputs($fp, "Connection: close\r\n\r\n"); 
		$headers = ""; 
		while (!feof($fp)) { 
			$headers .= fgets ($fp, 128); 
			} 
		} 
	fclose ($fp); 
	//echo $errno .": " . $errstr . "<br />";
	$return = -2; 
	$arr_headers = explode("\n", $headers); 
	// echo "HTTP headers for <a href='" . $url . "'>..." . substr($url,strlen($url)-20). "</a>:";
	// echo "<div class='http_headers'>";
	foreach($arr_headers as $header) { 
		// if (trim($header)) echo trim($header) . "<br />";
		$s1 = "HTTP/1.1"; 
		$s2 = "Content-Length: "; 
		$s3 = "Location: "; 
		if(substr(strtolower ($header), 0, strlen($s1)) == strtolower($s1)) $status = substr($header, strlen($s1)); 
		if(substr(strtolower ($header), 0, strlen($s2)) == strtolower($s2)) $size   = substr($header, strlen($s2));  
		if(substr(strtolower ($header), 0, strlen($s3)) == strtolower($s3)) $newurl = substr($header, strlen($s3));  
		} 
	// echo "</div>";
	if(intval($size) > 0) {
		$return=intval($size);
	} else {
		$return=$status;
	}
	// echo intval($status) .": [" . $newurl . "]<br />";
	if (intval($status)==302 && strlen($newurl) > 0) {
		// 302 redirect: get HTTP HEAD of new URL
		$return=remote_file_size($newurl);
	}
	return $return; 
} 
$html = new simple_html_dom();
$numofitems = 0;
$feed = array();
$html->load_file($target_url);
	//get title of post
	foreach($html->find('div.title-nobg h1 a') as $info)
	{	
		$feed[$numofitems]['link'] = "http://www.nysyd.org/" . $info->href;
		$feed[$numofitems]['guid'] = "http://www.nysyd.org/" . $info->href;
		
		$temptitle = $info->innertext;
		$temptitle = preg_replace('/\x92/', '/\'/', $temptitle);
		$temptitle = preg_replace('/\x93/', '/\'/', $temptitle);
		$temptitle = preg_replace('/\x94/', '/\'/', $temptitle);
		$feed[$numofitems]['title'] = $temptitle;
		$numofitems++;
	}
	
	$numofitems=0;	// reset counter
	
	foreach($html->find('div.title-nobg') as $authorinfo) // get post author's name
	{
		// split up the posted in stuff
		$tempText = strip_tags($authorinfo->innertext);
		$tempText2 = explode("by ",$tempText);
		$author = explode(" on ",$tempText2[1]);
		$feed[$numofitems]['author'] = $author[0]; //extract out author
		$tempDate = explode("        	",$author[1]); //exrract out the date
		$feed[$numofitems]['date'] = date("D, d M Y H:i:s T",strtotime($tempDate[0])); //convert date to UNIX timestamp and then convert it to RFC 822
		
		$numofitems++;
	}
	
	$numofitems=0;	// reset counter
	
	//get post and clean up formatting
	foreach($html->find('div.post') as $description)
	{
		$textdata = $description->innertext;
		
		
		$patterns = array();
		$patterns[0] = '/\x92/';
		$patterns[1] = '/\x96/';
		$patterns[2] = '/\x97/';
		$patterns[3] = '/\x93/';
		$patterns[4] = '/\x94/';
		$patterns[5] = '/\x85/';
		$patterns[6] = '/\x95/';
		$replacements = array();
		$replacements[0] = '\'';
		$replacements[1] = ' ';
		$replacements[2] = ' ';
		$replacements[3] = ' ';
		$replacements[4] = ' ';
		$replacements[5] = ' ';
		$replacements[6] = ' ';
		$textdata = preg_replace($patterns, $replacements, $textdata);
		$feed[$numofitems]['description'] = $textdata;
		$numofitems++;
	}	
function getEnclosure($url) // function to get the mime-type from the linked media files from blog page and generate enclosure from it
{
	//get podcasts
	$deschtml = str_get_html($url);
		foreach($deschtml->find('a[href$=mp3]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "audio/mpeg";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}
	
	}
	foreach($deschtml->find('a[href$=mp4]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/mp4";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=mov]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/quicktime";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=pdf]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "application/pdf";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=doc]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "application/msword";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}	
	foreach($deschtml->find('a[href$=xls]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "application/vnd.ms-excel";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}	
	foreach($deschtml->find('a[href$=wav]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "audio/x-wav";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}	
	foreach($deschtml->find('a[href$=mpg]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/mpeg";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=mpeg]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/mpeg";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=mpe]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/mpeg";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}	
	foreach($deschtml->find('a[href$=m4a]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "audio/mp4a-latm";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=m4v]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/x-m4v";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
	foreach($deschtml->find('a[href$=avi]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "video/x-msvideo";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}
foreach($deschtml->find('a[href$=mpga]') as $file)
		{
	if(!is_null($file))
		{
			$enclosureURL = $file->href;
			$enclosureSize = remote_file_size($enclosureURL);
			$enclosureType = "audio/mpeg";
			echo "<enclosure url=\"" . $enclosureURL . "\" length=\"" . $enclosureSize ."\" type=\"" . $enclosureType ."\" />";
		}	
	}									
}	
//output everything
header ("Content-Type:text/xml");
echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
echo "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:atom=\"http://www.w3.org/2005/Atom\" xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\" xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\">";
echo "\n";
echo "<channel>";
echo "\n";
echo "<title>New York State Young Democrats</title> \n";
echo "<link>http://www.nysyd.org</link> \n";
echo "<image> \n";
echo "<url>http://www.nysyd.org/images/nysyd_logo.png</url> \n";
echo "<title>New York State Young Democrats</title> \n";
echo "<link>http://www.nysyd.org</link> \n";
echo "</image> \n";
echo "<description>The New York State Young Democrats are the official youth arm of the New York State Democratic Committee. The New York State Young Democrats represent young democrats between the ages of 16-36 living in all of New York's 62 counties. Members of the organization strive to make a positive difference in the community by working within the political process and upholding the principles of the Democratic Party. The membership base reflects the broad diversity of New York and the party, including high school students, college students, young workers, professionals and families.</description> \n";
echo "<language>en-us</language> \n";
echo "<atom:link href=\"http://www.nysyd.org/rss/rss.php\" rel=\"self\" type=\"application/rss+xml\" /> \n";
$tempPubDate = sizeof($feed)-1; //date of first post
echo "<pubDate>" . $feed[$tempPubDate]['date'] . "</pubDate> \n";
echo "<lastBuildDate>" . $feed[0]['date'] . "</lastBuildDate>";
echo "\n\n";
  for($i=0;$i<sizeof($feed);$i++)
  {
  echo "<item>" . "\n";
    echo "<title>" . $feed[$i]['title'] . "</title>" . "\n";
	echo "<author> info@nysyd.net (" . $feed[$i]['author'] . ")</author>" . "\n";
	echo "<pubDate>" . $feed[$i]['date'] . "</pubDate>" . "\n";
	echo "<guid>" . $feed[$i]['guid'] . "</guid>" . "\n";
	echo "<link>" . $feed[$i]['link'] . "</link>" . "\n";
	getEnclosure($feed[$i]['description']);
	echo "<description>" . "<![CDATA[" . $feed[$i]['description'] . "]]>" . "</description>" . "\n";
	
	echo "<comments>" . $feed[$i]['link'] . "#form" . "</comments>" . "\n";
  echo "</item>";
  echo "\n\n";
}
echo "</channel>";
echo "\n";
echo "</rss>";
?>
