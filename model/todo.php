<?php


use Core\LoggerUtility;

class Todo{

    public function __construct($pdo){
        $this->pdo = $pdo;
    }
    public function getTodos($user_id,$limit=null,$offset=0, $orderBy ='id', $order ='ASC'){

        $query = "SELECT * FROM todos WHERE  1=1";

        $params =[];
        if($user_id !==null){
            $query .= " AND user_id =". intval($user_id);
        }
        if($limit !== null){
            $query .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);
            $params["limit"] = $limit;
            $params["offset"] = $offset;
        }
        return $this->pdo->query($query)->fetchall();

//        if ($order!==null) {
//            $query .= " ORDER BY :orderBy :order";
//            $params["orderBy"] = $orderBy;
//            $order['order']= $order;
//
//        }
//        $statement = $this->pdo->prepare($query);
//        if($limit !== null){
//         $statement->bindParam(":limit", $params["limit"], PDO::PARAM_INT);
//         $statement->bindParam(":offset", $params["offset"], PDO::PARAM_INT);
//        }
//dd($statement);
//        return $this->pdo->query($statement)->fetchAll();
    }

    public function getTodo($user_id,$id)
    {
        return $this->pdo->query("SELECT * FROM todos WHERE id = :id AND user_id =:user_id",
            ['id' => $id,
             'user_id'=>$user_id
            ])->fetch();
    }

    public function createTodo($data)
    {
        if (is_object($data)) {
            $data = json_decode(json_encode($data), true);
            LoggerUtility::logMessage('info', "Received data: " . json_encode($data) . " with status: ");

        }
        return $this->pdo->query("INSERT INTO todos (title, description, user_id) VALUES (:title, :description, :user_id)",
            ['title' => $data['title'], 'description' => $data['description'], 'user_id' => $data['user_id']]);
    }

    public function updateTodo($user_id,$id, $data)
    {
        $allowedColumns = ['title', 'description', 'is_completed'];

        $query = 'UPDATE todos SET ';

        $values = ['id' => $id, 'user_id'=>$user_id];

        $columns = [];

        foreach ($data as $key => $value) {

            if (in_array($key, $allowedColumns)) {

                $values[$key] = $value;

                $columns[] = $key . ' = :' . $key;
            }

        }

        $query .= implode(', ', $columns);

        $query .= ' WHERE id = :id and user_id = :user_id';

        return $this->pdo->query($query, $values);

    }

    public function deleteTodo($user_id,$id)
    {
        return $this->pdo->query("DELETE FROM todos WHERE id = :id AND user_id=:user_id",
            ['id' => $id,
                'user_id'=>$user_id]);
    }
    public function getCount(){

        return $this->pdo->query("SELECT  COUNT(*) FROM todos")->fetchColumn();
}

}