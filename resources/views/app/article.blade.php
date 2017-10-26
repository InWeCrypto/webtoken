<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes"/>
    <title>文章详情</title>
    <style>
        img {
            width: 100%;
            display: block;
        }

        /*#face {*/
            /*height: 200px;*/
        /*}*/
        *{
            padding:0;
            margin:0;
        }
        body {
            margin: 15px 15px;
        }
    </style>
</head>
<body>
<div class="title">
    <img id="face" src="{{ $record->detail['img'] }}" alt="">
    <h3>{{ $record->detail['title'] }}</h3>
    <p>{{ $record->created_at->toDateString() }}</p>
</div>
<div class="content">
    {!! $record->detail['content'] !!}
</div>
</body>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $(function () {
        var maxWidth = $('body').width();
        $('img').each(function (index,value) {
            var width = $(this).width();
            var height = $(this).height();
            $(this).css('height',maxWidth * height / width);
        });
    })
</script>
</html>