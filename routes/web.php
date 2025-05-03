<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Inertia\Response;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Login'); // Domyślny widok to Login.vue
});

Route::get('/welcome', function () {
    return Inertia::render('Welcome'); // Widok Welcome.vue
});
