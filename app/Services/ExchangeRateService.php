<?php

namespace App\Services;

use App\Repositories\ExchangeRateRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Goutte\Client;
use Illuminate\Support\Facades\Storage;

class ExchangeRateService
{
    /**
     * @var $exchangeRateRepository
     */
    protected $exchangeRateRepository;

    /**
     * ExchangeRateService constructor.
     *
     */
    public function __construct(ExchangeRateRepository $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }


    /**
     * Get all exchange rate.
     *
     * @return String
     */
    public function getAll()
    {
        $path = config('constants.rates.path');
        $files = Storage::disk('public')->allFiles($path);
        return $files;
    }


    /**
     * Scrape data.
     * Store to storage if there are no errors.
     *
     * @return String
     */
    public function scrapeAndSave()
    {
        $client = new Client();

        $website = $client->request('GET', 'https://kursdollar.org/');

        $table = $website->filter('table')->first();

        $exchangeRates = [];
        $result = [
            'meta' => [
                'date' => date('d-m-Y'),
                'day' => date('l')
            ],
            'rates' => []
        ];

        // Loop through each row in the table
        $table->filter('tr')->each(function ($row) use (&$exchangeRates, &$result) {
            // Initialize an array to store data for each row
            $rowData = [];

            // Loop through each cell in the row
            $row->filter('td')->each(function ($cell) use (&$rowData) {
                // Add the cell's text to the row data array
                $rowData[] = $cell->text();
            });

            // Add the row data to the exchange rates array
            $exchangeRates[] = $rowData;

            // Get meta data
            if ($rowData[0] == "Mata Uang") {
                $result['meta']['indonesia'] = $rowData[1];
                $result['meta']['word'] = $rowData[2];
            }

            // Get the real rates
            if (count($rowData) > 5) {
                $result['rates'][] = [
                    'currency' => $this->cleanText($rowData[0]),
                    'buy' => $this->cleanText($rowData[1]),
                    'sell' => $this->cleanText($rowData[2]),
                    'average' => $this->cleanText($rowData[3]),
                    'word_rate' => $this->cleanText($rowData[1]),
                ];
            }
        });

        $path = config('constants.rates.path');
        $filename = "rate-" . date('d-m-Y--H-i-s') . ".json";
        Storage::disk('public')->put($path . $filename, json_encode($result));
    }

    private function cleanText($text) {
        // Remove special chars
        $clean = preg_replace('/[^A-Za-z0-9\-.,()]/', '', $text);
        // Remove depisit info on amount data
        $clean = explode("(", $clean)[0];
        // Remove dot value
        $clean = str_replace('.', '', $clean);
        // Replace comma decimal separator to dot
        $clean = str_replace(',', '.', $clean);

        return $clean;
    }

}
