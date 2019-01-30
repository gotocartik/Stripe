<?php

use Log;
class Payment

{
        public static  function stripeExecuteCurlRequest($url, $data)
        {
                $stripe_sk_key = "Stripe_secert_key";
                $result = new stdClass();
                $result->success = false;
                try {
                        $http_build_query_data = http_build_query($data);
                        Log::info($http_build_query_data);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true); /*POST*/
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $http_build_query_data);
                        curl_setopt($ch, CURLOPT_USERPWD, $stripe_sk_key . ':');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $curl_result = curl_exec($ch);
                        Log::info($curl_result);


                        if ($curl_result === false) Log::info('Stripe Curl result error: ' . curl_error($ch));
                        else {
                                $stripe_result = json_decode($curl_result);
                                if (json_last_error() == JSON_ERROR_NONE) {
                                        if (property_exists($stripe_result, 'error')) $result->success = false;
                                        else $result->success = true;
                                        $result->stripe_result = $stripe_result;
                                }
                        }

                        curl_close($ch);
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe Curl execution error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeExecuteCurlRequestCharge($url, $data, $headers)
        {
                 $stripe_sk_key = "Stripe_secert_key";
                $result = new stdClass();
                $result->success = false;
                try {
                        $http_build_query_data = http_build_query($data);
                        Log::info($http_build_query_data);
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true); /*POST*/
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $http_build_query_data);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_USERPWD, $stripe_sk_key . ':');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $curl_result = curl_exec($ch);
                        Log::info($curl_result);
                        if ($curl_result === false) Log::info('Stripe Curl result error: ' . curl_error($ch));
                        else {
                                $stripe_result = json_decode($curl_result);
                                if (json_last_error() == JSON_ERROR_NONE) {
                                        if (property_exists($stripe_result, 'error')) $result->success = false;
                                        else $result->success = true;
                                        $result->stripe_result = $stripe_result;
                                }
                        }

                        curl_close($ch);
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe Curl execution error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeCreateAccount($request)
        {
                $result = new stdClass();
                $result->success = false;
                $result->stripe_acc_id = '';
                $url = 'https://files.stripe.com/v1/files';
                $file = "/public/uploads/filename.jpg"; // (No need to send image with your domain name . just add a public path)
                StripeStripe::setApiKey($sk_key->value);
                $fp = fopen($file, 'r');
                $cur = StripeFile::create(['purpose' => 'identity_document', 'file' => $fp]);
                $cur = json_encode($cur);
                $cur = json_decode($cur);
                Log::info('Checking file Upload Stripe: ' . $cur->id);
                if ($cur->id) {
                        $file_id = $cur->id;
                        Log::info('File Upload success: ' . $cur->id);
                }
                else {
                        $result->success = false;
                        $result->message = "File upload error";
                        Log::info('File Upload Error: ' . $cur);
                        return $result;
                }

                try {
                        $url = 'https://api.stripe.com/v1/accounts';
                        $data['country'] = 'NO';
                        $data['legal_entity']['type'] = 'individual';
                        $data['type'] = 'custom';
                        $data['legal_entity']['first_name'] = "john";
                        $data['legal_entity']['last_name'] = "kennady";
                        $data['legal_entity']['address']['city'] = "new york";
                        $data['legal_entity']['address']['country'] = "US";
                        $data['legal_entity']['address']['line1'] = "24, new park";
                        $data['legal_entity']['address']['line2'] = "htc road";
                        $data['legal_entity']['address']['postal_code'] = "123456";
                        $data['legal_entity']['address']['state'] = "tn";
                        $data['legal_entity']['dob']['day'] = 12;
                        $data['legal_entity']['dob']['month'] = 1;
                        $data['legal_entity']['dob']['year'] = 1990;
                        $data['legal_entity']['ssn_last_4'] = 0987;
                        $data['legal_entity']['verification']['document'] = $file_id;
                        $data['tos_acceptance']['date'] = time();
                        $data['tos_acceptance']['ip'] = $request->ip();
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->stripe_acc_id = $curl->stripe_result->id;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe Account creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeCreateCustomer()
        {
                $result = new stdClass();
                $result->success = false;
                $card_token = "tok_jkjjsncjuuhdhskd";
                $email = "john@yahoo.com";
                try {
                        $url = 'https://api.stripe.com/v1/customers';
                        $data['source'] = $card_token;
                        $data['description'] = $email;
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->customer_id = $curl->stripe_result->id;
                                $result->card_token = $curl->stripe_result->sources->data[0]->id;
                                $result->last4 = $curl->stripe_result->sources->data[0]->last4;
                                $result->card_type = $curl->stripe_result->sources->data[0]->brand;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        $result->message = 'Stripe Customer creation error: ' . $e->getMessage();
                        Log::info('User Stripe Customer creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function addBankAccount($request)
        {
                $result = new stdClass();
                $result->success = false;
                $result->bank_acc_id = '';
                try {
                        $url = 'https://api.stripe.com/v1/tokens';
                        $data['bank_account']['country'] = 'US';
                        $data['bank_account']['currency'] = 'usd';
                        $data['bank_account']['account_holder_name'] = "John kennady";
                        $data['bank_account']['account_holder_type'] = "individual";
                        $data['bank_account']['routing_number'] = "110000000";
                        $data['bank_account']['account_number'] = "000123456789";
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->bank_acc_id = $curl->stripe_result->id;
                        }
                        else {
                                $result->success = false;
                                $result->message = 'Stripe Account creation error';
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        $result->message = ' Stripe Account creation error: ' . $e->getMessage();
                        Log::info(' Stripe Account creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function transferToAccount()
        {
                $result = new stdClass();
                $result->success = false;
                $result->bank_acc_id = '';
                $acc_id = "acc_jknjsdncjududdd";
                try {
                        $amount = 10;
                        $total_in_cents = $amount * 100;
                        $url = 'https://api.stripe.com/v1/transfers';
                        $data['amount'] = $total_in_cents;
                        $data['currency'] = 'usd';
                        $data['destination'] = $acc_id;
                        $data['transfer_group'] = "Payment";
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        
                        if ($curl->success) {
                                $result->success = true;
                                $result->bank_acc_id = $curl->stripe_result->id;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info(' Stripe Account creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeAccountDetails()
        {
                $id = "acc_jknjsdncjududdd";
                StripeStripe::setApiKey("secret_key_stripe");
                $account = StripeAccount::retrieve($id);
                $cur = json_encode($account);
                $result = json_decode($cur);
                return $result;
        }

        public static function stripeCreateAdmin($request, $email)
        {
                $result = new stdClass();
                $result->success = false;
                $stripeToken = "tok_jkjjsncjuuhdhskd";
                $email = "john@yahoo.com";
                try {
                        $url = 'https://api.stripe.com/v1/customers';
                        $data['source'] = $stripeToken;
                        $data['description'] = $email;
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->customer_id = $curl->stripe_result->id;
                                $result->card_token = $curl->stripe_result->sources->data[0]->id;
                                $result->last4 = $curl->stripe_result->sources->data[0]->last4;
                                $result->card_type = $curl->stripe_result->sources->data[0]->brand;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('User Stripe Customer creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function StripeCreateCardToken($request)
        {
                $result = new stdClass();
                $result->success = false;
                try {
                        $url = 'https://api.stripe.com/v1/tokens';
                        $data['card']['number'] = "4242424242424242";
                        $data['card']['exp_month'] = 12;
                        $data['card']['exp_year'] = 2021;
                        $data['card']['cvc'] = 123;
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->card_token = $curl->stripe_result->id;
                                $result->card_id = $curl->stripe_result->card->id;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('User Stripe Customer creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeAddCard()
        {
                $result = new stdClass();
                $result->success = false;
                $card_Token = "card_jkjkcndnbndmd";
                try {
                        $url = 'https://api.stripe.com/v1/customers/' . $card->customer_id . '/sources';
                        $data['source'] = $card_Token;
                        $curl = self::stripeExecuteCurlRequest($url, $data);


                        if ($curl->success) {
                                $result->success = true;
                                $result->customer_id = $curl->stripe_result->customer;
                                $result->card_token = $curl->stripe_result->id;
                                $result->card_type = $curl->stripe_result->brand;
                                $result->last4 = $curl->stripe_result->last4;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        $result->message = 'Stripe Account creation error: ' . $e->getMessage();
                        Log::info('Stripe Account creation error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeAdminCharge($)
        {
                $result = new stdClass();
                $result->success = false;
                try {
                        $amount = 10;
                        $total_in_cents = $amount * 100;
                        $url = 'https://api.stripe.com/v1/charges';
                        $data['amount'] = round($total_in_cents);
                        $data['currency'] = 'usd';
                        $data['customer'] = "cus_kljicninineded";
                        $data['destination'] = "acc_jknjsdncjududdd"; // the amount will go to destination Stripe Account it will be deposit to his

                        // bank account by 6 to 8 days.

                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->id = $curl->stripe_result->id;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe transaction error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeCreateCharge($amout, $card)
        {
                $result = new stdClass();
                $result->success = false;
                try {
                        $total_in_cents = $amout * 100;
                        $url = 'https://api.stripe.com/v1/charges';
                        $data['amount'] = round($total_in_cents);
                        $data['currency'] = 'usdd';
                        $data['customer'] = "cus_kljicninineded";
                        $data['card'] = "card_jkjkcndnbndmd";
                        $headers = ['Idempotency-Key:' . rand(1234, 123465) ];
                        $curl = self::stripeExecuteCurlRequestCharge($url, $data, $headers);
                        if ($curl->success) {
                                $result->success = true;
                                $result->id = $curl->stripe_result->id;
                        }
                        else {
                                $result->success = false;
                                $result->message = $curl->stripe_result->error->message;
                                throw new Exception('stripe account details not found.');
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe transaction error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeAddBankAccount()
        {
                $result = new stdClass();
                $result->success = false;
                $accountId = "acc_jknjsdncjududdd";
                try {
                        $url = 'https://api.stripe.com/v1/accounts/' . $accountId;
                        $data['bank_account'] = 'ba_1Dxm7b2eZvKYlo2C2mY6ZA9Y'$curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe transaction error: ' . $e->getMessage());
                }

                return $result;
        }

        public static function stripeTransferSchedule()
        {
                $result = new stdClass();
                $result->success = false;
                $accountId = "acc_jknjsdncjududdd";
                try {
                        $url = 'https://api.stripe.com/v1/accounts/' . $accountId;
                        $data['transfer_schedule']['interval'] = 'weekly';
                        $data['transfer_schedule']['weekly_anchor'] = 'saturday';
                        $curl = self::stripeExecuteCurlRequest($url, $data);
                        if ($curl->success) {
                                $result->success = true;
                                $result->transfer_schedule = $data['transfer_schedule']['interval'] . '-' . $data['transfer_schedule']['weekly_anchor'];
                        }
                }

                catch(Exception $e) {
                        $result->success = false;
                        Log::info('Stripe transaction error: ' . $e->getMessage());
                }

                return $result;
        }
}



  

 
