<?php

namespace App\Utils\LogParser\Kottas;

abstract class HdfsLogParser
{
    /**
     * @var array
     * Initialize a hdfs log formatted array
     */
    protected $logsFormat = array(
        "timeStamp" => "",
        "blockId" => "",
        "sourceIp" => "",
        "destinationIp" => "",
        "size" => "",
        "type" => "",
    );
}