<?php

namespace App\Service;

use App\Entity\BanquePartenaire;
use App\Entity\Currencies;
use App\Entity\CurrencyConverter;
use App\Entity\FraisOperations;
use App\Entity\Pays;
use App\Entity\User;
use App\Entity\UserBank;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class CommonService
{
    private $cryptoService;
    private $entityManager;
    private $parameterBag;

    // ToDo - Add Key In Configuration File
    public const ENCRYPT_KEY = '9901:io=[<>602vV03&Whb>9J&M~Oq';
    private $ciphering = 'AES-128-CTR';
    private $cryption_key = 'GeeksforGeeks';
    private $options = 0;
    private $cryption_iv = '1234567891011121';

    public function __construct(CryptoService $cryptoService, EntityManagerInterface $entityManager, ParameterBagInterface $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
        $this->cryptoService = $cryptoService;
    }

    /**
     * @throws \Exception
     */
    public function encryptPass(string $password)
    {
        return $this->cryptoService::Encrypt($password, self::ENCRYPT_KEY);
    }

    /**
     * @throws \Exception
     */
    public function decryptPass(string $password): string
    {
        return $this->cryptoService::Decrypt($password, self::ENCRYPT_KEY);
    }

    public function getRamdomValue($n): string
    {
        $characters = '0123456789';
        $randomString = '';

        for ($i = 0; $i < $n; ++$i) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public function getDay($today): string
    {
        return date('d', strtotime($today));
    }

    public function getCurrentYear(): string
    {
        return date('y');
    }

    public function getMonth($today): string
    {
        return date('m', strtotime($today));
    }

    public function getDateFin($dateDebut): string
    {
        $timestamp1 = strtotime($dateDebut);
        $timestamp2 = $timestamp1 + (60 * 60 * 24 * 30); // Ajout de 30jours

        return date('Y-m-d H:i:s', $timestamp2);
    }

    public function getNewBPayReference(): ?string
    {
        return self::generateReference('BPAY');
    }

    public static function generateReference(string $prefix = ''): ?string
    {
        return $prefix.(time() + rand(1, 1000));
    }

    public function generateBPayLinkReference(): ?string
    {
        return self::generateReference('PAY');
    }

    public function generateECashAccountNumber(): ?string
    {
        return self::generateReference();
    }

    public function generateValidationCode(): ?string
    {
        $validationCode = str_pad((string) random_int(0000, 9999), 4, '0', STR_PAD_RIGHT);

        return (string) $validationCode;
    }

    public function generateBPayPaiementLinkCode(): ?string
    {
        $validationCode = str_pad((string) random_int(0000, 9999), 10, '0', STR_PAD_RIGHT);

        return (string) $validationCode;
    }

    public function getFraisOperationsActiveDefault(): ?FraisOperations
    {
        return $this->entityManager->getRepository(FraisOperations::class)->findOneBy([
            'active' => 1,
        ]);
    }

    public function getBanquePartenaires(?Pays $pays, ?bool $isActive): ?array
    {
        return $this->entityManager->getRepository(BanquePartenaire::class)->filteredPartenaireBanque($pays, $isActive);
    }

    public function getUserBankAccount(User $user): ?array
    {
        return $this->entityManager->getRepository(UserBank::class)->findBy([
            'user' => $user,
        ]);
    }

     public function calculateOperationPrice(string $operationType, float $amount, &$frais): void
     {
         $fraisOperationConfig = $this->getFraisOperationsActiveDefault();
         switch ($operationType) {
             case constants::FRAIS_INTER_BPAY:
                 if (!empty($fraisOperationConfig->getFraisInterEcash()) && $fraisOperationConfig->getFraisInterEcash() > 0) {
                     $frais = ($amount * ($fraisOperationConfig->getFraisInterEcash() / 100));
                 }
                 break;
             case constants::FRAIS_BPAY_VERS_SOUS_COMPTE:
                 if (!empty($fraisOperationConfig->getFraisEcashVersSousCompte()) && $fraisOperationConfig->getFraisEcashVersSousCompte() > 0) {
                     $frais = ($amount * ($fraisOperationConfig->getFraisEcashVersSousCompte() / 100));
                 }
                 break;
             case constants::FRAIS_BPAY_BANK:
                 if (!empty($fraisOperationConfig->getFraisEcashBank()) && $fraisOperationConfig->getFraisEcashBank() > 0) {
                     $frais = ($amount * ($fraisOperationConfig->getFraisEcashBank() / 100));
                 }
                 break;
             case constants::FRAIS_INTER_BANK:
                 if (!empty($fraisOperationConfig->getFraisInterBank()) && $fraisOperationConfig->getFraisInterBank() > 0) {
                     $frais = ($amount * ($fraisOperationConfig->getFraisInterBank() / 100));
                 }
                 break;
             default:
                 break;
         }
     }

    public function currencyConverter(string $fromCur, string $toCur, float $amount, float &$amountConverted)
    {
        $findCurrencyFrom = $this->entityManager->getRepository(Currencies::class)->findOneBy([
            'currencyIsoCode' => $fromCur,
        ]);
        $findCurrencyTo = $this->entityManager->getRepository(Currencies::class)->findOneBy([
            'currencyIsoCode' => $toCur,
        ]);
        if (empty($findCurrencyFrom)) {
            throw new \Exception('Devise source inconnu');
        }
        if (empty($findCurrencyTo)) {
            throw new \Exception('Devise destination inconnu');
        }

        $findCurrencyConverterEntry = $this->entityManager->getRepository(CurrencyConverter::class)->findOneBy([
            'fromCurrency' => $findCurrencyFrom,
            'toCurrency' => $findCurrencyTo,
        ]);

        if (empty($findCurrencyConverterEntry)) {
            throw new \Exception('Entry currency converter for this operation not found');
        }

        $amountConverted = $amount * $findCurrencyConverterEntry->getFromCurUnitConverter();
    }

    public function getImageBase64(string $image, string $storageFolder)
    {
        $base64 = null;
        $destination = $this->parameterBag->get($storageFolder);

        if (!empty($image)) {
            $fileSystem = new Filesystem();
            $fullPath = $destination.$image;
            if ($fileSystem->exists($fullPath)) {
                $img = file_get_contents($fullPath);
                // Encode the image string data into base64
                $base64 = base64_encode($img);
            }
        }

        return $base64;
    }
}
