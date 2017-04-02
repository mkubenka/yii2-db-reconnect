<?php

class MysqlTest extends TestCase {

	public function setUp()
	{
		parent::setUp();

		$this->mockApplication([
			'components' => [
				'db' => [
                    'class' => 'mkubenka\dbreconnect\mysql\Connection',
                    'reconnectMaxCount' => 2,
                    'dsn' => 'mysql:host=mysql;dbname=test',
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf8',
                    'on ' . yii\db\Connection::EVENT_AFTER_OPEN => function () {
                        Yii::$app->db->createCommand('SET SESSION wait_timeout = 5')->execute();
                    },
				],
			]
		]);
	}

    public function testQueryRetry()
    {
        $command = Yii::$app->db->createCommand('SELECT NOW()');
        $command->queryAll();
        sleep(10);
        $command->queryAll();

        $this->assertTrue(true);
    }

    public function testExecuteRetry()
    {
        Yii::$app->db->createCommand('SELECT NOW()')->execute();
        sleep(10);
        Yii::$app->db->createCommand('SELECT NOW()')->execute();

        $this->assertTrue(true);
    }

}
