<?php

class CatalogueSiteConfig extends DataExtension {

	private static $db = array(
		'CatalogueSettingReadonly' => 'Boolean'
	);

	public function updateCMSFields(FieldList $fields) {
		$fields->addFieldToTab("Root.Main", $field = new CheckboxField("CatalogueSettingReadonly", "Make all catalogue settings read-only"));
		$field->setDescription('Enable this setting to make configuration forms read only');
	}

}