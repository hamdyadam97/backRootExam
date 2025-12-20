<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhenixBillingService
{
    protected string $baseUrl = 'https://phenix.cloud:1983/api/rest/TPhenixApi/';

    public function sendInvoice(array $payload)
    {
        return Http::withBasicAuth('NHhI9ub', 'PHBNcrt')
            ->withHeaders([
                'phenixtoken' => '7d22626d-dc0c-11f0-98ee-3cecef7586b5',
                'ToClient'    => 'AA01F9FD0678B81B71158DAC707985BD',
                'Accept'      => 'application/json',
            ])
            ->put($this->baseUrl . 'CreateInvoice', $payload);
    }
}
