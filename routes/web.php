<?php

use App\Http\Controllers\Admin\AdminController;

use App\Http\Controllers\User\AdministrationController;
use App\Http\Controllers\User\OperationsController;
use App\Http\Controllers\User\AnalyseMasseSalarialeController;
use App\Http\Controllers\User\ContributionsController;
use App\Http\Controllers\User\EnteteActiviteController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\TaskController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/gf/{any?}', function () {
    return view('app');
})->where('any', '.*');






Route::prefix('admin')->group(function () {
    Route::controller(\App\Http\Controllers\Admin\Auth\AdminLoginController::class)->group(function () {
        Route::get('/login', 'index')->name('admin.login');
        Route::post('/login', 'login');
    });
    Route::middleware('auth:admin')->group(function () {
        Route::resource('contributions', \App\Http\Controllers\Admin\ContributionsController::class);
        Route::any('/logout', [\App\Http\Controllers\Admin\Auth\AdminLoginController::class, 'logout'])->name('admin.logout');
        Route::controller(\App\Http\Controllers\Admin\DashboardController::class)->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        });
        Route::controller(\App\Http\Controllers\Admin\AdminController::class)->group(function () {
            Route::get('/admins/create', 'create')->name('admin.admins.create');
            Route::get('/admins', 'index')->name('admin.admins.index');
            Route::get('/admins/{admin}', 'show')->name('admin.admins.show');
            Route::get('/admins/{admin}/edit', 'edit')->name('admin.admins.edit');
            Route::get('/admins/{admin}/edit-password', 'editPassword')->name('admin.admins.edit-password');
            Route::post('/admins', 'store')->name('admin.admins.store');
            Route::put('/admins/{admin}', 'update')->name('admin.admins.update');
            Route::put('admins/{admin}/edit-password', 'updatePassword')->name('admin.admins.update-password');
        });

        Route::controller(\App\Http\Controllers\Admin\UserController::class)->group(function () {
            Route::get('/users/create', 'create')->name('admin.users.create');
            Route::get('/users', 'index')->name('admin.users.index');
            Route::get('/users/{user}', 'show')->name('admin.users.show');
            Route::get('/users/{user}/edit', 'edit')->name('admin.users.edit');
            Route::post('/users', 'store')->name('admin.users.store');
            Route::put('/users/{user}', 'update')->name('admin.users.update');
            Route::delete('/users/{user}', 'destroy')->name('admin.user.destroy');
        });
        Route::controller(\App\Http\Controllers\Admin\RoleController::class)->group(function () {
            Route::get('/roles/create', 'create')->name('admin.roles.create');
            Route::get('/roles', 'index')->name('admin.roles.index');
            Route::get('/roles/{role}/edit', 'edit')->name('admin.roles.edit');
            Route::post('/roles', 'store')->name('admin.roles.store');
            Route::put('/roles/{role}', 'update')->name('admin.roles.update');
            Route::delete('/roles/{role}', 'destroy')->name('admin.roles.destroy');
        });
        Route::controller(\App\Http\Controllers\Admin\TypeController::class)->group(function () {
            Route::get('/types/create', 'create')->name('admin.types.create');
            Route::get('/types', 'index')->name('admin.types.index');
            Route::get('/types/{type}/edit', 'edit')->name('admin.types.edit');
            Route::post('/types', 'store')->name('admin.types.store');
            Route::put('/types/{type}', 'update')->name('admin.types.update');
            Route::delete('/types/{type}', 'destroy')->name('admin.types.destroy');
        });

        Route::controller(\App\Http\Controllers\Admin\SubtypeController::class)->group(function () {
            Route::get('/subtypes/create', 'create')->name('admin.subtypes.create');
            Route::get('/subtypes', 'index')->name('admin.subtypes.index');
            Route::get('/subtypes/{subtype}/edit', 'edit')->name('admin.subtypes.edit');
            Route::post('/subtypes', 'store')->name('admin.subtypes.store');
            Route::put('/subtypes/{subtype}', 'update')->name('admin.subtypes.update');
            Route::delete('/subtypes/{subtype}', 'destroy')->name('admin.subtypes.destroy');
        });

        Route::controller(\App\Http\Controllers\Admin\CompanyController::class)->group(function () {
            Route::get('/companies/create', 'create')->name('admin.companies.create');
            Route::get('/companies', 'index')->name('admin.companies.index');
            Route::get('/companies/{id}', 'show')->name('admin.companies.show');
            Route::get('/companies/{company}/edit', 'edit')->name('admin.companies.edit');
            Route::post('/companies', 'store')->name('admin.companies.store');
            Route::put('/companies/{company}', 'update')->name('admin.companies.update');
            Route::delete('/companies/{company}', 'destroy')->name('admin.companies.destroy');
            Route::get('/admin/companies/searchUsers', 'searchUsers')->name('admin.companies.searchUsers');

        });
        Route::controller(\App\Http\Controllers\Admin\ContributionsController::class)->group(function () {
            Route::get('/contributions', 'index')->name('admin.contributions.index'); // Liste des contributions
            Route::get('/contributions/create', 'create')->name('admin.contributions.create'); // Formulaire de création
            Route::post('/contributions', 'store')->name('admin.contributions.store'); // Enregistrement
            Route::get('/contributions/{contribution}', 'show')->name('admin.contributions.show'); // Détails
            Route::get('/contributions/{contribution}/edit', 'edit')->name('admin.contributions.edit'); // Formulaire de modification
            Route::put('/contributions/{contribution}', 'update')->name('admin.contributions.update'); // Mise à jour
            Route::delete('/contributions/{contribution}', 'destroy')->name('admin.contributions.destroy'); // Suppression
            Route::get('/contributions/search', 'search')->name('admin.contributions.search'); // Recherche (optionnelle)
           
        });
        


       // Route::resource('contributions', \App\Http\Controllers\Admin\ContributionsController::class);
  
    });
});

Route::controller(\App\Http\Controllers\User\Auth\UserLoginController::class)->group(function () {
    Route::get('/login', 'index')->name('user.login');
    Route::post('/login', 'login');
});
Route::middleware('auth')->group(function () {
    Route::any('/logout', [\App\Http\Controllers\User\Auth\UserLoginController::class, 'logout'])->name('user.logout');
    Route::controller(\App\Http\Controllers\User\DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('user.dashboard');
    });
    Route::controller(\App\Http\Controllers\User\CompanyController::class)->group(function () {
        Route::get('/company/select', 'select')->name('user.company.select');
        Route::post('/company/select/{company}', 'setCompany')->name('user.company.set');
    });
    Route::controller(\App\Http\Controllers\User\BudgetController::class)->group(function () {
       Route::get('/budgets/create', 'create')->name('user.budgets.create');
       Route::get('/budgets', 'index')->name('user.budgets.index');
       Route::get('/budgets/analysis',  'budgetAnalysis')->name('user.budgets.analysis');
       Route::get('/budgets/{year}', 'showByYear')->name('user.budgets.show');
       Route::post('/budgets/store', 'store')->name('user.budgets.store');
       Route::post('/budgets/import/{year}',  'import')->name('user.budgets.import');
       Route::post('/budgets/store-imported/{year}', 'storeImported')->name('user.budgets.storeImported');
       Route::delete('/budgets/{year}', 'destroy')->name('user.budgets.destroy');
       Route::get('/get-subtypes/{typeId}', 'getSubtypes');
    });
    Route::controller(\App\Http\Controllers\User\ConfigurationController::class)->group(function () {
        Route::get('/configurations/create', 'create')->name('user.configurations.create');
        Route::get('/configurations', 'index')->name('user.configurations.index');
        Route::post('/configurations/store', 'store')->name('user.configurations.store');
        Route::get('/configurations/{id}/edit', 'edit')->name('user.configurations.edit');
        Route::put('/configurations/{id}', 'update')->name('user.configurations.update');
        Route::delete('configurations/{id}', 'destroy')->name('user.configurations.destroy');
    });
  
    
   // 🔹 Routes pour "Administration" (CRUD)


// 🔹 Routes pour "Opérations" (CRUD)
Route::controller(\App\Http\Controllers\User\OperationsController::class)->group(function () {
    Route::get('/user/fardeauMO/operations', 'index')->name('user.fardeauMO.operations.index');
    Route::get('/user/fardeauMO/operations/create', 'create')->name('user.fardeauMO.operations.create');
    Route::post('/user/fardeauMO/operations/store', 'store')->name('user.fardeauMO.operations.store');
    Route::get('/user/fardeauMO/operations/{id}', 'show')->name('user.fardeauMO.operations.show');
    Route::get('/user/fardeauMO/operations/{id}/edit', 'edit')->name('user.fardeauMO.operations.edit');
    Route::put('/user/fardeauMO/operations/{id}', 'update')->name('user.fardeauMO.operations.update');
    Route::delete('/user/fardeauMO/operations/{id}', 'destroy')->name('user.fardeauMO.operations.destroy');
});



// 🔹 Routes pour "Contributions" (CRUD complet)
Route::controller(\App\Http\Controllers\User\ContributionsController::class)->group(function () {
    Route::get('/user/fardeauMO/contributions', 'index')->name('user.fardeauMO.contributions.index');
    Route::get('/user/fardeauMO/contributions/create', 'create')->name('user.fardeauMO.contributions.create');
    Route::post('/user/fardeauMO/contributions/store', 'store')->name('user.fardeauMO.contributions.store');
    Route::get('/user/fardeauMO/contributions/{id}', 'show')->name('user.fardeauMO.contributions.show');
    Route::get('/user/fardeauMO/contributions/{id}/edit', 'edit')->name('user.fardeauMO.contributions.edit');
    Route::put('/user/fardeauMO/contributions/{id}', 'update')->name('user.fardeauMO.contributions.update');
    Route::delete('/user/fardeauMO/contributions/{id}', 'destroy')->name('user.fardeauMO.contributions.destroy');
   
});
// 🔹 Routes pour "Types d'opérations" (CRUD complet)
Route::controller(\App\Http\Controllers\User\OperationTypeController::class)->group(function () {
    Route::get('/user/fardeauMO/operation_types', 'index')->name('user.fardeauMO.operation_types.index');
    Route::get('/user/fardeauMO/operation_types/create', 'create')->name('user.fardeauMO.operation_types.create');
    Route::post('/user/fardeauMO/operation_types/store', 'store')->name('user.fardeauMO.operation_types.store');
    Route::get('/user/fardeauMO/operation_types/{id}', 'show')->name('user.fardeauMO.operation_types.show'); // ✅ Ajout de la route "show"
    Route::get('/user/fardeauMO/operation_types/{id}/edit', 'edit')->name('user.fardeauMO.operation_types.edit');
    Route::put('/user/fardeauMO/operation_types/{id}', 'update')->name('user.fardeauMO.operation_types.update');
    Route::delete('/user/fardeauMO/operation_types/{id}', 'destroy')->name('user.fardeauMO.operation_types.destroy');
   

});

Route::controller(\App\Http\Controllers\User\EmployeeController::class)->group(function () {
    Route::get('/user/fardeauMO/employees', 'index')->name('user.fardeauMO.employees.index');
    Route::get('/user/fardeauMO/employees/create', 'create')->name('user.fardeauMO.employees.create');
    Route::post('/user/fardeauMO/employees/store', 'store')->name('user.fardeauMO.employees.store');
    Route::get('/user/fardeauMO/employees/{id}', 'show')->name('user.fardeauMO.employees.show'); // ✅ Route "show"
    Route::get('/user/fardeauMO/employees/{id}/edit', 'edit')->name('user.fardeauMO.employees.edit');
    Route::put('/user/fardeauMO/employees/{id}', 'update')->name('user.fardeauMO.employees.update');
    Route::delete('/user/fardeauMO/employees/{id}', 'destroy')->name('user.fardeauMO.employees.destroy');
    Route::get('/user/fardeauMO/employees/{operationType}', 'index')->name('employees.byType');
    Route::post('/user/fardeauMO/employees/bulk-save', 'bulkSave')->name('user.fardeauMO.employees.bulkSave');
});

   Route::controller(\App\Http\Controllers\User\CoutCamionController::class)->group(function () {
    Route::get('/user/coutscamion', 'index')->name('user.coutscamion.index');
    Route::get('/user/coutscamion/create', 'create')->name('user.coutscamion.create');
    Route::post('/user/coutscamion/store', 'store')->name('user.coutscamion.store');
    Route::get('/user/coutscamion/{id}', 'show')->name('user.coutscamion.show');
    Route::get('/user/coutscamion/{id}/edit', 'edit')->name('user.coutscamion.edit');
    Route::put('/user/coutscamion/{id}', 'update')->name('user.coutscamion.update');
    Route::delete('/user/coutscamion/{id}', 'destroy')->name('user.coutscamion.destroy');
    Route::delete('/user/coutscamion/column/{slug}', 'destroyColumn')
    ->name('user.coutscamion.destroyColumn');
});

Route::controller(\App\Http\Controllers\User\EnteteActiviteController::class)->group(function () {
    Route::get('/user/fardeauMO/entetes', 'index')->name('user.fardeauMO.entetes.index');
    Route::post('/user/fardeauMO/entetes', 'store')->name('user.fardeauMO.entetes.store'); // AJAX store
    Route::put('/user/fardeauMO/entetes/{id}', 'update')->name('user.fardeauMO.entetes.update'); // AJAX update
    Route::delete('/user/fardeauMO/entetes/{id}', 'destroy')->name('user.fardeauMO.entetes.destroy'); // AJAX delete
    Route::post('/user/fardeauMO/entetes/store-ajax', 'storeFromAjax')->name('user.fardeauMO.entetes.storeAjax');
});
Route::post('/user/fardeauMO/entetes/store-ajax', [EnteteActiviteController::class, 'storeFromAjax'])
->name('user.fardeauMO.entetes.storeAjax');
Route::controller(\App\Http\Controllers\User\RecapitulatifActiviteController::class)->group(function () {
    Route::get('/user/fardeauMO/recap', 'show')->name('user.fardeauMO.recap.show');
    Route::post('/user/fardeauMO/recap/recompute', 'recompute')->name('user.fardeauMO.recap.recompute');
});

Route::controller(\App\Http\Controllers\User\TaskController::class)->group(function () {
    Route::get('/user/tasks', 'index')->name('user.tasks.index');
    Route::get('/user/tasks/create', 'create')->name('user.tasks.create');
    Route::post('/user/tasks/store', 'store')->name('user.tasks.store');
    Route::get('/user/tasks/{id}', 'show')->name('user.tasks.show');
    Route::get('/user/tasks/{id}/edit', 'edit')->name('user.tasks.edit');
    Route::put('/user/tasks/{id}', 'update')->name('user.tasks.update');
    Route::delete('/user/tasks/{id}', 'destroy')->name('user.tasks.destroy');

    // ✅ Suppression multiple (via case à cocher)
    Route::post('/user/tasks/mass-delete', 'massDelete')->name('user.tasks.massDelete');
});
Route::controller(\App\Http\Controllers\User\AmortissementController::class)->group(function () {
    Route::get('/amortissements/create', 'create')->name('user.amortissements.create');
    Route::get('/amortissements', 'index')->name('user.amortissements.index');
    Route::get('/amortissements/{year}', 'showByYear')->name('user.amortissements.show');
    Route::post('/amortissements/store', 'store')->name('user.amortissements.store');
    Route::post('/amortissements/import/{year}', 'import')->name('user.amortissements.import');
    Route::post('/amortissements/store-imported/{year}', 'storeImported')->name('user.amortissements.storeImported');
    Route::delete('/amortissements/{year}', 'destroy')->name('user.amortissements.destroy');
      // ✅ Nouvelle route ajoutée
    Route::post('/amortissements/bulk-destroy',  'bulkDestroy') ->name('user.amortissements.bulkDestroy');
    Route::get('/amortissements/row', 'row')->name('user.amortissements.row');
});
Route::controller(\App\Http\Controllers\User\AnalyseMasseSalarialeController::class)->group(function () {
    // Page Analyse (tableau + graphique)
    Route::get('/user/fardeauMO/analyse-mo', 'index')
        ->name('user.fardeauMO.analyse-mo.index');

    // (Optionnel) Endpoint JSON si tu veux charger le graphe/les données en AJAX
    Route::get('/user/fardeauMO/analyse-mo/data', 'data')
        ->name('user.fardeauMO.analyse-mo.data');
});


    
       
    });
    
    
    
    

