<?php

namespace BizQuiz;

/**
 *  OAuth Helper
 *
 *  @author Martin Wind
 */
class OAuthHelper {

    /**
     * remove defualt ports and clean up url
     *
     * @param  string $url url
     * @return string      a clean url usable in OAuth signing
     */
    public static function cleanUrl($url) {
        // Parse & add query params as base string parameters if they exists
        $url = parse_url($url);

        // Remove default ports
        $explicitPort = isset($url['port']) ? $url['port'] : null;
        if (('https' === $url['scheme'] && 443 === $explicitPort) || ('http' === $url['scheme'] && 80 === $explicitPort)) {
            $explicitPort = null;
        }
        // Remove query params from URL
        $url = sprintf('%s://%s%s%s', $url['scheme'], $url['host'], ($explicitPort ? ':'.$explicitPort : ''), isset($url['path']) ? $url['path'] : '');

        return $url;
    }

    public static function getSignature(array $parameters, $secret, $url, $method = 'POST')
    {
        // Cleanup url
        $url = self::cleanUrl($url);

        // Build POST params array
        $params = [];
        $signature = null;
        foreach ($parameters as $key => $value) {
            $params[] = $key . "=" . rawurlencode($value);
        }
        sort($params);

        $base = strtoupper($method) . '&' . urlencode($url) . '&' . rawurlencode(implode('&', $params));
        $encodedSecret = urlencode($secret) . '&';
        return base64_encode(hash_hmac('sha1', $base, $encodedSecret, true));
    }
}
