<?php

namespace OCA\Libresign\Service;

use OCA\Libresign\AppInfo\Application;
use OCP\IConfig;

class AdminSignatureService {
	/** @var IConfig */
	private $config;

	public function __construct(
		IConfig $config
	) {
		$this->config = $config;
	}

	/**
	 * @return ((array|mixed)[]|string)[]
	 *
	 * @psalm-return array{cfsslUri: string, configPath: string, rootCert: array{names: array<empty, empty>|mixed}}
	 */
	public function loadKeys(): array {
		$return = [
			'cfsslUri' => '',
			'configPath' => '',
			'rootCert' => [
				'names' => [],
			],
		];
		$configPath = $this->config->getAppValue(Application::APP_ID, 'configPath');
		if (is_dir($configPath)) {
			$return['cfsslUri'] = $this->config->getAppValue(Application::APP_ID, 'cfsslUri');
			$return['configPath'] = $configPath;
		}
		$rootCert = $this->config->getAppValue(Application::APP_ID, 'rootCert');
		$rootCert = json_decode($rootCert, true);
		$hasCustomName = false;
		if (is_array($rootCert)) {
			foreach ($rootCert as $key => $value) {
				if ($key === 'names') {
					$hasCustomName = true;
					foreach ($value as $name => $customName) {
						$return['rootCert']['names'][$name]['id'] = $name;
						$return['rootCert']['names'][$name]['value'] = $customName['value'];
					}
				} else {
					$return['rootCert'][$key] = $value;
				}
			}
		}
		if (!$hasCustomName) {
			$return['rootCert']['names'] = new \stdClass;
		}
		return $return;
	}
}
