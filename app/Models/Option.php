<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key, bool $selectKey = false)
    {
        $items = $this->where('key', 'ILIKE', str_replace('*', '%', $key))->pluck('value', 'key')->toArray();

        return $selectKey ? $items[$key] : $items;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public function change(string $key, $value)
    {
        $this->where('key', $key)->update([ 'value' => (is_array($value) ? json_encode($value) : $value) ]);

        return [
            'success' => 'ok'
        ];
    }
}
