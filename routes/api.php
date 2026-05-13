<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\AdminReservationController;
use App\Http\Controllers\Api\Admin\AdminRoomController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\AnalyticsController;
use App\Http\Controllers\Api\WhatsAppController;

// ─── WhatsApp & AI Agent (public) ─────────────────────────────
Route::post('whatsapp/webhook', [WhatsAppController::class, 'webhook']);
Route::get('agent/test',        [WhatsAppController::class, 'testAgent']);

// ─── Auth (public) ────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password',  [AuthController::class, 'resetPassword']);
    Route::get('{provider}/redirect',  [AuthController::class, 'redirectToProvider']);
    Route::get('{provider}/callback',  [AuthController::class, 'handleProviderCallback']);
    Route::get('confirm-deletion/{id}/{hash}', [AuthController::class, 'confirmDeleteAccount'])
        ->middleware(['signed'])
        ->name('account.confirm-deletion');
});

// ─── Email verification (public) ──────────────────────────────
Route::get('auth/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed'])
    ->name('verification.verify');

// ─── Reservation confirmation depuis email (public) ───────────
Route::get('reservations/{reservation}/confirm-arrival/{token}',
    [ReservationController::class, 'confirmArrival']);
Route::get('reservations/{reservation}/cancel-arrival/{token}',
    [ReservationController::class, 'cancelArrival']);

// Landing page for QR Code (Public)
Route::get('reservations/reference/{reference}', [ReservationController::class, 'showByReference']);
Route::get('reservations/{reservation}/receipt', [ReservationController::class, 'downloadReceipt']);

// ─── Rooms (public) ───────────────────────────────────────────
Route::get('rooms',        [RoomController::class, 'index']);
Route::get('rooms/search', [RoomController::class, 'search']);
Route::get('rooms/types',  [RoomController::class, 'types']);
Route::get('rooms/{room}', [RoomController::class, 'show']);

// ─── Services & Amenities (public) ────────────────────────────
Route::get('services',  [ServiceController::class, 'index']);
Route::get('amenities', [\App\Http\Controllers\Api\AmenityController::class, 'index']);
Route::get('reviews',   [ReviewController::class, 'index']);

// ─── Protected routes ─────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::put('auth/profile',   [AuthController::class, 'updateProfile']);
    Route::put('auth/password',  [AuthController::class, 'updatePassword']);
    Route::post('auth/avatar',   [AuthController::class, 'uploadAvatar']);
    Route::post('auth/logout',   [AuthController::class, 'logout']);
    Route::get('auth/me',        [AuthController::class, 'me']);
    Route::post('auth/email/resend', [AuthController::class, 'resendVerification'])
        ->middleware('throttle:6,1');
    Route::post('auth/request-deletion', [AuthController::class, 'requestDeleteAccount']);

    // Reservations (client)
    Route::get('reservations',                        [ReservationController::class, 'index']);
    Route::post('reservations',                       [ReservationController::class, 'store']);
    Route::get('reservations/{reservation}',          [ReservationController::class, 'show']);
    Route::post('reservations/{reservation}/cancel',  [ReservationController::class, 'cancel']);
    Route::post('reservations/check-promo',           [ReservationController::class, 'checkPromo']);
    Route::get('auth/pending-reviews',               [ReservationController::class, 'pendingReviews']);

    // Payments
    Route::post('payments/{reservation}/intent',  [PaymentController::class, 'createIntent']);
    Route::post('payments/{reservation}/confirm', [PaymentController::class, 'confirm']);

    // Reviews
    Route::post('reservations/{reservation}/review', [ReviewController::class, 'store']);
    Route::post('reviews',                          [ReviewController::class, 'storeGeneral']);
    Route::get('auth/reviews',                      [ReviewController::class, 'userReviews']);

    // ─── Admin routes ──────────────────────────────────────────
    Route::prefix('admin')->middleware('role:admin|manager|receptionist')->group(function () {

        // Dashboard
        Route::get('dashboard',               [DashboardController::class, 'overview']);
        Route::get('dashboard/revenue-chart', [DashboardController::class, 'revenueChart']);
        Route::get('dashboard/today',         [DashboardController::class, 'reservationsToday']);

        // Reservations
        Route::get('reservations',                             [AdminReservationController::class, 'index']);
        Route::post('reservations/{reservation}/check-in',    [AdminReservationController::class, 'checkIn']);
        Route::post('reservations/{reservation}/check-out',   [AdminReservationController::class, 'checkOut']);
        Route::post('reservations/{reservation}/confirm',     [AdminReservationController::class, 'confirm']);
        Route::post('reservations/{reservation}/no-show',     [AdminReservationController::class, 'noShow']);

        // Rooms (admin + manager peuvent créer/modifier, réceptionniste peut voir)
        Route::get('rooms',             [AdminRoomController::class, 'index']);
        Route::get('rooms/{room}',      [AdminRoomController::class, 'show']);
        Route::post('rooms/{room}/status', [AdminRoomController::class, 'updateStatus']);

        // Rooms create/edit (admin + manager seulement)
        Route::middleware('role:admin|manager')->group(function () {
            Route::post('rooms',          [AdminRoomController::class, 'store']);
            Route::put('rooms/{room}',    [AdminRoomController::class, 'update']);
            Route::patch('rooms/{room}',  [AdminRoomController::class, 'update']);
        });

        // Reports (admin + manager)
        Route::middleware('role:admin|manager')->group(function () {
            Route::get('reports/occupancy',        [ReportController::class, 'occupancy']);
            Route::get('reports/revenue',          [ReportController::class, 'revenue']);
            Route::get('reports/advanced-stats',   [AnalyticsController::class, 'getAdvancedStats']);
            Route::get('reports/export/{type}',    [ReportController::class, 'export']);
            Route::get('reviews',                  [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'index']);
            Route::post('reviews/{review}/approve',[\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'approve']);
            Route::delete('reviews/{review}',      [\App\Http\Controllers\Api\Admin\AdminReviewController::class, 'destroy']);
        });

        // Users — admin peut tout, manager peut voir seulement
        Route::get('users',     [AdminUserController::class, 'index']);
        Route::get('users/{user}', [AdminUserController::class, 'show']);
        Route::get('permissions', [AdminUserController::class, 'permissions']);
        Route::get('roles/{role}/permissions', [AdminUserController::class, 'rolePermissions']);

        // Admin seulement
        Route::middleware('role:admin')->group(function () {
            Route::post('users',             [AdminUserController::class, 'store']);
            Route::put('users/{user}',       [AdminUserController::class, 'update']);
            Route::delete('users/{user}',    [AdminUserController::class, 'destroy']);
            Route::patch('users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus']);
        });

        // Services admin (admin + manager)
        Route::middleware('role:admin|manager')->group(function () {
            Route::get('services',                    [ServiceController::class, 'adminIndex']);
            Route::post('services',                   [ServiceController::class, 'store']);
            Route::put('services/{service}',          [ServiceController::class, 'update']);
            Route::delete('services/{service}',       [ServiceController::class, 'destroy']);
            Route::patch('services/{service}/toggle', [ServiceController::class, 'toggle']);
            
            // Promotions
            Route::get('promotions',                    [\App\Http\Controllers\Api\Admin\AdminPromotionController::class, 'index']);
            Route::post('promotions',                   [\App\Http\Controllers\Api\Admin\AdminPromotionController::class, 'store']);
            Route::put('promotions/{promotion}',          [\App\Http\Controllers\Api\Admin\AdminPromotionController::class, 'update']);
            Route::delete('promotions/{promotion}',       [\App\Http\Controllers\Api\Admin\AdminPromotionController::class, 'destroy']);
            // Announcements
            Route::post('announcements/send', [\App\Http\Controllers\Api\Admin\AnnouncementController::class, 'send']);
        });
        // Notifications
        Route::get('notifications',                [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('notifications/mark-as-read',   [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    });
});