<?php

namespace Drupal\hmp_track\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigForm extends FormBase {
	public function buildForm(array $form, FormStateInterface $form_state) {
		$hmp_track = \Drupal::state()->get('hmp_track');
		/** Woopra Info **/
		$form['woopra'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Woopra Information</h2>',
		);
		$form['woopra_id'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['woopra_id'],
			'#title' => 'Woopra ID (domain name): ',
		);
		
		/** Ad Server Info **/
		$form['advertserve'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Advert Server Info</h2>',
		);
		$form['advertserve_url'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['advertserve_url'],
			'#title' => 'AdvertServe URL (full url): ',
		);
		$form['advertserve_timeout'] = array(
			'#type' => 'number',
			'#default_value' => $hmp_track['advertserve_timeout'],
			'#title' => 'AdvertServe Timeout (Ad Load Delay): ',
		);

		/** Proclivity Info **/
		$form['proclivity'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Proclivity Info</h2>',
		);
		$form['proclivity_sid'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['proclivity_sid'],
			'#title' => 'SID (Site ID): ',
		);
		$form['proclivity_puid'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['proclivity_puid'],
			'#title' => 'PUID (Publisher ID): ',
		);
		$form['proclivity_url'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['proclivity_url'],
			'#title' => 'Proclivity Ad API Url:',
		);
		$form['proclivity_px'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['proclivity_px'],
			'#title' => 'Proclivity User PX URL:',
		);

		/** DMD Info **/
		$form['dmd'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>DMD Info</h2>',
		);
		$form['dmd_id'] = array(
			'#type' => 'textfield',
			'#default_value' => $hmp_track['dmd_id'],
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
		$hmp_track = array(
			'woopra_id'			=>	$form_state->getValue('woopra_id'),
			'advertserve_url'	=>	$form_state->getValue('advertserve_url'),
			'advertserve_timeout'	=>	$form_state->getValue('advertserve_timeout'),
			'proclivity_sid'	=>	$form_state->getValue('proclivity_sid'),
			'proclivity_puid'	=>	$form_state->getValue('proclivity_puid'),
			'proclivity_url'	=>	$form_state->getValue('proclivity_url'),
			'proclivity_px'		=>	$form_state->getValue('proclivity_px'),
			'dmd_id'			=>	$form_state->getValue('dmd_id')
		);
		\Drupal::state()->set('hmp_track',$hmp_track);
	}



}
