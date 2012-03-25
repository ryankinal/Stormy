<?php
include_once('lib/Controller.php');

class ContentController extends Controller
{
	public function index()
	{
		$renderer = new Renderer('layouts/overall.tpl');
		$renderer->setNav('nav/index.tpl');
		$renderer->setTitle('Put your stuff here');
		$renderer->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere'
		));
		$renderer->setDescription('Easy, cloud-based storage for your stuff.');
		$renderer->addContent('index.tpl');
		$renderer->render();
	}

	public function about()
	{
		$renderer = new Renderer('layouts/overall.tpl');
		$renderer->setNav('nav/index.tpl');
		$renderer->setTitle('Put your stuff here');
		$renderer->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere',
            'ownership',
            'licensing',
            'responsibility'
		));
		$renderer->setDescription('What is stor.me? Why should I use it? What happens when I do use it?');
		$renderer->addContent('about.tpl');
		$renderer->render();
	}
}
?>