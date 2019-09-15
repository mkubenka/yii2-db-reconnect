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
        $this->assertCount(1, $command->queryAll());
        sleep(10);
        $this->assertCount(1, $command->queryAll());
    }

    public function testExecuteRetry()
    {
        $this->assertEquals(1, Yii::$app->db->createCommand('SELECT NOW()')->execute());
        sleep(10);
        $this->assertEquals(1, Yii::$app->db->createCommand('SELECT NOW()')->execute());
    }

    public function testTransaction()
    {
        $this->expectException(\yii\db\Exception::class);

        Yii::$app->db->beginTransaction();

        $this->assertEquals(1, Yii::$app->db->createCommand('SELECT NOW()')->execute());
        sleep(10);
        $this->assertEquals(1, Yii::$app->db->createCommand('SELECT NOW()')->execute());
    }

}
