<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KPIs de l'équipe {{ $team->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eaeaea;
        }
        .header h1 {
            color: #2563eb;
            margin-bottom: 5px;
            font-size: 24px;
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
            gap: 15px;
        }
        .kpi-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 22%;
            box-sizing: border-box;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .kpi-card h3 {
            margin-top: 0;
            color: #555;
            font-size: 14px;
            font-weight: 600;
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
        .section-header {
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            background-color: #f0f4ff;
            padding: 10px 12px;
            border-radius: 6px;
            border-left: 4px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: none;
        }
        th {
            background-color: #f0f4ff;
            padding: 12px 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        td {
            padding: 12px 15px;
            font-size: 13px;
            border-bottom: 1px solid #eee;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .section-title {
            margin-top: 35px;
            margin-bottom: 15px;
            color: #2563eb;
            font-size: 18px;
            font-weight: 600;
            padding-bottom: 8px;
            border-bottom: 2px solid #eaeaea;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .progress-bar-container {
            width: 100%;
            background-color: #e5e7eb;
            height: 10px;
            border-radius: 10px;
            margin-top: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 10px;
            border-radius: 10px;
        }
        .progress-bar-green { background-color: #10b981; }
        .progress-bar-yellow { background-color: #f59e0b; }
        .progress-bar-red { background-color: #ef4444; }

        /* Ajout pour améliorer la mise en page */
        @page {
            margin: 40px;
        }
        .page-break {
            page-break-after: always;
        }
        /* Rendre les tableaux responsive pour qu'ils ne soient pas coupés */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport de KPIs - Équipe {{ $team->name }}</h1>
        <p>Généré le {{ $dateGeneration }}</p>
    </div>

    <!-- Section des tâches -->
    <div class="section-header">Statistiques des tâches</div>

    <div class="kpi-summary">
        <div class="kpi-card">
            <h3>Total des tâches</h3>
            <div class="kpi-value">{{ $teamKpiData['totalTasks'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Tâches terminées</h3>
            <div class="kpi-value green">{{ $teamKpiData['completedTasks'] }}</div>
            <div class="kpi-rate">Taux de complétion: {{ $teamKpiData['completionRate'] }}%</div>
        </div>

        <div class="kpi-card">
            <h3>Tâches en cours</h3>
            <div class="kpi-value blue">{{ $teamKpiData['inProgressTasks'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Tâches en attente</h3>
            <div class="kpi-value orange">{{ $teamKpiData['pendingTasks'] }}</div>
        </div>
    </div>

    <!-- Section des objectifs -->
    <div class="section-header">Statistiques des objectifs</div>

    <div class="kpi-summary">
        <div class="kpi-card">
            <h3>Total des objectifs</h3>
            <div class="kpi-value">{{ $teamKpiData['totalObjectives'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Objectifs atteints</h3>
            <div class="kpi-value green">{{ $teamKpiData['completedObjectives'] }}</div>
            <div class="kpi-rate">Taux de complétion: {{ $teamKpiData['objectivesCompletionRate'] }}%</div>
        </div>

        <div class="kpi-card">
            <h3>Objectifs en cours</h3>
            <div class="kpi-value blue">{{ $teamKpiData['totalObjectives'] - $teamKpiData['completedObjectives'] }}</div>
        </div>

        <div class="kpi-card">
            <h3>Objectifs par projet</h3>
            <div class="kpi-value purple">{{ $teamKpiData['objectivesPerProject'] }}</div>
            <div class="kpi-rate">Moyenne</div>
        </div>
    </div>

    <h2 class="section-title">Performance des collaborateurs</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Tâches totales</th>
                    <th>Tâches terminées</th>
                    <th>Taux</th>
                    <th>Temps moyen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collaborateurKpis as $kpi)
                    <tr>
                        <td>{{ $kpi['collaborateur']->name }}</td>
                        <td>{{ $kpi['collaborateur']->email }}</td>
                        <td>{{ $kpi['totalTasks'] }}</td>
                        <td>{{ $kpi['completedTasks'] }}</td>
                        <td>
                            <span style="color: {{ $kpi['completionRate'] >= 80 ? '#10b981' : ($kpi['completionRate'] >= 50 ? '#f59e0b' : '#ef4444') }}; font-weight: 600;">
                                {{ $kpi['completionRate'] }}%
                            </span>
                        </td>
                        <td>
                            @php
                                $averageTimeMinutes = $kpi['averageTimeMinutes'];
                                $hours = floor($averageTimeMinutes / 60);
                                $minutes = $averageTimeMinutes % 60;
                                $formattedTime = $hours > 0 ? $hours . 'h ' . $minutes . 'min' : $minutes . 'min';
                            @endphp
                            {{ $formattedTime }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <h2 class="section-title">Performance des projets</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Projet</th>
                    <th>Objectifs totaux</th>
                    <th>Objectifs atteints</th>
                    <th>Taux de complétion</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamKpiData['projectsObjectivesStats'] as $project)
                    <tr>
                        <td>{{ $project['name'] }}</td>
                        <td>{{ $project['totalObjectives'] }}</td>
                        <td>{{ $project['completedObjectives'] }}</td>
                        <td>
                            <span style="color: {{ $project['completionRate'] >= 80 ? '#10b981' : ($project['completionRate'] >= 50 ? '#f59e0b' : '#ef4444') }}; font-weight: 600;">
                                {{ $project['completionRate'] }}%
                            </span>
                            <div class="progress-bar-container">
                                <div class="progress-bar {{ $project['completionRate'] >= 80 ? 'progress-bar-green' : ($project['completionRate'] >= 50 ? 'progress-bar-yellow' : 'progress-bar-red') }}"
                                     style="width: {{ $project['completionRate'] }}%;">
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 class="section-title">Dernières tâches terminées</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Assigné à</th>
                    <th>Date de fin</th>
                    <th>Durée</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamKpiData['recentCompletedTasks'] as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->assignedTo ? $task->assignedTo->name : 'Non assigné' }}</td>
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
    </div>

    <h2 class="section-title">Derniers objectifs atteints</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Projet</th>
                    <th>Créé par</th>
                    <th>Date d'achèvement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teamKpiData['recentCompletedObjectives'] as $objective)
                    <tr>
                        <td>{{ $objective->title }}</td>
                        <td>{{ $objective->project ? $objective->project->name : 'N/A' }}</td>
                        <td>{{ $objective->creator ? $objective->creator->name : 'N/A' }}</td>
                        <td>
                            @if($objective->completed_at)
                                {{ \Carbon\Carbon::parse($objective->completed_at)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Ce rapport a été généré automatiquement par le système de gestion de tâches et objectifs.</p>
        <p>{{ $dateGeneration }}</p>
    </div>
</body>
</html>
