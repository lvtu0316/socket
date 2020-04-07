<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlarmResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $value =json_decode($this->value,true);

        return [
            'id' => $this->id,
            'message' => implode(',', json_decode($this->message)),
            'value' => '温度:'.$value['温度'].', '.'湿度:'.$value['湿度'].', '.'电量:'.$value['电量'],
            'created_at' => (string) $this->created_at,
        ];
    }
}
