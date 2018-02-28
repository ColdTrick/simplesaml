<?php

use Elgg\Router\Middleware\Gatekeeper;

return [
	'actions' => [
		'simplesaml/register' => [
			'access' => 'public',
		],
		'simplesaml/unlink' => [],
	],
	'routes' => [
		'default:saml:login' => [
			'path' => '/saml/login/{saml_source}',
			'resource' => 'saml/login',
			'walled' => false,
		],
		'default:saml:no_linked_account' => [
			'path' => '/saml/no_linked_account/{saml_source}',
			'resource' => 'saml/no_linked_account',
			'walled' => false,
		],
		'default:saml:authorize' => [
			'path' => '/saml/authorize/{saml_source}',
			'resource' => 'saml/authorize',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'default:saml:idp_login' => [
			'path' => '/saml/idp_login',
			'resource' => 'saml/idp_login',
			'walled' => false,
		],
	],
	'widgets' => [
		'simplesaml' => [
			'title' => elgg_echo('login'),
			'description' => elgg_echo('simplesaml:widget:description'),
			'context' => ['index'],
			'multiple' => true,
		],
	],
];
