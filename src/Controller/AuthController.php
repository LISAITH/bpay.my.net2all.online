<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Services\AppServices;


class AuthController extends AbstractController
{
    private $apiGetUser = '/api/users/';
    private $oneApiService = 'api_services/one/';
    private $apiGetEnseigne = '/api/enseignes/';
    private AppServices $appServices;

    public function __construct(AppServices $appServices)
    {
        $this->appServices = $appServices;
    }

    public function check_authentificated(Request $request)
    {
        $session = $request->getSession();
        $info = ['status' => true];
        if (!$session->get('currentuser')) {
            $url = $this->appServices->getMyServerAddress();
            $info = ['status' => false, 'url' => $url];
        }
        
        // if (!$session->get('currentuser')) {$info = ['status' => false, 'url' => 'https://my.net2all.online'];}

        return $info;
    }
    public function getApiKey(Request $request)
    {
        // return $this->api_key;
        $session = $request->getSession();
        $api_key = $session->get('currentuser')['api_key'] ?? '';

        return $api_key;
    }
    public function getBaseUrl(Request $request)
    {
        // return $this->api_key;
        $session = $request->getSession();
        $base_url = $session->get('currentuser')['base_url'] ?? '';

        return $base_url;
        // return "https://360.n2a.online/dev_enter/htdocs/api/index.php/";
    }

    public function ClientRequest($request, $client, $type, $url, $param)
    {
        $tab = [];
        try {
            $response = $client->request($type, $url, $param);
            $content = $response->getContent(false);

            $content_array = json_decode($content, true);
            // dd($content_array,$response->getStatusCode());
            if (200 == $response->getStatusCode() || 500 == $response->getStatusCode()) {
                $tab = $content_array;
            } else {
                $tab = [];
            }
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }

        return $tab;
    }

    public function ClientRequestContent($request, $client, $type, $url, $param)
    {
        $val = '';
        try {
            $response = $client->request($type, $url, $param);
            $val = $response->getContent(false);
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }

        return $val;
    }

    public function getUserFromApiInfo($appid, $userid, $client, $request): JsonResponse
    {
        $message = '';

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
            $message = 'user not found';
        }

        $type = $checkUser['type']['id'];

        // Verifier le type de l'utilisateur

        if (1 == $type) {
            $particulier = $checkUser['particuliers'][0];

            if (count($particulier) > 0) {
                $userE = array_merge($userE, $particulier);
            } else {
                $message = 'particulier not found';
            }
        } elseif (6 == $type) {
            // Verifier si l'enseigne appartient à une entreprise de l'utilisateur
            $enseigne_id = 0;
            foreach ($checkUser['entreprises'] as $entreprise) {
                foreach ($entreprise['enseignes'] as $enseigne) {
                    $enseigne_id = explode('/', $enseigne)[count(explode('/', $enseigne)) - 1];
                    if ($enseigne_id == $appid) {
                        $userE = array_merge($userE, $entreprise);
                        $this->isGrantedfromApi = true;
                    }
                }
            }
        } else {
            $message = 'non autorisé';
            //  return $this->redirectToRoute('app_login');
        }

        // (((((((((((((((((((((((((((())))))))))))))))))))))))))))

        if ($this->isGrantedfromApi) {
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
                    $message = 'enseigne not affiliate';
                }
            } else {
                $message = 'enseigne not affiliate';
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
                $message = 'enseigne not exist';
            }
        } else {
            $message = 'Acess not enabled to this enseigne';
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

        // $idp= intval($user["id_enseigne"]);
        // dd($this->findcode($idp));
        // $user['api_key'] = $user['api_key']??$this->api_key;

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

        $data = [
            'user' => $user,

            'message' => $message,
        ];

        return $this->json([
            'data' => $data,
        ]);
    }

}