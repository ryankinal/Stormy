<?php
include_once('lib/Collection.php');
include_once('classes/File.php');

class Files extends Collection
{
    public function __construct()
    {
        parent::__construct(new File());
    }
    
    public function getBy($properties = array())
    {
        $this->elements = array();
        $rows = $this->getData($properties);
        
        foreach ($rows as $row)
        {
            $this->elements[] = new File($row);
        }
        
        return $this->elements;
    }
}
?>