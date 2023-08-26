<?php

namespace App\Prosegur\Console\Commands;

use App\Prosegur\Processors\EmailProcessor;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class GetAllAlarms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:prosegur {--limit=10} {--page=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all alarms from Prosegur mailbox';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = Client::account("default");  
        $client->connect();  
        $time = date('Y-m-d H:i:s', time());
        $this->info($time . 'Connected to the mailbox');
        
        /** @var \Webklex\PHPIMAP\Support\FolderCollection $folders */  
        $inbox_folder = $client->getFolder('INBOX');
        $time = date('Y-m-d H:i:s', time());
        $this->info($time . 'Got the inbox folder');

        # Get the last n messages from the inbox folder
        $limit = $this->option('limit');
        $page = $this->option('page');
        $messages = $inbox_folder->query()->all()->limit($limit, $page)->get();;
        $time = date('Y-m-d H:i:s', time());
        $this->info($time . 'Got the messages from the inbox folder');
        # Iterate over the messages list and save the alarms in the database, if they don't exist
        
        $bar = $this->output->createProgressBar(count($messages));
        $processor = new EmailProcessor();
        foreach ($messages as $message) {
            $processor->process($message);
            $bar->advance();
        }
        $bar->finish();
        return 0;
    }
}
