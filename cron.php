<?php
$type = $_GET['do'];
function fn($url) {
$path_parts = pathinfo($url);
$name = $path_parts['basename'];
return $name;
}
if ($type == 'home') {
$data =  file_get_contents('https://rjvnapi.com/api2/iphone_slider');
$data2 =  file_get_contents('home.json');
file_put_contents('home.json', $data);
$data3 = file_get_contents('fl.json');
$data4 = file_get_contents('https://rjvnapi.com/api2/mp3_playlists_with_items?type=featured');
file_put_contents('fl.json', $data4);
//$data5 = file_get_contents("nl.json");
//$data6 = file_get_contents("https://apirjvn.com/api2/home_items");
//file_put_contents('nl.json', $data6);
exec ("wget -O nl.json https://radiojavan.com/api2/home_items");
$data6 = file_get_contents('nl.json');

//checking songs
foreach (json_decode($data, true) as $song) {
	$id = $song['id'];
	if (!preg_match("/$id/i", file_get_contents('home-list')) AND $song['type'] == 'mp3') {

$photo = $song['photo'];
$artist = $song['artist'];
$title = $song['song'];
$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Download!","url"=>"https://t.me/$botID?start=download_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$text = "New Song!\n\n$title by $artist";
$ch = curl_init();
$url = "https://api.telegram.org/RJBotKey/sendPhoto?chat_id=-1001104394963&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => $text));
$result = curl_exec($ch);
curl_close($ch);
}
}
///////checking pls
foreach (json_decode($data4, true) as $song) {
	$id = $song['id'];
	if (!preg_match("/$id/i", file_get_contents('play-list'))) {

$photo = $song['photo'];
$title = $song['title'];
$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Show!","url"=>"https://t.me/$botID?start=playlist_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$text = "Feautured Playlist!\n\n'$title'";
$ch = curl_init();
$url = "https://api.telegram.org/RJBotKey/sendPhoto?chat_id=-1001104394963&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => $text));
$result = curl_exec($ch);
//newdbg
file_put_contents('newdbg', $result);
curl_close($ch);
}
}

///////checking new pls
$data6 = json_decode($data6, true);
$data6 = $data6['playlists']['playlists'];
foreach ($data6 as $song) {
	$id = $song['id'];
	if (!preg_match("/$id/i", file_get_contents('newplay-list'))) {

$photo = $song['photo'];
$title = $song['title'];
$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Show!","url"=>"https://t.me/$botID?start=playlist_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$text = "NEW Playlist!\n\n'$title'";
$ch = curl_init();
$url = "https://api.telegram.org/RJBotKey/sendPhoto?chat_id=-1001104394963&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => $text));
$result = curl_exec($ch);
curl_close($ch);
}
}

//saving songs list
foreach(json_decode($data, true) as $song) {
	$ids = $ids . '-' . $song['id'];
}
file_put_contents('home-list', $ids);
//saving playlists
$ids = '';
foreach(json_decode($data4, true) as $song) {
	$ids = $ids . '-' . $song['id'];
}
file_put_contents('play-list', $ids);
////saving new playlists
$ids = '';
foreach($data6 as $song) {
	$ids = $ids . '-' . $song['id'];
}
file_put_contents('newplay-list', $ids);
echo 'Good';
}
elseif ($type == 'clean') {
$files = glob('./photo/*'); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file))
    unlink($file); // delete file
}
echo 'Cleaned';
}
