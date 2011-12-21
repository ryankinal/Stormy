<?php
include_once('lib/Collection.php');
include_once('classes/Share.php');

class Shares extends Collection
{
    public function __construct()
    {
        parent::__construct(new Share());
    }
    
    public function getBy($properties = array())
    {
        $this->elements = array();
        $rows = $this->getData($properties);
        
        foreach ($rows as $row)
        {
            $this->elements[] = new Share($row);
        }
        
        return $this->elements;
    }
}
?>