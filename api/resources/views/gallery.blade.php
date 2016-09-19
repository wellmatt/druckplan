<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta content="charset=utf-8">
    <title>Bildergalerie AppLL</title>
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;">
    <link rel="stylesheet" href="{{ asset('/css/flexslider.css') }}" type="text/css" media="screen" />

    <!-- jQuery -->
    <script src="{{ asset('/js/jquery.min.js') }}"></script>

    <!-- FlexSlider -->
    <script defer src="{{ asset('/js/jquery.flexslider.js') }}"></script>

    <script type="text/javascript">
        $(function(){
            SyntaxHighlighter.all();
        });
        $(window).load(function(){
            $('.flexslider').flexslider({
                animation: "slide",
                slideshow: false,
                start: function(slider){
                    $('body').removeClass('loading');
                }
            });
        });
    </script>

    <!-- Optional FlexSlider Additions -->
    <script src="{{ asset('/js/jquery.easing.js') }}"></script>
    <script src="{{ asset('/js/jquery.mousewheel.js') }}"></script>
</head>


<body class="loading">
<div id="main" role="main">
    <section class="slider">
        <div class="flexslider">
            <ul class="slides">
                @foreach($photos as $photo)
                    <li><img src='{{ asset('images/news/'.$photo->url) }}' /></li>
                @endforeach
            </ul>
        </div>
    </section>
</div>


</body>
</html>