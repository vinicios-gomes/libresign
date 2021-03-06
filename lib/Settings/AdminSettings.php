<?php

namespace OCA\Libresign\Settings;

use OCA\Libresign\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class AdminSettings implements ISettings {
	public function getForm() {
		return new TemplateResponse(Application::APP_ID, 'settings');
	}

	public function getSection() {
		return 'security';
	}

	public function getPriority() {
		return 00;
	}
}
