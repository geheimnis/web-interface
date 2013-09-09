<?php
class TASK{
    /*
     * A task instance
     *
     * A task instance is a record in database, that binds to a user, and can
     * be created, resumed(loaded from a record), approved(and executed)
     * and rejected.
     */
    private $loaded = false;
    private $database_record = null;

    private function get_user_id(){
        global $__SESSION_MANAGER;
        try{
            $userid = $__SESSION_MANAGER->token->get_user_id();
            return $userid;
        } catch (Exception $e){
            return false;
        }
    }

    public function __construct($record_id=false){
        if(is_numeric($record_id))
            $this->_load_task($record_id);
    }

    public function create($command, $arg=null){
        global $__DATABASE;
        if(false === $user_id = $this->get_user_id()) return false;
        $__DATABASE->insert(
            'tasks',
            array(
                'user_id'=>$userid,
                'created_time'=>time(),
                'description'=>'', #XXX
                'have_approved'=>0,
                'have_read'=>0,
                'core_result_id'=>'',
            )
        );
        return true;
    }

    public function reject(){
        if(!$this->loaded) return false;
    }

    public function approve(){
        if(!$this->loaded) return false;
    }

    public function get_result(){
        if(!$this->loaded) return false;
    }

    private function _load_task($record_id){
        global $__DATABASE;
        $this->loaded = false;
        if(!is_numeric($record_id)) return false;

        $result = $__DATABASE->select(
            'tasks',
            'id="' . $record_id . '"'
        );

        if(
            $row = $result->row() &&
            $row['user_id'] == $this->get_user_id()
        ){
            $this->database_record = $row;
            return $this->loaded = true;
        }

        return false;
    }

}
