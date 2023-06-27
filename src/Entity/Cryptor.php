<?php

namespace App\Entity;

class Cryptor
{
    private $cipher_algo;
    private $hash_algo;
    private $iv_num_bytes;

    private $format;

    const FORMAT_RAW = 0;
    const FORMAT_B64 = 1;
    const FORMAT_HEX = 2;

    public function __construct($cipher_algo = 'aes-256-ctr', $hash_algo = 'sha256', $fmt = Cryptor::FORMAT_B64)
    {
        $this->cipher_algo = $cipher_algo;
        $this->hash_algo = $hash_algo;
        $this->format = $fmt;

        if (!in_array($cipher_algo, openssl_get_cipher_methods(true)))
        {
            throw new \Exception("Cryptor:: - unknown cipher algo {$cipher_algo}");
        }

        if (!in_array($hash_algo, openssl_get_md_methods(true)))
        {
            throw new \Exception("Cryptor:: - unknown hash algo {$hash_algo}");
        }

        $this->iv_num_bytes = openssl_cipher_iv_length($cipher_algo);
    }


    /**
     * @return mixed
     */
    public function getCipherAlgo()
    {
        return $this->cipher_algo;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getHashAlgo()
    {
        return $this->hash_algo;
    }

    /**
     * @return mixed
     */
    public function getIvNumBytes()
    {
        return $this->iv_num_bytes;
    }

    /**
     * @param mixed $cipher_algo
     */
    public function setCipherAlgo($cipher_algo): void
    {
        $this->cipher_algo = $cipher_algo;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format): void
    {
        $this->format = $format;
    }

    /**
     * @param mixed $hash_algo
     */
    public function setHashAlgo($hash_algo): void
    {
        $this->hash_algo = $hash_algo;
    }

    /**
     * @param mixed $iv_num_bytes
     */
    public function setIvNumBytes($iv_num_bytes): void
    {
        $this->iv_num_bytes = $iv_num_bytes;
    }
}