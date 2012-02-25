<?php
    /**
     *
     */
    class TestService
    {
        /**
         * @route           /test/conneg
         * @routeMethods    POST
         */
        public function conneg($bob)
        {
            return $bob;
//            return array("toplevel" => array("nextlevel" => "value", "data"));
//            return Doctrine_Manager::getInstance()->connection()->getDbh()->query("SELECT * FROM User LIMIT 100")->fetchAll(PDO::FETCH_OBJ);
        }
    }
