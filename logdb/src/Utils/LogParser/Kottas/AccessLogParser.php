<?php


class AccessLogParser
{
    private $bad_rows; // Number of bad rows

    //Access.log Formatter
    private function format_log_line($line)
    {
        preg_match("/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) (\".*?\") (\".*?\")$/", $line, $matches); // pattern to format the line
        return $matches;
    }

    private function format_line($line)
    {
        $logs = $this->format_log_line($line); // format the line

        if (isset($logs[0])) // check that it formated OK
        {
            $formated_log = array(); // make an array to store the lin info in
            $formated_log['ip'] = $logs[1];
            $formated_log['identity'] = $logs[2];
            $formated_log['user'] = $logs[2];
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
            return $formated_log; // return the array of info
        }
        else
        {
            $this->badRows++; // if the row is not in the right format add it to the bad rows
            return false;
        }
    }
}