<?php
class FIREWALL{

    private $side = null;
    private $login_required = false;

    public function __construct(){
    }

    public function apply(){
        global $__IO;
        /*
         * Apply all rules to concerning objects.
         */
        $result_allow_access = true;
        $result_require_login = false;
        
        # Side control
        $result_allow_access &= 
            (
                ($this->side == 'front') or
                ($__IO->flag('local_visit') === true)
            );
        $__IO->set_side($this->side);

        # Login control
        $result_require_login = (
            ($this->side == 'back') &&
            ($this->login_required === true)
        );

        if(!$result_allow_access) $__IO->set_access_deny();
        if($result_require_login) $__IO->set_force_login();
    }

    public function declare_side($side){
        if($this->side == null)
            $this->side = $side;
        return $this;
    }

    public function require_login(){
        $this->login_required = true;
        return $this;
    }

}

$__FIREWALL = new FIREWALL();
