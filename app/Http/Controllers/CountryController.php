<?php

namespace App\Http\Controllers;

use App\Core\Countries\CountriesInterface;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Country services
     *
     * @var Object App\Core\Countries\CountriesIntterface
     */
    protected $country;

    public function __construct(CountriesInterface $country)
    {
        $this->country = $country;
    }

    public function show()
    {
        return $this->country->show();
    }
}
