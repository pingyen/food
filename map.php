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
			<li><a href="<?php echo substr($_SERVER['REQUEST_URI'], 0, -3) ?>" >列表</a></li>
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
	if (mobileCheck()) {
		var links = document.getElementsByTagName('a'),
			n = links.length;

		for (var i = 0; i < n; ++i) {
			links[i].target = '_self';
		}
	}

	function mobileCheck() {
  		var check = false;

		(function(a,b){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);

		return check;
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
