<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />

{{--    <meta property="og:url" content="{{ env('APP_URL') .'get-object-facebook/'. $object->id }}" />--}}
    <meta property="og:url" content="{{ env('APP_URL') .'get-object-facebook/' }}" />
    <meta property="og:title" content="{{ $object->name . " - " . $object->address }}" />
    <meta property="og:description" content="{{ $object->description }}" />
    <meta property="og:image" content="https://www.facebook.com/images/fb_icon_325x325.png" />
    <meta property="og:type" content="website" />
    <meta property="fb:app_id" content="1509381815774111"/>
</head>
<body>



</body>
</html>