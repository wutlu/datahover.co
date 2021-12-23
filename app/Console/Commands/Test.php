<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Server\CrawlerController as Crawler;
use Alaouy\Youtube\Facades\Youtube;
use App\Models\DataPool;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $fb = new \Facebook\Facebook([
          'app_id' => '468520428172471',
          'app_secret' => 'bf91d1e11675d2d20217480692835ce7',
          'default_graph_version' => 'v2.10',
          //'default_access_token' => '{access-token}', // optional
        ]);

        // Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
        //   $helper = $fb->getRedirectLoginHelper();
        //   $helper = $fb->getJavaScriptHelper();
        //   $helper = $fb->getCanvasHelper();
        //   $helper = $fb->getPageTabHelper();

        try {
          // Get the \Facebook\GraphNodes\GraphUser object for the current user.
          // If you provided a 'default_access_token', the '{access-token}' is optional.
          $response = $fb->get('/me', '0099a3695604c48d5ec47cf0dd15dd05');
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        $me = $response->getGraphUser();
        echo 'Logged in as ' . $me->getName();
    }
}
