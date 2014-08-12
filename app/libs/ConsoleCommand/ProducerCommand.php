<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 8.8.14
 * Time: 8:43
 */

namespace ConsoleCommand;


use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('admin:producer')
			->setDescription('Creates test consumer');
//			->addArgument('name', InputArgument::REQUIRED, 'Producer Name')
//			->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable Debugging', false);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
//		$channel->queue_declare('hello', false, false, false, false);

		for ($i = 1;$i <= 100;$i++)
		{
			usleep(1000);
			$msg = new AMQPMessage("[{$i}]  Hello World 2!");
			$channel->basic_publish($msg, "", "garnetstar");
		}
	}
}