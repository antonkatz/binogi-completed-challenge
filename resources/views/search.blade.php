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
            margin: 50px;
        }

        .full-height {
            height: 100vh;
        }

        .result {
        }
    </style>
</head>
<body>
<div class="full-height">
    <div class="result">
        Your Search Term Was: <b>{{$searchTerm}}</b>
    </div>


    @foreach ($items as $type => $itemsOfType)
        <div class="container {{$type}}">
            <h2>{{$typeToDisplayName[$type]}}</h2>
            <ul class="list {{$type}}">
                @foreach($itemsOfType as $item)
                    <li class="item">
                        <a href='{{$item->infoLink}}'>
                            <img src={{$item->imageUrl}} height=30/>
                            <span>{{$item->name}}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
</body>
</html>
