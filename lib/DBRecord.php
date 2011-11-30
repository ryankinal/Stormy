<?php
include_once('Database.php');

abstract class DBRecord
{
    protected $dirty;
    protected $attributes = array();
    protected $columns = array();
    protected $IDColumn = '';
    protected $table = '';
    protected $excludes = array();
    protected $required = array();
    protected $error = array();
    protected $statement = array();
    protected $result = array();
    
    public function __construct($attributes = array())
    {
        $this->setAttributes($attributes);
    }
    
    public function loadByAttributes($attributes = array())
    {
        if (count($attributes) == 0)
        {
            $attributes = $this->attributes;
        }
        
        if (count($attributes) > 0)
        {
            $database = new Database();
            $db = $database->getInterface();
            $wheres = array();
            
            foreach ($attributes as $column => $value)
            {
                $wheres[] = $column.' = :'.$column;
            }
            
            $select = $db->prepare('SELECT '.implode(',', $this->columns).' FROM '.$this->table.' WHERE '.implode(' AND ', $wheres));
            
            foreach ($attributes as $column => $value)
            {
                $select->bindValue(':'.$column, $value);
            }
            
            $this->statement = $select;
            
            if ($select->execute())
            {
                $row = $select->fetch(PDO::FETCH_ASSOC);
                
                if ($row && count($row) > 0)
                {
                    $this->result = $row;
                    $this->setAttributes($row);
                    $this->dirty = false;
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                $this->error = $select->errorInfo();
                return false;
            }
        }
        
        return false;
    }
    
    public function setAttribute($name, $value)
    {
        $this->attributs = array();
        if (in_array($name, $this->columns))
        {
            $this->attributes[$name] = $value;
            $this->dirty = true;
        }
    }
    
    public function setAttributes($attributes)
    {
        foreach ($attributes as $name => $value)
        {
            $this->setAttribute($name, $value);
        }
    }
    
    public function save()
    {
        if ($this->dirty)
        {
            $database = new Database();
            $db = $database->getInterface();
            
            if (array_key_exists($this->IDColumn, $this->attributes))
            {
                $updates = array();
            
                foreach ($this->attributes as $column => $value)
                {
                    if ($column != $this->IDColumn)
                    {
                        $updates[] = $column.' = :'.$column;
                    }
                }
                
                $update = $db->prepare('UPDATE '.$this->table.' SET '.implode(',', $updates).' WHERE '.$this->IDColumn.' = :id');
                $update->bindValue(':id', $this->attributes[$this->IDColumn]);
                
                foreach ($this->attributes as $column => $value)
                {
                    if ($column != $this->IDColumn)
                    {
                        $update->bindValue(':'.$column, $value);
                    }
                }
                
                $this->statement = $update;
                
                if ($update->execute())
                {
                    $this->dirty = false;
                    return true;
                }
                else
                {
                    $this->error = $update->errorInfo();
                }
            }
            else
            {
                $keys = array_keys($this->attributes);
                $statement = 'INSERT INTO '.$this->table.' ('.implode(',', $keys).') VALUES (:'.implode(',:', $keys).')';
                $insert = $db->prepare($statement);
                
                foreach ($this->attributes as $column => $value)
                {
                    $insert->bindValue(':'.$column, $value);
                }

                $this->statement = $insert;

                if ($insert->execute())
                {
                    $this->dirty = false;
                    return true;
                }
                else
                {
                    $this->error = $insert->errorInfo();
                }
            }
            
            return false;
        }
        
        return true;
    }
    
    public function delete()
    {
        if (isset($this->attributes[$this->IDColumn]))
        {
            $database = new Database();
            $delete = $database->getInterface()->prepare('DELETE FROM '.$this->table.' WHERE '.$this->IDColumn.' = :id');
            $delete->bindValue(':id', $this->attributes[$this->IDColumn]);
            
            if ($delete->execute())
            {
                return true;
            }
        }
        
        return false;
    }
    
    public function getAttribute($name)
    {
        if (array_key_exists($name, $this->attributes))
        {
            return $this->attributes[$name];
        }
        
        return false;
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getTable()
    {
        return $this->table;
    }
    
    public function getColumns()
    {
        return $this->columns;
    }
    
    public function getJSON()
    {
        return json_encode($this->attributes);
    }
    
    public function validate($input = array())
    {
        $errors = array();
        $requiredKeys = array_keys($this->required);
        $diff = array_diff($requiredKeys, array_keys($input));
        
        foreach ($diff as $key)
        {
            $errors[] = $this->required[$key]['error'];
        }
        
        foreach ($input as $key => $value)
        {
            if (isset($this->required[$key]['test']))
            {	
                if(!preg_match($this->required[$key]['test'], $value))
                {
                    $errors[] = $this->required[$key]['error'].' '.$this->required[$key]['test'].' '.$value;
                }
            }
        }
        
        return $errors;
    }
    
    public function getError()
    {
        return $this->error;
    }
    
    public function getStatement()
    {
        return $this->statement;
    }
}
?>