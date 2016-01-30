<?php
// Sitemap generator for old ishopbrooklyn.com website (archived at http://web.archive.org/web/20110324015853/http://www.ishopbrooklyn.com/)
require("simple_html_dom.php"); 
header("Content-Type:text/xml");
// function to find all links in a page
function collectURLs($url){
global $links;
$html = file_get_html($url);
foreach($html->find('a') as $element) {
	//go through page's urls on a given page
	if(in_array($element->href,$links))
	  {
	  continue;
	  } 
	  else
	  {
	  //ignore external links and anchors
	if($element->href=="http://www.facebook.com/ishopbrooklyn" || $element->href=="ShopBrooklyn@gmail.com" || $element->href=="http://www.ishopbrooklyn.com/#"  || $element->href=="http://www.ishopbrooklyn.com/mailto:ShopBrooklyn@gmail.com"  || $element->href=="mailto:shopbrooklyn@gmail.com"  || $element->href=="http://twitter.com/ishopbrooklyn" || $element->href=="http://www.facebook.com/profile.php?id=2839747724"  || $element->href=="http://www.facebook.com/profile.php?id=2839747724" || $element->href=="http://www.addthis.com/bookmark.php" || $element->target=="_blank" || $element->href=="#number" || $element->href=="#A" || $element->href=="#B" || $element->href=="#C" || $element->href=="#D" || $element->href=="#E" || $element->href=="#F" || $element->href=="#G" || $element->href=="#H" || $element->href=="#I" || $element->href=="#J" || $element->href=="#K" || $element->href=="#L" || $element->href=="#M" || $element->href=="#N" || $element->href=="#O" || $element->href=="#P" || $element->href=="#Q" || $element->href=="#R" || $element->href=="#S" || $element->href=="#T" || $element->href=="#U" || $element->href=="#V" || $element->href=="#W" || $element->href=="#X" || $element->href=="#Y" || $element->href=="#Z" || $element->href=="#top")
	  {
	  //if found, skip it
		continue;
	  }
	else{
	//othewise, add to the list of pages
		  $link = $element->href;
		  array_push($links,$link); 
		  }
	  }   
	}
}
$links = array(); //the list of pages
$counter=0; //number of pages being used to index the list
//the pages being used to index
$links[$counter++]="http://www.ishopbrooklyn.com/index/";
$links[$counter++]="http://www.ishopbrooklyn.com/shopnow/";
$links[$counter++]="http://www.ishopbrooklyn.com/resources/";
$links[$counter++]="http://www.ishopbrooklyn.com/storebyneighborhood/";
$links[$counter++]="http://www.ishopbrooklyn.com/storebycategory/";
$links[$counter++]="http://www.ishopbrooklyn.com/join/";
$counter = $counter+1; //because php doesn't behave like js
//go through each and add to  list
for($i=0;$i<$counter;$i++)
{
	collectURLs($links[$i]);
}
//add in search and 404 pages
array_push($links, "http://www.ishopbrooklyn.com/search.php");
array_push($links, "http://www.ishopbrooklyn.com/404/");
//I frankly forget why I had to add this
for($p=0;$p<$counter;$p++)
	{
	array_shift($links);
	}
	
$urls = array_unique($links); //make a list of unique list	
unset($links); // to save all of that precious memory that computers seem to lack
//all the nice xml header info
	$data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.google.com/schemas/sitemap/0.84 http://www.google.com/schemas/sitemap/0.84/sitemap.xsd\">\n";
	$data .= "<url><loc>http://www.ishopbrooklyn.com/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>"; //just to start off
// Pretty much, this splits up the urls and sees what "directory" (it's actually a mod_rewritten PHP script) it falls into. Depending on which it goes to, it is assigned a priority.
foreach($urls as $l)
	{
$path_parts = pathinfo($l);
$piece = $path_parts['dirname'];
$pieces = explode("/", $piece);
if($pieces[3]=="store")
	{
	$data .= "<url><loc>" . $l . "</loc>";
	$data .="<changefreq>weekly</changefreq><priority>0.6</priority></url>\n"; 
	}
else if($pieces[3]=="byneighborhood")
	{
	$data .= "<url><loc>" . $l . "</loc>";
	$data .="<changefreq>weekly</changefreq><priority>0.9</priority></url>\n"; 
	}
else if($pieces[3]=="bycategory")
	{
	$data .= "<url><loc>" . $l . "</loc>";
	$data .="<changefreq>weekly</changefreq><priority>0.9</priority></url>\n"; 
	}	
else
	{
	$data .= "<url><loc>" . $l . "</loc>";
	$data .="<changefreq>weekly</changefreq><priority>0.8</priority></url>\n"; 
	}
}
//let's finish it up!
	$data .= "</urlset>";
	echo $data;
	
?>
