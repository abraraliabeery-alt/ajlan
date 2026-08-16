<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment('Built for Ajlan & Bros Real Estate.');
})->purpose('Display the project message');
