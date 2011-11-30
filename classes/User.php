<?php
include_once('lib/DBRecord.php');

class User extends DBRecord
{
    protected $columns = array(
        'user_id',
        'email',
        'password',
        'joined',
        'ip_address',
        'confirmed'
    );
    protected $required = array(
        'email' => array('test' => '/./', 'error' => 'Please provide a valid email address'),
        'password' => array('error' => 'Please enter a password')
    );
    protected $IDColumn = 'user_id';
    protected $table = 'app_users';
    protected $permissions;
    
    const SALT = 'oaiwehnfklajbnwef';
    
    public function setPassword($p_password)
    {
        $this->setAttribute('password', sha1(self::SALT.sha1($p_password.self::SALT)));
    }
    
    public function login()
    {
        if (isset($this->attributes['email']) && isset($this->attributes['password']))
        {
            $test = $this->loadByAttributes();
            
            if ($test)
            {
                if (session_id() == '')
                {
                    session_start();
                }
                
                $_SESSION['user_id'] = $this->attributes[$this->IDColumn];
                $_SESSION['token'] = md5(time().self::SALT.$this->attributes['joined']);
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    
    public function loadCurrent()
    {
        if (session_id() == '')
        {
            session_start();
        }

        if (isset($_SESSION['user_id']))
        {
            $this->setAttribute('user_id', $_SESSION['user_id']);
            return $this->loadByAttributes();
        }
        else
        {
            return false;
        }
    }
}
?>