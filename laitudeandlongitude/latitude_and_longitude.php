<?php
// function to generate latitude and longitude from an address in the United States via the Google Maps API
function getLatandLong($addr,$city,$state)
{ 
	global $lat;
	global $lng;
	
  $doc = new DOMDocument();
  $doc->load("http://maps.google.com/maps/api/geocode/xml?address=".$addr.",+".$city.",+".$state."&sensor=false"); //input address
  
  //traverse the nodes to get to latitude and longitude
  $results = $doc->getElementsByTagName("result");
  $results = $results->item(0);
  $results = $results->getElementsByTagName("geometry");
  $results = $results->item(0);
  $results = $results->getElementsByTagName("location");
  
  foreach($results as $result)
		{
		$lats = $result->getElementsByTagName("lat");
		$lat = $lats->item(0)->nodeValue;
					
		$lngs = $result->getElementsByTagName("lng");
		$lng = $lngs->item(0)->nodeValue;
		}
}		
?>
