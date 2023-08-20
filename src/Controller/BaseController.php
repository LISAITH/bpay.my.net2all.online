<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Services\AppServices;

class BaseController extends AuthController
{
    private $ciphering = 'AES-128-CTR';
    private $cryption_key = 'GeeksforGeeks';
    private $options = 0;
    private $cryption_iv = '1234567891011121';
    private $apiGetUser = '/api/users/';
    private $oneApiService = '/api/api_services/one/';
    private $apiGetEnseigne = '/api/enseignes/';
    private AppServices $appServices;

    public function __construct(AppServices $appServices)
    {
        $this->appServices = $appServices;
    }

    /**
     * @Route("/welo", name="welo")
     */
    public function welo(Request $request, HttpClientInterface $client): Response
    {

        $session = $request->getSession();
        $appid = $request->query->get('id');
        $userid = $request->query->get('querer');
        $session->set('querer', $userid);
        $session->set('id', $appid);

        $this->getUserInfo($appid, $userid, $client, $request);

        return $this->redirectToRoute('account_secret');
    }

    protected function getUserInfo($appid, $userid, $client, $request)
    {
        $url_enseigne_img = '';
        $this->isGranted = false;
        $session = $request->getSession();
        $userE = [];
        // Verifier si l'utilisateur existe

        $checkUser = $this->ClientRequest(
            $request,
            $client,
            'GET',
            $this->appServices->getBpayServerAddress().$this->apiGetUser.$userid,
            [
                'query' => [],
            ]
        );

        if (count($checkUser) > 0) {
            $userE = array_merge($userE, $checkUser);
        } else {
            dd('user not found');
        }
        //  dd($checkUser);
        $type = $checkUser['type']['id'];

        // Verifier le type de l'utilisateur

        if (1 == $type) {
            $particulier = $checkUser['particuliers'][0];

            if (count($particulier) > 0) {
                $userE = array_merge($userE, $particulier);
            } else {
                dd('particulier not found');
            }
        } elseif (6 == $type) {
            // Verifier si l'enseigne appartient à une entreprise de l'utilisateur
            $enseigne_id = 0;
            foreach ($checkUser['entreprises'] as $entreprise) {
                foreach ($entreprise['enseignes'] as $enseigne) {
                    $enseigne_id = explode('/', $enseigne)[count(explode('/', $enseigne)) - 1];
                    if ($enseigne_id == $appid) {
                        $userE = array_merge($userE, $entreprise);
                        $this->isGranted = true;
                    }
                }
            }
        } else {
            dd('non autorisé');
        }

        if ($this->isGranted) {
            // Verifier si l'enseigne est affilié à MAGMA ERP
            $checkEnseigneAffiliate = $this->ClientRequest(
                $request,
                $client,
                'GET',
                $this->appServices->getBpayServerAddress().$this->oneApiService.$appid.'/1',
                [
                    'query' => [],
                ]
            );
            $content_array = $checkEnseigneAffiliate['data'];
            if (0 != count($content_array)) {
                if (true == $content_array['is_installed']) {
                    $userE = array_merge($userE, $content_array);
                } else {
                    dd('enseigne not affiliate');
                }
            } else {
            }

            // Recuperer les imformations sur l'enseigne
            $checkEnseigneInfo = $this->ClientRequest(
                $request,
                $client,
                'GET',
                $this->appServices->getBpayServerAddress().$this->apiGetEnseigne.$appid,
                [
                    'query' => [],
                ]
            );
            if (count($checkEnseigneInfo) > 0) {
                $userE = array_merge($userE, $checkEnseigneInfo);
                $url_enseigne_img = $checkEnseigneInfo['url_image'];
            } else {
                dd('enseigne not exist');
            }
        } else {
            dd('Acess not enabled to this enseigne');
        }

        $user = $userE;

        // Attribute default value
        if (!array_key_exists('nim', $user)) {
            $user['nim'] = '';
        }

        if (!array_key_exists('type_ifu', $user)) {
            $user['type_ifu'] = '1';
        } else {
            if (null == $user['type_ifu']) {
                $user['type_ifu'] = '1';
            }
        }
        if (!array_key_exists('can_send_sms', $user)) {
            $user['can_send_sms'] = 'on';
        }

        if (!array_key_exists('mode', $user)) {
            $user['mode'] = '';
        }

        if (!array_key_exists('logo', $user)) {
            $user['logo'] = 'default.png';
        }

        if (!array_key_exists('title_sms', $user)) {
            $user['title_sms'] = 'MAGMAERP';
        }

        if (!array_key_exists('phone', $user)) {
            $user['phone'] = '+22998765644';
        }

        $user['url_image'] = $url_enseigne_img;
        // Attribute default value

        $nim = $user['nim'] ?? null;
        if ($nim) {
            $user['nim'] = $this->decrypt($user['nim']);
        }

        $user['api_key'] = $user['api_key'] ?? '';
        $user['mode'] = $user['mode'] ?? '';
        $base_url = $user['baseurl'] ?? '';
        $base_urls = 'https://'.str_replace('https://', '', $base_url).'index.php/';
        $base_urls_path = str_replace('htdocs/api/index.php/', 'documents/', $base_urls);
        $base_urls_pos = str_replace('htdocs/api/index.php/', 'htdocs/takepos/', $base_urls);
        $user['uploader_path'] = $base_urls_path;
        $user['url_pos'] = $base_urls_pos;
        $base_urls_path .= 'produit/fileuploader.php';
        $testurl = $base_urls.'explorer/';

        // dd($user,$valid_link,$testurl);
        $user['base_url_uploader'] = $base_urls_path;
        $user['base_url'] = $base_urls;
        $user['isAuthentificated'] = true;

        $user['id'] = (int) $userid;
        $user['type'] = (int) $type;
        $session->set('currentuser', $user);
    }

    protected function decrypt($encryption)
    {
        $iv_length = openssl_cipher_iv_length($this->ciphering);

        return openssl_decrypt(
            $encryption,
            $this->ciphering,
            $this->cryption_key,
            $this->options,
            $this->cryption_iv
        );
    }

    public function isPack360Active(mixed $user): bool
    {
        $isActive = false;
        if (is_array($user) && isset($user['is_installed'])) {
            $isActive = true;
        }

        return $isActive;
    }

    public function getCurrentUser(Request $request): ?array
    {
        return $request->getSession()->get('currentuser');
    }
}
