<?php

use App\Providers\AppServiceProvider;
use Illuminate\View\ViewServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;

return [
    // Explicitly register providers required by the serverless runtime.
    ViewServiceProvider::class,
    ExcelServiceProvider::class,
    AppServiceProvider::class,
];
