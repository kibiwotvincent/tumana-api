<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Crypto\Rsa\KeyPair;

class JengaKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jenga:keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create private and public keys for use in Jenga API  signature';

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
        // save generated keys to specified paths
        $privateKeyPath = config('jenga.keys_path').'/jenga.key';
        $publicKeyPath =  config('jenga.keys_path').'/jenga.pub.key';
        
        (new KeyPair())->generate($privateKeyPath, $publicKeyPath);
        $this->info('Jenga API keys have been generated successfully.');
        
        return self::SUCCESS;
    }
}
