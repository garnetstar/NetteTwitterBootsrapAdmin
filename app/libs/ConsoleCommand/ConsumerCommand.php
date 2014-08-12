<?php
namespace ConsoleCommand;


use PhpAmqpLib\Connection\AMQPConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('admin:consumer')
			->setDescription('Creates test consumer')
			->addArgument('consumerID', InputArgument::REQUIRED, 'consumerID');
//			->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable Debugging', false);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();

		$consumerID = $input->getArgument('consumerID');

		//$channel->queue_declare('hello', false, false, false, false);

		echo " [{$consumerID}] Waiting for messages. To exit press CTRL+C", "\n";

		$callback = function ($msg) use ($consumerID)
		{
			$message = " [{$consumerID}] Received " . $msg->body . "\n";
			echo $message;
			file_put_contents("logg", $message, FILE_APPEND);
		};

		$channel->basic_consume('garnetstar', '', false, true, false, false, $callback);

		while (count($channel->callbacks))
		{
			{
				$channel->wait();
			}
		}
	}
}