<?php

require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
/* PHP SDK v5.0.0 */
/* make the API call */

// Set Max execuation Time Until All Process completed
ini_set('max_execution_time', 0);

$fb = new \Facebook\Facebook([
  'app_id' => 'xxxx',
  'app_secret' => 'xxxx',
  'default_graph_version' => 'v2.10',
  //'default_access_token' => '{access-token}', // optional
]);

$page_token = "xxxxx";

// Getting Images from 'images' folder
$images = scandir('images');
unset($images[0]);
unset($images[1]);

// Set Initital count Start For Dynamic Post
$initial_count = 532; // next count
$post_title = "Today Puzzle...#";
$dir = "images/";

// Set Date Init
$today_date = "2021-12-31 02:00:00";
$time_day = strtotime($today_date);
$days = 1;

// for Backup Purpose Save Return Response in txt file
$myfile = fopen("post/post_data.txt", "a") or die("Unable to open file!");

// Loop run number of images present on the images folder
foreach ($images as $key => $image) {
	
	$add = "+".$days." days";
	$schedule_time = strtotime($add,$time_day);
	
	$new_dir = $dir.$image;
	$title = $post_title.$initial_count;
	$title = $title."\n #chess #mate_in_2 #chess_puzzle";
	
	try {
	  $response = $fb->post(
	    '/100216311410235/photos', //photos
	    array (
	      'message' => $title,
	      'scheduled_publish_time' => $schedule_time,
	      'source' => $fb->fileToUpload($new_dir),
	      'published' => false,
	    ),
	    $page_token
	  );
	} catch(FacebookExceptionsFacebookResponseException $e) {
	  echo 'Graph returned an error: ' . $e->getMessage();
	  exit;
	} catch(FacebookExceptionsFacebookSDKException $e) {
	  echo 'Facebook SDK returned an error: ' . $e->getMessage();
	  exit;
	}
	$graphNode = $response->getGraphNode();

	$initial_count++;
	$days++;

	$txt = "Post Title: ".$title.", Image Number: ".$image.", Post ID: ,".$graphNode['id']."\n";
	fwrite($myfile, $txt);
	unlink($new_dir);
	echo $txt;
	echo "<br/>";
}

fclose($myfile);
echo "Successfully published all  posts";
exit();

