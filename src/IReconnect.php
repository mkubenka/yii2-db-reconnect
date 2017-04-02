<?php
namespace mkubenka\dbreconnect;

/**
 * Interface IReconnect
 * @author xjflyttp <xjflyttp@gmail.com>
 * @author Michal Kubenka
 */
interface IReconnect
{
    public function getReconnectCodeOptions();

    public function isMaxReconnect();

    public function resetReconnectCount();

    public function incrementReconnectCount();

    public function isReconnectErrMsg($message);

    public function reconnect();
}
