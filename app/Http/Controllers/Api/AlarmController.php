<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AlarmResource;
use Illuminate\Http\Request;
use App\Models\Alarm;
use App\Http\Controllers\Controller;

class AlarmController extends Controller
{
    public function index(Alarm $alarm)
    {
        $alarms = $alarm->orderBy('created_at','desc')->get();
        return AlarmResource::collection($alarms);

    }


}
