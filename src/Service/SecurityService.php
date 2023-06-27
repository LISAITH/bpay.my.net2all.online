<?php

namespace App\Service;

use App\Entity\CompteBPay;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Entity\User;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;

class SecurityService
{
    private $entityManager;
    private $businessService;

    public function __construct(EntityManagerInterface $entityManager, BusinessService $businessService, CommonService $commonService)
    {
        $this->entityManager = $entityManager;
        $this->businessService = $businessService;
    }

/*    public function getUserByProfil(User $user, string &$profil = null): ?object
    {
        $member = null;

        switch (\strtoupper($user->getType()->getSysName())) {
            case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                $member = $this->entityManager->getRepository(Particuliers::class)->findOneBy([
                    'user' => $user,
                ]);
                $profil = constants::USER_TYPE_SYS_NAME_PARTICULIER;
                break;
            case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                $member = $this->entityManager->getRepository(Entreprises::class)->findOneBy([
                    'user' => $user,
                ]);
                $profil = constants::USER_TYPE_SYS_NAME_ENTREPRISE;
                break;
            case constants::USER_TYPE_SYS_SYS_NAME_PARTENAIRE:
                $member = $this->entityManager->getRepository(Partenaire::class)->findOneBy([
                    'user' => $user,
                ]);
                $profil = constants::USER_TYPE_SYS_SYS_NAME_PARTENAIRE;
                break;
            case constants::USER_TYPE_SYS_NAME_VENDOR:
                $member = $this->entityManager->getRepository(PointVente::class)->findOneBy([
                    'user' => $user,
                ]);
                break;
            default:
                break;
        }

        return $member;
    }

    public function getUserByIdentifier(string $emailAddress): ?User
    {
        if (empty($emailAddress)) {
            throw new \Exception('User email could\'nt be empty');
        }

        return $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailAddress,
        ]);
    }

    public function getUserById(string $id): ?User
    {
        if (empty($id)) {
            throw new \Exception('User email could\'nt be empty');
        }

        return $this->entityManager->getRepository(User::class)->findOneBy([
            'id' => $id,
        ]);
    }*/

    /**
     * @throws \Exception
     */
    public function getUserECashAccount(User $user): ?CompteBPay
    {
        return $this->businessService->getECashAccountByUser($user);
    }
}
