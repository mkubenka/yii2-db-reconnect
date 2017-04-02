<?php
namespace mkubenka\dbreconnect;

/**
 * Base Connection
 *
 * @author xjflyttp <xjflyttp@gmail.com>
 * @author Michal Kubenka
 */
class Connection extends \yii\db\Connection implements IReconnect
{
    /**
     * @var string
     */
    public $commandClass = 'mkubenka\dbreconnect\Command';

    /**
     * @var int
     */
    public $reconnectMaxCount = 3;

    /**
     * @var int
     */
    public $reconnectCurrentCount = 0;

    /**
     * @return array
     */
    public function getReconnectCodeOptions()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isMaxReconnect()
    {
        return $this->reconnectCurrentCount >= $this->reconnectMaxCount;
    }

    /**
     * Reset Reconnect Counter
     */
    public function resetReconnectCount()
    {
        $this->reconnectCurrentCount = 0;
    }

    /**
     * Increment Reconnect Counter
     */
    public function incrementReconnectCount()
    {
        $this->reconnectCurrentCount += 1;
    }

    /**
     * @param string $message
     * @return bool
     */
    public function isReconnectErrMsg($message)
    {
        $errorOptions = static::getReconnectCodeOptions();
        foreach ($errorOptions as $errCode => $errMsg) {
            if (false !== stripos($message, $errMsg)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Delay function that calculates an exponential delay.
     *
     * Exponential backoff with jitter, 100ms base, 20 sec ceiling
     *
     * @param $retries - The number of retries that have already been attempted
     *
     * @return int
     */
    public static function exponentialDelay($retries)
    {
        return mt_rand(0, (int) min(20000, (int) pow(2, $retries) * 100));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function reconnect()
    {
        $this->close();

        usleep($this->exponentialDelay($this->reconnectCurrentCount) * 1000);

        $this->open();
    }
}
