<?php namespace Gitpi\Commands;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{

    public function configure()
    {
        $this->setName('search')
            ->setDescription('Search for a user on github.')
            ->addArgument('term', InputArgument::REQUIRED, 'The term which we will use to search github.');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->search($input->getArgument('term'));

        $output->writeln("<info>We have found {$result['total_count']} records.</info>");

        $table = new Table($output);
        $table->addRows($result['result'])->render();

    }


    private function search($term)
    {
        $client = new Client([
            'base_uri' => 'https://api.github.com/'
        ]);

        $response = $client->get('/search/users', [
            'query' => "q={$term}"
        ]);

        $body                = json_decode($response->getBody());
        $data['total_count'] = $body->total_count;
        $data['result'][]    = ['id', 'login', 'score'];

        foreach ($body->items as $item) {
            $data['result'][] = [
                $item->id,
                $item->login,
                $item->score
            ];
        }

        return $data;
    }
}