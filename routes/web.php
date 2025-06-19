<?php

use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\KategoriUjianController;
use App\Http\Controllers\MatkulController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserManagerController;
use App\Http\Controllers\UserManagerEditController;
use App\Http\Controllers\PesertaManagerController;
use App\Http\Controllers\PesertaManagerEditController;
use App\Http\Controllers\PesertaImportController;
use App\Http\Controllers\JenisUjianEditController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\MonitoringUjianController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JenisUjianController;
use App\Http\Controllers\BankSoalControllerCheckbox;
use App\Http\Controllers\PaketSoalController;
use App\Http\Controllers\PaketSoal\PaketSoalEditController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Matakuliah;
use App\Models\PaketSoal;
use App\Http\Controllers\DosenManagerController;
use App\Http\Controllers\DosenManagerEditController;
use App\Http\Controllers\DosenImportController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\MasterData\BidangController;
use App\Http\Controllers\PaketSoal\MakeEventController;
use App\Http\Controllers\PaketSoal\AddSoalController;

Route::prefix('event')->name('event.')->group(function () {
    Route::get('/create', [MakeEventController::class, 'create'])->name('create');
    Route::post('/store', [MakeEventController::class, 'store'])->name('store');
    Route::get('/list', [MakeEventController::class, 'index'])->name('list');
});

Route::get('/paket-soal/list', [\App\Http\Controllers\PaketSoal\PaketSoalController::class, 'list']);

Route::get('/bidangs', [BidangController::class, 'index']); // dropdown bidang
Route::get('/paket-soal/create', function () {
    return Inertia::render('master-data/paket-soal/CreatePaketSoal');
})->name('paket-soal.create');

Route::post('/paket-soal', [PaketSoalEditController::class, 'store'])->name('paket-soal.store');

// Custom binding
Route::bind('matakuliah', fn($value) => Matakuliah::where('id_mk', $value)->firstOrFail());

// Custom binding agar {paket_soal} resolve ke JadwalUjianSoal berdasarkan id_ujian
Route::bind('paket_soal', function ($value) {
    return \App\Models\JadwalUjianSoal::where('id_ujian', $value)->firstOrFail();
});

Route::get('/', function () {
    return Inertia::render('auth/login');
})->name('home');
Route::get('/paket-soal/add-soal', [AddSoalController::class, 'showAddSoalForm'])->name('paket-soal.add-soal');
// Login
Route::get('/', fn () => Inertia::render('auth/login'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', fn () => Inertia::render('dashboard'))->name('dashboard');

    // Monitoring
    Route::get('monitoring-ujian', [MonitoringUjianController::class, 'index'])->name('monitoring.ujian');
    Route::get('monitoring-ujian/{id}', [MonitoringUjianController::class, 'show'])->name('monitoring.ujian.detail');

    // Penjadwalan
    Route::prefix('penjadwalan')->name('penjadwalan.')->group(function () {
        Route::get('/', [PenjadwalanController::class, 'index'])->name('index');
        Route::get('/create', [PenjadwalanController::class, 'create'])->name('create');
        Route::post('/', [PenjadwalanController::class, 'store'])->name('store');
        Route::get('/{penjadwalan}/edit', [PenjadwalanController::class, 'edit'])->name('edit');
        Route::put('/{penjadwalan}', [PenjadwalanController::class, 'update'])->name('update');
        Route::delete('/{penjadwalan}', [PenjadwalanController::class, 'destroy'])->name('destroy');
    });

    // Rekap Nilai
    Route::get('rekap-nilai', [App\Http\Controllers\RekapNilaiController::class, 'index'])->name('rekap.nilai');
    Route::get('rekap-nilai/{id}', [App\Http\Controllers\RekapNilaiController::class, 'show'])->name('rekap.nilai.detail');
    Route::get('rekap-nilai/{id}/export', [App\Http\Controllers\RekapNilaiController::class, 'export'])->name('rekap.nilai.export');

    // MASTER DATA
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', fn () => redirect()->route('dashboard'))->name('index');

        // Route::get('peserta', function () {
        //     return Inertia::render('peserta');
        // })->name('peserta');

        Route::get('dosen', function () {
            return Inertia::render('dosen');
        })->name('dosen');

        Route::get('kategori-ujian', function () {
            return Inertia::render('kategori-ujian');
        })->name('kategori.ujian');

        Route::get('soal', function () {
            return Inertia::render('soal');
        })->name('soal');

        Route::get('matakuliah', [MatkulController::class, 'index'])->name('matakuliah');
        Route::get('jenisujian', [JenisUjianController::class, 'index']); // ini tidak pakai name


        Route::get('paket-soal', function () {
            return Inertia::render('paket-soal');
        })->name('paket.soal');

        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/', [DosenManagerController::class, 'index'])->name('manager');
            Route::get('{id}/edit', [DosenManagerEditController::class, 'edit'])->name('edit');
            Route::put('{id}', [DosenManagerEditController::class, 'update'])->name('update');
            Route::delete('{user}', [DosenManagerController::class, 'delete'])->name('destroy');
            Route::get('create', [DosenManagerEditController::class, 'create'])->name('create');
            Route::post('/', [DosenManagerEditController::class, 'store'])->name('store');
            Route::post('import', [DosenImportController::class, 'import'])->name('import');
        });

        Route::get('import-dosen', [DosenImportController::class, 'importViewDosen'])->name('import-dosen.view');

        Route::prefix('peserta')->name('peserta.')->group(function () {
            Route::get('/', [PesertaManagerController::class, 'index'])->name('manager');
            Route::get('{id}/edit', [PesertaManagerEditController::class, 'edit'])->name('edit');
            Route::put('{id}', [PesertaManagerEditController::class, 'update'])->name('update');
            Route::delete('{peserta}', [PesertaManagerController::class, 'delete'])->name('destroy');
            Route::get('create', [PesertaManagerEditController::class, 'create'])->name('create');
            Route::post('/', [PesertaManagerEditController::class, 'store'])->name('store');
            Route::post('import', [PesertaImportController::class, 'import'])->name('import');
        });

        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [PesertaImportController::class, 'importView'])->name('view');
        });

        // Route show bank soal
        Route::get('bank-soal', [BankSoalController::class, 'index'])->name('bank.soal');

        // Route hapus bank soal
        Route::delete('bank-soal/{id}', [BankSoalController::class, 'destroy'])->name('bank.soal.destroy');

        // Route edit bank soal
        Route::put('bank-soal/update/{id}', [BankSoalController::class, 'update'])->name('bank.soal.update');
        Route::get('bank-soal/{id}/edit', [BankSoalController::class, 'edit'])->name('bank.soal.edit');

        // Route tambah bank soal
        Route::get('bank-soal/create', function () {
            return Inertia::render('banksoalcreate');
        })->name('bank.soal.create');
        // Route edit bank soal
        //Route::put('bank-soal/{id}', [BankSoalController::class, 'update'])->name('bank.soal.update');
        Route::get('bank-soal/{id}/edit', [BankSoalController::class, 'edit'])->name('bank.soal.edit');

        Route::post('bank-soal', [BankSoalController::class, 'store'])->name('bank.soal.store');

        // Route untuk matakuliah dipindahkan ke dalam grup master-data
        Route::prefix('matakuliah')->name('matakuliah.')->group(function () {
            Route::get('/', [MatkulController::class, 'index'])->name('index');
            Route::get('/create', [MatkulController::class, 'create'])->name('create');
            Route::post('/', [MatkulController::class, 'store'])->name('store');
            Route::get('/{matakuliah}/edit', [MatkulController::class, 'edit'])->name('edit');
            Route::put('/{matakuliah}', [MatkulController::class, 'update'])->name('update');
            Route::delete('/{matakuliah}', [MatkulController::class, 'destroy'])->name('destroy');
        });

        // Route untuk paket soal
        Route::prefix('paket-soal')->name('paket-soal.')->group(function () {
            Route::get('/', fn () => Inertia::render('master-data/paket-soal/paket-soal-manager'))->name('manager');
            Route::get('/create', [PaketSoalEditController::class, 'create'])->name('create');
            Route::get('/create-paket-soal', fn () => Inertia::render('master-data/paket-soal/create-paket-soal'))->name('create-paket-soal');
            Route::get('/create-event', fn () => Inertia::render('master-data/paket-soal/create-event'))->name('create-event');
            Route::post('/', [PaketSoalEditController::class, 'store'])->name('store');
            Route::get('/{paket_soal}/edit', [PaketSoalEditController::class, 'edit'])->name('edit');
            Route::put('/{paket_soal}', [PaketSoalEditController::class, 'update'])->name('update');
            Route::delete('/{paket_soal}', [PaketSoalController::class, 'delete'])->name('destroy');
            Route::post('/store', [PaketSoalEditController::class, 'store_data'])->name('store_data');
        });

        Route::prefix('event')->name('event.')->group(function () {
            Route::get('/', fn () => Inertia::render('master-data/event/EventManager'))->name('manager');
        });

        Route::get('/kategorisoal', [BankSoalController::class, 'getKategoriSoal']);
    });

    Route::middleware(['role:super_admin'])->prefix('user-management')->name('user-management.')->group(function () {
        Route::get('/', fn () => redirect()->route('dashboard'))->name('index');

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/', [UserManagerController::class, 'index'])->name('manager');
            Route::get('{id}/edit', [UserManagerEditController::class, 'edit'])->name('edit');
            Route::put('{id}', [UserManagerEditController::class, 'update'])->name('update');
            Route::delete('{user}', [UserManagerController::class, 'delete'])->name('destroy');
            Route::get('create', [UserManagerEditController::class, 'create'])->name('create');
            Route::post('/', [UserManagerEditController::class, 'store'])->name('store');
        });

        Route::get('roles', fn () => Inertia::render('user-management/role-manager'))->name('roles');
    });

    Route::get('/token/current', [TokenController::class, 'getCurrentToken'])->name('token.current');
    Route::get('/token/generate', [TokenController::class, 'generateNewToken'])->name('token.generate');
    Route::get('/token/copy', [TokenController::class, 'copyToken'])->name('token.copy');

    Route::get('/events/list', [\App\Http\Controllers\PaketSoal\MakeEventController::class, 'list']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
