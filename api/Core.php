<?php
    /**
     * Created by PhpStorm.
     * User: user
     * Date: 17.06.2020
     * Time: 16:18
     */
    namespace api;
    use models\Tokens;
    use models\Currency;

    class Core
    {
        private $minfinToken = '';

        private function getAuthorizationHeader(){
            $headers = null;
            if (isset($_SERVER['Authorization'])) {
                $headers = trim($_SERVER["Authorization"]);
            }
            else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
            } elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['Authorization'])) {
                    $headers = trim($requestHeaders['Authorization']);
                }
            }
            return $headers;
        }

        private function getBearerToken() {
            $headers = $this->getAuthorizationHeader();
            if (!empty($headers)) {
                if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                    return $matches[1];
                }
            }
            return null;
        }

        public function checkAuth() {
            $Tokens = new Tokens();
            $currentToken = $this->getBearerToken();
            if ($currentToken !== null) {
                if ($Tokens->checkToken($currentToken))
                    return [1, 'Success auth!'];
                else
                    return [0, 'Bad bearer token!'];
            }
            return [0, 'No bearer token!'];
        }

        public function getNewExRate() {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
               // CURLOPT_URL => "http://api.minfin.com.ua/nbu/$this->minfinToken/",
                CURLOPT_URL => "https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5",
                CURLOPT_USERAGENT => 'CurrencyAPI Request'
            ));
            $response = curl_exec($curl);
            // todo no token minfin(
            curl_close($curl);
            foreach (json_decode($response, true) as $value) {
                //$returnArray[$key] = "продажа - " . $value['bid'] . "/покупка - " . $value['ask'];
                $returnArray[$value['ccy']] = "sale - " . $value['sale'] . "|buy - " . $value['buy'];
            }

            return $returnArray;
        }

        public function updateData() {
            $currency = new Currency();
            $currency->updateCurrency();
        }

        public function getAllCurrencies()
        {
            $currency = new Currency();
            $currencies = $currency->getCurrencies();
            array_push($currencies, $currency->getHistory());

            return $currencies;
        }

        public function getMinfinData($token) {

        }
    }