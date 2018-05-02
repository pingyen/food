<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" >
<meta charset="UTF-8" >
<title>食</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css" rel="stylesheet" >
<style>
	@font-face {
		font-family: cwTeXKai;
		src: url(//fonts.gstatic.com/ea/cwtexkai/v3/cwTeXKai-zhonly.eot);
		src: url(//fonts.gstatic.com/ea/cwtexkai/v3/cwTeXKai-zhonly.eot?#iefix) format('embedded-opentype'),
			url(//fonts.gstatic.com/ea/cwtexkai/v3/cwTeXKai-zhonly.woff2) format('woff2'),
		   	url(//fonts.gstatic.com/ea/cwtexkai/v3/cwTeXKai-zhonly.woff) format('woff'),
		   	url(//fonts.gstatic.com/ea/cwtexkai/v3/cwTeXKai-zhonly.ttf) format('truetype');
	}

	h1 {
		font-family: cwTeXKai;
		font-size: 42px;
		margin: 6px 12px;
	}

	body > aside {
		left: 64px;
		position: absolute;
		top: 12px;
	}

	body > aside > nav {
		display: inline-block;
	}

	body > aside > nav > ul {
		display: inline-block;
		margin: 0;
		padding: 0;
	}

	body > aside > nav > ul > li {
		display: inline-block;
	}

	body > aside > nav > ul > li > a {
		color: #000;
		display: inline-block;
		padding: 4px;
		text-decoration: none;
	}

	body > aside > nav > ul > li > a.current {
		border-bottom: 3px solid #dd4b39;
		color: #dd4b39;
		font-weight: bold;
	}

	body > aside > a {
		display: inline-block;
		margin-left: 4px;
		font-size: 12px;
		text-decoration: none;
		color: #333;
	}

	body > main > article {
		background: #FFF;
		box-shadow: 0 2px 1px rgba(0, 0, 0, 0.3), 0 0 1px rgba(0, 0, 0, 0.3);
		border-radius: 2px;
		margin: 8px 12px;
		padding: 12px;
	}

	body > main > article.hidden {
		animation: bounce 2s infinite;
		opacity: 0.3;
	}

	body > main > article.fadeIn {
		animation: fadeIn 0.4s;
	}

	body > main > article > h2 {
		font-family: cwTeXKai;
		font-size: 28px;
		margin: 6px 0 0;
	}

	body > main > article > h2 > a {
		color: #1a0dab;
		text-decoration: none;
	}

	body > main > article > pre {
		line-height: 1;
		margin: 12px 0 6px;
		overflow: visible;
		padding-left: 2px;
	}

	body > .ad {
		margin: 8px 12px;
	}

	@media only screen and (max-device-width: 736px) {
		body > article > h2 {
			font-size: 22px;
			margin-top: 0;
		}
	}

	@keyframes fadeIn {
	    from { opacity: 0; }
	    to   { opacity: 1; }
	}

	@keyframes bounce {
		0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
		40% { transform: translateY(12px); }
		60% { transform: translateY(6px); }
	}
</style>
<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv-printshiv.min.js" ></script>
<![endif]-->
</head>
<body>
<h1>食</h1>
<aside>
	<nav>
		<ul>
			<li><a href="<?php echo $_SERVER['REQUEST_URI'] ?>" class="current" >列表</a></li>
			<li><a href="map" >地圖</a></li>
		</ul>
	</nav>
	<a href="#">以距離排序</a>
</aside>
<main>
<?php
	$data = json_decode(file_get_contents('data.json'), true);

	usort($data, function($a, $b) {
			return $b['latitude'] - $a['latitude'];
		});

	foreach($data as $item) {
		$name = $item['name'];
		$name2 = urlencode($name);
?>
	<article data-latitude="<?php echo $item['latitude'] ?>" data-longitude="<?php echo $item['longitude'] ?>" >
		<h2><a href="https://www.google.com.tw/search?q=<?php echo $name2 ?>" target="_blank" ><?php echo $name ?></a></h2>
		<pre><?php echo $item['description'] ?></pre>
	</article>
<?php
	}
?>
</main>
<script>
	document.querySelector('aside > a').addEventListener('click', function(e) {
		var main = document.querySelector('main'),
			articles = Array.from(main.getElementsByTagName('article'));

		articles.forEach(function(article) {
			article.classList.add('hidden');
		});

		navigator.geolocation.getCurrentPosition(function (position) {
			var coords = position.coords,
				latitude = coords.latitude,
				longitude = coords.longitude;

			articles.forEach(function(article) {
				article.distance = (function(lat, lng, lat2, lng2) {
					var dLat = deg2rad(lat2 - lat),
						dLng = deg2rad(lng2 - lng),
						a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat)) * Math.cos(deg2rad(lat2)) * Math.sin(dLng / 2) * Math.sin(dLng / 2),
						c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

					return c *  6371;

					function deg2rad(deg) {
						return deg * (Math.PI / 180);
					}
				})(latitude, longitude, article.getAttribute('data-latitude'), article.getAttribute('data-longitude'));
			});

			articles.sort(function(a, b) {
				return a.distance - b.distance;
			});


			var tokens = [];

			articles.forEach(function(article) {
				article.className = 'fadeIn';
				tokens.push(article.outerHTML);
			});

			main.innerHTML = tokens.join('\n');
		}, function() {
			articles.forEach(function(article) {
				article.className = 'fadeIn';
			});
		});

		e.preventDefault();
	});
</script>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Food Bottom -->
<ins class="ad adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-1821434700708607"
     data-ad-slot="8174544792"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
<script>
(function() {
	if (window.orientation !== undefined) {
		var links = document.getElementsByTagName('a'),
			n = links.length;

		for (var i = 0; i < n; ++i) {
			links[i].target = '_self';
		}
	}
})();
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-6851063-2', 'auto');
  ga('send', 'pageview');
</script>
</body>
</html>
