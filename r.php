<?php
ini_set('default_charset', 'utf-8');
mkdir('./photo');
$botapi = getenv('RJBotKey');
$siteurl = getenv('RJSiteUrl');
$tg = json_decode(file_get_contents('php://input'));
file_put_contents('tg.json', json_encode($tg));
if (isset($tg->callback_query)) {
	goto cb;
}
$mid = $tg->message->message_id;
$uid = $tg->message->from->id;
//file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=32709704&text=Ok");
if ($uid !== 32709704) {
	if ($uid !== 340809697) {
		exit;
	}
}
$m = urldecode($tg->message->text);
if (isset($tg->callback_query)) {
	cb:
	$uid = $tg->callback_query->from->id;
	$m = urldecode($tg->callback_query->data);
}
function fn($url) {
$path_parts = pathinfo($url);
$name = $path_parts['basename'];
return $name;
}
if (stripos($m, '/start ') !== false) {
$m =	'/' . str_replace('/start ', '', $m);
}
if ($m == '/start' OR $m == 'Menu 🏠') {

$data = json_decode(file_get_contents('home.json'), true);
//var_dump($data);
foreach ($data as $data) {
if ($data['type'] == 'mp3')
{
$id = $data['id'];
$photo = $data['photo'];
$artist = $data['artist'];
$title = $data['song'];
$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Download!","callback_data"=>"/download_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendPhoto?chat_id=$uid&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => "$title by $artist"));
$result = curl_exec($ch);
curl_close($ch);
}
}
exit;
} 
elseif ($m == "Featured Lists 🎤" OR $m == "/fl") {
	$data = json_decode(file_get_contents('fl.json'), true);
	foreach ($data as $playlist) {
		$id = $playlist['id'];
		$title = $playlist['title'];
		$photo = $playlist['photo'];
		$r = fn($photo);
				if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Show!","callback_data"=>"/playlist_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendPhoto?chat_id=$uid&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => "$title"));
$result = curl_exec($ch);
curl_close($ch);
	}
	exit;
}

elseif ($m == "/nl") {
	$data = json_decode(file_get_contents('nl.json'), true);
	$data = $data['playlists']['playlists'];
	foreach ($data as $playlist) {
		$id = $playlist['id'];
		$title = $playlist['title'];
		$photo = $playlist['photo'];
		$r = fn($photo);
				if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
$opz = [[array("text"=>"Show!","callback_data"=>"/playlist_$id")]];
$dlkey = json_encode(array("inline_keyboard"=>$opz));
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendPhoto?chat_id=$uid&reply_markup=$dlkey";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => "$title"));
$result = curl_exec($ch);
curl_close($ch);
	}
	exit;
}


elseif (stripos($m, '/search') !== false) {
	$q = urlencode(str_replace('/search ', '', $m));
	if (strlen($q) <1) {
		file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=Nothing found!");
		exit;
	}
	$s = "https://rjvnapi.com/api2/search?query=$q";
	$data = json_decode(file_get_contents($s), true);
//	$ca = count($data['albums']);
	//$cm = count($data['mp3s']);
//	$cp = count($data['playlists']);
	$x = 1;
	foreach($data['mp3s'] as $song) {
		$id = $song['id'];
		$title = $song['title'];
		$plays = $song['plays'];
		$text = $text . "$x. $title (/download_$id)\n($plays Plays)\n\n";
		$x++;
	}
	$text = $c . $text;
	//file_put_contents('tg', $text);
	//file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=$text");

	$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendMessage?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("text" => $text));
$result = curl_exec($ch);
curl_close($ch);
exit;
}

elseif ($m == "/topweek") {
	$id = "topweek";
	goto CameFromTopWeekCommand;
}
elseif (strpos($m, '/playlist_') !== false) {
	//file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=pl");
	$id = str_replace('/playlist_', '', $m);
	CameFromTopWeekCommand:
	$data = json_decode(file_get_contents("https://rjvnapi.com/api2/mp3_playlist?id=$id"), true);
	$tex = "Playlist #$id";
	$x = 1;
	foreach($data as $song) {
		$id = $song['id'];
		$title = $song['title'];
		$plays = $song['plays'];
		//$array[] = [["text"=>"$title","callback_data"=>"download&$id"]];
		$text = $text . "\n\n$x. $title (/download_$id)\n$plays Plays";
		$x++;
				} 
		$text = $tex . $text;
//$l = ["text"=>"hi","callback_data"=>"download&$id"]; 
//	$ll = ["text"=>"hi2","callback_data"=>"download&$id"];
		//$opz = [[$l],[$ll]];
		
//	$opz = $array;
//$dlkey = json_encode(array("inline_keyboard"=>$opz));
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendMessage?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("text" => $text, "parse_mode" => "HTML", "disable_web_page_preview" => true));
$result = curl_exec($ch);
curl_close($ch);
//file_put_contents('array2', json_encode($opz));
exit;
}

elseif (strpos($m, '/download_') !== false) {
	//file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=pl");
	$id = str_replace('/download_', '', $m);
	$song = json_decode(file_get_contents("https://rjvnapi.com/api2/mp3?id=$id"), true);
		$title = $song['title'];
		$mp3 = $song['link'];
		$photo = $song['photo'];
		$rel = count($song['related']);
		$lyric = urlencode($song['lyric'] . "\n\n<a href = '$mp3'>Download $title</a>\n\n$rel Related songs : /related_$id");
		$plays = $song['plays'];
		$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
		file_put_contents("./photo/$r", file_get_contents($photo));
		}
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendPhoto?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => "$title ($plays Plays)"));
curl_exec($ch);
curl_close($ch);
file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=$lyric&parse_mode=HTML");
exit;
}
elseif (stripos($m, 'http') !== false) {
file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=" . urlencode("Trying to get $m .........."));
$a = file_get_contents($m);
if (!strlen($a) > 500) { exit; }
file_put_contents(fn($m), $a);
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendDocument?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("document" => new CURLFile(realpath(fn($m)))));
curl_exec($ch);
curl_close($ch);
unlink(realpath(fn($m)));
exit;
}

//related

elseif (strpos($m, '/related_') !== false) {
	//file_get_contents("https://api.telegram.org/$botapi/sendMessage?chat_id=$uid&text=pl");
	$id = str_replace('/related_', '', $m);
	$song = json_decode(file_get_contents("https://rjvnapi.com/api2/mp3?id=$id"), true);
	$otitle = $song['title'];
	$songs = $song['related'];
	$x = 1;
	$text = "Related songs to $otitle\n\n";
	foreach ($songs as $song) {
		$id = $song['id'];
		$title = $song['title'];
		$plays = $song['plays'];
		$text = $text . "$x. $title (/download_$id)\n($plays Plays)\n\n";
		$x++;
		}
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendMessage?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("text" => $text));
curl_exec($ch);
curl_close($ch);
exit;
}
elseif ($m == '/update') {
	file_get_contents("$siteurl/cron.php?do=home");
	exit;
}
elseif ($m == '/mr') {
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://mrtehran.com/mt-app/v402/browse_popular.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"a=0&");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$headers = [
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'User-Agent: Dalvik/1.6.0 (Linux; U; Android 4.4.2; GT-I9505 Build/KOT49H)',
    'Connection: Keep-Alive',
    'Accept-Encoding: gzip',
    'Content-Length: 4'
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$mr = curl_exec ($ch);
curl_close ($ch);
$mr = json_decode($mr, true);
foreach ($mr as $mr) {
$album = $mr['tfa'];
$artist = $mr['afa'];
$title =  $mr['trtfa'];
$photo = $mr['ph'];
$artisten = $mr['a'];
$titleen = $mr['trt'];
$mp3 ='https://storage.backtory.com/mrtehran/media/' . $mr['u'] . "?filename=$artisten - $titleen.mp3";
$r = fn($photo);
		if (file_exists("./photo/$r") !== true) {
file_put_contents("./photo/$r", file_get_contents($photo));
}
//$opz = [[array("text"=>"Download!","callback_data"=>"/download_$id")]];
//$dlkey = json_encode(array("inline_keyboard"=>$opz));
$ch = curl_init();
$url = "https://api.telegram.org/$botapi/sendPhoto?chat_id=$uid";
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, array("photo" => new CURLFile(realpath("./photo/$r")), "caption" => "$title از $artist\n$mp3"));
$result = curl_exec($ch);
curl_close($ch);
}
exit;
} 
else {
	exit;
}
exit;
