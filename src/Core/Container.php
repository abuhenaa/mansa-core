<?php
/**
 * Dependency Injection Container.
 *
 * @package Mansa\Core
 */

namespace Mansa\Core;

/**
 * Simple dependency injection container.
 */
class Container {

	/**
	 * Array of service bindings.
	 *
	 * @var array
	 */
	protected $bindings = array();

	/**
	 * Array of resolved instances.
	 *
	 * @var array
	 */
	protected $instances = array();

	/**
	 * Register a service binding.
	 *
	 * @param string   $key     The service key.
	 * @param callable $factory The factory function.
	 * @return void
	 */
	public function register( $key, callable $factory ) {
		$this->bindings[ $key ] = $factory;
	}

	/**
	 * Get a service instance.
	 *
	 * @param string $key The service key.
	 * @return mixed
	 * @throws \Exception If service is not registered.
	 */
	public function get( $key ) {
		if ( isset( $this->instances[ $key ] ) ) {
			return $this->instances[ $key ];
		}

		if ( ! isset( $this->bindings[ $key ] ) ) {
			throw new \Exception( "Service '{$key}' is not registered." );
		}

		$factory = $this->bindings[ $key ];
		$instance = $factory( $this );

		$this->instances[ $key ] = $instance;

		return $instance;
	}

	/**
	 * Check if a service is registered.
	 *
	 * @param string $key The service key.
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->bindings[ $key ] );
	}
}
