<?php

namespace App\Utils\LogParser\Kottas;

/**
 * Extends Hdfs log parser
 *
 */
class HdfsLogParserDataXReceiverLog extends HdfsLogParser
{

    /**
     * Examples of accepted logs
     * 081116 203518 143 INFO dfs.DataNode$DataXceiver: Receiving block blk_-1608999687919862906 src: /10.250.19.102:54106 dest: /10.250.19.102:50010
     * 081118 005410 17129 WARN dfs.DataNode$DataXceiver: 10.250.15.67:50010:Got exception while serving blk_702432797797823248 to /10.251.201.204:
     * 081116 203523 148 INFO dfs.DataNode$DataXceiver: 10.250.11.100:50010 Served block blk_-3544583377289625738 to /10.250.19.102
     *
     * Example of not parsed logs
     * 081116 205120 741 INFO dfs.DataNode$DataXceiver: writeBlock blk_6445911672318944838 received exception java.io.IOException: Could not read from stream
     */
    private function format_hdfs_DataXReceiver_log_line($line, $type)
    {
        $explodedLine = explode(" ", $line);
        if (strpos($explodedLine, "java") !== false) {
            return array(
                "badEntry" => true,
                "originalLog" => $line,
                "formattedLog" => NULL
            );
        } else {
            if ($explodedLine[3] == 'INFO') {
                if ($explodedLine[10] == 'dest:') {
                    $formatted_line = array(
                        "timeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                        "blockId" => $explodedLine[7],
                        "sourceIp" => $explodedLine[10],
                        "destinationIp" => $explodedLine[11],
                        "size" => $explodedLine[6],
                        "type" => $explodedLine[5]
                    );
                } else {
                    $formatted_line = array(
                        "timeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                        "blockId" => $explodedLine[8],
                        "sourceIp" => $explodedLine[10],
                        "destinationIp" => $explodedLine[11],
                        "size" => $explodedLine[7],
                        "type" => $explodedLine[6]
                    );
                }
                return array(
                    "badEntry" => false,
                    "originalLog" => $line,
                    "formattedLog" => $formatted_line
                );
            } elseif ($explodedLine[3] == 'WARN') {
                //Destination Ip is before type Ip
                $exceptionLineExploded = explode(":", $explodedLine[5]);
                $formatted_line = array(
                    "timeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                    "blockId" => $explodedLine[10],
                    "sourceIp" => $exceptionLineExploded[0],
                    "destinationIp" => $explodedLine[12],
                    "size" => $explodedLine[9],
                    "type" => $explodedLine[8]
                );
                return array(
                    "badEntry" => true,
                    "originalLog" => $line,
                    "formattedLog" => $formatted_line
                );
            }
        }
    }
}