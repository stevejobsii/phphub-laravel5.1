<!DOCTYPE html>
<html lang="zh">
	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<meta name="keywords" content="PHP,Laravel,PHP论坛,Laravel论坛,PHP社区,Laravel社区" />
		<meta name="author" content="The PHP China Community." />
		<meta name="description" content="@section('description') PHP China 是 PHP 和 Laravel 的中文社区，致力于推动 Laravel, php-fig 等国外 PHP 新技术, 新理念在中国的发展。 @show" />

        <link rel="stylesheet" href="https://dn-phphub.qbox.me/assets/css/styles-2cf576b7.css">
        <link rel="stylesheet" href="{{ url('/css/hub.css') }}">

        <link rel="shortcut icon" href="/images/catalog/30avatardefault.jpg">
        <script>
            Config = {
                'cdnDomain': '{{ getCdnDomain() }}',
                'user_id': {{ Auth::user() ? Auth::user()->id : 0 }},
                'routes': {
                    'notificationsCount' : '{{ route('notifications.count') }}',
                    'upload_image' : '{{ route('upload_image') }}'
                },
                'token': '{{ csrf_token() }}',
            };
        </script>

	    @yield('styles')

	</head>
	<body id="body">

		<div id="wrap">

			@include('layouts.partials.nav')

			<div class="container">

				@include('flash')

				@yield('content')

			</div>

		</div>

	  <div id="footer" class="footer">
	    <div class="container small">
	      <p class="pull-left">
	      	<i class="fa fa-heart-o"></i> Made With Love By <a href="http://est-group.org/" style="color:#989898;">The EST Group</a>. <br>
			&nbsp;<i class="fa fa-lightbulb-o"></i> Inspired by v2ex & ruby-china.
	      </p>
	    </div>
	  </div>

        <script src="https://dn-phphub.qbox.me/assets/js/scripts-6484ddb8.js"></script>
        <script src="/js/hub.js"></script>

	</body>
</html>
