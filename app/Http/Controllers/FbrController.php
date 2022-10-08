<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Exception;

class FbrController extends Controller
{
    public function fbr_sand_live()
    {
        $client = new Client();
        $result = $client->get('http://e32a-111-119-187-10.ngrok.io/fbr');
        echo json_decode($result->getBody()->getContents())->data;
        // $res = $result->getBody()->getContents();
    // dd(json_decode($res));
    // dd((String)($result->getBody()));
    }
    public function check()
    {
      $curl = curl_init();
      
      curl_setopt_array($curl, array(
        CURLOPT_PORT => "8244",
        CURLOPT_URL => "https://esp.fbr.gov.pk:8244/FBR/v1/api/Live/PostData",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => '{
          "InvoiceNumber": "",
          "POSID": 987316,
          "USIN": 1111,
          "DateTime": "2022-03-04 17:44:39",
          "BuyerNTN": 0,
          "BuyerCNIC": 1234567890123,
          "BuyerName": "Test",
          "BuyerPhoneNumber": "03027533988",
          "TotalBillAmount": "8500",
          "TotalQuantity": 3,
          "TotalSaleValue": "8500",
          "TotalTaxCharged": 500,
          "Discount": "100",
          "FurtherTax": 0,
          "PaymentMode": 1,
          "RefUSIN": "",
          "InvoiceType": 1,
          "Items": [
            {
              "ItemCode": 0,
              "ItemName": "Honey",
              "Quantity": "2",
              "PCTCode": 0,
              "TaxRate": 0,
              "SaleValue": "4000",
              "TotalAmount": 8000,
              "TaxCharged": 250,
              "Discount": "4000",
              "FurtherTax": 0,
              "InvoiceType": 1,
              "RefUSIN": ""
            },
            {
              "ItemCode": 0,
              "ItemName": "Honey",
              "Quantity": "1",
              "PCTCode": 0,
              "TaxRate": 0,
              "SaleValue": "500",
              "TotalAmount": 500,
              "TaxCharged": 250,
              "Discount": "500",
              "FurtherTax": 0,
              "InvoiceType": 1,
              "RefUSIN": ""
            }
          ]
        }',
        CURLOPT_HTTPHEADER => array(
          "authorization: Bearer 1298b5eb-b252-3d97-8622-a4a69d5bf818",
          "cache-control: no-cache",
          "content-type: application/json"
        ),
      ));
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($curl, CURLOPT_NOBODY, true);
      curl_setopt($curl, CURLOPT_HEADER, true);
      
      $response = curl_exec($curl);
      $err = curl_error($curl);
      
      curl_close($curl);
      
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response;
      }
    }
}
