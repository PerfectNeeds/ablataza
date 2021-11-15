<?php

namespace MD\Utils;

/**
 * Description of OAuth
 *
 * @author Peter Soliman
 */
class OAuth {

    public $clientId, $secret, $redirectUri, $scope, $responseType;

    function __construct($clientId, $secret, $redirectUri, $scope, $responseType) {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->redirectUri = $redirectUri;
        $this->scope = $scope;
        $this->responseType = $responseType;
    }

    //returns session token for calls to API using oauth 2.0
    public function get_oauth2_token($code, $oauth2token_url, $type = "f") {

        $clienttoken_post = array(
            "code" => $code,
            "client_id" => $this->clientId,
            "client_secret" => $this->secret,
            "redirect_uri" => $this->redirectUri,
            "grant_type" => "authorization_code"
        );

        $curl = curl_init($oauth2token_url);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $clienttoken_post);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $json_response = curl_exec($curl);
        curl_close($curl);
        $authObj = json_decode($json_response);

        if (isset($authObj->error)) {
            exit($authObj->error->message);
        }

        parse_str($json_response, $authArr);
        if (isset($authObj->refresh_token)) {
            global $refreshToken;
            $refreshToken = $authObj->refresh_token;
            $accessToken = $authObj->access_token;
        }
        if (isset($authArr['access_token'])) {
            $accessToken = $authArr['access_token'];
        }
        return $accessToken;
    }

    //calls api and gets the data
    public function call_api($accessToken, $url, $scope, $type = "f") {
        if ($type == "f") {
            $url = $url . "?access_token=" . $accessToken;
        }
        if ($scope != '' AND $type == "f") {
            $url .= "&fields=" . $scope;
        }

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $curlheader[0] = "Authorization: Bearer " . $accessToken;
        curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

        $json_response = curl_exec($curl);
        curl_close($curl);
        if ($type == "f") {
            parse_str($json_response, $responseObj);
        } else {
            $responseObj = json_decode($json_response);
        }
        $responseObj = json_decode($json_response);
        return $responseObj;
    }

}

?>
