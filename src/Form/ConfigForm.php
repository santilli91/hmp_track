<?php

namespace Drupal\hmp_track\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigForm extends FormBase {
	public function buildForm(array $form, FormStateInterface $form_state) {

		/** Woopra Info **/
		$form['woopra'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Woopra Information</h2>',
		);
		$form['woopra_id'] = array(
			'#type' => 'textfield',
			'#default_value' => \Drupal::state()->get('woopra_id'),
			'#title' => 'Woopra ID (domain name): ',
		);
		
		/** Ad Server Info **/
		$form['advertserve'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Advert Server Info</h2>',
		);
		$form['advertserve_url'] = array(
			'#type' => 'textfield',
			'#default_value' => \Drupal::state()->get('advertserve_url'),
			'#title' => 'AdvertServe URL (full url): ',
		);

		/** Proclivity Info **/
		$form['proclivity'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Proclivity Info</h2>',
		);
		$form['proclivity_sid'] = array(
			'#type' => 'textfield',
			'#default_value' => \Drupal::state()->get('proclivity_sid'),
			'#title' => 'SID (Site ID): ',
		);
		$form['proclivity_puid'] = array(
			'#type' => 'textfield',
			'#default_value' => \Drupal::state()->get('proclivity_puid'),
			'#title' => 'PUID (Publisher ID): ',
		);

		/** DMD Info **/
		$form['dmd'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>DMD Info</h2>',
		);
		$form['dmd_id'] = array(
			'#type' => 'textfield',
			'#default_value' => \Drupal::state()->get('dmd_id'),
			'#title' => 'DMD Site ID: ',
		);

		/** Submit Button **/
		$form['break'] = array(
			'#type' => 'item',
			'#markup' => '<br><hr><br>',
		);
		$form['submit'] = array(
			'#type' => 'submit',
			'#value' => 'Submit',
		);
		return $form;
	}

	public function getFormId() {
		return 'config_form_admin';
	}


	public function submitForm(array &$form, FormStateInterface $form_state) {
		\Drupal::state()->set('woopra_id',$form_state->getValue('woopra_id'));
		\Drupal::state()->set('advertserve_url',$form_state->getValue('advertserve_url'));
		\Drupal::state()->set('proclivity_sid',$form_state->getValue('proclivity_sid'));
		\Drupal::state()->set('proclivity_puid',$form_state->getValue('proclivity_puid'));
		\Drupal::state()->set('dmd_id',$form_state->getValue('dmd_id'));
	}



}
