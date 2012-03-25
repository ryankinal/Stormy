<?php
include_once('lib/Controller.php');

class ContentController extends Controller
{
	protected $layout = 'layouts/overall.tpl';
	protected $nav = 'nav/index.tpl';

	public function index()
	{
		$this->setTitle('Put your stuff here');
		$this->setKeywords(array(
			'store',
			'files',
			'upload',
			'easy',
			'fast',
			'anywhere'
		));
		$this->setDescription('Easy, cloud-based storage for your stuff.');
		$this->addContent('index.tpl');
		$this->view();
	}

	public function about()
	{
		$this->setTitle('Put your stuff here');
		$this->setKeywords(array(
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
		$this->setDescription('What is stor.me? Why should I use it? What happens when I do use it?');
		$this->addContent('about.tpl');
		$this->view();
	}
}
?>