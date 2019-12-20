<?php

namespace App\Utils;

class AjaxJSON {

    private $headers = [];

    private $data = [];

    public function __construct()
    {
        $this->headers[] = 'Content-Type: application/json';
    }

    public function setHeaders(Array $headers)
    {
        foreach ($headers as $index => $value) {
            if ($index != 'Content-Type') {
                $this->headers[] = "$index: $value";
            }
        }
    }

    public function setData(Array $data)
    {
        $this->data = $data;
    }

    public function get(String $url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $res = curl_exec($ch);

        curl_close($ch);

        return (Object) json_decode($res);
    }

    public function post(String $url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $res = curl_exec($ch);

        curl_close($ch);

        return (Object) json_decode($res);
    }

}
