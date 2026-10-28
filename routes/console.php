<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Comando para probar el envío de correos
Artisan::command('mail:test {email=germanbianchi2011@hotmail.com}', function (string $email) {
    $this->info("Enviando correo de prueba a {$email}...");
    Mail::raw("¡Hola! Este es un correo de prueba enviado desde tu aplicación Artfolio para verificar el correcto funcionamiento del sistema de correos en Laravel.", function ($message) use ($email) {
        $message->to($email)->subject('Prueba de Sistema de Correo - Artfolio');
    });
    $this->info("¡Correo enviado con éxito! Puedes revisarlo en tu bandeja de Mailpit: http://127.0.0.1:8025");
})->purpose('Enviar un correo de prueba para verificar la configuración de email');

// Purga automática de SoftDeletes a 30 días (RN-04)
Schedule::command('model:prune')->daily();
