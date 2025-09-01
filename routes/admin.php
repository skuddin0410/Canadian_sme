<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;   
use App\Http\Controllers\UserController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SponsorsController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\AttendeeUserController;
use App\Http\Controllers\ExhibitorUserController;
use App\Http\Controllers\RepresentativeUserController;
use App\Http\Controllers\ExhibitorAdmin\BoothController;
use App\Http\Controllers\EventTrackController;

Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    Route::resource('pages',   App\Http\Controllers\PageController::class);
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::resource('coupons', App\Http\Controllers\CouponController::class);
    Route::resource('faqs', App\Http\Controllers\FaqController::class);
    Route::resource('otps', App\Http\Controllers\OtpController::class);
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::resource('transactions', App\Http\Controllers\TransactionController::class);
    Route::resource('settings', App\Http\Controllers\SettingController::class);
    Route::resource('admin-users', App\Http\Controllers\AdminUsersController::class);

    Route::post('/delete/photo', [App\Http\Controllers\EventController::class, 'removePhoto'])->name('events.removePhoto');
    Route::resource('events', App\Http\Controllers\EventController::class);
    
    Route::get('/event-tracks', [EventTrackController::class, 'index'])->name('event-tracks.index');
    // Show form to create a new event track (modal can trigger this)
    Route::get('/event-tracks/create', [EventTrackController::class, 'create'])->name('event-tracks.create');
    // Store new event track
    Route::post('/event-tracks', [EventTrackController::class, 'store'])->name('event-tracks.store');

    Route::get('/role-permission-matrix', [App\Http\Controllers\RoleController::class, 'matrix'])->name('roles.matrix');
    Route::post('/assign-permission', [App\Http\Controllers\RoleController::class, 'assignPermission'])->name('roles.assign.permission');
    Route::get('/roles/create', [App\Http\Controllers\RoleController::class, 'create']);
    Route::post('/roles', [App\Http\Controllers\RoleController::class, 'store']);

    Route::get('users/export/', '\App\Http\Controllers\UserController@export')->name('user_export');
    Route::post('users/import/', '\App\Http\Controllers\UserController@importUser')->name('user_import');
    Route::post('sendmail', '\App\Http\Controllers\UserController@sendTrackedEmail')->name('sendmail_to_user');

    Route::resource('users', UserController::class);
    Route::get('/representatives', [UserController::class, 'representativeIndex'])
    ->name('users.representative');

Route::get('/attendees', [UserController::class, 'attendeeIndex'])
    ->name('users.attendee');
    
      Route::resource('usergroup', UserGroupController::class);
    
    Route::patch('exhibitor-users/{id}/approve', [ExhibitorUserController::class, 'approve'])->name('exhibitor-users.approve');
    Route::get('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBoothForm'])->name('exhibitor-users.assign-booth-form');
    Route::post('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBooth'])->name('exhibitor-users.assign-booth');
     Route::get('/exhibitors/export', [ExhibitorUserController::class, 'exportExhibitors'])
    ->name('exhibitors.export');
      Route::get('/sponsors/export', [SponsorsController::class, 'exportSponsors'])
    ->name('sponsors.export');
     Route::get('/speaker/export', [SpeakerController::class, 'exportSpeakers'])
    ->name('speaker.export');
    Route::post('/attendee-users/{id}/allow-access', [AttendeeUserController::class, 'allowAccess'])
    ->name('attendee-users.allow-access');



    
    Route::resource('speaker', SpeakerController::class);
    Route::resource('booths', BoothController::class);


    Route::any('categories/{id}/order/{order}', '\App\Http\Controllers\CategoryController@order');
   
    Route::any('categories/tags/store', '\App\Http\Controllers\CategoryController@storeTags')->name('categories.store-tags');
    
    
    Route::any('home/settings/', '\App\Http\Controllers\SettingController@indexHome')->name('indexHome');

    Route::prefix('audit')->group(function () {
        Route::get('/', [App\Http\Controllers\AuditController::class, 'index'])->name('audit.index');
        Route::get('/{log}', [App\Http\Controllers\AuditController::class, 'show'])->name('audit.show');
        Route::get('/entity/{entityType}/{entityId}', [App\Http\Controllers\AuditController::class, 'entityLogs'])->name('audit.edit');
    });

    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::prefix('calendar')->group(function () {
        Route::get('/data', [CalendarController::class, 'getCalendarData']);
        Route::get('/sessions', [CalendarController::class, 'getSessions'])->name('calendar.sessions');
        Route::post('/sessions', [CalendarController::class, 'createSession'])->name('calendar.sessions.store');
        Route::get('/sessions/{session}', [CalendarController::class, 'getSessionDetails']);
        Route::put('/sessions/{session}', [CalendarController::class, 'updateSession'])->name('calendar.sessions.update');
        Route::delete('/sessions/{session}', [CalendarController::class, 'deleteSession'])->name('calendar.sessions.destroy');
        Route::post('/sessions/bulk-update', [CalendarController::class, 'bulkUpdateSessions']);

        Route::get('/speakers', [CalendarController::class, 'speakers'])->name('speakers.list');
        Route::get('/exhibitors', [CalendarController::class, 'exhibitors'])->name('exhibitors.list');
        Route::get('/sponsors', [CalendarController::class, 'sponsors'])->name('sponsors.list');
        
    });

     Route::get('/events/{event_id}/sessions/', [CalendarController::class, 'eventSessionList']);
});

    Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {
    Route::resource('exhibitor-users', ExhibitorUserController::class)->parameters([
        'exhibitor-users' => 'exhibitor_user',
    ]);
    Route::get('/sponsors/{user}/qr/download', [SponsorsController::class,'downloadQr'])->name('sponsors.qr.download');
     Route::resource('representative-users', RepresentativeUserController::class);
     Route::resource('attendee-users', AttendeeUserController::class);
     Route::get('/attendees/export', [AttendeeUserController::class, 'exportAttendees'])
    ->name('attendee-users.export');

     Route::resource('sponsors', SponsorsController::class);
     
     Route::resource('speaker', SpeakerController::class);
     Route::get('/speaker/{user}/qr/download', [SpeakerController::class,'downloadQr'])->name('speaker.qr.download');
    



    Route::patch('/users/{user}/toggle-block', [ExhibitorUserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::patch('/users/{user}/toggle-block', [RepresentativeUserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::patch('/users/{user}/toggle-block', [AttendeeUserController::class, 'toggleBlock'])->name('users.toggleBlock');
    Route::patch('/users/{user}/toggle-block', [SpeakerController::class, 'toggleBlock'])->name('users.toggleBlock');

    });


Route::group(['middleware' => ['webauth']], function () {

 require __DIR__.'/newsletters.php';
 require __DIR__.'/formbuilder.php';
  require __DIR__.'/lead.php';

});