<?php
/**
 * User: Victor Häggqvist
 * Date: 6/7/15
 * Time: 1:42 AM
 */

namespace AppBundle\Security;


use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface {


    /**
     * @var Logger
     */
    private $logger;

    function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        if (!$userProvider instanceof ApiKeyUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

//        $this->logger->debug($token->getCredentials());

        list($keyToken, $timestamp) = explode(':', $token->getCredentials());
        $valid = $userProvider->validateKeyPair($keyToken, $timestamp);

        if (!$valid) {
            throw new AuthenticationException(
                sprintf('API Key verification failed.')
            );
        }

        return new PreAuthenticatedToken(
            new User('noname', null),
            $keyToken,
            $providerKey,
            ['ROLE_USER']
        );

    }

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $providerKey) {

        // look for an apikey query parameter
        $token = $request->query->get('token');
        $timestamp = $request->query->get('timestamp');

        if (!$token || !$timestamp) {
            throw new BadCredentialsException('No API key found');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $token.':'.$timestamp,
            $providerKey
        );

    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new Response("Authentication Failed.", 403);
    }

}
