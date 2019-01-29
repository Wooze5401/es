<?php

namespace Wooze\Es\Commands;

use Illuminate\Console\Command;

class Migrate extends Command
{
    protected $es;

    public function __construct()
    {
        parent::__construct();
    }

    protected $signature = 'es:migrate {--key=}';

    public function handle()
    {
        $this->es = app('es');

        $key = $this->option('key');

        $indices = config('es_indices.indices');

        if (!array_key_exists($key, $indices)) {
            $msg = "config中未配置该类";
            throw new \Exception($msg);
        }



        foreach ($indices as $key => $index) {
            $this->info('正在处理索引'.$index['name']);

            if (!$this->es->indices()->exists(['index' => $index['name']])) {
                $this->info('索引不存在，准备创建');
                $this->createIndex($index);
                $this->info('创建成，准备初始化数据');
                $this->rebuild($key);
                $this->info('操作成功');
                continue;
            }

            try {
                $this->info('索引存在，准备更新');
                $this->updateIndex($index);
            } catch (\Exception $e) {
                $this->warn('更新失败，准备重建');
                $this->reCreateIndex($index, $key);
            }

            $this->info($index['name'].' 操作成功');

        }
    }

    protected function createIndex($index)
    {
        $this->es->indices()->create([
            'index' => $index['name']."_0",
            'body' => [
                'settings' => $index['settings'],
                'mappings' => [
                    '_doc' => [
                        'properties' => $index['properties'],
                    ],
                ],
                'aliases' => [
                    $index['name'] => new \stdClass(),
                ],
            ],
        ]);
    }

    protected function updateIndex($index)
    {
        $this->es->indices()->close(['index' => $index['name']]);

        $this->es->indices()->putSettings([
            'index' => $index['name'],
            'body' => $index['settings'],
        ]);

        $this->es->indices()->putMapping([
            'index' => $index['name'],
            'type' => '_doc',
            'body' => [
                '_doc' => [
                    'properties' => $index['properties'],
                ]
            ]
        ]);

        $this->es->indices()->open(['index' => $index['name']]);
    }

    protected function reCreateIndex($index, $key)
    {
        $indexInfo = $this->es->indices()->getAliases(['index' => $index['name']]);

        //取出第一个key即为索引名称
        $indexName = array_keys($indexInfo)[0];

        if (!preg_match('~_(\d+)$~', $indexName, $m)) {
            $msg = '索引名称不正确：'.$indexName;
            $this->error($msg);
            throw new \Exception($msg);
        }

        $newIndexName = $index['name'].'_'.($m[1] + 1);
        $this->info('正在创建索引'.$newIndexName);
        $this->es->indices()->create([
            'index' => $newIndexName,
            'body' => [
                'settings' => $index['settings'],
                'mappings' => [
                    '_doc' => [
                        'properties' => $index['properties'],
                    ],
                ],
            ],
        ]);

        $this->info('重建成功，准备修改别名');
        $this->es->indices()->putAlias(['index' => $newIndexName, 'name' => $index['name']]);
        $this->info('修改成功，准备删除旧索引');
        $this->es->indices()->delete(['index' => $indexName]);
        $this->info('删除成功');
        $this->info('准备重建数据');
        $this->rebuild($key);
    }

    protected function rebuild($key)
    {
        Artisan::call('es:sync', ['--key' => $key]);
    }
}
