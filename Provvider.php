<?php

namespace Erre2Web\Esl;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'ESL';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['basicprofile.read fullprofile.read gameaccounts.read openid'];

    /**
     * {@inherticdoc}.
     */
    protected $scopeSeparator = ' ';
    
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            'https://account.eslgaming.com/oauth2/v1/auth', $state
        );
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://account.eslgaming.com/oauth2/v1/token';
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://account.eslgaming.com/oauth2/v1/userinfo', [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['sub'],
            'nickname' => $user['nickname'],
            'name'     => $user['nickname'],
            'email'    => $user['email'],
            'avatar'   => $user['picture'],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
