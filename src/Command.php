<?php
namespace mkubenka\dbreconnect;

use Yii;
use yii\db\Exception;

/**
 * Base Command
 *
 * @author xjflyttp <xjflyttp@gmail.com>
 * @author Michal Kubenka
*/
class Command extends \yii\db\Command
{
    /**
     * Cache PendingParams
     * @var array
     */
    protected $_prevPendingParams = [];

    /**
     * 目标捕获pendingParams
     * @param string|integer $name Parameter identifier. For a prepared statement
     * using named placeholders, this will be a parameter name of
     * the form `:name`. For a prepared statement using question mark
     * placeholders, this will be the 1-indexed position of the parameter.
     * @param mixed $value The value to bind to the parameter
     * @param integer $dataType SQL data type of the parameter. If null, the type is determined by the PHP type of the value.
     * @return $this the current command being executed
     * @see http://www.php.net/manual/en/function.PDOStatement-bindValue.php
     */
    public function bindValue($name, $value, $dataType = null)
    {
        $this->_prevPendingParams[$name] = [$value, $dataType];
        return parent::bindValue($name, $value, $dataType);
    }

    /**
     * 目标捕获pendingParams
     * @param array $values the values to be bound. This must be given in terms of an associative
     * array with array keys being the parameter names, and array values the corresponding parameter values,
     * e.g. `[':name' => 'John', ':age' => 25]`. By default, the PDO type of each value is determined
     * by its PHP type. You may explicitly specify the PDO type by using an array: `[value, type]`,
     * e.g. `[':name' => 'John', ':profile' => [$profile, \PDO::PARAM_LOB]]`.
     * @return $this the current command being executed
     */
    public function bindValues($values)
    {
        $schema = $this->db->getSchema();
        foreach ($values as $name => $value) {
            if (is_array($value)) {
                $this->_prevPendingParams[$name] = $value;
            } else {
                $type = $schema->getPdoType($value);
                $this->_prevPendingParams[$name] = [$value, $type];
            }
        }
        return parent::bindValues($values);
    }

    /**
     * 重新绑定值
     * @return $this the current command being executed
     */
    public function rebindValues()
    {
        foreach ($this->_prevPendingParams as $name => $data) {
            $value = $data[0];
            $dataType = !empty($data[1]) ? $data[1] : null;
            parent::bindValue($name, $value, $dataType);
        }
        return $this;
    }

    /**
     * 清空 pdoStatment
     * 重新绑定 pendingParams
     * @return $this the current command being executed
     */
    public function prepareForReconnect()
    {
        $this->cancel();
        $this->rebindValues();
        return $this;
    }

    /**
     * Performs the actual DB query of a SQL statement.
     * @param string $method method of PDOStatement to be called
     * @param integer $fetchMode the result fetch mode. Please refer to [PHP manual](http://www.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return mixed the method execution result
     * @throws Exception if the query causes any problem
     * @since 2.0.1 this method is protected (was private before).
     */
    protected function queryInternal($method, $fetchMode = null)
    {
        $db = $this->db;
        /* @var $db IReconnect */
        try {
            $result = parent::queryInternal($method, $fetchMode);
            $db->resetReconnectCount();
            return $result;
        } catch (Exception $e) {
            if ($db->isReconnectErrMsg($e->getMessage()) === false && $db->getTransaction() !== null) {
                throw $e;
            }
            Yii::info('Lost connection', __METHOD__);
            if (true === $db->isMaxReconnect()) {
                Yii::error('ReconnectCounter is max', __METHOD__);
                throw $e;
            }
            //BEGIN RECONNECT
            Yii::info('Reconnect', __METHOD__);
            $db->reconnect();
            $db->incrementReconnectCount();
            $this->prepareForReconnect();

            //REQUERY
            return $this->queryInternal($method, $fetchMode);
        }
    }

    /**
     * Executes the SQL statement.
     * This method should only be used for executing non-query SQL statement, such as `INSERT`, `DELETE`, `UPDATE` SQLs.
     * No result set will be returned.
     * @return integer number of rows affected by the execution.
     * @throws Exception execution failed
     */
    public function execute()
    {
        $db = $this->db;
        /* @var $db IReconnect */
        try {
            $result = parent::execute();
            $db->resetReconnectCount();
            return $result;
        } catch (Exception $e) {
            if ($db->isReconnectErrMsg($e->getMessage()) === false && $db->getTransaction() !== null) {
                throw $e;
            }
            Yii::info('Lost connection', __METHOD__);
            if (true === $db->isMaxReconnect()) {
                Yii::error('ReconnectCounter is max', __METHOD__);
                throw $e;
            }
            //BEGIN RECONNECT
            Yii::info('Reconnect', __METHOD__);
            $db->reconnect();
            $db->incrementReconnectCount();
            $this->prepareForReconnect();

            return $this->execute();
        }
    }
}
