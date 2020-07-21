<?php

namespace Api\Management;

/**
 * FINISH THIS FILE±
 * 
 * User status docs:
 * -> 0 everything is alright
 * -> 1 user is not confirmed
 * -> 2 user is restricted (notifications is populated at "restrictions")
 * -> 3 user is banned
 */

class Users extends \Api\Database\DbModel{
    protected static $db_table = "users";
    protected static $db_fields = array('id', 'uuid', 'username', 'email', 'password', 'firstname', 'lastname', 'registration_date', 'notifications','status');

    private static $token_reset = "reset_password";
    private $token_confirm = "confirm_email";

    public $id;
    public $uuid;
    public $username;
    public $email;
    public $password;
    public $firstname;
    public $lastname;
    public $registration_date;
    public $status;
    public $notifications;
    public $role;

    /**
     * This function validates the username, password and email.
     * @param
     * -> $data which must be an array and it will only modify
     * username, password and email values. 
     * 
     * @return
     * -> 0 if everything is alright
     * -> 1 if the username doesn't meet the criteria.
     * -> 2 if email doesn't meet the criteria.
     * -> 3 if the password doesn't meet the criteria.
     */
    public function dataValidation(Array $data){
        /**
         * Checking the username
         * Criteria: only allowed
         * -> a-z
         * -> A-Z
         * -> 0-9
         */
        $pattern = "/[^0-9a-zA-Z]/";
        if(\preg_match($pattern, $data['username']))
            return 1;
        /**
         * Checking the email
         * Criteria: Same as user
         */
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            return 2;

        /**
         * Checking the password
         * Criteria: at least one 
         * -> a-z
         * -> A-Z
         * -> 0-9
         * -> special char.
         */
        $pattern = "#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
        if(!\preg_match($pattern, $data['password']))
            return 3;
        return 0;
    }
    /**
     * This function is not fully implemented but it will check if an user can acces specific
     * restricted pages
     * 
     * @param
     *  acess_level -> An integer that specifies the user access level, defined in role
     *  operation -> Not nneded, but it can bypass certain access level restrictions.
     * 
     * @return
     *  It can only return true or false, depdending on failchecks.
     */
    // params: Integers $acess_level, String $operation
    public function userAcess(){
        /**
         * This user is banned or not confirmed, so he can't acces the application.
         */
        if($this->status == 1 || $this->status == 3)
            return false;
        /**
         * check if he has enough acess to see that
         * An implementation is needed. for now it's disabled.
         */

        /**
         * If all tests pass he can enter.s
         */

       return true;
    }


    /**
     * This function checks if the user exists already
     * 
     * @param
     *  It can either take the email or username. They are not required both
     * 
     * @return
     *  boolean, true if the user exists, false otherwise
     */
    public function confirm_user_exists(String $email, String $username=NULL){
        if(!empty($email) && self::find_by_attribute("email", $email))
            return true;
        if(!empty($username) && self::find_by_attribute("username", $username))
            return true;
        return false; 
    }



    public function create_user(Array $data){
        /**
         * Checking user authenticity
         */
        $data['email']=strtolower($data['email']);
        if(self::find_by_attribute("email",$data['email']))
            return "Email already exists!";
        if(self::find_by_attribute("username",$data['username']))
            return "Username already exists";

        /**
         * Creating the user
         */
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->firstname = $data['firstname'];
        $this->lastname = $data['lastname'];

        /**
         * Adding the security layers
         */
        $this->password = $this->hashPassword($data['password']);
        // $this->uuid = \Core\TokenAuth::getUUID();
        $this->registration_date = date('d-m-Y');
        $this->confirmedStatus = false;
            

        return true;
    }

    public static function check_user(String $username, String $password){
        $user = self::find_by_attribute("username",$username);
        if(!empty($user)){
            if(password_verify($password,$user->password)){
                return $user;
            }
        }
        return false;
    }

    public function hashPassword($password){
        return password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
    }
}