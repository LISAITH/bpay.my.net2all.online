<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\Filesystem\Filesystem;

class QrCodeService
{
    public function generateQrCodeDataUri(string $qrCode): ?string
    {
        $writer = new PngWriter();
        try {
            if (empty($qrCode)) {
                throw new \Exception('QrCode can\'t empty');
            }
            $qrCodeGenerator = self::generateQrCode($qrCode);
            $result = $writer->write($qrCodeGenerator);

            return $result->getDataUri();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public static function generateQrCode(string $qrCode): QrCode
    {
        return QrCode::create($qrCode)
              ->setEncoding(new Encoding('UTF-8'))
              ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
              ->setSize(250)
              ->setMargin(10)
              ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
              ->setForegroundColor(new Color(0, 0, 0))
              ->setBackgroundColor(new Color(255, 255, 255));
    }

    public function generateQrCodeSaveInPath(string $qrCode, string $fileName, string $path)
    {
        $writer = new PngWriter();
        try {
            if (empty($qrCode) || empty($path)) {
                throw new \Exception('QrCode or savingPath can\'t empty');
            }
            $qrCodeGenerator = self::generateQrCode($qrCode);
            $result = $writer->write($qrCodeGenerator);
            $destination = sprintf('%s/%s', $path, $fileName.'.png');
            $fileSystem = new Filesystem();
            if ($fileSystem->exists($destination)) {
                $fileSystem->remove($destination);
            }
            $result->saveToFile(sprintf('%s/%s', $path, $fileName.'.png'));

            return $destination;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}