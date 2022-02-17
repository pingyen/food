<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no" >
<meta charset="UTF-8" >
<title>食 - 地圖</title>
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

	html {
		height: 100%;
	}

	body {
		height: 100%;
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

	#map-canvas {
		height: 90vh;
		height: calc(100% - 54px);
	}

	#map-canvas h2 {
		font-family: cwTeXKai;
		font-size: 24px;
		margin: 6px 0 0;
	}

	#map-canvas h2 > a {
		color: #000;
		text-decoration: none;
	}

	#map-canvas pre {
		line-height: 1;
		margin: 12px 0 6px;
		overflow: visible;
		padding-left: 2px;
	}
</style>
<!--[if lt IE 9]>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv-printshiv.min.js" ></script>
<![endif]-->
<script src="https://maps.googleapis.com/maps/api/js<?php if ($_SERVER['SERVER_NAME'] !== 'localhost') { echo '?key=AIzaSyCoDq0N1wYtdX_Oien1ZZ-wRhE2tIqHJ4k'; } ?>" ></script>
</head>
<body>
<h1>食</h1>
<aside>
	<nav>
		<ul>
			<li><a href="./" >列表</a></li>
			<li><a href="map" class="current" >地圖</a></li>
		</ul>
	</nav>
	<a href="#">以目前位置為中心</a>
</aside>
<div id="map-canvas"></div>
<script>
	(function() {
		var maps = google.maps,
			event = maps.event,
			zIndex = 1000,
			map = new maps.Map(document.getElementById('map-canvas')),
			data = <?php echo file_get_contents('data.json') ?>,
			latLngBounds = new maps.LatLngBounds();

		data.forEach(function(item) {
			var name = item.name,
				latLng = new maps.LatLng(item.latitude, item.longitude),
				infowindow = new maps.InfoWindow({
					content: '<h2><a href="https://www.google.com.tw/search?q=' + encodeURIComponent(name) + '" target="_blank" >' + name + '</a></h2><pre>' + item.description + '</pre>'
				}),
				marker = new maps.Marker({
					position: latLng,
					map: map,
					title: name
				});

			event.addListener(marker, 'click', function() {
				infowindow.setZIndex(++zIndex);
				infowindow.open(map, marker);
			});

			latLngBounds.extend(latLng);
		});

		map.setCenter(latLngBounds.getCenter());
		map.fitBounds(latLngBounds);

		event.addListenerOnce(map, 'idle', function() {
			var setCurrentPosition = function(latitude, longitude) {
				var latLng = new maps.LatLng(latitude, longitude),
					infowindow = new maps.InfoWindow({
						content: '目前位置'
					}),
					marker = new maps.Marker({
						position: latLng,
						map: map,
						title: '目前位置',
						icon: {
							anchor: new maps.Point(15, 16),
							path: 'M 15.625,0.625 19.375,11.25 30.625,11.25 21.875,18.125 25,28.75 15.625,22.5 6.25,28.75 9.375,18.125 0.375,11.25 11.875,11.25 z',
							fillColor: 'yellow',
							fillOpacity: 0.8,
							strokeColor: 'gold',
							strokeWeight: 2.4
						},
						zIndex: 10000
					});

				event.addListener(marker, 'click', function() {
					infowindow.open(map, marker);
				});

				map.setCenter(latLng);
				map.setZoom(16);
			};

			if (function() {
				var search = location.search;

				if (search === '') {
					return false;
				}

				var tokens = search.substr(1).split(',');

				if (tokens.length !== 2) {
					return false;
				}

				var latitude = parseFloat(tokens[0]),
					longitude = parseFloat(tokens[1]);

				if (isNaN(latitude) === true ||
					isNaN(longitude) === true) {
					return false;
				}

				setCurrentPosition(latitude, longitude);

				return true;
			}() === true) {
				return;
			}

			document.querySelector('aside > a').addEventListener('click', function(e) {
				navigator.geolocation.getCurrentPosition(function (position) {
					var coords = position.coords;

					setCurrentPosition(coords.latitude, coords.longitude);
				});

				e.preventDefault();
			});
		});
	})();
</script>
<script>
(function() {
	if (window.orientation === undefined) {
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
