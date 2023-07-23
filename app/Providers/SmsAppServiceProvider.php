<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SmsAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('App\\Core\\Users\\RootUserInterface','App\\Core\\Users\\RootUserRepository');
        $this->app->bind('App\\Core\\Users\\ManagerInterface','App\\Core\\Users\\ManagerRepository');
        $this->app->bind('App\\Core\\Users\\ResellerInterface','App\\Core\\Users\\ResellerRepository');
        $this->app->bind('App\\Core\\Users\\ClientInterface','App\\Core\\Users\\ClientRepository');
        $this->app->bind('App\\Core\\Countries\\CountriesInterface','App\\Core\\Countries\\Countries');
        $this->app->bind('App\\Core\\ClientDocuments\\ClientDocumentsInterface','App\\Core\\ClientDocuments\\ClientDocumentUpload');
        $this->app->bind('App\\Core\\ResellerDocuments\\ResellerDocumentsInterface','App\\Core\\ResellerDocuments\\ResellerDocumentUpload');
        $this->app->bind('App\\Core\\SmsAdminSettings\\SmsAdminSettings','App\\Core\\SmsAdminSettings\\SmsAdminSettingsDetails');
        $this->app->bind('App\\Core\\OperatorsGateways\\Operators','App\\Core\\OperatorsGateways\\OperatorsDetails');
        $this->app->bind('App\\Core\\OperatorsGateways\\OperatorsApi','App\\Core\\OperatorsGateways\\OperatorsApiDetails');
        $this->app->bind('App\\Core\\Senderid\\SenderId','App\\Core\\Senderid\\SenderidDetails');
        $this->app->bind('App\\Core\\ClientSenderid\\ClientSenderid','App\\Core\\ClientSenderid\\ClientSenderidDetails');
        $this->app->bind('App\\Core\\ResellerSenderid\\ResellerSenderid','App\\Core\\ResellerSenderid\\ResellerSenderidDetails');
        $this->app->bind('App\\Core\\AccountsChart\\AccountsHead','App\\Core\\AccountsChart\\AccountsHeadDetails');
        $this->app->bind('App\\Core\\ProductSales\\ProductSales','App\\Core\\ProductSales\\ProductSaleDetails');
        $this->app->bind('App\\Core\\Accounts\\Accounts','App\\Core\\Accounts\\AccountDetails');
        $this->app->bind('App\\Core\\ContactsAndGroups\\ContactsAndGroups','App\\Core\\ContactsAndGroups\\ContactsAndGroupsDetails');
        $this->app->bind('App\\Core\\SmsSend\\SmsSend','App\\Core\\SmsSend\\SmsSendDetails');
        $this->app->bind('App\\Core\\UserCountSms\\UserCountSms','App\\Core\\UserCountSms\\UserCountSmsDetails');
        $this->app->bind('App\\Core\\Reports\\SmsReport','App\\Core\\Reports\\SmsReportDetails');
        $this->app->bind('App\\Core\\Templates\\Template','App\\Core\\Templates\\TemplateDetails');
        $this->app->bind('App\\Core\\HandleFile\\HandleFile','App\\Core\\HandleFile\\HandleFileDetails');
        $this->app->bind('App\\Core\\BalanceReconciliation\\BalanceReconciliation','App\\Core\\BalanceReconciliation\\BalanceReconciliationDetails');

    }
}
