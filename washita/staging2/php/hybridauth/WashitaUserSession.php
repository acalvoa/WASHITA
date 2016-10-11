<?php
    
class WashitaUserSession{

    public $Id;
    /**
     * @var string
     */
    public $ShortName;
    /**
     * @var boolean
     */
    public $IsComplete;
    

    public $UserType;
    
    /**
     * @param integer $id
     * @param string $name
     * @param string $lastname
     * @param boolean $isComplete
     * @return WashitaUser
     */
	public static function Create($id, $name, $lastname, $isComplete, $userType)
	{
        $user = new WashitaUserSession();
        $user->Id = $id;
        $user->ShortName = (empty($name) && empty($lastname))? "": $name." ".substr($lastname, 0,1).".";
        $user->IsComplete = $isComplete;
        $user->UserType = $userType;
        return $user;
    }
    

}