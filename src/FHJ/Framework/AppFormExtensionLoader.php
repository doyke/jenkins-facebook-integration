<?php

namespace FHJ\Framework;

use Symfony\Component\Form\AbstractExtension;
use Genemu\Bundle\FormBundle\Form\Core\Type\PlainType;

/**
 * AppFormExtensionLoader
 * @package FHJ\Framework
 */
class AppFormExtensionLoader extends AbstractExtension {

	protected function loadTypes() {
		return array(
			new PlainType(),
			new SubmitCancelButtonComboType()
		);
	}

}
