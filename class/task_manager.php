<?php
class TASK_MANAGER{

    private $approval_needed_commands = array(
        'identity-delete',
        'identity-add',
    );
    private $possible_commands = array(
        'identity-delete',
        'identity-add',
        'identity-list',
        'identity-test',
    );

    private $ready = false;
    private $last_overview = 0;

    private $user_id = false;

    private $tasks_overview = array();
    private $tasks_unread_overview = array();

    public function __construct(){
        global $__SESSION_MANAGER;
        if(false !== $user_id = $__SESSION_MANAGER->token->get_user_id()){
            $this->user_id = $user_id;
            $this->ready = true;
        }
    }

    public function create_task($user_id, $command_name, $argv=null){
        /*
         * One can create a task, even if he is not logged in. Here user_id
         * is the user, to which the task assigned to.
         *
         * According to the code in class/task.php, anonymously created tasks
         * can be stored and approved immediately. But to reload this stored
         * task, requires login.
         *
         * But for logged in users, user_id will be overwrited to his own
         * user_id. In other words, he will not be able to create tasks for
         * other users.
         */
        $command_name = strtolower(trim($command_name));
        if(!in_array($command_name, $this->possible_commands)) return false;
        if($this->ready) $user_id = $this->user_id;

        $need_approval = 
            in_array($command_name, $this->approval_needed_commands);

        $new_task = new TASK();
        $new_task->create($user_id, $command_name, $argv);

        if(!$need_approval){
            $new_task->approve();
            $result = $new_task->get_result();
            $new_task->delete();
            return $result;
        }

        return true;
    }

    public function get_tasks_count(){
        if(!$this->ready) return false;
        $this->refresh_task_overview();
        return count($this->tasks_overview);
    }

    public function get_tasks_unread_count(){
        if(!$this->ready) return false;
        $this->refresh_task_overview();
        return count($this->tasks_unread_overview);
    }

    private function refresh_task_overview(){
        global $_CONFIGS, $__DATABASE;
        if(!$this->ready) return false;

        $cache_life = $_CONFIGS['performance']['tasks']['cache_life'];
        $max_tasks = $_CONFIGS['performance']['tasks']['max_tasks_overview'];
        $nowtime = time();

        $this->tasks_overview = $this->tasks_unread_overview = array();

        if($nowtime - $this->last_overview > $cache_life){
            $result = $__DATABASE->select(
                'tasks',
                'user_id="' . $this->user_id . '"',
                'id,created_time,have_read',
                'ORDER BY created_time LIMIT ' . $max_tasks
            );
            $this->tasks_overview = $result->all();

            $result = $__DATABASE->select(
                'tasks',
                'user_id="' . $this->user_id . '" AND have_read=0',
                'id,created_time',
                'ORDER BY created_time LIMIT ' . $max_tasks
            );
            $this->tasks_unread_overview = $result->all();

            $this->last_overview = $nowtime;
            return true;
        }
        return false;
    }

}
