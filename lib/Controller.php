<?php
include_once('lib/smarty/Smarty.class.php');

class Controller
{
    protected $layout;
    protected $nav;
    protected $title = '';
    protected $description = '';
    protected $contents = array();
    protected $keywords = array();
    protected $scripts = array();
    protected $styles = array();
    
    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    protected function addContent($template, $properties = array())
    {
        $smarty = new Smarty();
        $smarty->config_load('server.conf');
        
        foreach ($properties as $name => $value)
        {
            $smarty->assign($name, $value);
        }
        
        $this->contents[] = $smarty->fetch($template);
    }
    
    protected function setTitle($title)
    {
        $this->title = $title;
    }
    
    protected function setKeywords($keywords = array())
    {
        $this->keywords = $keywords;
    }
    
    protected function addKeyword($word)
    {
        $this->keywords[] = $word;
    }
    
    protected function setDescription($description)
    {
        $this->description = $description;
    }
    
    protected function addScript($script)
    {
        $this->scripts[] = $script;
    }
    
    protected function addStyle($style)
    {
        $this->styles[] = $style;
    }
    
    protected function setNav($nav)
    {
        $this->nav = $nav;
    }
    
    protected function view()
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