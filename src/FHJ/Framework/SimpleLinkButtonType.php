<?php

namespace FHJ\Framework;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SimpleLinkButtonType
 * @package FHJ\Framework
 */
class SimpleLinkButtonType extends AbstractType {

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		parent::setDefaultOptions($resolver);

		$resolver->setDefaults(array(
			'widget'  => 'field',
			'read_only' => true,
			'href' => '#',
			'attr' => array(
				'class' => 'btn'
			),
		));
	}

	public function getParent() {
		return 'button';
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
		$value = $form->getViewData();

		$view->vars['href'] = (string) $value;

		$view->vars = array_replace($view->vars, array(
			'label' => $options['label'],
			'attr'  => $options['attr'],
			'href'  => $options['href'],
		));
	}

	/**
	 * Returns the name of this type.
	 * @return string The name of this type
	 */
	public function getName() {
		return 'linkbutton';
	}
}