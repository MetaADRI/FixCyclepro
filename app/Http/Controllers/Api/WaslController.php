<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Helper\PolygenController;
use App\Models\CountryArea;
use App\Models\WebsiteFeaturesComponents;
use App\Models\WebSiteHomePage;
use App\Models\WebsiteApplicationFeature;
use App\Models\WebsiteFeature;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Application;

class WaslController extends Controller
{

    //
   public function driverVehicleRegister(Request $request)
   {

       $curl = curl_init();

       curl_setopt_array($curl, array(
           CURLOPT_URL => 'https://wasl.api.elm.sa/api/dispatching/v2/drivers',
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'POST',
           CURLOPT_POSTFIELDS =>'{
                    "driver": {
                    "identityNumber": "1119246377",
                    "dateOfBirthHijri": "1423/12/02",
                    "emailAddress": "***REMOVED***",
                    "mobileNumber": "***REMOVED***"
                    },
                    "vehicle": { 
                    "sequenceNumber": "383771810",
                    "plateLetterRight": "د",
                    "plateLetterMiddle": "ه",
                    "plateLetterLeft": "ح",
                    "plateNumber": "9202",
                    "plateType": "1"
                    }
                    }',
           CURLOPT_HTTPHEADER => array(
               'Content-Type: application/json',
               'client-id: ***REMOVED***',
               'app-id: ***REMOVED***',
               'app-key: ***REMOVED***',
               'Cookie: ***REMOVED***; NSC_Q-PDQ-3tdbmf_qspevdujpo-TTM=***REMOVED***; TS0131994e=***REMOVED***'
           ),
       ));

       $response = curl_exec($curl);

       curl_close($curl);
       p($response);

   }

}
