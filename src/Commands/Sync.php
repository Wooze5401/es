<?php

namespace Wooze\Es\Commands;

use Illuminate\Console\Command;

class Sync extends Command
{
    protected $signature = 'es:sync {--key=}';


    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $es = app('es');

        $key = $this->option('key');


        $indices = config('es_indices.indices');

        if (array_key_exists($key, $indices)) {
            throw new \Exception('Key不存在');
        }

        $class = $indices[$key]['model'];

        $class::query()
            ->chunkById(100, function ($items) use ($es, $key) {
                $this->info(sprintf('正在同步ID范围为 %s 至 %s 的数据', $items->first()->id, $items->last()->id));
                $req = ['body' => []];

                foreach ($items as $item) {
                    $data = $item->toESArray();

                    $req['body'][] = [
                        'index' => [
                            '_index' => $key,
                            '_type' => '_doc',
                            '_id' => $data['id'],
                        ],
                    ];

                    $req['body'][] = $data;
                }

                try {
                    //bulk批量创建
                    $es->bulk($req);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
            });
        $this->info('同步完成');
    }
}
