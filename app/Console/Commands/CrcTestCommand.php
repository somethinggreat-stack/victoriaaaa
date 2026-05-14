<?php

namespace App\Console\Commands;

use App\Services\CreditRepairCloud;
use Illuminate\Console\Command;

class CrcTestCommand extends Command
{
    protected $signature = 'crc:test
                            {--lead : Insert as a "Lead" instead of a "Client" so it is easy to clean up}
                            {--email= : Override the test email}
                            {--first= : Override the test first name}
                            {--last= : Override the test last name}
                            {--phone= : Override the test phone}';

    protected $description = 'Insert a sandbox record into Credit Repair Cloud to verify API connectivity.';

    public function handle(CreditRepairCloud $crc): int
    {
        if (!$crc->isConfigured()) {
            $this->error('CRC_API_KEY / CRC_SECRET_KEY are not set in .env');
            return self::FAILURE;
        }

        $timestamp = now()->format('YmdHis');
        $email = $this->option('email') ?: "crc-test+{$timestamp}@example.com";

        $payload = [
            'type'      => $this->option('lead') ? 'Lead' : 'Client',
            'firstname' => $this->option('first') ?: 'Test',
            'lastname'  => $this->option('last')  ?: 'User' . $timestamp,
            'email'     => $email,
            'phone'     => $this->option('phone') ?: '5555550100',
            'state'     => 'TX',
            'memo'      => 'Inserted by `php artisan crc:test` at ' . now()->toDateTimeString(),
        ];

        $this->line('Sending test record to Credit Repair Cloud...');
        $this->line('Email: ' . $email);
        $this->line('Type:  ' . $payload['type']);

        $result = $crc->insertClient($payload);

        $this->newLine();
        $this->line('HTTP status: ' . $result['status']);
        $this->line('Response body:');
        $this->line($result['body'] ?: '<empty body>');

        if ($result['ok']) {
            $this->info('✓ CRC accepted the record. Check your dashboard at https://app.creditrepaircloud.com');
            return self::SUCCESS;
        }

        $this->error('✗ CRC rejected the record.' . ($result['message'] ? ' Reason: ' . $result['message'] : ''));
        $this->line('See storage/logs/laravel.log for the full request/response trace.');
        return self::FAILURE;
    }
}
