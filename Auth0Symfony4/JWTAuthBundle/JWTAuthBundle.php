<?php

namespace Auth0Symfony4\JWTAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\API\Helpers\InformationHeaders;
use Auth0Symfony4\JWTAuthBundle\DependencyInjection\Auth0Extension;

class JWTAuthBundle extends Bundle
{
	const SDK_VERSION = "3.0.0";

	public function __construct()
    {
		$oldInfoHeaders = ApiClient::getInfoHeadersData();

        if ($oldInfoHeaders) {
            $infoHeaders = InformationHeaders::Extend($oldInfoHeaders);

            $infoHeaders->setEnvironment('Symfony', Kernel::VERSION);
            $infoHeaders->setPackage('jwt-auth-bundle', self::SDK_VERSION);

            ApiClient::setInfoHeadersData($infoHeaders);
        }
	}

    public function getAlias()
    {
        return 'jwt_auth';
    }
}
