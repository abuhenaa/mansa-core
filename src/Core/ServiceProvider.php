<?php
/**
 * Service Provider Interface.
 *
 * @package Mansa\Core
 */

namespace Mansa\Core;

/**
 * Abstract service provider.
 */
abstract class ServiceProvider {

	/**
	 * Register services.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	abstract public function register( Container $container );

	/**
	 * Boot the provider.
	 *
	 * @param Container $container The container instance.
	 * @return void
	 */
	abstract public function boot( Container $container );
}