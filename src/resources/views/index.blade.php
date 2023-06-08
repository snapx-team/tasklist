<!DOCTYPE html>
<html>
<head>
    <title>Tasklist</title>
    <link href="{{ secure_asset(mix("app.css", 'vendor/tasklist')) }}?v={{config('tasklist.version')}}"
          rel="stylesheet" type="text/css">
</head>
<body>
<div id="app"></div>
<script
    src="{{ secure_asset(mix('app.js', 'vendor/tasklist')) }}?v={{config('tasklist.version')}}"></script>
</body>
</html>
