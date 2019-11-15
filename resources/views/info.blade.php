<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Code Challenge</title>
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: sans-serif;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
            flex-direction: column;
        }
    </style>
</head>
<body>
<div class="flex-center full-height">
    @include('image-list')

    <h2>Details</h2>
    <div>
        <h4>Name:</h4>
        <h3>{{$name}}</h3>
    </div>

    @if(isset($genres) && !empty($genres))
        <div>
            <h4>Genres:</h4>
            <ul>
                @foreach($genres as $genre)
                <li>{{$genre}}</li>
                @endforeach
            <ul>
        </div>
    @endif

    @if(isset($tracksCount))
        <div>
            <h4>Total tracks:</h4>
            <h3>{{$tracksCount}}</h3>
        </div>
    @endif

    @if(isset($followersCount))
        <div>
            <h4>Followers:</h4>
            <h3>{{$followersCount}}</h3>
        </div>
    @endif

    @if(isset($popularity))
        <div>
            <h4>Popularity score:</h4>
            <h3>{{$popularity}}</h3>
        </div>
    @endif
</div>
</body>
</html>
