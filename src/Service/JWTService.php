<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    // Generating the token

    /**
     * Generation of the JWT
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * @return string
     */

    public function generate(array $header, array $payload, string $secret, int $validity = 1800): string
    {
        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;
    
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }


        // Encoding to base 64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // Cleaning the encoding values (removing +, / and =)
        $base64Header = str_replace(['+', '/', '='], ['-', '-', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '-', ''], $base64Payload);

        // Generating the signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '-', ''], $base64Signature);

        // Token creation

        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    // Verifying the token's validity (token's validity form)

    public function isValid(string $token) : bool 
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) == 1;
    }

    // Taking back the payload
    public function getPayload(string $token) : array 
    {
        // Exploding the token
        $array = explode('.', $token);
        
        // decoding the payload

        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    // Taking back the header
    public function getheader(string $token) : array 
    {
        // Exploding the token
        $array = explode('.', $token);
        
        // decoding the header

        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // Verifying if the token has experied
    public function isExperied(string $token) : bool 
    {
        $payload = $this->getPayload($token);
        
        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    // Verifying the token's signature
    public function check(string $token, string $secret)
    {
        // Taking back the header and the payload
        $header = $this->getheader($token);
        $payload = $this->getPayload($token);

        // Regenerating a token
        $verifToken = $this->generate($header, $payload, $secret, 0);

        return $token === $verifToken;
    }
}