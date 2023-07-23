<?php

namespace App\Http\Controllers;

use App\Core\Countries\CountriesInterface;
use App\Core\Users\ManagerInterface;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    
    /**
     * Manager serive
     *
     * @var App\Core\Users\ManagerRepository
     */
    protected $manager;

    /**
     * Country Service
     *
     * @var Object App\Core\Countries\CountriesInterface
     */
    protected $country;

    public function __construct(
        ManagerInterface $manager,
        CountriesInterface $country
    )
    {
        $this->middleware('auth:manager,root');

        $this->manager = $manager;

        $this->country = $country;
    }

    /**
     * Manager dashboard
     *
     * @return void
     */
    public function manager()
    {
            return view('smsview.manager.index');
    }

    /**
     * User registration form
     *
     * @return void
     */
    public function smsappUserRegister()
    {
        $countries = $this->country->show();

        return view('smsview.common.client-registration',compact('countries'));
    }


    /**
     * Reseller user list view
     *
     * @return void
     */
    public function managerResellerList() {

        return view('smsview.manager.manage-resellers');

    }

    /**
     * Reseller user list data
     *
     * @return void
     */
    public function resellerData() {

        return $this->manager->showResellers();

    }

    /**
     * Reseller user list view
     *
     * @return void
     */
    public function managerClientList() {

        return view('smsview.manager.manager-clients');

    }

    /**
     * Client user list data
     *
     * @return void
     */
    public function clientData() {

        return $this->manager->showClients();

    }

    /**
     * Total support manager
     *
     * @return void
     */
    public function totalSupportManagers()
    {
        return $this->manager->totalSupportManagers();
    }
}
