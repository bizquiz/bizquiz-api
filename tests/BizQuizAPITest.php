<?php

use PHPUnit\Framework\TestCase;

/**
 *  @author Martin Wind
 */
final class BizQuizAPITest extends TestCase {

    public function testIsThereAnySyntaxError() {
        $api = new BizQuiz\BizQuizAPI('demo', 'key');
        $this->assertTrue(is_object($api));
    }

    public function testLtiLaunchUrl() {
        $api = new BizQuiz\BizQuizAPI('demo', 'key');
        $this->assertEquals('https://bizquiz.cloud/api/lti', $api->getLTILaunchURL());
    }

    public function testGetLtiLaunchParameter() {
        $api = new BizQuiz\BizQuizAPI('demo', 'key');
        $parameters = $api->getLTILaunchParameter('12345');
        $this->assertArraySubset([
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'user_id' => '12345',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
            'oauth_consumer_key' => 'demo',
            'oauth_callback' => 'about:blank',
        ], $parameters);
    }

    public function testGetLtiLaunchParameterExt() {
        $api = new BizQuiz\BizQuizAPI('demo', 'key');
        $parameters = $api->getLTILaunchParameter('12345', 'email', 'first', 'last', ['group-1', 'group-2'], 'team');
        $this->assertArraySubset([
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => 'LTI-1p0',
            'user_id' => '12345',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0',
            'oauth_consumer_key' => 'demo',
            'oauth_callback' => 'about:blank',
            'lis_person_name_given' => 'first',
            'lis_person_name_family' => 'last',
            'lis_person_name_full' => 'first last',
            'lis_person_contact_email_primary' => 'email',
            'custom_groups' => 'group-1,group-2',
            'custom_team' => 'team',
        ], $parameters);
    }

    /*
    public function testLocalDashboardRequest() {
        $api = new BizQuiz\BizQuizAPI('demo', 'key', 'http://localhost:8000/api');
        $dashboard = $api->request('dashboard', ['userId' => 'extern']);
        print_r($dashboard);
    }
    */

}
