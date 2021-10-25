<?php

error_reporting(0);//报告

$id = $_GET['url'];

$api = "http://49.234.56.246/danmu/json.php?url=$id";
	 //连接本地的 Redis 服务


	
//随机IP地址
function get_ip() {
    $ip_long = array(
        array('607649792', '608174079'), //36.56.0.0-36.63.255.255
        array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
        array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
        array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
        array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
        array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
        array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
        array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
        array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
        array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
    );
    $rand_key = mt_rand(0, 9);
    return long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
}
function getSubstr($str, $leftStr, $rightStr)
{
    $left = strpos($str, $leftStr);
    
    $right = strpos($str, $rightStr,$left);
    
    if($left < 0 or $right < $left) return '';
    return substr($str, $left + strlen($leftStr), $right-$left-strlen($leftStr));
}

$curl = curl_init(); 
curl_setopt($curl, CURLOPT_URL, $api); 
curl_setopt($curl, CURLOPT_REFERER, $api); //伪装来源
curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.get_ip(), 'CLIENT-IP:'.get_ip()));//随机IP访问
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);  // 从证书中检查SSL加密算法是否存在 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
$content = curl_exec($curl); 
curl_close($curl); 
$url=getSubstr($content,'"url":"','"}');
$playurl=str_replace('\/','/',$url);

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
 <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<meta http-equiv="content-language" content="zh-CN"/>
<meta http-equiv="X-UA-Compatible" content="chrome=1"/>
<meta http-equiv="pragma" content="no-cache"/>
<meta http-equiv="expires" content="0"/>
<meta name="referrer" content="never"/>
<meta name="renderer" content="webkit"/>
<meta name="msapplication-tap-highlight" content="no"/>
<meta name="HandheldFriendly" content="true"/>
<meta name="x5-page-mode" content="app"/>
<meta name="Viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0"/>
  <title>player</title>
  <style>
    body,html{padding: 0;margin: 0;background-color:#000;color:#aaa;width: 100%;height: 100%;}
    #player{width: 100%;height: 100%;}
	#tips{position:absolute;top:20px;left:50%;padding: 0 25px;height:32px;line-height:32px;font-size:14px;color:#00a2ff;-moz-transform: translateX(-50%);-webkit-transform: translateX(-50%);transform: translateX(-50%);border-radius: 10px;border:#00a2ff solid 1px;z-index: 999999;display: none;}
    a{text-decoration: none;}
  </style>
 
</head>
<body>
<div id="tips"><b>重要提示: 视频加载慢,请及时反馈报错！</b></div>
<div id="player"></div>
<link href="//s1.pstatp.com/cdn/expire-1-M/dplayer/1.25.0/DPlayer.min.css" rel="stylesheet">
<script src="/DPlayer.min.js"></script>
<script src="//s0.pstatp.com/cdn/expire-1-M/hls.js/0.12.4/hls.min.js"></script>
<script>
    var url = '<?php echo $playurl; ?>';   
	var type = 'mp4';
	var box = document.getElementById("tips");
    var pic = 'https://cdn.jsdelivr.net/gh/k8dmss/repository2@master/20200814104439bc133b6a36c6de1dae618e97fed9e3d9.jpg';
    var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
	function tips() {
		box.style.display="none";
	}
	if(url.indexOf('.m3u8') !== -1) {
		box.style.display="block";
		setTimeout("tips()", 5000);
		type = 'hls';
	}

    function play() {
		if(url.indexOf('/play/')!== -1) {
			document.getElementById('player').innerHTML = '<iframe src='+ url +' style="width:100%;height:100%;" frameborder="0" scrolling="no" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>';
		} else if (isMobile) {
			document.getElementById('player').innerHTML = '<video id="videoPlay" webkit-playsinline autoplay controls style="width:100%;height:100%;" src="'+ url +'" poster="'+ pic +'"></video>';
		}  else {
			var dp = new DPlayer({
				container: document.getElementById('player'),
				autoplay: true,
				lang: 'zh-cn',
				video: {
					url: url,
					pic: pic,
					type: type
				}
			});
		}
		if(/1392_|1445_|1380_|1440_|1393_|1444_|1455_|605_|604_/.test(url)) {
			if(isMobile) {
				document.getElementById('videoPlay').currentTime = 6
			} else {
				dp.seek(6);
			}
		}
    }
	play()
</script>


</body>
</html>