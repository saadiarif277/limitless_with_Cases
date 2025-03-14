<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::permanentRedirect('/', '/login');

/*
|--------------------------------------------------------------------------
| Panel Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the authenticated area
| of the application or the panel. These are further grouped into the
| admin and user panels.
|
*/
Route::group(['middleware' => ['auth']], function() {

    /*
    |--------------------------------------------------------------------------
    | User Panel Routes
    |--------------------------------------------------------------------------
    |
    | The routes found here are catered to the usage of the user.
    |
    */

    Route::group(['middleware' => ['panel.redirect.to.admin']], function() {

        Route::get('/dashboard', [\App\Http\Controllers\Panel\User\DashboardController::class, 'index'])
            ->name('panel.user.dashboard');

        /**
         * Documents
         */
        Route::get('documents/{document}/download', [\App\Http\Controllers\Panel\User\DocumentController::class, 'download'])
            ->name('panel.user.documents.download');

        Route::delete('documents/{document}', [\App\Http\Controllers\Panel\User\DocumentController::class, 'destroy'])
            ->name('panel.user.documents.destroy');

        /**
         * Patients
         */
        Route::get('/patients', [\App\Http\Controllers\Panel\User\UserController::class, 'getPatients'])
            ->name('panel.user.patients.index');

        Route::get('/patients/{user}', [\App\Http\Controllers\Panel\User\UserController::class, 'showPatient'])
            ->name('panel.user.patients.show');

        Route::match(['PUT', 'PATCH'], '/patients/{user}', [\App\Http\Controllers\Panel\User\UserController::class, 'update'])
            ->name('panel.user.patients.update');

        Route::post('/patients', [\App\Http\Controllers\Panel\User\UserController::class, 'store'])
            ->name('panel.user.patients.store');

        Route::delete('/patients/{user}', [\App\Http\Controllers\Panel\User\UserController::class, 'destroy'])
            ->name('panel.user.patients.destroy');

        /**
         * User Profile Management
         */
        Route::get('/me/profile', [\App\Http\Controllers\Panel\User\ProfileController::class, 'edit'])
            ->name('panel.user.profile.edit');

        Route::patch('/me/profile', [\App\Http\Controllers\Panel\User\ProfileController::class, 'update'])
            ->name('panel.user.profile.update');

        Route::group(['as' => 'panel.user.'], function () {

            Route::resources([
                'appointments' => \App\Http\Controllers\Panel\User\AppointmentController::class,
                'referrals' =>  \App\Http\Controllers\Panel\User\ReferralController::class,
                'cases' =>  \App\Http\Controllers\Panel\User\CasesController::class,
            ]);

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Routes
    |--------------------------------------------------------------------------
    |
    | The routes found here are catered to application management.
    |
    */

    Route::group(['prefix' => 'admin', 'middleware' => ['panel.redirect.to.user', 'can:app.panel.admin']], function() {

        Route::get('/', function () {
            return Inertia::location('/admin/dashboard');
        });

        Route::get('dashboard', [\App\Http\Controllers\Panel\Admin\DashboardController::class, 'index'])
            ->name('panel.admin.dashboard');

        Route::get('activity-log', [\App\Http\Controllers\Panel\Admin\ActivityLogController::class, 'index'])
            ->name('panel.admin.activity-log.index');

        Route::get('documents/{document}/download', [\App\Http\Controllers\Panel\Admin\DocumentController::class, 'download'])
            ->name('panel.admin.documents.download');

        Route::delete('documents/{document}', [\App\Http\Controllers\Panel\Admin\DocumentController::class, 'destroy'])
            ->name('panel.admin.documents.destroy');

        Route::group(['as' => 'panel.admin.'], function () {

            Route::resources([
                'appointments' => \App\Http\Controllers\Panel\Admin\AppointmentController::class,
                'clinics' => \App\Http\Controllers\Panel\Admin\ClinicController::class,
                'document-types' => \App\Http\Controllers\Panel\Admin\DocumentTypeController::class,
                'law-firms' => \App\Http\Controllers\Panel\Admin\LawFirmController::class,
                'referrals' => \App\Http\Controllers\Panel\Admin\ReferralController::class,
                'cases' => \App\Http\Controllers\Panel\Admin\CasesController::class,

                'roles' => \App\Http\Controllers\Panel\Admin\RoleController::class,
                'states' => \App\Http\Controllers\Panel\Admin\StateController::class,
                'users' => \App\Http\Controllers\Panel\Admin\UserController::class,
            ]);
            Route::get('ict-codes',[\App\Http\Controllers\Panel\Admin\Icd10CodeController::class,'index'])->name('ictcodes.list');
            Route::get('/create/ict-codes',[\App\Http\Controllers\Panel\Admin\Icd10CodeController::class,'create'])->name('ictcodes.create');
            Route::post('/api/ictcodes', [\App\Http\Controllers\Panel\Admin\Icd10CodeController::class, 'store'])->name('ictcodes.store');
            Route::post('admin/ict-codes/bulk-upload', [\App\Http\Controllers\Panel\Admin\Icd10CodeController::class, 'bulkUpload'])->name('ictcodes.bulk-upload');
            Route::get('cpt-codes',[\App\Http\Controllers\Panel\Admin\CptCodeController::class,'index'])->name('cptcodes.list');
            Route::post('admin/cpt-codes/bulk-upload', [\App\Http\Controllers\Panel\Admin\CptCodeController::class, 'bulkUpload'])->name('cptcodes.bulk-upload');
            Route::get('/create/cpt-codes',[\App\Http\Controllers\Panel\Admin\CptCodeController::class,'create'])->name('cptcodes.create');
            Route::post('/api/cptcodes', [\App\Http\Controllers\Panel\Admin\CptCodeController::class, 'store'])->name('cptcodes.store');
        });

    });

});

require __DIR__.'/auth.php';
