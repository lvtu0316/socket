<?php
function hex2readable( $str)
{
    //去掉空格
    $str = preg_replace('# #', '', $str);
    $ft = substr($str,0,2);//包头FT
    $sn=substr($str,2,12);//设备编号SN
    $funCode = substr($str, 14,2);//功能码
    $dataLength = substr($str, 16, 4 );//数据长度
    $len = hexdec($dataLength);
    $data = substr($str,20, $len*2 );

    $res['data'] = typeMatch($data, $sn);
    $res['alarm'] = alarm($data);
    if (count($res['alarm'])>0)
    {
        $alarm = new \App\Models\Alarm();
        $alarm->message = json_encode($res['alarm']);
        $alarm->value = json_encode($res['data']);
        $alarm->save();
    }

    return $res;

}

/**
 * 根据sn类型匹配
 * @param $data 数据项
 * @param $sn  sn编号
 * @return mixed
 */
function typeMatch($data,$sn)
{
    $length = strlen($data);
    $typeCode = substr($sn, 0,4);//类型匹配标识位
    $typeCode2 = decbin(hexdec($typeCode));//2进制类型匹配标识位
    $arrType = str_split($typeCode2); // 分割为数组
    $start = 0;
    if ($arrType[0] == 1)//温度
    {
        $res['温度'] = (hexdec(substr($data, $start,4))*0.1).'℃';
        $start = $start + 4;
    }
    if ($arrType[1] == 1)//湿度
    {
        $res['湿度'] = (hexdec(substr($data,$start,4))/1000).'%RH';
        $start = $start + 4;

    }
    if ($arrType[2] == 1)//甲醛
    {
        $res['甲醛'] = (hexdec(substr($data,$start,4))/1000).'mg/m3';
        $start = $start + 4;

    }
    if ($arrType[3] == 1) //TVOC
    {
        $res['TVOC'] = (hexdec(substr($data,$start,4))/100).'mg/m3';
        $start = $start + 4;
    }
    if ($arrType[4] == 1)
    {
        $res['PM2.5'] = (hexdec(substr($data,$start,4))/10).'μg/m3';
        $start = $start + 4;

    }
    if ($arrType[5] == 1)
    {
        $res['PM1.0'] = (hexdec(substr($data,$start,4))/10).'μg/m3';
        $start = $start + 4;

    }
    if ($arrType[6] == 1)
    {
        $res['PM10'] = (hexdec(substr($data,$start,4))/10).'μg/m3';
        $start = $start + 4;
    }
    if ($arrType[7] == 1)
    {
        $res['二氧化碳'] = (hexdec(substr($data,$start,4))/10).'ppm';
        $start = $start + 4;


    }
    if ($arrType[8] == 1)
    {
        $res['氨气'] = (hexdec(substr($data,$start,4))/10).'ppm';
        $start = $start + 4;
    }
    if ($arrType[9] == 1)
    {
        $res['经度'] = hexdec(substr($data,$start,8)).'ppm';
        $res['纬度'] = hexdec(substr($data,$start+8,8)).'ppm';
        $start = $start + 16;



    }

    if ($arrType[14] == 1)
    {
        $res['电量'] = hexdec(substr($data,$start,2)).'格';
        $start = $start + 16;

    }

    return $res;


}

/**
 * @param $data   数据项
 * @return array 报警信息
 */
function alarm($data)
{
    $alarmCode = substr($data, 10,4);//告警状态
    $alarmCode2 = decbin(hexdec($alarmCode));//2进制
    $arrAlarm = str_split($alarmCode2); // 分割为数组
    $len = strlen($alarmCode2);
    $res = [];
    $alarmMessage = ['高温告警','低温告警','高湿告警','低湿告警','甲醛超标',
        'TVOC超标','PM2.5超标','PM1.0超标','PM10超标','二氧化碳超标','氨气',
        '掉电告警'];
    for ($i=1;$i<=$len;$i++)
    {
        if ($arrAlarm[$len-$i] == 1)
        {
            array_push($res, $alarmMessage[$i-1]);
        }

    }


    return $res;


}