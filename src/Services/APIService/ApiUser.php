<?php

namespace App\Services\APIService;

use App\Entity\CompteBPay;
use App\Entity\Entreprises;
use App\Entity\Particuliers;
use App\Entity\User;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiUser
{
    private $httpClient;
    private $parameterBag;
    private $entityManager;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
    }

    public function getUserByEmailAddress(string $emailAddress): ?User
    {
        $user = new User();

        return $user;
    }

    public function getUserByCompteBPay(CompteBPay $compteBPay): ?object
    {
        $userFind = null;
        $user = new User();
        try {
            if (empty($compteBPay)) {
                return null;
            }
            if (!empty($compteBPay->getParticulierId())) {
                $userFind = $this->getParticular($compteBPay->getParticulierId());
            } elseif (!empty($compteBPay->getEntrepriseId())) {
                $userFind = $this->getParticular($compteBPay->getEntrepriseId());
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $userFind;
    }

    public function getUserAccountBPay(User $user): ?CompteBPay
    {
        $compte = new CompteBPay();
        try {
            if (empty($user) || empty($user->getType())) {
                return null;
            }
            switch ($user->getType()) {
                case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                    $compte = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                        'entreprise_id' => $user->getEntrepriseId(),
                    ]);
                    break;
                case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                    $compte = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                        'particulier_id' => $user->getParticulierId(),
                    ]);
                    break;
                default:
                    throw new \Exception('Type is required');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $compte;
    }

    public function getUserById(int $user_id): ?User
    {
        $user = new User();
        try {

            $baseUri = $this->parameterBag->get('API_URL').'/users/'.$user_id;

            $response = $this->httpClient->request('GET', $baseUri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            if (200 == $response->getStatusCode()) {
                $decodeUser = json_decode($response->getContent(true));

                if (empty($decodeUser)) {
                    return null;
                }
                $user->setEmail($decodeUser->email);
                $user->setNumero($decodeUser->numero);
                $user->setImageUrl($decodeUser->image_url ?? '');
                $user->setStatus($decodeUser->status ?? '');
                $user->setRoles($decodeUser->roles ?? '');
                $user->setType(\strtoupper($decodeUser->type->sys_name));
                $user->setId($decodeUser->id);
                switch (\strtoupper($decodeUser->type->sys_name)) {
                    case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                        $user->setEntrepriseId($decodeUser->entreprises[0]->id);
                        break;
                    case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                        $user->setParticulierId($decodeUser->particuliers[0]->id);
                        break;
                    default:
                        throw new \Exception('Type is required');
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $user;
    }

    public function getUserByProfilV2(User $getUser): ?object
    {
        $userFind = null;
        $user = new User();
        try {
            if (empty($getUser) || empty($getUser->getType())) {
                return null;
            }
            switch ($getUser->getType()) {
                case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                    $userFind = $this->getEntreprise($getUser->getEntrepriseId());
                    break;
                case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                    $userFind = $this->getParticular($getUser->getParticulierId());
                    break;
                default:
                    throw new \Exception('Type is required');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $userFind;
    }

    public function getUserByProfil(int $userId, string &$profil): ?object
    {
        $userFind = null;
        $user = new User();
        try {
            $getUser = $this->getUserById($userId);
            if (empty($getUser) || empty($getUser->getType())) {
                return null;
            }
            switch ($getUser->getType()) {
                case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                    $profil = constants::USER_TYPE_SYS_NAME_ENTREPRISE;
                    $userFind = $this->getEntreprise($getUser->getEntrepriseId());
                    break;
                case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                    $profil = constants::USER_TYPE_SYS_NAME_PARTICULIER;
                    $userFind = $this->getParticular($getUser->getParticulierId());
                    break;
                default:
                    throw new \Exception('Type is required');
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $userFind;
    }

    public function getEntreprise(int $id): ?Entreprises
    {
        $entreprise = new Entreprises();
        try {
            $baseUri = $this->parameterBag->get('API_URL').'/entreprises/'.$id;
            $response = $this->httpClient->request('GET', $baseUri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            if (200 == $response->getStatusCode()) {
                $decodeUser = json_decode($response->getContent(true));
                $entreprise->setNomEntreprise($decodeUser->nom_entreprise);
                $entreprise->setPrenoms($decodeUser->prenoms);
                $entreprise->setNom($decodeUser->nom);
                $entreprise->setId($decodeUser->id);
                $entreprise->setUrlImage($decodeUser->url_image);
                $entreprise->setNumTelephone($decodeUser->num_tel);
                $entreprise->setPays($decodeUser->pays);
            }
        } catch (\Exception $e) {
            return null;
        }

        return $entreprise;
    }

    public function getParticular(int $id): ?Particuliers
    {
        $particulier = new Particuliers();
        try {
            $baseUri = $this->parameterBag->get('API_URL').'/particuliers/'.$id;
            $response = $this->httpClient->request('GET', $baseUri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            if (200 == $response->getStatusCode()) {
                $decodeUser = json_decode($response->getContent(true));
                $particulier->setPrenom($decodeUser->prenoms);
                $particulier->setNom($decodeUser->nom);
                $particulier->setNumTel($decodeUser->num_tel);
                $particulier->setId($decodeUser->id);
                $particulier->setPays($decodeUser->pays);
            }
        } catch (\Exception $e) {
            return null;
        }


        return $particulier;
    }
}