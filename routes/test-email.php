<?php

use Illuminate\Support\Facades\Route;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    try {
        // Force the mailer to use the SMTP configuration
        config(['mail.default' => 'smtp']);
        config(['mail.mailers.smtp.host' => '127.0.0.1']);
        config(['mail.mailers.smtp.port' => 1025]);
        
        // Send the email
        Mail::to('test@example.com')->send(new TestMail());
        
        return 'Email sent successfully! Check Mailhog at <a href="http://127.0.0.1:8025" target="_blank">http://127.0.0.1:8025</a>';
    } catch (\Exception $e) {
        return 'Failed to send email: ' . $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre>';
    }
});
