<?php

namespace Drupal\hmp_track\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Routing\TrustedRedirectResponse;

class RoadBlockForm extends FormBase {
	public function buildForm(array $form, FormStateInterface $form_state, $aid = null) {
		$values = $delete = array();
		if(is_numeric($aid)) {
			$values = $this->getDefaults($aid);
			if($values['id']) {
				$delete = array(
					'#type' => 'checkbox',
					'#title' => '<b>DELETE(THIS WILL DELETE):</b>',
				);
			}
			else {
				drupal_set_message('That roadblock does not exist, you have been redirected here');
				$response =  new TrustedRedirectResponse('/admin/content/roadblock/');
				$response->send();
			}
		}
		/** Ad Road Blocking Info **/
		$form['Ad'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>Road Block Information</h2>',
		);
		$form['aid'] = array(
			'#type' => 'hidden',
			'#default_value' => $aid,
		);
		$form['name'] = array(
			'#type' => 'textfield',
			'#default_value' => isset($values['name'])?$values['name']:'',
			'#title' => "Roadblock Name:",
			'#required' => true,
		);
		$form['id'] = array(
			'#type' => 'textfield',
			'#default_value' => isset($values['id'])?$values['id']:'',
			'#title' => "Roadblock ID:",
			'#required' => true,
		);
		$form['enabled'] = array(
			'#type' => 'checkbox',
			'#title' => 'Enabled',
			'#default_value' => isset($values['enabled'])?$values['enabled']:'',
		);
		$form['uri'] = array(
			'#type' => 'textarea',
			'#title' => 'URI (one per line):',
			'#default_value' => isset($values['uri'])?$values['uri']:'',
		);
		$form['delete'] = $delete;
		/** Submit Button **/
		$form['break'] = array(
			'#type' => 'item',
			'#markup' => '<br><hr><br>',
		);
		
		$form['submit'] = array(
			'#type' => 'submit',
			'#value' => 'Save',
		);
		return $form;
	}

	public function getFormId() {
		return 'road_block_form';
	}


	public function submitForm(array &$form, FormStateInterface $form_state) {
		$sub = array(
			'id' => $form_state->getValue('aid'),
			'name' => $form_state->getValue('name'),
			'ad_id' => $form_state->getValue('id'),
			'enabled' => $form_state->getValue('enabled'),
			'uris' => preg_split('/\r\n|\r|\n/',$form_state->getValue('uri'))
		);
		/** If there is a new item **/
		if($sub['id'] == 'new') {
			$query = \Drupal::database()->insert('hmp_track_ads')
			->fields(array(
				'name' => $sub['name'],
				'ad_id' => $sub['ad_id'],
				'enabled' => $sub['enabled']
			))->execute();
			$id = \Drupal::database()->select('hmp_track_ads','a')->fields('a',['id'])->orderBy('a.id','DESC')->range(0,1)->execute()->fetchField();
			$this->insertURI($sub['uris'],$id);
			drupal_set_message('Created!');
			$response =  new TrustedRedirectResponse('/admin/content/roadblock/' . $id);
			$response->send();
		} 
		/** Updating items **/
		else {
			/** Delete a roadblock and all of its data **/
			if($form_state->getValue('delete') == 1) {
				$query = \Drupal::database()->delete('hmp_track_ads')->condition('id',$sub['id'],'=')->execute();
				$query = \Drupal::database()->delete('hmp_track_ads_uri')->condition('aid',$sub['id'],'=')->execute();
				drupal_set_message('Roadblock: ' . $sub['name'] . ' has been deleted');
				$response =  new TrustedRedirectResponse('/admin/content/roadblock/');
				$response->send();
			} 
			/** Update item **/
			else {
				$query = \Drupal::database()->update('hmp_track_ads')->fields(array(
					'name' => $sub['name'],
					'ad_id' => $sub['ad_id'],
					'enabled' => $sub['enabled']
				))->condition('id',$sub['id'])->execute();
				$this->insertURI($sub['uris'],$sub['id']);
				drupal_set_message('Updated!');
			}
		}

	}

	private function insertURI($uris,$id) {
		$query = \Drupal::database()->delete('hmp_track_ads_uri')->condition('aid',$id,'=')->execute();
		foreach($uris as $uri) {
			$query = \Drupal::database()->insert('hmp_track_ads_uri')
				->fields(array(
					'uri' => $uri,
					'aid' => $id
				))->execute();
		}
	} 

	private function getDefaults($aid) {
		$query = \Drupal::database()->select('hmp_track_ads','a');
		$query->fields('a');
		$query->condition('a.id',$aid,'=');
		$results = $query->execute();
		$data = array();
		foreach($results as $result) {
			$data = array(
				'name' => $result->name,
				'id' => $result->ad_id,
				'enabled' => $result->enabled,
			);
		}
		$query = \Drupal::database()->select('hmp_track_ads_uri','u');
		$query->fields('u');
		$query->condition('u.aid',$aid,'=');
		$results = $query->execute();
		$uris = '';
		foreach($results as $uri) {
			$uris .= $uri->uri . PHP_EOL;
		}
		$data['uri'] = $uris;
		return $data;
	}
}
