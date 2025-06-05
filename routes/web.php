<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Inertia\Inertia;
use Illuminate\Auth\Middleware\Authenticate;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Controllers\{
    TaskController,
    ProjectController,
    ObjectiveController,
    DashboardController,
    UserPresenceController,
    AdminController,
    MessageController
};
use App\Http\Middleware\CheckRole;

// ------------------
// 🌐 Routes publiques
// ------------------

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/terms', function () {
    return Inertia::render('TermsOfService');
})->name('terms.show');

Route::get('/privacy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('policy.show');

Route::get('/acces-refuse', function () {
    return Inertia::render('AccessDenied');
})->name('access.denied');

// -----------------------------
// 🔐 Routes protégées (auth + verified)
// -----------------------------

Route::middleware([
    Authenticate::class,
    AuthenticateSession::class,
    EnsureEmailIsVerified::class,
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // ----------------------
    // 📌 Tâches (accessibles à tous les rôles connectés)
    // ----------------------

    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [TaskController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'completeTask'])->name('tasks.complete');

    // ----------------------
    // 📁 Projets (accès général en lecture seule)
    // ----------------------

    Route::resource('projects', ProjectController::class)->only([
        'index', 'show', 'edit', 'create'
    ]);

    // ----------------------
    // 📈 KPI Collaborateur (individuel)
    // ----------------------

    Route::get('/collaborateur/{collaborateur}/kpi', [TaskController::class, 'collaborateurKpi'])->name('collaborateur.kpi');
    Route::get('/my-kpi', [TaskController::class, 'collaborateurKpi'])->name('my.kpi');

    Route::get('/collaborateur/{collaborateur}/kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])->name('collaborateur.kpi.pdf');
    Route::get('/my-kpi/pdf', [TaskController::class, 'exportCollaborateurKpiPdf'])->name('my.kpi.pdf');

    // ----------------------
    // 📬 Test Email
    // ----------------------

    Route::get('/test-email', function () {
        try {
            Mail::raw('Test d\'envoi d\'email via Mailtrap', function ($message) {
                $message->to('lc.gautier@icloud.com')
                        ->subject('Test Mailtrap Configuration');
            });
            return 'Email envoyé avec succès, vérifiez votre boîte Mailtrap';
        } catch (\Exception $e) {
            return 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage();
        }
    });

    // ----------------------
    // 👑 Routes administrateur
    // ----------------------

    // Routes pour l'administrateur
    Route::middleware([CheckRole::class.':administrateur'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestion des utilisateurs
        Route::get('/users', [AdminController::class, 'usersList'])->name('users.list');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        Route::get('/teams', [AdminController::class, 'teamsList'])->name('teams.list');

        Route::get('/{team}', [AdminController::class, 'teamDetails'])->name('teams.show');
        Route::get('/{team}/edit', [AdminController::class, 'editTeam'])->name('teams.edit');
        Route::put('/{team}', [AdminController::class, 'updateTeam'])->name('teams.update');

        // Création d'équipe
        Route::get('/teams/create', [AdminController::class, 'createTeam'])->name('teams.create');
        Route::post('/teams', [AdminController::class, 'storeTeam'])->name('teams.store');

        // Détails, modification et suppression d'équipe
        Route::get('/teams/{team}', [AdminController::class, 'teamDetails'])->name('teams.details');
        Route::get('/teams/{team}/edit', [AdminController::class, 'editTeam'])->name('teams.edit');
        Route::put('/teams/{team}', [AdminController::class, 'updateTeam'])->name('teams.update');
        Route::delete('/teams/{team}', [AdminController::class, 'destroyTeam'])->name('teams.destroy');

        // Gestion des membres d'équipe
        Route::get('/teams/{team}/members/create', [AdminController::class, 'createTeamMember'])->name('teams.members.create');
        Route::post('/teams/{team}/members', [AdminController::class, 'storeTeamMember'])->name('teams.members.store');
        Route::get('/teams/{team}/members/{user}/edit', [AdminController::class, 'editTeamMember'])->name('teams.members.edit');
        Route::put('/teams/{team}/members/{user}', [AdminController::class, 'updateTeamMember'])->name('teams.members.update');
        Route::delete('/teams/{team}/members/{user}', [AdminController::class, 'destroyTeamMember'])->name('teams.members.destroy');

            // Gestion des projets - Ajoutez ces routes
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('list');
            Route::get('/create', [ProjectController::class, 'create'])->name('create');
            Route::post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
            Route::get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
        });

        // Gestion système
        Route::get('/system/logs', [AdminController::class, 'systemLogs'])->name('system.logs');

        Route::post('/system/maintenance/enable', [AdminController::class, 'enableMaintenance'])->name('system.maintenance.enable');
        Route::post('/system/maintenance/disable', [AdminController::class, 'disableMaintenance'])->name('system.maintenance.disable');
    });

// ----------------------
// 🧑‍💼 Routes manager
// ----------------------

        // Modifier ce middleware pour inclure les administrateurs
        Route::middleware([CheckRole::class.':manager,administrateur'])->group(function () {
            Route::get('/manager/dashboard', [DashboardController::class, 'managerDashboard'])->name('manager.dashboard');

            // KPI équipe
            Route::get('/manager/team-kpi', [TaskController::class, 'teamKpi'])->name('manager.team.kpi');
            Route::get('/manager/team-kpi/pdf', [TaskController::class, 'exportTeamKpiPdf'])->name('manager.team.kpi.pdf');

            // Projets manager (création / mise à jour / suppression)
            Route::resource('projects', ProjectController::class)->except([
                'index', 'show'
            ]);

            Route::get('/manager/team-presence', [UserPresenceController::class, 'teamPresence'])->name('manager.team.presence');
            Route::get('/manager/collaborateur/{collaborateur}/presence', [UserPresenceController::class, 'userPresence'])->name('manager.user.presence');

            // Gestion des tâches
            Route::get('/manager/tasks', [TaskController::class, 'managerTasks'])->name('manager.tasks');
            Route::get('/manager/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
            Route::post('/manager/tasks', [TaskController::class, 'store'])->name('tasks.store');

            // Objectifs liés à un projet
            Route::post('/projects/{project}/objectives', [ObjectiveController::class, 'store'])->name('objectives.store');
            Route::put('/objectives/{objective}', [ObjectiveController::class, 'update'])->name('objectives.update');
            Route::delete('/objectives/{objective}', [ObjectiveController::class, 'destroy'])->name('objectives.destroy');
            Route::post('/objectives/{objective}/toggle', [ObjectiveController::class, 'toggleCompletion'])->name('objectives.toggle');
        });

    // ----------------------
    // 👤 Routes collaborateur
    // ----------------------

    Route::middleware([CheckRole::class.':collaborateur'])->group(function () {
        Route::get('/collaborateur/dashboard', [DashboardController::class, 'collaborateurDashboard'])
        ->middleware([CheckRole::class.':collaborateur'])
        ->name('collaborateur.dashboard');

        Route::get('/collaborateur/tasks', [TaskController::class, 'collaborateurTasks'])->name('collaborateur.tasks');

        Route::get('/collaborateur/my-presence', [UserPresenceController::class, 'userPresence'])->name('collaborateur.my.presence');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        // Messagerie
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('/create/{user?}', [MessageController::class, 'create'])->name('create');
            Route::post('/', [MessageController::class, 'store'])->name('store');
            Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
            Route::post('/{conversation}/reply', [MessageController::class, 'reply'])->name('reply');
            Route::delete('/{message}', [MessageController::class, 'destroy'])->name('destroy');
            Route::patch('/{message}/read', [MessageController::class, 'markAsRead'])->name('markAsRead');
            Route::get('/api/unread-count', [MessageController::class, 'unreadCount'])->name('unreadCount');
        });
    });

    // ----------------------
    // 👤 Routes Slack
    // ----------------------

        Route::middleware([
        Authenticate::class,
        AuthenticateSession::class,
        EnsureEmailIsVerified::class,
    ])->prefix('slack')->name('slack.')->group(function () {

        // Page principale Slack
        Route::get('/', [SlackController::class, 'index'])->name('index');

        // Envoi de messages
        Route::post('/send-message', [SlackController::class, 'sendMessage'])->name('send.message');

        // Partage de KPIs
        Route::post('/share-kpis', [SlackController::class, 'shareKpis'])->name('share.kpis');
    });
});
