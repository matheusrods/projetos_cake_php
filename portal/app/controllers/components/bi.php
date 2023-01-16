<?php

App::import('Component', 'StringView');

App::import('Helper', 'Javascript');

App::import('Helper', 'Buonny');

class BiComponent
{

    private $groupId;

    private $reportId;

    private $dataSet;

    private $reportUrl;

    private $tokenPwBi;

    private $chavesPw;

    private $tokenChavesPw;

    private $role;

    private $codigoUsuarioLogado;

    private $dadosPost;

    private $error;

    private $Javascript;

    private $biLinkJs;

    private $biCodeblockJs;

    private $reportDatasetMap = array(
        '1f99d27e-a5d4-4b66-8f93-22dc8422dd61' => 'b83379b4-7929-4c6b-8105-768e8928cece',
        '9c9afe77-b54b-4b0b-91cb-57946504aaa7' => 'da081283-9cc1-4c1e-928f-10b590adfc75',
        '1ad25c94-6b01-43a5-8245-6308d2b8322d' => '4d954bd0-590c-4380-9b4c-b5a341317623'
    );

    const GROUP_ID = '22715f02-063f-4c43-b11d-278afa3e3e3f';
    const BASE_URL = 'https://api.powerbi.com/v1.0/myorg/';
    const EMBED_URL = 'https://app.powerbi.com/reportEmbed';
    const TOKEN_URL = 'https://login.microsoftonline.com/d87506ea-36fa-43b0-bd0f-4631c99d847a/oauth2/v2.0/token';
    const COOKIE_FPC = 'AkGpFmeiNmZEqbhgBekNdWVEsycAAQAAAP6CptYOAAAA';
    const CLIENT_ID = 'b5410605-8cce-4ea0-bcad-9f8ac7290350&';
    const CLIENT_SECRET = '3cEz2bUR.ijQgmThm-km0Ij076%7E%7EwctE9B';
    //https://api.powerbi.com/v1.0/myorg/groups/22715f02-063f-4c43-b11d-278afa3e3e3f/reports/{$reportId}/GenerateToken

    public function __construct($reportId = null, $dadosUsuario = null,$filters=false,$pageNavigation = false)
    {

        $this->groupId = self::GROUP_ID;

        if (!empty($reportId) && !empty($dadosUsuario)) {

            $this->codigoUsuarioLogado = $dadosUsuario['Usuario']['codigo'];

            $this->role = $this->resolveRole($reportId, $dadosUsuario);

            $this->setReportId($reportId);

            $this->Javascript = new JavascriptHelper();

            $this->style = '
            <style>
                .container {
                    width: 100% !important;
                    height: 100% !important;
                }

                body {
                    overflow: hidden !important;
                }
            </style>            
            ';
            $this->biLinkJs = '<script type="text/javascript" src="/portal/js/powerbi/powerbi.' . strtotime(date('Y-m-d')) . '.js"></script>';
            $this->embedContainer = '<div style="border: none; width:100%;min-height:100%; height:calc(100vh - 80px);" id="embedContainer"></div>';

            $this->resolveReport($filters,$pageNavigation);
        }
    }

    public function resolveRole($reportId, $dadosUsuario)
    {

        if (empty($dadosUsuario['Usuario']['codigo_cliente'])) {

            return 'usuario_interno';
        } elseif ($dadosUsuario['Usuario']['codigo_cliente'] == '94896' && $reportId == '9c9afe77-b54b-4b0b-91cb-57946504aaa7') {

            return 'usuario_externo_resolv';
        }

        return 'usuario_externo';
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        if (!empty($this->reportId)) {

            $this->setReportUrl(self::BASE_URL . 'groups/' . $this->groupId . '/reports/' . $this->reportId . '/GenerateToken');
        }

        return $this;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setReportId($reportId)
    {
        try {

            if (empty($this->reportDatasetMap[$reportId]))
                throw new Exception('Não existe dataset mapeado para este relatório');

            $this->reportId = $reportId;

            $this->dataSet = $this->reportDatasetMap[$reportId];

            $this->dadosPost = '{
                "accessLevel": "Edit",
                "identities": [
                    {
                        "username": "' . $this->codigoUsuarioLogado . '",
                        "roles": [
                            "' . $this->role . '"
                        ],
                        "datasets": [
                            "' . $this->dataSet . '"
                        ]
                    }
                ]
            }';

            $this->setReportUrl(self::BASE_URL . 'groups/' . $this->groupId . '/reports/' . $this->reportId . '/GenerateToken');

            return $this;
        } catch (Exception $e) {

            $this->error = $e->getMessage();
        }
    }

    public function getReportId()
    {
        return $this->reportId;
    }

    public function getReportUrl()
    {
        return $this->reportUrl;
    }

    private function setReportUrl($reportUrl)
    {
        $this->reportUrl = $reportUrl;

        return $this;
    }

    public function setRole($role)
    {
        $this->role = $role;

        if (!empty($this->dataSet)) {
            $this->dadosPost = '{
                "accessLevel": "Edit",
                "identities": [
                    {
                        "username": "' . $this->codigoUsuarioLogado . '",
                        "roles": [
                            "' . $this->role . '"
                        ],
                        "datasets": [
                            "' . $this->dataSet . '"
                        ]
                    }
                ]
            }';
        }

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setCodigoUsuarioLogado($codigoUsuarioLogado)
    {
        $this->codigoUsuarioLogado = $codigoUsuarioLogado;

        if (!empty($this->dataSet)) {

            $this->dadosPost = '{
                "accessLevel": "Edit",
                "identities": [
                    {
                        "username": "' . $this->codigoUsuarioLogado . '",
                        "roles": [
                            "' . $this->role . '"
                        ],
                        "datasets": [
                            "' . $this->dataSet . '"
                        ]
                    }
                ]
            }';
        }

        return $this;
    }

    public function getCodigoUsuarioLogado()
    {
        return $this->codigoUsuarioLogado;
    }

    public function getDadosPost()
    {
        return $this->dadosPost;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getBiLinkJs()
    {
        return $this->biLinkJs;
    }

    public function getBiCodeblockJs()
    {
        return $this->biCodeblockJs;
    }

    public function getTokenPwBi($return = false)
    {

        try {
            $curl = curl_init();

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => self::TOKEN_URL,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=https%3A//analysis.windows.net/powerbi/api/.default&client_id=" . self::CLIENT_ID . "&client_secret=" . self::CLIENT_SECRET,
                    CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/x-www-form-urlencoded",
                        "Cookie: fpc=" . self::COOKIE_FPC . "; x-ms-gateway-slice=prod; stsservicecookie=ests"
                    ),
                )
            );

            $responseJson = curl_exec($curl);

            curl_close($curl);

            $responseDataObj = json_decode($responseJson);

            if (empty($responseDataObj->access_token))
                throw new Exception('Não foi possível obter o token do Power BI');

            $this->tokenPwBi = $responseDataObj->access_token;

            if ($return)
                return $responseDataObj->access_token;
        } catch (Exception $e) {

            $this->error = $e->getMessage();

            return false;
        }
    }

    public function getChavesPw($return = false)
    {

        $token = $this->getTokenPwBi(true);

        $curl = curl_init();

        $curl_options = array(
            CURLOPT_URL => $this->reportUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->dadosPost,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ),
        );

        curl_setopt_array(
            $curl,
            $curl_options
        );

        $responseJson = curl_exec($curl);

        curl_close($curl);
        $responseDataObj = json_decode($responseJson);

        $this->chavesPw = $responseDataObj;

        $this->tokenChavesPw = $responseDataObj->token;

        if ($return)
            return $responseDataObj;
    }

    public function resolveReport($filters = false,$pageNavigation = false)
    {
        try {

            if (empty($this->reportId))
                throw new Exception('O ID do relatório não foi informado.');

            $token = $this->getChavesPw(true);

            if (!empty($this->chavesPw->error)) {
                throw new Exception('Não foi possível obter chaves do Power BI: ' . $this->chavesPw->error->message);
            }

            $this->biCodeblockJs = $this->Javascript->codeBlock('
                jQuery(document).ready(function() {                    
                    
                    var accessToken = "' . $this->tokenChavesPw . '";
            
                    var embedUrl = "' . self::EMBED_URL . '?reportId=' . $this->reportId . '&groupId=' . $this->groupId . '&w=2&config=eyJjbHVzdGVyVXJsIjoiaHR0cHM6Ly9XQUJJLUJSQVpJTC1TT1VUSC1CLVBSSU1BUlktcmVkaXJlY3QuYW5hbHlzaXMud2luZG93cy5uZXQiLCJlbWJlZEZlYXR1cmVzIjp7Im1vZGVybkVtYmVkIjp0cnVlfX0%3d";
            
                    var embedReportId = "' . $this->reportId . '";
            
                    var models = window["powerbi-client"].models;
                    
                    var config = {
                        type: "report",
                        tokenType: models.TokenType.Embed,
                        accessToken: accessToken,
                        embedUrl: embedUrl,
                        id: embedReportId,
                        permissions: models.Permissions.All,
                        settings: {
                            panes: {
                                filters: {                
                                    visible: '.(($filters)?'true':'false').'                
                                },                
                                pageNavigation: {                
                                    visible: '.(($pageNavigation)?'true':'false').'                
                                }                
                            }
                        }
                    };
            
                    // Get a reference to the embedded report HTML element
                    // var reportContainer = $(\'#embedContainer\')[0];
                    var reportContainer = document.getElementById("embedContainer");
            
                    // Embed the report and display it within the div container.
                    var report = powerbi.embed(reportContainer, config);                    
                        
                });
            ');
        } catch (Exception $e) {

            $this->error = $e->getMessage();
            debug($this->error);

            return false;
        }
    }

    public function render()
    {

        if (!empty($this->error))
            return '<div class="bi-error">' . $this->error . '</div>';

        return $this->style . '
        ' . $this->biLinkJs . '
        ' . $this->biCodeblockJs . '
        ' . $this->embedContainer;
    }
}
