<?php

class Syncsms
{
    private $api_key = '';
    private $service = '';
    private $whatsapp = '';
    private $device = '';
    private $gateway = '';
    private $sim = '';

    public function __construct($api_key = null, $service = null, $whatsapp = null, $device = null, $gateway = null, $sim = null)
    {
        $this->api_key = trim($api_key);
        $this->service = trim($service);
        $this->whatsapp = trim($whatsapp);
        $this->device = trim($device);
        $this->gateway = trim($gateway);
        $this->sim = trim($sim);
    }

    public function sendSMS($phone, $message)
    {
        if(empty($this->service) || $this->service < 2):
            if(!empty($this->device)):
                $mode = "devices";
            else:
                $mode = "credits";
            endif;

            if($mode == "devices"):
                $params = [
                    "secret" => $this->api_key,
                    "mode" => "devices",
                    "device" => $this->device,
                    "phone" => $phone,
                    "message" => $message,
                    "sim" => $sim < 2 ? 1 : 2
                ];
            else:
                $params = [
                    "secret" => $this->api_key,
                    "mode" => "credits",
                    "gateway" => $this->gateway,
                    "phone" => $phone,
                    "message" => $message
                ];
            endif;

            $apiurl = "https://syncsms.net/api/send/sms";
        else:
            $params = [
                "secret" => $this->api_key,
                "account" => $this->whatsapp,
                "type" => "text",
                "recipient" => $phone,
                "message" => $message
            ];

            $apiurl = "https://syncsms.net/api/send/whatsapp";
        endif;

        return $this->invokeApi($params, $apiurl);
    }

    private function invokeApi($params = array(), $apiurl)
    {
        $params = array_merge($params, array('key' => $this->api_key));

        $rest_request = curl_init();

        $query_string = '';
        foreach ($params as $parameter_name => $parameter_value) {
            $query_string .= '&'.$parameter_name.'='.urlencode($parameter_value);
        }
        $query_string = substr($query_string, 1);

        curl_setopt($rest_request, CURLOPT_URL, $apiurl . '?' . $query_string);
        curl_setopt($rest_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rest_request, CURLOPT_SSL_VERIFYPEER, false);
        $rest_response = curl_exec($rest_request);

        if ($rest_response === false) {
            throw new Exception('curl error: ' . curl_error($rest_request));
        }

        curl_close($rest_request);

        return $rest_response;
    }
}
