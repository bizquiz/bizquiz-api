<?php

use PHPUnit\Framework\TestCase;

/**
 *  @author Martin Wind
 */
final class OAuthHelperTest extends TestCase {

    public function testCleanUrlWithHttpPort(){
        $url = 'http://bizquiz.cloud:80/api';
        $cleanUrl = 'http://bizquiz.cloud/api';
        $this->assertEquals($cleanUrl, BizQuiz\OAuthHelper::cleanUrl($url));
    }

    public function testCleanUrlWithPort80OnHttps(){
        $url = 'https://bizquiz.cloud:80/api';
        $cleanUrl = 'https://bizquiz.cloud:80/api';
        $this->assertEquals($cleanUrl, BizQuiz\OAuthHelper::cleanUrl($url));
    }

    public function testCleanUrlWithHttpsPort(){
        $url = 'https://bizquiz.cloud:443/api';
        $cleanUrl = 'https://bizquiz.cloud/api';
        $this->assertEquals($cleanUrl, BizQuiz\OAuthHelper::cleanUrl($url));
    }

    public function testCleanUrlWithPort443OnHttp(){
        $url = 'http://bizquiz.cloud:443/api';
        $cleanUrl = 'http://bizquiz.cloud:443/api';
        $this->assertEquals($cleanUrl, BizQuiz\OAuthHelper::cleanUrl($url));
    }

    public function testGetSignature1() {
        $url = 'https://bizquiz.cloud/api';
        $secret = 'test';
        $params = [
            'hello' => 'oauth',
            'some' => 'param',
        ];
        $signature = 'wvk+uEPXPw551h3HGWqXKsQrrts=';
        $this->assertEquals($signature, BizQuiz\OAuthHelper::getSignature($params, $secret, $url));
    }

    public function testGetSignature2() {
        $url = 'https://bizquiz.cloud:8888/api';
        $secret = 'test2';
        $params = [
            'hello' => 'oauth',
            'some' => 'param',
            'aaaa' => 'check order'
        ];
        $signature = 'GZneiIEojPrcSN1btvkMlgHGyM4=';
        $this->assertEquals($signature, BizQuiz\OAuthHelper::getSignature($params, $secret, $url));
    }

}
