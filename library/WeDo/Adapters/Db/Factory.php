<?php

class WeDo_Adapters_Db_Factory
{

    /**
     * returns adapter based on 'adapter' attribute
     * @param unknown_type $simplexmlDescriptor
     */
    public static function getAdapter(&$simplexmlDescriptor)
    {
        try {
            $adapterType = $simplexmlDescriptor['adapter'];

            $uri = $simplexmlDescriptor->uri;
            $login = $simplexmlDescriptor->login;
            $password = $simplexmlDescriptor->password;

            $upon_connection = strval($simplexmlDescriptor->params->upon_connection);
            $conn_name = $simplexmlDescriptor['connection_name'];

            list($host, $database_name) = explode("/", $uri);

            switch ($adapterType)
            {
                case 'mysql':
                    return new WeDo_Adapters_Db_Mysql_Adapter($conn_name, $host, $login, $password, $database_name, $upon_connection);
                 case 'pdo'://require_once APP_PATH . "code/core/adapters/MysqlAdapter.class.php";
                    return new WeDo_Adapters_Db_PDO_Adapter($conn_name, $host, $login, $password, $database_name, $upon_connection);
                case 'mysqli':
                    return new WeDo_Adapters_Db_Mysqli_Adapter($conn_name, $host, $login, $password, $database_name, $upon_connection);
                case 'mongo':
                    return new WeDo_Adapters_Db_Mongo_Adapter($conn_name, $host, $login, $password, $database_name, $upon_connection);
                default:
                    throw new Exception("Adapter not found");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

}