<?php
namespace mkubenka\dbreconnect\mysql;

/**
 * 定义连接丢失的错误类型
 * @author xjflyttp<xjflyttp@gmail.com>
 * @author Michal Kubenka
 */
class Connection extends \mkubenka\dbreconnect\Connection
{
    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html#error_er_lock_wait_timeout
     */
    const ER_LOCK_WAIT_TIMEOUT = 1205;

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html#error_er_lock_deadlock
     */
    const ER_LOCK_DEADLOCK = 1213;

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/error-messages-client.html#error_cr_server_gone_error
     */
    const CR_SERVER_GONE_ERROR = 2006;

    /**
     * @see https://dev.mysql.com/doc/refman/5.7/en/error-messages-client.html#error_cr_server_lost
     */
    const CR_SERVER_LOST = 2013;

    /**
     * @return array
     */
    public function getReconnectCodeOptions()
    {
        return [
            self::ER_LOCK_WAIT_TIMEOUT => 'Lock wait timeout exceeded',
            self::ER_LOCK_DEADLOCK => 'Deadlock found when trying to get lock',
            self::CR_SERVER_GONE_ERROR => 'server has gone away',
            self::CR_SERVER_LOST => 'Lost connection',
        ];
    }
}
