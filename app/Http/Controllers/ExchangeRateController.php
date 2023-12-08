<?php

namespace App\Http\Controllers;

use App\Services\ExchangeRateService;
use Exception;
use Illuminate\Http\Request;
use App\Jobs\ClearRateData;
use Illuminate\Support\Facades\Storage;

class ExchangeRateController extends Controller
{
    /**
     * @var ExchangeRateService
     */
    protected $exchangeRateService;

    /**
     * ExchangeRateService Constructor
     *
     * @param ExchangeRateService $exchangeRateService
     *
     */
    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = [];

        try {
            $result['data'] = $this->exchangeRateService->getAll();
        } catch (Exception $e) {
            session()->flash('error', 'Error ' . $e->getMessage());
        }


        return view('home', $result);
    }

    /**
     * Process scraping data
     *
     * @return \Illuminate\Http\Response
     */
    public function scrape()
    {
        try {
            $this->exchangeRateService->scrapeAndSave();
            return redirect()->back()->with('success', 'Berhasil scrapping data!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error' . $e->getMessage());
        }
    }

    /**
     * Process clear scraping data
     *
     * @return \Illuminate\Http\Response
     */
    public function clear()
    {
        ClearRateData::dispatch();
        return redirect()->back()->with('success', 'Proses berhasil ditambahkan pada Job!');
    }


}
