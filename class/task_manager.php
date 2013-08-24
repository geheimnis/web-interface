<?
class TASK_MANAGER{

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

    public function get_tasks_count(){
        $this->refresh_task_overview();
        return count($this->tasks_overview);
    }

    public function get_tasks_unread_count(){
        $this->refresh_task_overview();
        return count($this->tasks_unread_overview);
    }

    private function refresh_task_overview(){
                'user_id="' . $this->user_id . '"'
        global $_CONFIGS;
        if(!$this->ready) return false;

        $cache_life = $_CONFIGS['performance']['tasks']['cache_life'];
        $max_tasks = $_CONFIGS['performance']['tasks']['max_tasks_overview'];
                'user_id="' . $this->user_id . '"'
        $nowtime = time();

        $this->tasks_overview = $this->tasks_unread_overview = array();

        if($nowtime - $this->last_overview > $cache_life){
            $result = $__DATABASE->select(
                'tasks',
                'user_id="' . $this->user_id . '"',
                'id,created_time,description,read',
                'ORDER BY created_time LIMIT ' . $max_tasks
            );
            $this->tasks_overview = $result->all();

            $result = $__DATABASE->select(
                'tasks',
                'user_id="' . $this->user_id . '" AND read=0',
                'id,created_time,description',
                'ORDER BY created_time LIMIT ' . $max_tasks
            );
            $this->tasks_unread_overview = $result->all();

            $this->last_overview = $nowtime;
            return true;
        }
        return false;
    }

}
