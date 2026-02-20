<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SpeakerController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SponsorsController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\EventGuideController;
use App\Http\Controllers\EventTrackController;
use App\Http\Controllers\TicketTypeController;
use App\Http\Controllers\AttendeeUserController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ExhibitorUserController;
use App\Http\Controllers\RepresentativeUserController;
use App\Http\Controllers\ExhibitorAdmin\BoothController;
use App\Http\Controllers\UserConnectionController;
use App\Http\Controllers\LandingPageSettingController;
use App\Http\Controllers\LandingPageMainController;
use App\Http\Controllers\LandingPageLogoController;
use App\Http\Controllers\LandingPageAboutController;
use App\Http\Controllers\LandingEventBannerController;
use App\Http\Controllers\LandingEventTypeController;
use App\Http\Controllers\LandingApartTextController;
use App\Http\Controllers\LandingApartCardController;


Route::group(['middleware' => ['webauth', 'role:Admin|Exhibitor|Representative|Attendee|Speaker|Support Staff Or Helpdesk|Registration Desk']], function () {

  Route::get('/home-page/apart/text', [LandingApartTextController::class, 'index'])->name('admin.home-page.apart.text');
  Route::post('/home-page/apart/text', [LandingApartTextController::class, 'update'])->name('admin.home-page.apart.text.update');

  Route::get('/home-page/apart/cards', [LandingApartCardController::class, 'index'])->name('admin.home-page.apart.cards.index');
  Route::get('/home-page/apart/cards/create', [LandingApartCardController::class, 'create'])->name('admin.home-page.apart.cards.create');
  Route::post('/home-page/apart/cards', [LandingApartCardController::class, 'store'])->name('admin.home-page.apart.cards.store');
  Route::get('/home-page/apart/cards/{id}/edit', [LandingApartCardController::class, 'edit'])->name('admin.home-page.apart.cards.edit');
  Route::post('/home-page/apart/cards/{id}', [LandingApartCardController::class, 'update'])->name('admin.home-page.apart.cards.update');
  Route::delete('/home-page/apart/cards/{id}', [LandingApartCardController::class, 'destroy'])->name('admin.home-page.apart.cards.destroy');

  Route::get('/home-page/events/banner', [LandingEventBannerController::class, 'index'])->name('admin.home-page.events.banner');
  Route::post('/home-page/events/banner', [LandingEventBannerController::class, 'update'])->name('admin.home-page.events.banner.update');

  Route::get('/home-page/events/types', [LandingEventTypeController::class, 'index'])->name('admin.home-page.events.types.index');
  Route::get('/home-page/events/types/create', [LandingEventTypeController::class, 'create'])->name('admin.home-page.events.types.create');
  Route::post('/home-page/events/types', [LandingEventTypeController::class, 'store'])->name('admin.home-page.events.types.store');
  Route::get('/home-page/events/types/{id}/edit', [LandingEventTypeController::class, 'edit'])->name('admin.home-page.events.types.edit');
  Route::post('/home-page/events/types/{id}', [LandingEventTypeController::class, 'update'])->name('admin.home-page.events.types.update');
  Route::delete('/home-page/events/types/{id}', [LandingEventTypeController::class, 'destroy'])->name('admin.home-page.events.types.destroy');

  Route::get('/home-page/about', [LandingPageAboutController::class, 'index'])->name('admin.home-page.about');
  Route::post('/home-page/about', [LandingPageAboutController::class, 'update'])->name('admin.home-page.about.update');

  Route::get('/home-page/logos', [LandingPageLogoController::class, 'index'])->name('admin.home-page.logos.index');
  Route::get('/home-page/logos/create', [LandingPageLogoController::class, 'create'])->name('admin.home-page.logos.create');
  Route::post('/home-page/logos', [LandingPageLogoController::class, 'store'])->name('admin.home-page.logos.store');
  Route::get('/home-page/logos/{id}/edit', [LandingPageLogoController::class, 'edit'])->name('admin.home-page.logos.edit');
  Route::post('/home-page/logos/{id}', [LandingPageLogoController::class, 'update'])->name('admin.home-page.logos.update');
  Route::delete('/home-page/logos/{id}', [LandingPageLogoController::class, 'destroy'])->name('admin.home-page.logos.destroy');

  Route::get('/home-page/main', [LandingPageMainController::class, 'index'])->name('admin.home-page.main');
  Route::post('/home-page/main', [LandingPageMainController::class, 'update'])->name('admin.home-page.main.update');

  Route::resource('user-connections', UserConnectionController::class);
  Route::get('/user-connections/{user}/export', [UserConnectionController::class, 'export'])
    ->name('user-connections.export');

  Route::any('/webview', [App\Http\Controllers\PageController::class, 'webview'])->name('webview');
  Route::resource('pages',   App\Http\Controllers\PageController::class);
  Route::resource('categories', App\Http\Controllers\CategoryController::class);
  Route::resource('otps', App\Http\Controllers\OtpController::class);
  Route::resource('settings', App\Http\Controllers\SettingController::class);
  Route::resource('admin-users', App\Http\Controllers\AdminUsersController::class);

  Route::post('/delete/photo', [App\Http\Controllers\EventController::class, 'removePhoto'])->name('events.removePhoto');
  Route::resource('events', App\Http\Controllers\EventController::class);
  Route::post('/events/clone/{id}', [App\Http\Controllers\EventController::class, 'clone'])->name('events.clone');

  Route::get('/event-tracks', [EventTrackController::class, 'index'])->name('event-tracks.index');
  // Show form to create a new event track (modal can trigger this)
  Route::get('/event-tracks/create', [EventTrackController::class, 'create'])->name('event-tracks.create');
  // Store new event track
  Route::post('/event-tracks', [EventTrackController::class, 'store'])->name('event-tracks.store');

  Route::get('/event-guides/gallery', [EventGuideController::class, 'showGallery'])
    ->name('event-guides.showGallery');
  Route::post('/event-guides/gallery/upload', [EventGuideController::class, 'uploadGallery'])->name('event-guides.uploadGallery');

  Route::delete('event-guides/delete-gallery-image', [EventGuideController::class, 'deleteGalleryImage'])->name('event-guides.deleteGalleryImage');




  Route::resource('event-guides', EventGuideController::class);


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

  Route::post('/attendee-users/send-both', [AttendeeUserController::class, 'sendBoth'])
    ->name('attendee-users.send-both');
  Route::post('/attendee-users/generate-badge', [AttendeeUserController::class, 'generateBadge'])
    ->name('attendee-users.generateBadge');

  Route::resource('usergroup', UserGroupController::class);

  Route::patch('exhibitor-users/{id}/approve', [ExhibitorUserController::class, 'approve'])->name('exhibitor-users.approve');
  Route::get('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBoothForm'])->name('exhibitor-users.assign-booth-form');
  Route::post('exhibitor-users/{id}/assign-booth', [ExhibitorUserController::class, 'assignBooth'])->name('exhibitor-users.assign-booth');
  Route::get('/exhibitors/export', [ExhibitorUserController::class, 'exportExhibitors'])
    ->name('exhibitors.export');
  Route::post('/exhibitor/{companyId}/upload-docs', [ExhibitorUserController::class, 'uploadDocs'])->name('exhibitor.uploadDocs');
  Route::delete('/exhibitors/docs/{id}', [ExhibitorUserController::class, 'deleteDoc'])
    ->name('exhibitor.deleteDoc');


  Route::get('/sponsors/export', [SponsorsController::class, 'exportSponsors'])
    ->name('sponsors.export');
  Route::get('/speaker/export', [SpeakerController::class, 'exportSpeakers'])
    ->name('speaker.export');
  Route::post('/attendee-users/{id}/allow-access', [AttendeeUserController::class, 'allowAccess'])
    ->name('attendee-users.allow-access');
  Route::post('/speakers/{id}/allow-access', [SpeakerController::class, 'allowAccess'])
    ->name('speakers.allow-access');
  Route::post('/speakers/{id}/send-mail', [SpeakerController::class, 'sendMail'])
    ->name('speakers.sendMail');
  Route::delete('/speaker/private-docs/{id}', [SpeakerController::class, 'deletePrivateDoc'])
    ->name('speaker.private-docs.delete');

  Route::post('/attendee-users/{id}/send-mail', [AttendeeUserController::class, 'sendMail'])
    ->name('attendee-users.sendMail');
  Route::post('/attendee-users/bulk-action', [AttendeeUserController::class, 'bulkAction'])
    ->name('attendee-users.bulkAction');


  Route::get('/events/{event}/sessions', [TicketTypeController::class, 'getByEvent'])->name('events.sessions');


  Route::resource('speaker', SpeakerController::class);
  Route::resource('booths', BoothController::class);
  Route::resource('supports', SupportController::class);
  Route::patch('/support/{id}/status', [SupportController::class, 'updateStatus'])
    ->name('support.updateStatus');



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
  Route::get('/sponsors/{user}/qr/download', [SponsorsController::class, 'downloadQr'])->name('sponsors.qr.download');
  Route::resource('representative-users', RepresentativeUserController::class);
  Route::resource('attendee-users', AttendeeUserController::class);
  Route::get('/attendees/export', [AttendeeUserController::class, 'exportAttendees'])
    ->name('attendee-users.export');

  Route::get('/attendees/generate-qr-code-manually', [AttendeeUserController::class, 'generateQrCodeManually'])
    ->name('attendee-users.generateQrCodeManually');

  Route::resource('sponsors', SponsorsController::class);

  Route::resource('speaker', SpeakerController::class);
  Route::get('/speaker/{user}/qr/download', [SpeakerController::class, 'downloadQr'])->name('speaker.qr.download');




  Route::patch('/users/{user}/toggle-block', [ExhibitorUserController::class, 'toggleBlock'])->name('users.toggleBlock');
  Route::patch('/users/{user}/toggle-block', [RepresentativeUserController::class, 'toggleBlock'])->name('users.toggleBlock');
  Route::patch('/users/{user}/toggle-block', [AttendeeUserController::class, 'toggleBlock'])->name('users.toggleBlock');
  Route::patch('/users/{user}/toggle-block', [SpeakerController::class, 'toggleBlock'])->name('users.toggleBlock');
  Route::resource('email-templates', EmailTemplateController::class);

  Route::post('/send-email-template', [EmailTemplateController::class, 'send'])->name('send.email.template');

  Route::get('/landing-page-settings', [LandingPageSettingController::class, 'index'])->name('landing-page-settings');
  Route::post('/landing-page-settings', [LandingPageSettingController::class, 'update'])->name('landing-page-settings');
});
