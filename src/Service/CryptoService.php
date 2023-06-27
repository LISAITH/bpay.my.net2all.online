<?php

namespace App\Service;

use App\Entity\Cryptor;

class CryptoService
{
    private static function encryptString($in, $key, $fmt = null)
    {
        $crypto = new Cryptor();
        if (!empty($fmt)) {
            $crypto->setFormat($fmt);
        }

        // Build an initialisation vector
        $iv = openssl_random_pseudo_bytes($crypto->getIvNumBytes(), $isStrongCrypto);
        if (!$isStrongCrypto) {
            throw new \Exception('Cryptor::encryptString() - Not a strong key');
        }

        // Hash the key
        $keyhash = openssl_digest($key, $crypto->getHashAlgo(), true);

        // and encrypt
        $opts = OPENSSL_RAW_DATA;
        $encrypted = openssl_encrypt($in, $crypto->getCipherAlgo(), $keyhash, $opts, $iv);

        if (false === $encrypted) {
            throw new \Exception('Cryptor::encryptString() - Encryption failed: '.openssl_error_string());
        }

        // The result comprises the IV and encrypted data
        $res = $iv.$encrypted;

        // and format the result if required.
        if (Cryptor::FORMAT_B64 == $fmt) {
            $res = base64_encode($res);
        } elseif (Cryptor::FORMAT_HEX == $fmt) {
            $res = unpack('H*', $res)[1];
        }

        return $res;
    }

    /**
     * Decrypt a string.
     *
     * @param string $in  string to decrypt
     * @param string $key decryption key
     * @param int    $fmt Optional override for the input encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     *
     * @return string the decrypted string
     */
    private static function decryptString($in, $key, $fmt = null)
    {
        $crypto = new Cryptor();
        if (!empty($fmt)) {
            $crypto->setFormat($fmt);
        }

        $raw = $in;

        // Restore the encrypted data if encoded
        if (Cryptor::FORMAT_B64 == $fmt) {
            $raw = base64_decode($in);
        } elseif (Cryptor::FORMAT_HEX == $fmt) {
            $raw = pack('H*', $in);
        }

        // and do an integrity check on the size.
        if (strlen($raw) < $crypto->getIvNumBytes()) {
            throw new \Exception('Cryptor::decryptString() - data length '.strlen($raw)." is less than iv length {$crypto->getIvNumBytes()}");
        }

        // Extract the initialisation vector and encrypted data
        $iv = substr($raw, 0, $crypto->getIvNumBytes());
        $raw = substr($raw, $crypto->getIvNumBytes());

        // Hash the key
        $keyhash = openssl_digest($key, $crypto->getHashAlgo(), true);

        // and decrypt.
        $opts = OPENSSL_RAW_DATA;
        $res = openssl_decrypt($raw, $crypto->getCipherAlgo(), $keyhash, $opts, $iv);

        if (false === $res) {
            throw new \Exception('Cryptor::decryptString - decryption failed: '.openssl_error_string());
        }

        return $res;
    }

    /**
     * Static convenience method for encrypting.
     *
     * @param string $in  string to encrypt
     * @param string $key encryption key
     * @param int    $fmt Optional override for the output encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     *
     * @return string the encrypted string
     */
    public static function Encrypt($in, $key, $fmt = null)
    {
        try {
            return self::encryptString($in, $key, $fmt);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Static convenience method for decrypting.
     *
     * @param string $in  string to decrypt
     * @param string $key decryption key
     * @param int    $fmt Optional override for the input encoding. One of FORMAT_RAW, FORMAT_B64 or FORMAT_HEX.
     *
     * @return string the decrypted string
     */
    public static function Decrypt($in, $key, $fmt = null): string
    {
        try {
            return self::decryptString($in, $key, $fmt);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
