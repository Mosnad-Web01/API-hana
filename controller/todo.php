<?php


require_once 'model\todo.php';

use Core\LoggerUtility;
use Core\Validator;

class TodoController{
    private $todoModel;
    public function __construct($pdo){
        $this->todoModel = new Todo($pdo);
    }
    public  function getTodos($user_id){
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

        $offset = ($page - 1) * $limit;

        $todos =$this ->todoModel->getTodos($user_id,$limit, $offset);

        $totalCount = $this->todoModel->getCount();

        $response= [
            'todos'=> $todos,
            'pagination'=> [
                'totalCount'=>$totalCount,
                'limit'=>$limit,
                'page'=>$page
            ]
        ];

        if ($limit ){
            $response['pagination']['totalPages'] = ceil($totalCount / $limit);
            $response['pagination']['hasMore'] = ($offset + $limit) < $totalCount;
        }
        jsonResponse($response);
    }
    public  function getTodo($user_id,$id){
        $todo =$this ->todoModel->getTodo($user_id,$id);
        if($todo) {
            jsonResponse($todo);
        }else{
            jsonResponse(['message'=> 'Todo not found'],404,'error');
        }
    }
    public function createTodo($user_id){

        $data = json_decode(file_get_contents("php://input"));

        LoggerUtility::logMessage('info',"Received data: " . json_encode($data) );

        $allowedColumns = ['title', 'description', 'user_id'];
        $errors = Validator::validate($allowedColumns, $data);
        if(!empty($errors)) {
            jsonResponse($errors,400,'error');
        }

        $state = $this->todoModel->createTodo($user_id,$data);
        if ($state){
          jsonResponse(['message'=> 'Todo create successfully']);
        } else {
          jsonResponse(['message'=>'Failed to create todo'],500,'error');
        }
    }

    public function updateTodo($user_id,$id){
        $data = json_decode(file_get_contents("php://input"),'true');

        LoggerUtility::logMessage('info', "Received data: " . json_encode($data) . " with status: ");

        $allowedColumns = ['title', 'description', 'user_id'];
        //$errors = Validator::validate($allowedColumns, $data);
        if(!empty($errors)) {
            jsonResponse($errors,400);
        }
        if ($this->todoModel->updateTodo($user_id,$id,$data)){
            jsonResponse(['message'=> 'Todo update successfully']);
        } else {
            jsonResponse(['message'=>'Failed to create todo'],500);
        }
    }
    public function deleteTodo($user_id,$id){
         if ($this->todoModel->deleteTodo($user_id,$id)){
            jsonResponse(['message'=> 'Todo delete successfully']);
        } else {
            jsonResponse(['message'=>'Failed to create todo'],500,'error');
        }
    }





}