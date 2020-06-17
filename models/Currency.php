<?php
    /**
     * Created by PhpStorm.
     * User: user
     * Date: 17.06.2020
     * Time: 15:55
     */

    namespace models;
    use api\Core;
    use PDO;
    use DateTime;

    class Currency
    {
        private $connect;
        private $tableName = "currency";
        private $historyTableName = 'story_currency';
        private $core;

        private $ids = [
            '1' => 'USD',
            '2' => 'EUR',
            '3' => 'RUR',
        ];

        public function __construct(){
            $this->connect = (new Database())->getConnection();
            $this->core = new Core();
        }

        public function updateCurrency() {
            $newData = $this->core->getNewExRate();
            $error = '';
            $date = (new \DateTime('-1 day'))->format('Y-m-d');
            $query = "  SELECT
                          $this->tableName.id, $this->tableName.name, $this->tableName.to_ua   
                        FROM $this->tableName ";

            $data = $this->connect->prepare($query);

            $data->execute();
            // TODO error

            while ($row = $data->fetch(PDO::FETCH_ASSOC))
            {
                // TODO create if no exist
                $query = "INSERT INTO  $this->historyTableName 
                          ($this->historyTableName.currency_id, $this->historyTableName.to_ua, $this->historyTableName.date )  
                          VALUES (". $row['id'] .", '". $row['to_ua'] ."', '$date')";

                $dataQuery = $this->connect->prepare($query);

                $dataQuery->execute();
                // TODO error

                $newToUa = $newData[$row['name']];
                $query = "UPDATE $this->tableName SET $this->tableName.to_ua = '$newToUa'
                          WHERE $this->tableName.name = '". $row['name'] ."'";

                $dataQuery = $this->connect->prepare($query);

                $dataQuery->execute();
                // TODO error
            }
        }

        public function getCurrencies($id = null) {
            $returnArray = [];
            if ($id) {
                $query = "  SELECT
                          $this->tableName.name, $this->tableName.to_ua   
                        FROM $this->tableName 
                        WHERE $this->tableName.id = $id";
            } else {
                $query = "  SELECT
                          $this->tableName.id, $this->tableName.name, $this->tableName.to_ua   
                        FROM $this->tableName ";
            }

                $data = $this->connect->prepare($query);

                $data->execute();
                // TODO error
            while ($row = $data->fetchAll(PDO::FETCH_ASSOC)) {
                $returnArray['today'] = $row;
            }
            return $returnArray;
        }

        public function getHistory($id = null) {
            $returnArray = [];
            if ($id) {
                $query = "  SELECT
                          $this->historyTableName.to_ua, $this->historyTableName.date
                        FROM $this->historyTableName 
                        WHERE $this->historyTableName.currency_id = $id";
            } else {
                $query = "  SELECT
                          $this->historyTableName.currency_id, $this->historyTableName.to_ua, $this->historyTableName.date
                        FROM $this->historyTableName ";
            }

            $data = $this->connect->prepare($query);

            $data->execute();
            // TODO error
            while ($row = $data->fetchAll(PDO::FETCH_ASSOC)) {
                foreach ($row as $key => $value) {
                    $row[$key]['name'] = $this->ids[$value['currency_id']];
                }
                $returnArray['history'] = $row;
            }
            return $returnArray;
        }
    }