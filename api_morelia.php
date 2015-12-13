<?php
header('Content-Type: application/json');
class Twitter{
 
    function getTweets($user){
        ini_set('display_errors', 1);
        require_once('TwitterAPIExchange.php');
 
       $settings = array(
    'oauth_access_token' => "1898038981-H5XxQBmicDj32sfC22gn2jIXl9PAtN5VFry3p1z",
    'oauth_access_token_secret' => "Yu6UZv3gk19B4jzXGGQQgDpE18jjFNOJoZvcGNRYmTOST",
    'consumer_key' => "yRehIiKnmfNz8gY3RxiYtc3dz",
    'consumer_secret' => "zH7aZcvoBdC2mzuupdJ55QJKRgiL5dOp21m1EaQUyuQlQkgUBp"
);
 
       //url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
       $url='https://api.twitter.com/1.1/search/tweets.json';
       //getfield = '?screen_name='.$user.'&count=100';   
        $getfield = '?q='.$user.'&count=100';  
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $json =  $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        return $json;
 
    }
 
    function getArrayTweets($jsonraw,$claves){
        $rawdata = "";
        $json = json_decode($jsonraw);
        $num_items = count($json->statuses);
        $newArray = array();
        //print_r($json);
        for($i=0; $i<$num_items; $i++){
 
            $user = $json->statuses[$i];
 
           /*$fecha = $user->created_at;
            $url_imagen = $user->user->profile_image_url;
            $screen_name = $user->user->screen_name;
            $tweet = $user->text;
 
           $imagen = "<a href='https://twitter.com/".$screen_name."' target=_blank><img src=".$url_imagen."></img></a>";
            $name = "<a href='https://twitter.com/".$screen_name."' target=_blank>@".$screen_name."</a>";
 
            $rawdata[$i][0]=$fecha;
            $rawdata[$i]["FECHA"]=$fecha;
            $rawdata[$i][1]=$imagen;
            $rawdata[$i]["imagen"]=$imagen;
            $rawdata[$i][2]=$name;
            $rawdata[$i]["screen_name"]=$name;
            $rawdata[$i][3]=$tweet;
            $rawdata[$i]["tweet"]=$tweet;*/

            

            if(!empty($claves)) {
                $keys = explode(",", $claves);
                for($k = 0; $k < count($keys); $k++) {
                    if (strpos($user->text, $keys[$k]) !== FALSE) {
                        $newArray[] = $user;
                    }
                }
            } else {
                $newArray[] = $user;
            }

        } 
        return $newArray;
    }
 
    function displayTable($rawdata){
 
        echo '<table border=1>';
        $columnas = count($rawdata[0])/2; 
        $filas = count($rawdata);
 
        for($i=1;$i<count($rawdata[0]);$i=$i+2){
            next($rawdata[0]);
            echo "<th><b>".key($rawdata[0])."</b></th>";
            next($rawdata[0]);
        }
        for($i=0;$i<$filas;$i++){
            echo "<tr>";
            for($j=0;$j<$columnas;$j++){
                echo "<td>".$rawdata[$i][$j]."</td>";
 
            }
            echo "</tr>";
        }       
        echo '</table>';
    }
}

$hashtag = (isset($_REQUEST['hashtag'])) ? $_REQUEST['hashtag'] : "morelia";

$claves = (isset($_GET['claves'])) ? $_GET['claves'] : null;

if(empty($hashtag)){
    echo "  Parametros hashtag y claves son requeridos";
}
else{
   $twitterObject = new Twitter();
$jsonraw =  $twitterObject->getTweets($hashtag);
//print_r($jsonraw);
$rawdata =  $twitterObject->getArrayTweets($jsonraw,$claves);
echo json_encode($rawdata);
//$twitterObject->displayTable($rawdata); 
}
?>