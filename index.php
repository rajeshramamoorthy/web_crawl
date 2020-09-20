<?php
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
require __DIR__ . '/vendor/autoload.php';

$shortopts = "";  
$shortopts .= "s:";  
$shortopts .= "m:";  
$shortopts .= "z:";  
$shortopts .= "y:";  
$longopts  = array(
    "site::",
    "match::",
    "sites::",
    "matches::",
);

$options = getopt($shortopts, $longopts);

$json_array = array();
$client = new Client(HttpClient::create(['timeout' => 60]));

if(strpos($options['site'], 'https') !== true)
{
	$options['site'] = "https://www.".$options['site'];
}

try{
	$parse = parse_url($options['site']);
	$json_array['domain']  = $parse['host'];
	$crawler = $client->request('GET', $options['site']);
	if (200 == $client->getInternalResponse()->getStatusCode()) {
	    $content = $client->getInternalResponse()->getStatusCode();
		$test = $crawler->text();

		$splited_array = explode("|",$options["match"]);
		$matchstring_file_path = fopen($options['matches'], "w");
		$arr_matched_string = [];
		if($matchstring_file_path)
		{
			foreach($splited_array as $splited_str)
			{
				$similar_str_count =similar_text($test, $splited_str);
				$similar_str_count = substr_count($test, $splited_str);
				fwrite($matchstring_file_path, $splited_str."\n");
				array_push($arr_matched_string, array($splited_str => $similar_str_count));
			}	
		}
		else{
			$json_array['status'] =  '400';
			$json_array['error'] = array('code' => "400", 'message'=> 'File location is not found!');
		}
		$url_file_path = fopen($options['sites'], "w");
		if($url_file_path)
		{
			$url_list = $crawler->filter('a')->each(function ($node) {
				return $node->attr('href');
			});
			$url_list = array_unique($url_list);
			foreach($url_list as $d){
				if($d){
					if(strpos($d, "https") !== false or strpos($d, "http") !== false)
					{
						fwrite($url_file_path, $d."\n");
					}
					else
					{
						fwrite($url_file_path, $options['site'].$d."\n");
					}
				}
			}
		}
		else{

			$json_array['status'] =  '400';
			$json_array['error'] = array('code' => "400", 'message'=> 'File location is not found!');
		}

		$json_array['status'] =  $content;
		$matches_string = [];
		foreach($arr_matched_string as $parse_json)
		{
			foreach ($parse_json as $key => $value) {
				if($value)
				{
					$v = "true";
				}
				else
				{
					$v= "false";
				}
				array_push($matches_string, array('value' => $key, 'matching_strings' => $key, 'no_of_matches' => $value, 'is_match_found' => $v));
			}
		}
		$json_array['matches'] =  $matches_string;
		file_put_contents('response.json', json_encode($json_array));
	}
}
catch(Exception  $e){
	$json_array['status'] =  '400';
	$json_array['error'] = array('code' => "400", 'message'=> $e->getMessage());
	file_put_contents('response.json', json_encode($json_array));
}
