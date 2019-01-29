<?php

namespace Wooze\Es\Models;

use Illuminate\Database\Eloquent\Model;

class EsModel extends Model
{
    protected $esArray = [];


    public function toESArray()
    {
        $arr = array_only($this->toArray(), $this->esArray);
        return $arr;
    }
}
