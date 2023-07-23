<?php

use App\ArchiveSentSms;
use App\UserBalance;
use App\UserSentSms;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

//log incoming request


    // $data = sprintf(
    //     "%s, %s, %s,",
    //     $_SERVER['REQUEST_METHOD'],
    //     $_SERVER['REQUEST_URI'],
    //     $_SERVER['SERVER_PROTOCOL']
    // );

    // if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])){
    //     $data .= " ".$_SERVER['HTTP_REFERER']." \n";
    // }

    // file_put_contents(
    //     'requestsLogs.csv',
    //     $data, FILE_APPEND
    // );





// $message  = "Received Request at " . time() . "\n";
// $message .= "------------------------------------------------------------------------\n";
// $message .= "\n";
// $message .= json_encode($_REQUEST, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT) . "\n";

// $filename = "requestsLogs.txt";

// file_put_contents($filename, $message, FILE_APPEND);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
use Illuminate\Support\Str;

//weblink pages routes
Route::get('/{code}/weblink','WeblinkController@index');


Route::post('migrate-client-balance','TestController@clientBalance')->name('client-balance');


Route::post('client-balance','TestController@clientBalance')->name('client-balance');
Route::post('clientbalance','ClientController@getClientBalance')->name('client.balance');

Route::get('get-reseller-balance','TestController@clientBalance')->name('client-balance');
// Route::post('reseller-balance','ResellerController@resellerBalance')->name('reseller-balance');



Route::get('verify-your-phone', function(){
    return view('firebasetest');
})->name('verify-your-phone');

Route::get('sent-sms', function(){
    return UserBalance::sum('mask');
    $currentdate = Carbon::now()->subDays(1)->toDateString();
    $totalrecord = UserSentSms::count();

    return $totalrecord;
});

Route::get('/copy-table',function(){
    UserSentSms::query()
    ->whereDate('submitted_at','<',Carbon::today())
    ->each(function ($oldRecord) {
        $newRecord = $oldRecord->replicate();
        $newRecord->setTable('archive_sent_smses');
        $newRecord->save();

        $oldRecord->delete();
    });
});

Route::get('populate-archive-report', function(){
    ini_set('max_execution_time', 0);
    $currentdate = Carbon::now()->subDays(1)->toDateString();//Carbon::today();
    
    //DB::table('archive_campaign')->truncate();

    $archive = DB::select(DB::raw("
    insert into archive_campaign(name,email,userid,smsid,totalcampaign,send_from,sms_catagory,sms_type,sender_name,contact,sms_content,smscount,status,submitted_at)
    select   `u`.`name` AS `name`,`u`.`email` AS `email`,`us`.`user_id` AS `userid`,substr(`us`.`remarks`,10,length(`us`.`remarks`)) AS `smsid`,
            count(`us`.`remarks`) AS `totalcampaign`,`us`.`send_type` AS `send_from`,
            `us`.`sms_catagory` AS `sms_catagory`,`us`.`sms_type` AS `sms_type`,
            `s`.`sender_name` AS `sender_name`,count(`us`.`to_number`) AS `contact`,`us`.`sms_content` AS `sms_content`,
            sum(`us`.`number_of_sms`) AS `smscount`,`us`.`status` AS `status`,
            `us`.`submitted_at` AS `submitted_at` 
            from `archive_sent_smses` us
    join `users` `u` 
    on `u`.`id` = `us`.`user_id` 
    join `sms_senders` `s` 
    on `s`.`id` = `us`.`user_sender_id`
    where DATE(us.submitted_at) between '$currentdate' and '$currentdate'
    group by `us`.`remarks`
    "));

    echo "Archive completed";

});

Route::get('/execution-time', function(){

    echo Carbon::now()->subDays(1);
});

//Auth::routes();
Route::get('/randomno', function(){
    echo '98131597304719'.mt_rand(100,1000);
});

Route::get('/checkstring', function(){
    // if (!preg_match('/^[0-9-\s\.\_]+$/D', $string)) {
    //     return 'Not match';
    // }
    $string = "Hi how are you {{name}}. Your new mobile no {{mobile}}, you can now call to your {{friend}}";

    if (preg_match_all("/{{(.*?)}}/", $string, $m)) {
        foreach ($m[1] as $i => $varname) {
            $template = str_replace($m[0][$i], sprintf('%s', $varname), $string);
            echo $varname;
        }
    }
    // $string = str_replace(" ", "", $string);
    // if (!is_numeric($string)) {
    //     return "Invalid Format";
    // } 
    //return $template;
});

Route::get('/check-firebase-client','SmsAppRegistrationController@checkClient')->name('check-firebase-client');
Route::get('/', 'HomeController@index')->name('superadmin');
Route::get('/manager', 'ManagerController@manager')->name('manager');
Route::get('/resellers', 'ResellerController@reseller')->name('reseller');
Route::get('/client', 'ClientController@client')->name('client');
Route::get('/user-registration', 'SmsAppRegistrationController@smsappUserRegister')->name('user-registration');
Route::get('/client-registration', 'HomeController@smsappUserRegister')->name('client-registration');
Route::get('/root-user-registration', 'HomeController@smsappRootUserRegister')->name('root-user-registration');
Route::get('/support-manager-registration', 'HomeController@smsappManagerUserRegister')->name('support-manager-registration');
Route::get('/reseller-registration', 'HomeController@smsappResellerRegister')->name('reseller-registration');
Route::get('/reseller-client-signup', 'ResellerController@smsappUserRegister')->name('reseller-client-signup');
Route::get('/client-signup', 'ResellerController@smsappUserRegister')->name('client-signup');

Route::get('check-user-using-firebase','SmsAppRegistrationController@checkClientUsingFirebase')->name('check-user-using-firebase');
Route::post('/doregistration', 'SmsAppRegistrationController@register')->name('doregistration');
Route::get('/signin', 'SmsAppLoginController@showLoginForm')->name('signin');
Route::get('/manager-login', 'SmsAppLoginController@showManagerLoginForm')->name('manager-login');
Route::get('/reseller-login', 'SmsAppLoginController@showResellerLoginForm')->name('reseller-login');
Route::get('/root-login', 'SmsAppLoginController@showRootLoginForm')->name('root-login');
// Route::get('/root-login', 'SmsAppLoginController@showRootLoginForm')->name('root-login');
Route::get('/verify-phone', 'SmsAppLoginController@clientPhoneVerifyForm')->name('verify-phone');
Route::post('/verify-client-phone', 'SmsAppLoginController@verifyClientPhone')->name('verify-client-phone');
Route::get('root-client-phone-verified-sms-send/{user}/{phone}/{requestfor?}/{passwordresetfor?}','VerifyClientPhoneController@rootClientPhonVerifiedSmsSend')->name('root-client-phone-verified-sms-send');
Route::post('/smslogin', 'SmsAppLoginController@smsLogin')->name('smslogin');
Route::get('/logout', 'SmsAppRegisterController@logout')->name('userlogout');


Route::get('forget-password','PasswordResetController@passwordResetForm')->name('forget-password');
Route::post('send-password-information','PasswordResetController@sendPasswordInformation')->name('send-password-information');
Route::get('reset-password','PasswordResetController@resetNewPassword')->name('reset-password');

Route::get('/root-users','HomeController@rootUserList')->name('root-users');
Route::get('/root-users-data','HomeController@rootUserData')->name('root-users-data');

Route::get('/root-managers','HomeController@rootManagerList')->name('root-managers');
Route::get('/root-managers-data','HomeController@supportManagerData')->name('root-managers-data');


Route::get('/root-clients','HomeController@rootClientList')->name('root-clients');
Route::get('/root-clients-data','HomeController@clientData')->name('root-clients-data');
Route::post('/client-reseller-info','HomeController@clientResellerData')->name('client-reseller-info');

Route::get('client-login-from-root/{email}','HomeController@clientLoginFromRoot')->name('client-login-from-root');
Route::get('client-login-from-reseller/{email}','ResellerController@clientLoginFromRoot')->name('client-login-from-reseller');
Route::get('reseller-login-from-root/{email}','HomeController@resellerLoginFromRoot')->name('reseller-login-from-root');


/**Manager Client */

Route::get('/manager-resellers','ManagerController@managerResellerList')->name('manager-resellers');
Route::get('/manager-resellers-data','ManagerController@resellerData')->name('manager-resellers-data');

Route::get('/manager-clients','ManagerController@managerClientList')->name('manager-clients');
Route::get('/manager-clients-data','ManagerController@clientData')->name('manager-clients-data');


/** Reseller Client */

Route::get('/reseller-clients','ResellerController@resellerClientList')->name('reseller-clients');
Route::get('/reseller-clients-data','ResellerController@clientData')->name('reseller-clients-data');


/** Countries */

Route::get('all-countries', 'CountryController@show');


/** User Edit from root */

Route::get('root-user/{user}/edit','HomeController@rootUserEdit');
Route::post('root-user-update','SmsAppRegistrationController@rootUserUpdate')->name('root-user-update');

Route::get('root-manager/{user}/edit','HomeController@rootManagerEdit');
Route::post('root-manager-update','SmsAppRegistrationController@rootUserUpdate')->name('root-manager-update');

Route::get('root-reseller/{userid}/edit','HomeController@rootResellerEdit')->name('root-reseller');
Route::post('root-reseller-update','SmsAppRegistrationController@rootUserUpdate')->name('root-reseller-update');

Route::get('root-client/{userid}/edit','HomeController@rootClientEdit')->name('root-client');
Route::get('client-profile/{userid}/profile','HomeController@rootClientProfile')->name('client-profile');
Route::get('client-profile/{userid}/document-upload','HomeController@rootClientProfileDocument')->name('client-document');
Route::get('client-profile/{userid}/senderid','HomeController@rootClientProfileSenderid')->name('client-profile-senderid');
Route::get('client-profile/{userid}/template','HomeController@rootClientProfileTemplate')->name('client-profile-template');
Route::get('client-profile/{userid}/smssale','HomeController@rootClientProfileSmsSale')->name('client-profile-smssale');
Route::get('client-profile/{userid}/invoice','HomeController@rootClientProfileManageInvoice')->name('client-profile-invoice');
Route::get('client-profile/{userid}/index','HomeController@rootClientProfileIndex')->name('client-profile-index');
Route::get('client-profile-total-sms/{userid}', 'HomeController@clientTotalMonthlySms')->name('client-profile-total-sms');
Route::post('search-unassigned-senderid','HomeController@searchUnAssignedSenderid')->name('search-unassigned-senderid');
Route::post('assign-senderids-to-client','ClientSenderidController@assignSenderIDsToClient')->name('assign-senderids-to-client');

Route::post('root-client-update','SmsAppRegistrationController@rootUserUpdate')->name('root-client-update');


Route::get('reseller-client-profile/{userid}/index','ResellerController@resellerClientProfileIndex')->name('reseller-client-profile-index');
Route::get('reseller-client-profile/{userid}/profile','ResellerController@resellerClientProfile')->name('reseller-client-profile');
Route::get('reseller-client-profile/{userid}/document-upload','ResellerController@resellerClientProfileDocument')->name('reseller-client-document');

Route::get('reseller-client-profile-total-sms/{userid}', 'ResellerController@resellerClientTotalMonthlySms')->name('reseller-client-profile-total-sms');
Route::post('reseller-client-search-unassigned-senderid','ResellerController@searchUnAssignedSenderid')->name('reseller-client-search-unassigned-senderid');
Route::get('reseller-client-profile/{userid}/template','ResellerController@resellerClientProfileTemplate')->name('reseller-client-profile-template');
Route::get('reseller-client-profile/{userid}/invoice','ResellerController@resellerClientProfileManageInvoice')->name('reseller-client-profile-invoice');
Route::get('reseller-client-profile/{userid}/smssale','ResellerController@resellerClientProfileSmsSale')->name('client-profile-smssale');



Route::get('reseller-profile/{userid}/index','ResellerController@resellerProfileIndex')->name('reseller-profile-index');
Route::get('reseller-profile/{userid}/profile','ResellerController@resellerProfile')->name('reseller-profile');
Route::get('reseller-profile/{userid}/document-upload','ResellerController@resellerProfileDocumentUpload')->name('reseller-document');
Route::get('reseller-profile/{userid}/senderid','ResellerController@resellerProfileSenderid')->name('reseller-profile-senderid');
Route::get('reseller-profile/{userid}/invoice','ResellerController@resellerProfileManageInvoice')->name('reseller-profile-invoice');
Route::get('reseller-profile/{userid}/smssale','ResellerController@resellerProfileSmsSale')->name('reseller-profile-smssale');
Route::post('assign-senderids-to-reseller','ResellerSenderidController@assignSenderIdsToReseller')->name('assign-senderids-to-reseller');
Route::post('search-unassigned-reseller-senderid','ResellerSenderidController@searchUnAssignedSenderid')->name('search-unassigned-reseller-senderid');
Route::get('reseller-profile-total-sms/{userid}', 'ResellerController@clientTotalMonthlySms')->name('reseller-profile-total-sms');


/** Client Edit from reseller */
Route::get('reseller-client/{userid}/edit','ResellerController@resellerClientEdit')->name('reseller-client');
Route::post('reseller-client-update','SmsAppRegistrationController@rootUserUpdate')->name('reseller-client-update');
Route::post('reseller-client-document-upload/{userid?}', 'ResellerClientDocumentUploadController@uploadDocuments')->name('reseller-client-document-upload');



/** Client profile edit from client */

Route::get('myprofile/{userid}/edit','ClientController@rootClientProfile')->name('myprofile-edit');
Route::get('myprofile/{userid}/document-upload','ClientController@rootClientProfileDocument')->name('myprofile-document');
Route::get('myprofile/{userid}/index','ClientController@rootClientProfileIndex')->name('myprofile-index');
Route::get('myprofile/{userid}/senderid','ClientController@rootClientProfileSenderid')->name('myprofile-senderid');
Route::get('myprofile/{userid}/template','ClientController@rootClientProfileTemplate')->name('myprofile-template');
Route::get('myprofile/{userid}/invoice','ClientController@rootClientProfileManageInvoice')->name('myprofile-invoice');

Route::get('client-profile-total-sms/{userid}', 'ClientController@clientTotalMonthlySms')->name('client-profile-total-sms');
Route::get('client-settings/{userid}/edit','ClientController@clientEdit')->name('client-settings');
Route::post('client-update','SmsAppRegistrationController@rootClientUpdate')->name('client-update');
Route::get('active-clients','HomeController@activeClients')->name('active-clients');


/** Client document upload */

Route::post('client-document-upload/{userid?}', 'ClientDocumentUploadController@uploadDocuments')->name('client-document-upload');
Route::post('client-document-upload-by-root/{userid}', 'RootClientDocumentUploadController@uploadDocuments')->name('root-client-document-upload');
Route::post('reseller-document-upload-by-root/{userid}', 'RootResellerDocumentUploadController@uploadDocuments')->name('root-reseller-document-upload');


/** Client document verify */

Route::post('root-client-decument-verify/{userid}', 'RootClientDocumentUploadController@verifyDocument')->name('root-client-decument-verify');
Route::post('root-client-status/{userid}', 'RootClientDocumentUploadController@changeClientStatus')->name('root-client-status');

Route::post('root-reseller-decument-verify/{userid}', 'RootResellerDocumentUploadController@verifyDocument')->name('root-reseller-decument-verify');
Route::post('root-reseller-status/{userid}', 'RootResellerDocumentUploadController@changeClientStatus')->name('root-reseller-status');


/** Root Admin setttings */

Route::get('smsadmin-settings','SmsAdminSettings@smsAdminSettings')->name('smsadmin-settings');
Route::post('smsadmin-settings-update','SmsAdminSettings@smsAdminSettingsUpdate')->name('smsadmin-settings-update');

/** Add Operator */

Route::get('add-operator', 'OperatorController@index')->name('add-operator');
Route::post('edit-operator', 'OperatorController@editOperator')->name('edit-operator');
Route::post('insert-operator', 'OperatorController@addOperator')->name('insert-operator');
Route::get('show-operators','OperatorController@showOperators')->name('show-operators');
Route::get('render-operators','OperatorController@renderOperators')->name('render-operators');
Route::post('delete-operator','OperatorController@deleteOperator')->name('delete-operator');

// edit divider
Route::get('edit-divider', 'OperatorController@editDivider')->name('edit-divider');
Route::post('update-divider/{id}', 'OperatorController@updateDivider')->name('update-divider');


/** Api gateway */

Route::post('add-operator-gateway', 'GatewayController@addOperatorGateway')->name('add-operator-gateway');
Route::get('render-gateways', 'GatewayController@renderGateways')->name('render-gateways');
Route::get('show-gateways','GatewayController@showGateways')->name('show-gateways');


/** Sms Sender ID */
Route::post('add-sms-senderid','SenderidController@addSenderId')->name('add-sms-senderid');

//modified by rubel

//create senderid
Route::get('create-senderid','SenderidNewController@createSenderid')->name('create-senderid');

//get gateways of a operator
Route::post('gateway-of-operator','OperatorController@getGetwayOfOperator')->name('gateway-of-operator');

//create sender id post
Route::post('create-senderid','SenderidNewController@createSenderidPost')->name('create-senderid-post');


Route::get('senderid/manage-senderid','SenderidNewController@showSenderIds')->name('manage-senderid');
Route::get('senderid/edit-senderid/{id}','SenderidNewController@editSenderIds')->name('edit-senderid');

Route::post('senderid/edit-senderid/{id}','SenderidNewController@editSenderidPost')->name('edit.senderid-post');

Route::post('senderid/get-senderids','SenderidNewController@getSenderIds')->name('senderid.get-senderids');

Route::get('load-assigned-sender-id','SenderidNewController@loadAssignedSenderId')->name('load-assigned-sender-id');

Route::get('load-assigned-sender-id-reseller','SenderidNewController@loadAssignedSenderIdReseller')->name('load-assigned-sender-id-reseller');

Route::post('assign-senderid','SenderidNewController@assignSenderIdToClient')->name('assign-senderid');

Route::post('assign-senderid-resellers','SenderidNewController@assignSenderIdToReseller')->name('assign-senderid-resellers');


Route::get('delete-client-senderid/{senderid_users_id}/{senderid}','SenderidNewController@deleteClientAssignedSenderId')->name('delete-client-senderid');

Route::get('delete-reseller-senderid/{reseller}/{senderid}','SenderidNewController@deleteResellerAssignedSenderId')->name('delete-reseller-senderid');


/* Download Page*/

Route::get('downloads',function(){ return view('smsview/client/download');})->name('downloads');
Route::get('downloads/AutLtrBlank','SmsSendingSystem@download1')->name('download1');
Route::get('downloads/AutLtrBlankGP','SmsSendingSystem@download2')->name('download2');
Route::get('downloads/BTRC-Form-20200001','SmsSendingSystem@download3')->name('download3');

Route::get('send-sms','SmsSendingSystem@showSendSmsForm')->name('send-sms');
Route::get('send-sms/api','SmsSendingSystem@processSendSms')->name('send-sms-api');


Route::get('single-sms-report','SmsReportController@clientSingleSmsReport')->name('single-sms-report')->middleware('auth:root,reseller,web');

Route::post('single-sms-report-data','SmsReportController@clientSingleSmsReportData')->name('single-sms-report-data')->middleware('auth:root,reseller,web');



//client
Route::get('campaign-report','SmsReportController@clientCampaignReport')->name('campaign-report')->middleware('auth:root,reseller,web');
Route::post('campaign-report-data','SmsReportController@clientCampaignReportData')->name('campaign-report-data')->middleware('auth:root,reseller,web');

//root
Route::get('root-campaign-report','SmsReportController@clientCampaignReport')->name('root-campaign-report')->middleware('auth:root');
Route::post('root-campaign-report-data','SmsReportController@clientCampaignReportData')->name('root-campaign-report-data')->middleware('auth:root');

Route::post('root-singlesms-data','SmsReportController@rootSingleSmsReportData')->name('root-singlesms-data')->middleware('auth:root');



Route::get('client-campaign-details/{campaignId}','SmsReportController@clientCampaignReportDetails')->name('campaign-report-details')->middleware('auth:root,reseller,web');

Route::post('client-campaign-data/{campaignId}','SmsReportController@campaignDetailsData')->name('campaign-details-data')->middleware('auth:root,reseller,web');


Route::get('campaign-details-live/{campaignId}','SmsReportController@campaignDetailsLive')->name('campaign-details-live')->middleware('auth:root,reseller,web');

Route::get('roottest','SmsReportController@roottest')->middleware('auth:root,reseller,web');


Route::get('developer-doc','SmsSendingSystem@developerDoc')->name('developer-doc')->middleware('auth:web');


Route::get('/root-resellers','HomeController@rootResellerList')->name('root-resellers');
Route::get('/root-resellers-data','HomeController@resellerData')->name('root-resellers-data');

Route::post('reseller-senderids','SenderidNewController@resellerSenderidList')->name('reseller-senderids');

Route::post('resellerbalance','ResellerNewController@resellerBalance')->name('reseller.balance');

Route::get('reseller-client-profile/{userid}/senderid','ResellerNewController@resellerClientProfileSenderid')->name('reseller-client-profile-senderid');

Route::post('assign-senderid-by-reseller/{userid}','ResellerNewController@assignSenderIdToClientByReseller')->name('assign-senderid-by-reseller');

Route::get('delete-senderid-by-reseller/{user}/{senderid}','ResellerNewController@deleteClientSenderId')->name('delete-reseller-senderid');


Route::post('client-assigned-senderids','SenderidNewController@clientSenderidList')->name('client-assigned-senderids');
Route::post('reseller-client-assigned-senderids','SenderidNewController@clientSenderidList')->name('reseller-client-assigned-senderids');



/**Contact and groups */
 
Route::get('manage-groups','ContactGroupController@manageGroups')->name('manage-groups');
Route::post('contact-groups-data','ContactGroupController@contactGroupsData')->name('contact-groups-data');
Route::get('contact-group-details/{id}','ContactGroupController@contactGroupDetails')->name('contactgroup-details');
Route::post('contact-group-numbers/{id}','ContactGroupController@contactGroupNumbers')->name('contact-group-numbers');
Route::get('contactgroup-delete/{id}','ContactGroupController@deleteGroup')->name('contactgroup-delete');

Route::post('create-contacts','ContactGroupController@addContactGroup')->name('create-contacts');
Route::post('update-contacts','ContactGroupController@updateContactInfo')->name('update-contacts');
Route::post('delete-contacts','ContactGroupController@deleteContactInfo')->name('delete-contacts');
Route::post('delete-multiple-contacts','ContactGroupController@deleteMultipleContactInfo')->name('delete-multiple-contacts');



//old routes-----------
Route::post('create-group','ContactAndGroupController@createGroup')->name('create-group');
Route::get('show-groups','ContactAndGroupController@showGroups')->name('show-groups');
Route::get('render-groups','ContactAndGroupController@renderGroups')->name('render-groups');
Route::post('delete-group','ContactAndGroupController@deleteGroup')->name('delete-group');

Route::get('show-contactin-group','ContactAndGroupController@showContactGroups')->name('show-contactin-group');
Route::get('render-contactin-groups','ContactAndGroupController@renderContactsInGroup')->name('render-contactin-groups');

//old routes end--------

//low cost sms
Route::get('send-sms-low-cost','SmsSendingSystem@lowCostSmsForm')->name('send-sms-lowcost');


//sms sales
Route::get('sms-saleto-client','SmsSaleController@smsSale')->name('sms-saleto-client');
Route::post('product-sale','ProductSaleController@addSmsSale')->name('product-sale');


//--------- Modem Manager ---------
//manage modems
Route::get('manage-modems','ModemController@manageModems')->name('manage-modems');
//add modems
Route::get('add-modem','ModemController@addModem')->name('add-modems');
Route::post('add-modem','ModemController@addModemPost')->name('add-modems');
//edit modem
Route::post('edit-modem','ModemController@editModemPost')->name('edit-modem');
//regenerate api key
Route::get('regenerate-modem-apitoken/{id}','ModemController@regenerateModemToken')->name('regenerate-modem-apitoken');













// modify by rubel end



Route::post('add-rotation-sms-senderid','SenderidController@addRotationSenderId')->name('add-rotation-sms-senderid');
Route::post('add-rotation-multiple-template-sms-senderid','SenderidController@addMultipleTemplateRotationSenderId')->name('add-rotation-multiple-template-sms-senderid');
Route::get('sms-senderid/{senderidtype}','SenderidController@showSmsSenderId')->name('sms-senderid');
Route::get('rotation-sms-senderid/{senderidtype}','SenderidController@showRotationSmsSenderId')->name('rotation-sms-senderid');
Route::get('turbo-sms-senderid/{senderidtype}','SenderidController@showMultipleRotationSmsSenderId')->name('turbo-sms-senderid');
Route::get('teletalk-sms-senderid/{senderidtype}','SenderidController@showSmsSenderIdTeletalk')->name('teletalk-sms-senderid');
Route::get('reseller-senderid/{senderidtype}','SenderidController@showSenderIdForReseller')->name('reseller-senderid');
Route::get('reseller-teletalk-senderid/{senderidtype}','SenderidController@showSmsSenderIdTeletalkForReseller')->name('reseller-teletalk-senderid');
Route::get('rander-sms-senderid/{senderidtype}','SenderidController@renderSenderId')->name('rander-sms-senderid');



// 

/** Client & reseller senderid */

Route::post('assign-client-senderid','ClientSenderidController@assignSenderIdToClient')->name('assign-client-senderid');


Route::post('assign-reseller-senderid','ResellerSenderidController@assignSenderIdToReseller')->name('assign-reseller-senderid');



Route::get('load-assigned-reseller-sender-id/{senderid}','ResellerSenderidController@loadAssignedSenderId')->name('load-assigned-reseller-sender-id');




/** Accounts Head */

Route::get('manage-root-accounts','AccountHeadController@showAccountsHead')->name('manage-root-accounts');
Route::get('render-accounts-head','AccountHeadController@renderRootAccountsHead')->name('render-accounts-head');

Route::get('manage-group-accounts','AccountHeadController@showGroupAccountsHead')->name('manage-group-accounts');
Route::get('render-group-accounts-head','AccountHeadController@renderGroupAccountsHead')->name('render-group-accounts-head');

Route::get('manage-bottom-accounts','AccountHeadController@showTransectionAccountsHead')->name('manage-bottom-accounts');
Route::get('render-bottom-accounts-head','AccountHeadController@renderTransectionAccountsHead')->name('render-bottom-accounts-head');

Route::post('add-accounts-head','AccountHeadController@addAccountsRootHead')->name('add-accounts-head');
Route::get('delete-accounts-head/{id}','AccountHeadController@deleteAccountsHead')->name('delete-accounts-head');

/** Sms Sale */


Route::get('sms-saleto-reseller','SmsSaleController@smsSaleToReseller')->name('sms-saleto-reseller');
Route::get('reseller-sms-saleto-client','ResellerSmsSaleToClientController@smsSale')->name('reseller-sms-saleto-client');
Route::post('reseller-sms-balance-check','ResellerSmsSaleToClientController@resellerSmsBalanceCheck')->name('reseller-sms-balance-check');

Route::post('reseller-product-sale-toclient','ResellerProductSaleToClientController@addSmsSale')->name('reseller-product-sale-toclient');
Route::get('client-invoicelist','ProductSaleController@rootAndManagerInvoiceList')->name('client-invoicelist');
Route::get('reseller-client-invoicelist','ResellerProductSaleToClientController@rootAndManagerInvoiceList')->name('reseller-client-invoicelist');
Route::get('my-reseller-invoicelist','ResellerProductSaleToClientController@resellerMyInvoiceList')->name('my-reseller-invoicelist');
Route::get('show-my-reseller-invoicelist/{userid?}','ResellerProductSaleToClientController@showMyResellerInvoices')->name('show-my-reseller-invoicelist');
Route::get('my-invoicelist','ClientInvoiceListController@clientInvoiceList')->name('my-invoicelist');
Route::get('show-my-invoicelist/{userid?}','ClientInvoiceListController@showClientInvoices')->name('show-my-invoicelist');
Route::get('reseller-invoicelist','ProductSaleController@resellerInvoiceList')->name('reseller-invoicelist');
Route::get('show-root-client-invoices','ProductSaleController@showRootClientInvoices')->name('show-root-client-invoices');
Route::get('show-reseller-client-invoices','ResellerProductSaleToClientController@showRootClientInvoices')->name('show-reseller-client-invoices');
Route::get('show-reseller-invoices','ProductSaleController@showResellerInvoices')->name('show-reseller-invoices');


/**Client Panel */
Route::get('client-senderids','ClientSenderIdListController@clientSenderIdList')->name('client-senderids');
Route::get('client-senderid-list/{clientid}','ClientSenderIdListController@showClientSenderId')->name('client-senderid-list');
Route::post('set-default-client-senderid','ClientSenderIdListController@setClientDefaultSenderId')->name('set-default-client-senderid');




Route::get('send-sms-dipping','ClientSmsSendController@showSendMsgFormDipping')->name('send-sms-dipping');

Route::get('generate-apitoken/{user}','SmsAppRegistrationController@generateNewApiToken')->name('generate-apitoken');




Route::post('reseller-client-balance','ResellerController@clientBalance')->name('reseller-client-balance');



/**Sms Send */

Route::post('senderid-type', 'ClientSmsSendController@getSenderIdType')->name('senderid-type');
Route::post('total-contacts-ina-group', 'ClientSmsSendController@getTotalNumberOfContacts')->name('total-contacts-ina-group');
Route::post('total-sms-on-message-setup', 'ClientSmsSendController@numberOfSmsOnMessageSetup')->name('total-sms-on-message-setup');
Route::post('valid-mobile-by-prefix', 'ClientSmsSendController@validMobile')->name('valid-mobile-by-prefix');

//route for sending message instantly
Route::post('manage-sms-messages', 'SmsSendingSystem@sendSMS')->name('manage-sms-messages');
//controller modified by rubel

Route::post('send-sms-using-template', 'TemplateController@manageSmsMessage')->name('send-sms-using-template');
Route::get('template-approved-content/{tempid}','TemplateController@showApprovedTemplate')->name('template-approved-content');
Route::post('resend-failed-sms-messages', 'ClientSmsSendController@resendFailedSms')->name('resend-failed-sms-messages');
Route::post('set-schedule-sms-messages', 'ClientSmsSendController@schedulingSms')->name('set-schedule-sms-messages');

Route::post('manage-sms-messages-dipping', 'ClientSmsSendController@manageSmsMessageDipping')->name('manage-sms-messages-dipping');

Route::get('manage-campaing', 'ClientSmsSendController@bulkSmsCampaing')->name('manage-campaing');
Route::get('render-campaing', 'ClientSmsSendController@manageBulkMessage')->name('render-campaing');
Route::post('send-pending-sms', 'ClientSmsSendController@jobSms')->name('send-pending-sms');


/*Reseller Panel*/
Route::post('assign-reseller-client-senderid','ResellerClientAssignSenderidController@assignSenderIdToClient')->name('assign-reseller-client-senderid');
Route::get('assign-senderid-toclient','ResellerClientSenderidController@showSenderIdForResellerClient')->name('assign-senderid-toclient');
Route::get('rander-sms-senderid-for-reseller/{userid?}','ResellerClientSenderidController@renderSenderId')->name('rander-sms-senderid-for-reseller');
Route::get('load-assigned-reseller-client-sender-id/{senderid}','ResellerClientAssignSenderidController@loadAssignedSenderId')->name('load-assigned-reseller-client-sender-id');
Route::get('delete-reseller-client-senderid/{user_sender_id}/{sms_sender_id}','ResellerClientAssignSenderidController@deleteClientAssignedSenderId')->name('delete-reseller-client-senderid');

/*Reseller Panel End*/

/**SMS report */

Route::get('clients-sms-report','SmsReportController@clientSmsReport')->name('clients-sms-report')->middleware('auth:root,reseller,web');
Route::get('client-sms-send-data','SmsReportController@clientSmsSendReport')->name('client-sms-send-data')->middleware('auth:root,reseller,web');




Route::get('campaign-archive-send-data','SmsReportController@clientArchiveConsulateSmsSendReport')->name('campaign-archive-send-data')->middleware('auth:root,reseller,web');

Route::get('root-client-campaign-report','SmsReportController@rootClientSmsConsulateReport')->name('root-client-campaign-report')->middleware('auth:root,reseller,web,manager');
Route::get('root-client-campaign-send-data','SmsReportController@rootClientConsulateSmsSendReport')->name('root-client-campaign-send-data')->middleware('auth:root,reseller,web,manager');
Route::get('root-client-archive-campaign-send-data','SmsReportController@rootClientArchiveConsulateSmsSendReport')->name('root-client-archive-campaign-send-data')->middleware('auth:root,reseller,web,manager');

Route::get('root-client-archive-campaign-report','SmsReportController@rootClientArchiveSmsConsulateReport')->name('root-client-archive-campaign-report')->middleware('auth:root,reseller,web,manager');

Route::get('reseller-client-campaign-report','SmsReportController@resellerClientSmsConsulateReport')->name('reseller-client-campaign-report')->middleware('auth:root,reseller,web');
Route::get('reseller-client-archive-campaign-report','SmsReportController@resellerClientArchiveSmsConsulateReport')->name('reseller-client-archive-campaign-report')->middleware('auth:root,reseller,web');
Route::get('reseller-client-campaign-send-data','SmsReportController@resellerClientConsulateSmsSendReport')->name('reseller-client-campaign-send-data')->middleware('auth:root,reseller,web');
Route::get('reseller-client-archive-campaign-send-data','SmsReportController@resellerClientArchiveConsulateSmsSendReport')->name('reseller-client-archive-campaign-send-data')->middleware('auth:root,reseller,web');

Route::post('root-client-campaign-mobile-list','SmsReportController@rootClientCampaignMobile')->name('root-client-campaign-mobile-list')->middleware('auth:root,reseller,web');

Route::post('root-client-archive-campaign-mobile-list','SmsReportController@rootClientArchiveCampaignMobile')->name('root-client-archive-campaign-mobile-list')->middleware('auth:root,reseller,web');
Route::get('currentday-gateway-errors','SmsReportController@todayGatewayErrorReport')->name('currentday-gateway-errors');
Route::get('render-gateway-error','SmsReportController@gatewayErrorReport')->name('render-gateway-error');

Route::get('export-excel','SmsReportController@exportExcel')->name('export-excel')->middleware('auth:root,reseller,web');
Route::get('export-archive-sms-excel','SmsReportController@exportArchiveSmsToExcel')->name('export-archive-sms-excel')->middleware('auth:root,reseller,web');



Route::get('root-clients-sms-report','SmsReportController@rootClientSmsReport')->name('root-clients-sms-report')->middleware('auth:root,manager');
Route::get('root-clients-sms-send-data','SmsReportController@rootClientSmsSendReport')->name('root-clients-sms-send-data')->middleware('auth:root,manager');

Route::get('reseller-clients-sms-report','SmsReportController@resellerClientSmsReport')->name('reseller-clients-sms-report')->middleware('auth:reseller');
Route::get('reseller-clients-sms-send-data','SmsReportController@resellerClientSmsSendReport')->name('reseller-clients-sms-send-data')->middleware('auth:reseller');

Route::get('client-total-sms-send','SmsReportController@totalClientSmsSendReport')->name('client-total-sms-send')->middleware('auth:root,reseller,web');
Route::get('client-archive-total-sms-send','SmsReportController@totalClientArchiveSmsSendReport')->name('client-archive-total-sms-send')->middleware('auth:root,reseller,web');
Route::get('root-client-total-sms-send','SmsReportController@totalRootClientSmsSendReport')->name('root-client-total-sms-send')->middleware('auth:root,manager');
Route::get('root-client-archive-total-sms-send','SmsReportController@totalRootClientArchiveSmsSendReport')->name('root-client-archive-total-sms-send')->middleware('auth:root,manager');
Route::get('reseller-client-total-sms-send','SmsReportController@totalResellerClientSmsSendReport')->name('reseller-client-total-sms-send')->middleware('auth:reseller');
Route::get('reseller-client-archive-total-sms-send','SmsReportController@totalResellerClientArchiveSmsSendReport')->name('reseller-client-archive-total-sms-send')->middleware('auth:reseller');

Route::get('client-total-sms-campaign','SmsReportController@totalClientSmsCampaign')->name('client-total-sms-campaign')->middleware('auth:root,reseller,web');
Route::get('client-archive-total-sms-campaign','SmsReportController@totalClientArchiveSmsCampaign')->name('client-archive-total-sms-campaign')->middleware('auth:root,reseller,web');
Route::get('root-client-total-sms-campaign','SmsReportController@totalRootClientSmsCampaign')->name('root-client-total-sms-campaign')->middleware('auth:root,reseller,web,manager');
Route::get('root-client-archive-total-sms-campaign','SmsReportController@totalRootClientArchiveSmsCampaign')->name('root-client-archive-total-sms-campaign')->middleware('auth:root,reseller,web,manager');
Route::get('reseller-client-total-sms-campaign','SmsReportController@totalResellerClientSmsCampaign')->name('reseller-client-total-sms-campaign')->middleware('auth:root,reseller,web');
Route::get('reseller-client-archive-total-sms-campaign','SmsReportController@totalResellerClientArchiveSmsCampaign')->name('reseller-client-archive-total-sms-campaign')->middleware('auth:root,reseller,web');

Route::get('root-clients-send-sms-count','SmsReportController@rootClientSendSmsCount')->name('root-clients-send-sms-count')->middleware('auth:root');
Route::post('root-clients-send-sms-consulate-rpt','SmsReportController@rootClientSmsSentConsulateReport')->name('root-clients-send-sms-consulate-rpt')->middleware('auth:root');

Route::get('reseller-clients-send-sms-count','SmsReportController@resellerClientSendSmsCount')->name('reseller-clients-send-sms-count')->middleware('auth:reseller');
Route::post('reseller-clients-send-sms-consulate-rpt','SmsReportController@resellerClientSmsSentConsulateReport')->name('reseller-clients-send-sms-consulate-rpt')->middleware('auth:reseller');

Route::get('clients-send-sms-count','SmsReportController@clientSendSmsCount')->name('clients-send-sms-count')->middleware('auth:root,reseller,web');
Route::post('clients-send-sms-consulate-rpt','SmsReportController@clientSmsSentConsulateReport')->name('clients-send-sms-consulate-rpt')->middleware('auth:root,reseller,web');

Route::get('root-client-sms-sent-total-consulate-report','SmsReportController@rootClientSmsSentTotalConsulateReport')->name('root-client-sms-sent-total-consulate-report')->middleware('auth:root');
Route::get('reseller-client-sms-sent-total-consulate-report','SmsReportController@resellerClientSmsSentTotalConsulateReport')->name('reseller-client-sms-sent-total-consulate-report')->middleware('auth:reseller');
Route::get('client-sms-sent-total-consulate-report','SmsReportController@clientSmsSentTotalConsulateReport')->name('client-sms-sent-total-consulate-report')->middleware('auth:root,reseller,web');

Route::get('client-faild-sms-report','SmsReportController@clientFaildSmsReport')->name('client-faild-sms-report')->middleware('auth:root,reseller,web');
Route::post('client-faild-sms-send-data','SmsReportController@clientFaildSmsSendReport')->name('client-faild-sms-send-data')->middleware('auth:root,reseller,web');


/**End Sms Report */

/** Root dashboard */

//Route::get('total-support-manager', 'ManagerController@totalSupportManagers')->name('total-support-manager');

/**
 * Template
 */

Route::get('manage-template','TemplateController@manageTemplate')->name('manage-template')->middleware('auth:root');
Route::get('client-template','TemplateController@clientTemplate')->name('client-template')->middleware('auth:root,web');
Route::get('rander-root-template','TemplateController@manageRootTemplate')->name('rander-root-template')->middleware('auth:root');
Route::get('rander-client-template/{userid?}','TemplateController@manageClientTemplate')->name('rander-client-template')->middleware('auth:web,root,manager,reseller');
Route::get('approve-btrc-file/{id}','TemplateController@btrcFileApproved')->name('approve-btrc-file')->middleware('auth:root');
Route::post('save-template','TemplateController@saveTemplate')->name('save-template')->middleware('auth:root,web,manager');

Route::get('staff-activity','HomeController@staffActivity')->name('staff-activity');
Route::get('staff-invoice-activity','HomeController@staffInvoiceActivity')->name('staff-invoice-activity');


/**
 * Customer Ledger
 * 
 */
Route::get('root-customer-ledger','LedgerController@customerSelection')->name('customer-ledger-selection')->middleware('auth:root');
Route::get('root-customer-ledger-view','LedgerController@rootCustomerLedgerDetails')->name('customer-ledger-details')->middleware('auth:root');

