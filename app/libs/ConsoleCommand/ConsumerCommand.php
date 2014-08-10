<?php
namespace ConsoleCommand;


use PhpAmqpLib\Connection\AMQPConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumerCommand extends Command
{

	protected function configure()
	{
		$this
			->setName('admin:consumer')
			->setDescription('Creates test consumer');
//			->addArgument('name', InputArgument::REQUIRED, 'Producer Name')
//			->addOption('debug', 'd', InputOption::VALUE_OPTIONAL, 'Enable Debugging', false);
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$connection = new AMQPConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();

        $channel->exchange_declare('logs', 'fanout', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);

        $channel->queue_bind($queue_name, 'logs');

        echo ' [*] Waiting for logs. To exit press CTRL+C', "\n";

        $callback = function($msg){
            echo ' [x] ', $msg->body, "\n";
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        while(count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
	}
} 