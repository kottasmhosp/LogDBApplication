<?php

namespace App\Utils\LogParser\Kottas;


class HdfsLogParserNamesystemLog extends HdfsLogParser
{

    /**
     * Examples of accepted logs
     * 081116 203521 19 INFO dfs.FSNamesystem: BLOCK* ask 10.250.14.224:50010 to replicate blk_-1608999687919862906 to datanode(s) 10.251.215.16:50010 10.251.71.193:50010
     * 081116 204559 19 INFO dfs.FSNamesystem: BLOCK* ask 10.251.122.65:50010 to delete  blk_-6899869435641005946
     * 081116 204638 19 INFO dfs.FSNamesystem: BLOCK* ask 10.251.71.146:50010 to replicate blk_-4304326910587241051 to datanode(s) 10.251.127.191:50010 10.251.110.196:50010
     * 081117 010147 19 INFO dfs.FSNamesystem: BLOCK* ask 10.251.123.99:50010 to delete  blk_1144694970068436138 blk_-6765102699427173469 blk_6590764659902528225 blk_4860852783621033787 blk_-7303875001410390680 blk_-935572074683789693 blk_3303452023305825407 blk_-1925792882715624840 blk_8139010917764050939 blk_8639543042781470601 blk_-6163282985589986349 blk_3024301897039930638 blk_-2078938301599741806 blk_5464269303613018077 blk_4868503342651838479 blk_2417844371316867616 blk_-160944270199923177 blk_-7614477644304773305 blk_-7842441385421377508 blk_-5114399346914160800 blk_2277730199696121615 blk_-1774382138577347758 blk_2118851193190170291 blk_1691191444137319327 blk_-2807193698972165323 blk_4075690213074306499 blk_-2052595229372582295 blk_2748492298025317659
     *
     * We assume that if there is not block number in specific potion (10th position or 11th in a space separated line) then it is bad format and we parsed as error
     *
     * @param $line
     * @return array
     */
    public function format_hdfs_Namesystem_log_line($line)
    {
        $explodedLine = explode(" ", $line);
        if (empty(intval(str_replace("blk_", "", $explodedLine[10]))) && empty(intval(str_replace("blk_", "", $explodedLine[11])))) {
            return array(
                "badEntry" => true,
                "originalLog" => $line,
                "formattedLog" => NULL
            );
        } else {
            $this->logsFormat = array(
                "timeStamp" => $explodedLine[0] . " " . $explodedLine[1],
                "sourceIp" => $explodedLine[7],
                "type" => $explodedLine[9],
                "destionationIps" => array()
            );

            $i = 0;
            $isNode = 0;
            foreach ($explodedLine as $column) {
                if($i>=10){
                    $block_id = str_replace("blk_", "", $column);
                    if(intval($block_id) && $isNode == 0){
                        $this->logsFormat['blocks'][] = $block_id;
                    } elseif($column == "datanode(s)"){
                        $isNode = 1;
                    } elseif($isNode == 1){
                        $this->logsFormat['destinationIps'][] = $column;
                    }
                }
                $i++;
            }

            if(empty($this->logsFormat["destinationIps"])){
                $this->logsFormat["destinationIps"][] = "-";
            }

            return array(
                "badEntry" => false,
                "originalLog" => $line,
                "formattedLog" => $this->logsFormat
            );
        }
    }
}