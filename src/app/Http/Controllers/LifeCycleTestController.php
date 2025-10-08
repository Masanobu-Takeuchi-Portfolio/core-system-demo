<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LifeCycleTestController extends Controller
{

    public function showServiceProviderTest()
    {

        // $sample = app()->make('serviceProviderTest');

        // dd($test, app());
    }
    public function showServiceContainerTest(Request $request)
    {
        app()->bind('lifeCycleTest', function () {
            return 'ライフサイクルのテスト';
        });

        $test = app()->make('lifeCycleTest');
        // サービスコンテナ無しのパターン
        // $message = new Message();
        // $sample = new Sample($message);
        // $sample->run();

        // サービスコンテナapp()ありの場合
        app()->bind('sample', Sample::class);
        // $sample = app()->make('sample');
        // $sample->run();


        $sample = app()->make('serviceProviderTest');

        dd($test, $sample);

    }
}

class Sample
{
    public $message;
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
    public function run()
    {
        $this->message->send();
    }
}
class Message
{
    public function send()
    {
        echo ('メッセージ表示a');
    }
}
