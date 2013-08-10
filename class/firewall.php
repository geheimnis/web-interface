<?php
class FIREWALL{

    private $side = null;

    public function __construct(){
    }

    public function apply(){
        global $__IO;
        /*
         * Apply all rules to concerning objects.
         */
        $result_allow_access = true;
        
        # Side control
        $result_allow_access &= 
            (
                ($this->side == 'front') or
                ($__IO->flag('local_visit') === true)
            );
        $__IO->set_side($this->side);

        if(!$result_allow_access) $__IO->set_access_deny();            
    }

    public function declare_side($side){
        if($this->side == null)
            $this->side = $side;
        return $this;
    }

}

$__FIREWALL = new FIREWALL();
