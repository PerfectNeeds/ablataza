<?php

namespace MD\Utils;

/**
 * Mailer API
 * @author Peter Nassef <peter.nassef@gmail.com>
 */
class Mailer {

    /**
     * $data =  $adminEmail = array(
     *               'subject' => 'SUBJECT',
     *               'from' => 'no-reply@example.com',
     *               'to' => 'marketing@example.com',
     *               'body' => '',
     *               'attach'=>array('encode'=>'', 'ext'=>'') 
     *           );
     * 
     *  if ($attachment) {
     *         $imageFileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
     *         $b64Doc = chunk_split(base64_encode(file_get_contents($_FILES['file']['tmp_name'])));
     *         $adminEmail['attach'] = array('encode' => $b64Doc, 'ext' => $imageFileType);
     *  }
     * 
     * @param type $data
     * @return json
     */
    public static function sendEmail($data) {
        $url = 'http://mailer.perfectneeds.com';

        $curl = curl_init($url);

        $fields_string = http_build_query($data);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $json_response = curl_exec($curl);
        curl_close($curl);
        $authObj = json_decode($json_response);
        return $authObj;
    }

}
