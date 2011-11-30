<?php
include('lib/smarty/Smarty.class.php');

class Renderer
{
    private $layout;
    private $contents = array();
    private $title = '';
    private $keywords = array();
    private $description = '';
    private $scripts = array();
    private $styles = array();
    
    public function __construct($layout, $nav = '')
    {
        $this->layout = $layout;
        $this->nav = $nav;
    }
    
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    public function addContent($template, $properties = array())
    {
        $smarty = new Smarty();
        $smarty->config_load('server.conf');
        
        foreach ($properties as $name => $value)
        {
            $smarty->assign($name, $value);
        }
        
        $this->contents[] = $smarty->fetch($template);
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setKeywords($keywords = array())
    {
        $this->keywords = $keywords;
    }
    
    public function addKeyword($word)
    {
        $this->keywords[] = $word;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function addScript($script)
    {
        $this->scripts[] = $script;
    }
    
    public function addStyle($style)
    {
        $this->styles[] = $style;
    }
    
    public function setNav($nav)
    {
        $this->nav = $nav;
    }
    
    public function render()
    {
        $smarty = new Smarty();
        $smarty->config_load('server.conf');
        $smarty->assign('title', $this->title);
        $smarty->assign('keywords', implode(',', $this->keywords));
        $smarty->assign('description', $this->description);
        $smarty->assign('content', implode("\n", $this->contents));
        $smarty->assign('scripts', $this->scripts);
        $smarty->assign('styles', $this->styles);
        $smarty->assign('nav', $this->nav);
        $smarty->display($this->layout);
    }
}
?>