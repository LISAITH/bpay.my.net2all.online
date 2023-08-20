<?php

namespace App\Services;

class AppServices
{
    public function getBpayServerAddress()
    {
        $bpayServerAddress = $_ENV['bpayLink'];
        return $bpayServerAddress;
    }

    public function getBusinesServerAddress()
    {
        $buninesServerAddress = $_ENV['businessLink'];
        return $buninesServerAddress;
    }

    public function getMyServerAddress()
    {
        $myServerAddress = $_ENV['myNet2AllLink'];
        return $myServerAddress;
    }
}