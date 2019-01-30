<?php

namespace Wooze\Es\SearchBuilders;

class SearchBuilder
{
    protected $params = [];

    protected $fields = [];

    public function __construct($key, $fields)
    {
        $indices = config('es_indices.indices');

        if (!array_key_exists($key, $indices)) {
            throw new \Exception('es_indices中不存在'.$key);
        }

        $this->params = [
            'index' => $key,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [],
                        'must' => [],
                    ]
                ]
            ]
        ];

        $this->fields = $fields;
    }

    public function paginate($size, $page)
    {
        $this->params['body']['from'] = ($page - 1) * $size;
        $this->params['body']['size'] = $size;

        return $this;
    }

    public function keywords($keywords)
    {
        $keywords = is_array($keywords) ? $keywords : [$keywords];

        foreach ($keywords as $keyword) {
            $this->params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => $this->fields
                ],
            ];
        }
        return $this;
    }

    public function asc($field)
    {
        $this->params['body']['sort'][] = [
            $field => 'asc'
        ];

        return $this;
    }

    public function desc($field)
    {
        $this->params['body']['sort'][] = [
            $field => 'desc'
        ];

        return $this;
    }

    public function range($field, $operation, $value)
    {
        $this->params['body']['query']['bool']['filter']['range'][] = [
            $field => [$operation => $value]
        ];

        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }
}