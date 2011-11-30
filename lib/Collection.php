<?php
include_once('Database.php');

abstract class Collection
{
    protected $table;
    protected $columns;
    protected $elements = array();
    protected $reference;
    protected $orderBy = '';
    protected $offset;
    protected $limit;
    
    public function __construct($reference)
    {
        $this->table = $reference->getTable();
        $this->columns = $reference->getColumns();
        $this->reference = $reference;
    }
    
    protected function getData($attributes)
    {
        $database = new Database();
        $db = $database->getInterface();
        $wheres = array();
        $columns = $this->reference->getColumns();
        $table = $this->reference->getTable();
    
        foreach ($attributes as $column => $value)
        {
            if (in_array($column, $columns))
            {
                $wheres[] = $column.' = :'.$column;
            }
            else
            {
                unset($attributes[$column]);
            }
        }
        
        $statement = 'SELECT '.implode(',', $this->reference->getColumns()).' FROM '.$table;
        
        if (count($wheres) > 0)
        {
            $statement .= ' WHERE '.implode(' AND ', $wheres);
        }
        
        $statement .= $this->orderBy;
        
        if (isset($this->limit))
        {
            $statement .= ' LIMIT :limit';
        }
        
        if (isset($this->offset))
        {
            $statement .= ' OFFSET :offset';
        }
        
        $select = $database->getInterface()->prepare($statement);
        
        if (isset($this->limit))
        {
            $select->bindValue(':limit', $this->limit, PDO::PARAM_INT);
        }
        
        if (isset($this->offset))
        {
            $select->bindValue(':offset', $this->offset, PDO::PARAM_INT);
        }
        
        foreach ($attributes as $column => $value)
        {
            $select->bindValue(':'.$column, $value);
        }
        
        if ($select->execute())
        {
            return $select->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return array();
    }
    
    public function getElements()
    {
        return $this->elements;
    }
    
    public function orderBy($column)
    {
        $parts = explode(' ', $column);
        
        if (in_array($parts[0], $this->reference->getColumns())
            && (count($parts) === 1
                || strtolower($parts[1]) === 'asc' 
                || strtolower($parts[1]) === 'desc'))
        {
            $this->orderBy = ' ORDER BY '.$column;
        }
        else
        {
            $this->orderBy = '';
        }
    }
    
    public function page($page)
    {
        if ($page > 0)
        {
            $page--;
        }
    
        $this->offset = $page * $this->limit;
    }
    
    public function perPage($perPage)
    {
        $this->limit = $perPage;
    }
    
    public function getJSON()
    {
        $elements = array();
        
        foreach ($this->elements as $item)
        {
            $elements[] = $item->getAttributes();
        }
        
        return json_encode($elements);
    }
    
    abstract function getBy($attributes = array());
}
?>