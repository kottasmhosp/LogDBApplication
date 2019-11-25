<?php

namespace App\Utils\LogParser\Kottas;

class AccessLogParser
{
    private $formated_log = array(
        "ip" => "",
        "identity" => "",
        "user" => "",
        "date" => "",
        "time" => "",
        "timezone" => "",
        "method" => "",
        "path" => "",
        "protocal" => "",
        "status" => "",
        "bytes" => "",
        "referer" => "",
        "agent" => ""
    );

    private $allowed_methods = [
        "GET",
        "PUT",
        "POST",
        "DELETE",
        "UPDATE",
        "HEAD",
        "OPTIONS",
        "CONNECT",
        "TRACE"
        ];

    //Access.log Formatter
    private function format_log_line($line)
    {
        preg_match("/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/", $line, $matches); // pattern to format the line
        return $matches;
    }

    public function format_line($line)
    {
        $logs = $this->format_log_line($line); // format the line

        if (isset($logs[0]) &&  in_array($logs[7],$this->allowed_methods) == true) // check that it formated OK
        {
            $formated_log['ip'] = str_replace(array("\\","[:[0-9]+]"),"",$logs[1]);
            $formated_log['identity'] = str_replace(array("\\","[:[0-9]+]"),"",$logs[2]);
            $formated_log['user'] = $logs[3];
            $formated_log['date'] = $logs[4];
            $formated_log['time'] = $logs[5];
            $formated_log['timezone'] = $logs[6];
            $formated_log['method'] = $logs[7];
            $formated_log['path'] = $logs[8];
            $formated_log['protocal'] = $logs[9];
            $formated_log['status'] = $logs[10];
            $formated_log['bytes'] = $logs[11];
            $formated_log['referer'] = $logs[12];
            $formated_log['agent'] = $logs[13];
            return array(
                "badEntry" => false,
                "originalLog" => $line,
                "formattedLog" => $formated_log
            );
        } else {
            return array(
                "badEntry" => true,
                "originalLog" => $line,
                "formattedLog" => NULL
            );
        }
    }
}