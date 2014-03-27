<?php

namespace FHJ\Framework;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SimpleLinkButtonType
 * @package FHJ\Framework
 */
class SubmitCancelButtonComboType extends SubmitType {

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		parent::setDefaultOptions($resolver);

		$resolver->setDefaults(array(
			'widget'  => 'field',
			'mapped' => false,

			'label' => '',
			'attr' => array(
				'class' => 'btn btn-primary'
			),


			'label_cancel' => '',
			'href_cancel' => '#',
			'attr_cancel' => array(
				'class' => 'btn'
			),
		));
	}

	public function getParent() {
		return 'submit';
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		parent::buildView($view, $form, $options);

		$view->vars = array_replace($view->vars, array(
			'label' => $options['label'],
			'value' => $options['label'],
			'type' => 'submit',

			'attr_cancel'  => $options['attr_cancel'],
			'href_cancel'  => $options['href_cancel'],
			'label_cancel'  => $options['label_cancel'],

			// stub variables needed to get block('form_widget_simple') in template to work
			'read_only' => false,
			'disabled' => false,
			'required' => false,
			'max_length' => 0,
			'pattern' => ''
		));
	}

	/**
	 * Returns the name of this type.
	 * @return string The name of this type
	 */
	public function getName() {
		return 'submit_cancel_combo';
	}
}