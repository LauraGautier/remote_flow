<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KPIs de {{ $collaborateur->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin-top: 0;
        }
        .kpi-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .kpi-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            width: 22%;
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        .kpi-card h3 {
            margin-top: 0;
            color: #666;
            font-size: 14px;
        }
        .kpi-value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .kpi-rate {
            font-size: 12px;
            color: #666;
        }
        .green { color: #10b981; }
        .blue { color: #2563eb; }
        .orange { color: #f59e0b; }
        .purple { color: #8b5cf6; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-size: 12px;
        }
        td {
            padding: 10px;
            font-size: 12px;
        }
        .section-title {
            margin-top: 30px;
            margin-bottom: 10px;
            color: #2563eb;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport de KPIs - {{ $collaborateur->name }}</h1>
        <p>{{ $collaborateur->email }}</p>
        <p>Généré le {{ $dateGeneration }}</p>
    </div>

    <div class="kpi-summary">
        <div class="kpi-card">
            <h3>Total des tâches</h3>
            <div class="kpi-value">{{ $kpiData['totalTasks'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Tâches terminées</h3>
            <div class="kpi-value green">{{ $kpiData['completedTasks'] }}</div>
            <div class="kpi-rate">Taux de complétion: {{ $kpiData['completionRate'] }}%</div>
        </div>

        <div class="kpi-card">
            <h3>Tâches en cours</h3>
            <div class="kpi-value blue">{{ $kpiData['inProgressTasks'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Temps moyen par tâche</h3>
            <div class="kpi-value purple">
                @php
                    $averageTimeMinutes = $kpiData['averageTimeMinutes'];
                    $hours = floor($averageTimeMinutes / 60);
                    $minutes = $averageTimeMinutes % 60;
                    $formattedTime = $hours > 0 ? $hours . 'h ' . $minutes . 'min' : $minutes . 'min';
                @endphp
                {{ $formattedTime }}
            </div>
        </div>
    </div>

    <h2 class="section-title">Dernières tâches terminées</h2>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Équipe</th>
                <th>Date de fin</th>
                <th>Durée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kpiData['recentCompletedTasks'] as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->team->name }}</td>
                    <td>
                        @if($task->end_time)
                            {{ \Carbon\Carbon::parse($task->end_time)->format('d/m/Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($task->start_time && $task->end_time)
                            @php
                                $start = \Carbon\Carbon::parse($task->start_time);
                                $end = \Carbon\Carbon::parse($task->end_time);
                                $durationMinutes = $end->diffInMinutes($start);
                                $hours = floor($durationMinutes / 60);
                                $minutes = $durationMinutes % 60;
                                $formattedDuration = $hours > 0 ? $hours . 'h ' . $minutes . 'min' : $minutes . 'min';
                            @endphp
                            {{ $formattedDuration }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système de gestion de tâches.</p>
    </div>
</body>
</html>
