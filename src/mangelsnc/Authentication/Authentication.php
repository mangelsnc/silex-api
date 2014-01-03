<?php

namespace Authentication;

class Authentication
{
    private $publicPart;
    private $userUID;
    private $signature;
    private $secret;
    private $timestamp;

    public function __construct($userUID)
    {
        $this->userUID = $userUID;
        $this->timestamp = time();
        $this->secret = 'Password';
        $this->setPublicPart();
        $this->setSignature($this->publicPart);
    }

    private function generateSignature($publicPart)
    {
        return hash_hmac('tiger192,4', $publicPart, $this->secret);
    }

    private function setSignature($publicPart)
    {
        $this->signature = $this->generateSignature($publicPart);
    }

    public function getSignature()
    {
        return $this->signature;
    }

    private function setPublicPart()
    {
        $this->publicPart = sha1($this->userUID . ':' . $this->timestamp);
    }

    public function getPublicPart()
    {
        return $this->publicPart;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function getAccessToken()
    {
        return $this->getPublicPart() . '_' . $this->getSignature();
    }

    public function validateToken($token)
    {
        list($publicPart, $signature) = explode('_', $token);
        $calculatedSignature = $this->generateSignature($publicPart);

        if($calculatedSignature != $signature) {
            return false;
        }
    }
}