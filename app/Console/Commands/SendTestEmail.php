<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-test-email {email? : Email address to send the test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info("Sending test email to {$email}...");
        $this->info("Mail configuration: " . config('mail.default') . " / " . config('mail.mailers.smtp.host') . ":" . config('mail.mailers.smtp.port'));
        
        try {
            // Force the mailer to use the SMTP configuration
            config(['mail.default' => 'smtp']);
            config(['mail.mailers.smtp.host' => '127.0.0.1']);
            config(['mail.mailers.smtp.port' => 1025]);
            
            // Send with debug mode
            $this->info('Attempting to send email with debug info...');
            Mail::to($email)->send(new TestMail());
            $this->info('Test email sent successfully! Check Mailhog at http://127.0.0.1:8025');
        } catch (\Exception $e) {
            $this->error("Failed to send email: {$e->getMessage()}");
            $this->error("Exception trace: " . $e->getTraceAsString());
        }
    }
}
