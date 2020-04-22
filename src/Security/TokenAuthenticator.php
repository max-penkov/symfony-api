<?php

declare(strict_types=1);

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class TokenAuthenticator
 * @package App\Security
 */
class TokenAuthenticator extends JWTTokenAuthenticator
{
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        $user = parent::getUser($preAuthToken, $userProvider);

        if ($user->getPasswordChangeDate() &&
            $preAuthToken->getPayload()['iat'] < $user->getPasswordChangeDate()
        ) {
            throw new ExpiredTokenException();
        }

        return $user;
    }

}