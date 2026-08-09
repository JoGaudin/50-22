<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 4px; }
        p.subtitle { text-align: center; color: #666; margin-top: 0; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 6px 8px; vertical-align: top; width: 50%; }
        th { background: #f2f2f2; text-align: left; }
        .param-name { font-weight: bold; background: #fafafa; }
    </style>
</head>
<body>
    <h1>{{ $match->homeTeam->name }} — {{ $match->outsideTeam->name }}</h1>
    <p class="subtitle">{{ $match->date }}</p>

    <table>
        <tr>
            <th>{{ $match->homeFiche->name }} — {{ $match->homeTeam->name }}</th>
            <th>{{ $match->outsideFiche->name }} — {{ $match->outsideTeam->name }}</th>
        </tr>
        @foreach ($paramDescriptions as $param)
            <tr>
                <td>
                    <div class="param-name">{{ $param->name }}</div>
                    <div>{{ $match->homeFiche->paramDescriptions->firstWhere('id', $param->id)?->pivot->description ?: '—' }}</div>
                </td>
                <td>
                    <div class="param-name">{{ $param->name }}</div>
                    <div>{{ $match->outsideFiche->paramDescriptions->firstWhere('id', $param->id)?->pivot->description ?: '—' }}</div>
                </td>
            </tr>
        @endforeach
    </table>
</body>
</html>
