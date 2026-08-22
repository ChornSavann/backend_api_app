 <?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Categorycontroller;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;



// Public route for login
Route::post('/login-user', [AuthController::class, 'login']);
Route::get('/users', [AuthController::class, 'index']);

Route::post('user/register', [AuthController::class, 'register']);

// Example of a protected route requiring the generated token
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    
});



Route::get('/categories', [Categorycontroller::class, 'index']);
Route::get('/categories/{id}', [Categorycontroller::class, 'show']);
Route::post('/categories', [Categorycontroller::class, 'store']);
Route::put('/categories/{id}', [Categorycontroller::class, 'update']);
Route::delete('/categories/{id}', [Categorycontroller::class, 'destroy']);

Route::get('/products/count', [ProductController::class, 'getProductCount']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::post('/products', [ProductController::class, 'store']);
Route::post('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/products/category/{category_id}', [ProductController::class, 'getProductsByCategoryId']);
// Route::get('/products/id}', [ProductController::class, 'getProductsByid']);
// Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{id}', [BrandController::class, 'show']);
Route::post('/brands', [BrandController::class, 'store']);
Route::get('/brands/{id}/edit', [BrandController::class, 'edit']);
Route::post('/brands/{id}', [BrandController::class, 'update']);
Route::delete('/brands/{id}', [BrandController::class, 'destroy']);

Route::get('/units', [\App\Http\Controllers\UnitsController::class, 'index']);
Route::post('/units', [\App\Http\Controllers\UnitsController::class, 'store']);
Route::get('/units/{id}', [\App\Http\Controllers\UnitsController::class, 'show']);
Route::get('/units/{id}/edit', [\App\Http\Controllers\UnitsController::class, 'edit']);
Route::post('/units/{id}', [\App\Http\Controllers\UnitsController::class, 'update']);
Route::delete('/units/{id}', [\App\Http\Controllers\UnitsController::class, 'destroy']);

Route::get('/customers', [\App\Http\Controllers\CustomerController::class, 'index']);
Route::post('/customers', [\App\Http\Controllers\CustomerController::class, 'store']);
Route::get('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'show']);
Route::get('/customers/{id}/edit', [\App\Http\Controllers\CustomerController::class, 'edit']);
Route::post('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'update']);
Route::delete('/customers/{id}', [\App\Http\Controllers\CustomerController::class, 'destroy']);

Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index']);
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store']);
Route::get('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'show']);
Route::get('/orders/{id}/edit', [\App\Http\Controllers\OrderController::class, 'edit']);
Route::post('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'update']);
Route::delete('/orders/{id}', [\App\Http\Controllers\OrderController::class, 'destroy']);
Route::get('/orders/{id}/invoice', [OrderController::class, 'showInvoice']);

Route::get('/purchases', [\App\Http\Controllers\PurchaseController::class, 'index']);
Route::post('/purchases', [\App\Http\Controllers\PurchaseController::class, 'store']);
Route::get('/purchases/{id}', [\App\Http\Controllers\PurchaseController::class, 'show']);
Route::get('/purchases/{id}/edit', [\App\Http\Controllers\PurchaseController::class, 'edit']);
Route::post('/purchases/{id}', [\App\Http\Controllers\PurchaseController::class, 'update']);
Route::delete('/purchases/{id}', [\App\Http\Controllers\PurchaseController::class, 'destroy']);

Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index']);
Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store']);
Route::get('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'show']);
Route::get('/suppliers/{id}/edit', [\App\Http\Controllers\SupplierController::class, 'edit']);
Route::post('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'update']);
Route::delete('/suppliers/{id}', [\App\Http\Controllers\SupplierController::class, 'destroy']);

// 🚀 User Management & Auth Routes
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/register', [UserController::class, 'store']);
Route::post('/users/{id}', [UserController::class, 'update']);

// ត្រូវមាន middleware('auth:sanctum') គ្របដណ្តប់
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/user/update', [UserController::class, 'update']);
// });
Route::middleware('auth:sanctum')->put('/profile/change-password', [UserController::class, 'changePassword']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/update-password', [UserController::class, 'updatePassword']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/auth/google', [UserController::class, 'handleGoogleApiLogin']);

// // 📧 Email Verification Routes
// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     return response()->json(['message' => 'Email verified successfully!']);
// })->middleware(['signed'])->name('verification.verify');

// // 🔗 Route សម្រាប់ផ្ញើ Link បញ្ជាក់សារជាថ្មី (Resend)
// Route::post('/email/verification-notification', function (Request $request) {
//     $request->user()->sendEmailVerificationNotification();

//     return response()->json(['message' => 'Verification link sent!']);
// })->middleware(['auth:sanctum'])->name('verification.send');

Route::get('/test-telegram', function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');

   $response = Http::withoutVerifying() 
                         ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                             'chat_id'    => $chatId,
                             'text'       => "សាកល្បងការផ្ញើសារទៅ Telegram! \nTime: " . now()->format('Y-m-d H:i:s'),
                         ]);

    return $response->json();
});


//report
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'getReportData']);
    Route::get('/summary', [ReportController::class, 'getReportSummary']);
    Route::get('/daily', [ReportController::class, 'DailyReport']);
    route::get('/weekly', [ReportController::class, 'WeeklyReport']);
    Route::get('/monthly', [ReportController::class, 'MonthlyReport']);
    Route::get('/yearly', [ReportController::class, 'YearlyReport']);
    Route::get('/sales', [ReportController::class, 'saleReport']);
    Route::get('/history', [ReportController::class, 'historyReport']);
    Route::get('/sales', [ReportController::class, 'saleReport']);
    Route::get('/history', [ReportController::class, 'historyReport']);
    Route::get('customers/history', [ReportController::class, 'getAllcustomers']);
    Route::get('/low-stock', [ReportController::class, 'lowStockReport']);
    Route::get('/top-selling', [ReportController::class, 'topSellingProducts']);
    Route::get('/purchases', [ReportController::class, 'purchaseHistoryReport']);
    Route::get('/customers', [ReportController::class, 'getAllcustomers']);
    Route::get('/customers/{customerId}/history', [ReportController::class, 'customerHistoryReport']);
    Route::get('/purchases/{purchaseId}', [ReportController::class, 'purchaseHistoryReportbyid']);
    Route::get('/{id}', [ReportController::class, 'getReportById']);
});

