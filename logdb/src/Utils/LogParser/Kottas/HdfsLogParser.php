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
        "blocks" => array(),
        "sourceIp" => "",
        "destinationIps" => array(),
        "size" => "",
        "type" => "",
    );
}