<?php
class TASK{
    /*
     * A task instance
     *
     * A task instance is a record in database, that binds to a user, and can
     * be created, resumed(loaded from a record), approved(and executed)
     * and rejected.
     */

    public function __construct($record_id=false){
        if(is_numeric($record_id))
            $this->_load_task($record_id);
    }

    public function create($command, $arguments_array){
    }

    public function reject(){
    }

    public function approve(){
    }

    public function get_result(){
    }

    private function _load_task($record_id){
    }

}
