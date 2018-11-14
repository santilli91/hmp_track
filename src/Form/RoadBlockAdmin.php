<?php

namespace Drupal\hmp_track\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Routing\TrustedRedirectResponse;

class RoadBlockAdmin extends FormBase {
	public function buildForm(array $form, FormStateInterface $form_state) {
		$search = isset($_GET['search'])?$_GET['search']:'';
		/** Ad Road Blocking Info **/
		$form['Ad'] = array(
			'#type' => 'item',
			'#markup' => '<hr><h2>RoadBlock Information</h2>',
		);
		
		/** Search Form (Title Search) **/
		$form['search'] = array(
			'#type' => 'textfield',
			'#title' => "Search: ",
			'#default_value' => $search,
		);

		/** Submit Button **/
		$form['submit'] = array(
			'#type' => 'submit',
			'#value' => 'Search',
		);

		$form['break'] = array(
			'#type' => 'item',
			'#markup' => '<br><hr><br><a href="/admin/content/roadblock/new">New Roadblock</a><br/><br/><hr><br>',
		);

		$form['list'] = array(
			'#type' => 'item',
			'#markup' => $this->getRoadBlocks($search),
		);
		return $form;
	}

	public function getFormId() {
		return 'road_block_admin';
	}


	public function submitForm(array &$form, FormStateInterface $form_state) {
		$response =  new TrustedRedirectResponse('/admin/content/roadblock?search=' . $form_state->getValue('search'));
		$response->send();
	}

	private function getRoadBlocks($title = null) {
		$query = \Drupal::database()->select('hmp_track_ads','a');
		$query->fields('a');
		if($title != null)
			$query->condition('a.name',"%$title%",'LIKE');
		$results = $query->orderBy('a.enabled','DESC')->execute();
		$output = '<table><tbody><tr><th>Name</th><th>ID</th><th>Enabled</th><th></th></tr>';
		foreach($results as $row) {
			$output .= sprintf("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",
				$row->name,
				$row->ad_id,
				$row->enabled == 1?"Enabled":"Disabled",
				"<a href='/admin/content/roadblock/$row->id'>Edit</a>"
			);
		}
		$output .= "</tbody></table>";
		return $output;
	}
}
