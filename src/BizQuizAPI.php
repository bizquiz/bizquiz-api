<?php

namespace BizQuiz;

use BizQuiz\OAuthHelper;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
*  BizQuiz API
*
*  Main server side API class to communicate with the BizQuiz API.
*
*  @author Martin Wind
*/
class BizQuizAPI {

    private $consumerKey;

    private $sharedSecret;

    private $baseURL;

    private $client;

    /**
     * @param string $consumerKey  consumer key as provided by bizgames
     * @param string $sharedSecret shared secret as provided by bizgames
     * @param string $baseURL      api base url
     */
    public function __construct($consumerKey, $sharedSecret, $baseURL = 'https://bizquiz.cloud/api', $guzzleClientOrOptions = null) {
        $this->consumerKey = trim($consumerKey);
        $this->sharedSecret = trim($sharedSecret);
        $baseURL = trim($baseURL);
        if (substr($baseURL, -1) === '/') {
            $this->baseURL = substr($baseURL, 0, -1);
        } else {
            $this->baseURL = $baseURL;
        }
        if ($guzzleClientOrOptions instanceof ClientInterface) {
            $this->client = $guzzleClientOrOptions;
        } else {
            $this->client = new Client(is_array($guzzleClientOrOptions) ? $guzzleClientOrOptions : []);
        }
    }

    public function request($apiPath, array $options = []) {
        $ops = array_merge([
            'method' => 'GET',
            'time' => time(),
            'userId' => null,
            'userEmail' => null,
        ], $options);

        if (substr($apiPath, 0, 1) == '/') {
            $apiPath = substr($apiPath, 1);
        }

        $url = $this->baseURL . '/' . $apiPath;

        $parameters = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_timestamp' => strval($ops['time']),
            'oauth_nonce' => strval(mt_rand(10000, 99999)),
        ];

        if ($ops['userId']) {
            $parameters['api_user_external_id'] = $ops['userId'];
        } else if ($ops['userEmail']) {
            $parameters['api_user_email'] = $ops['userEmail'];
        } else {
            throw new \Exception('either userId or userEmail is required');
        }

        $parameters['oauth_signature'] = OAuthHelper::getSignature($parameters, $this->sharedSecret, $url, $ops['method']);

        $response = $this->client->request($ops['method'], $url, [
            'query' => $parameters,
        ]);

        return json_decode($response->getBody(), true);
    }

    public function getLTILaunchURL() {
        return $this->baseURL.'/lti';
    }

    public function getLTILaunchParameter($userId, $email = null, $firstname = null, $lastname = null, $resourceLinkId = 'dashboard', $time = null)
    {
        $parameters = [
            // type and verion
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',

            // unique id referencing the link, or "placement", of the app in the consumer. If an app was added twice to the same class, each placement would send a different id, and should be considered a unique "launch". For example, if the provider were a chat room app, then each resource_link_id would be a separate room.
            'resource_link_id' => $resourceLinkId,

            // user id used to uniquely identify the user
            'user_id' => $userId,

            // OAuth paramters
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_timestamp' => empty($time) ? time() : $time,
            'oauth_callback' => 'about:blank',
        ];

        // optional paramters
        if (!empty($firstname)) {
            $parameters['lis_person_name_given'] = $firstname;
        }

        if (!empty($lastname)) {
            $parameters['lis_person_name_family'] = $lastname;
        }

        if (!empty($firstname) || !empty($lastname)) {
            $parameters['lis_person_name_full'] = trim($firstname.' '.$lastname);
        }

        if (!empty($email)) {
            $parameters['lis_person_contact_email_primary'] = $email;
        }

        // sign the parameters
        $parameters['oauth_signature'] = OAuthHelper::getSignature($parameters, $this->sharedSecret, $this->getLTILaunchURL());
        ksort($parameters);
        return $parameters;
    }
}
