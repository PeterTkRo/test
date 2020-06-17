<?php
    /**
     * Created by PhpStorm.
     * User: user
     * Date: 17.06.2020
     * Time: 15:55
     */

    namespace models;


    class Tokens
    {
        private $connect;
        private $tableName = "tokens";

        public function __construct(){
            $this->connect = (new Database())->getConnection();
        }

        public function checkToken($token) {
            $query = "  SELECT
                          $this->tableName.id   
                        FROM $this->tableName 
                        WHERE 
                           $this->tableName.token = '$token'";

            $data = $this->connect->prepare($query);

            $data->execute();

            $rowsCount = $data->rowCount();

            return ($rowsCount > 0) ? true :false;
        }
    }