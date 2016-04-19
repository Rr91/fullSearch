<?php
	/**
	 Полнотекстовый поиск по сайту
	*/
    require_once "config_class.php";        
    
    class DataBase{
    
        private $config;
        private $mysqli;
        
        public function __construct()
        {
            $this->config = new Config();
            $this->mysqli = new mysqli($this->config->host, $this->config->user,$this->config->password,$this->config->db);
            $this->mysqli->query("SET NAMES 'utf8'");   
        }
        
        private function query($query){ 

            return $this->mysqli->query($query);
        }

        private function createFullTextIndex($table_name){
            $this->query("CREATE FULLTEXT INDEX ixFullText ON $table_name(*)");
        }

        private function searchFullTextIndex($table_name, $words)
        {
            $result_set = $this->query("
                        SELECT * FROM $table_name 
                        WHERE MATCH (*)
                        AGAINST('$words' IN NATURAL LANGUAGE MODE)"
            );

            if(!$result_set) return false;

            $i = 0;
            while ($row = $result_set->fetch_assoc()){
                $data[$i] = $row;
                $i++;
            } 
            $result_set->close();
            return $data;
        }

        public function search($table_name, $words)
        {      
            $words = mb_strtolower($words);
            if($words == "") return false;  
            $result = searchFullTextIndex($table_name, $words);
            if(!$result) return false;
            return $result;
        }
}
?>