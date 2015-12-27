<?php

/**
 * Class ApacheStatus
 *
 * Grabs server status information for given host. Uses curl to grab /server-status/?auto
 */
class ApacheStatus
{

    /**
     * @var int
     */
    protected $totalAccesses;

    /**
     * @var int
     */
    protected $totalKBytes;

    /**
     * @var float
     */
    protected $cpuLoad;

    /**
     * @var int
     */
    protected $uptime;

    /**
     * @var float
     */
    protected $reqPerSec;

    /**
     * @var float
     */
    protected $bytesPerSec;

    /**
     * @var float
     */
    protected $bytesPerReq;

    /**
     * @var int
     */
    protected $busyWorkers;

    /**
     * @var int
     */
    protected $idleWorkers;

    /**
     * @var string
     */
    protected $scoreboard;

    /**
     * @var float a derived utilization percentage based on the scoreboard
     */
    protected $utilization;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * ApacheStatus constructor.
     * @param $hostname
     * @param int $port
     * @param string $proto
     */
    public function __construct($hostname, $port = 80, $proto = 'http')
    {
        $url = $proto . '://' . $hostname . ':' . $port . '/server-status/?auto';

        $this->data = $this->_curl($url);

        $this->_parse();

        return $this;

    }

    /**
     * Grabs server-status information from curl output.
     * @return null
     */
    protected function _parse()
    {
        $this->totalAccesses = (int) $this->_match('/Total Accesses: (.*)/');
        $this->totalKBytes = (int) $this->_match('/Total kBytes: (.*)/');
        $this->cpuLoad = (float) $this->_match('/CPULoad: (.*)/');
        $this->uptime = (int) $this->_match('/^Uptime: (.*)$/m');
        $this->reqPerSec = (float) $this->_match('/ReqPerSec: (.*)/');
        $this->bytesPerSec = (float) $this->_match('/BytesPerSec: (.*)/');
        $this->bytesPerReq = (float) $this->_match('/BytesPerReq: (.*)/');
        $this->busyWorkers = (int) $this->_match('/BusyWorkers: (.*)/');
        $this->idleWorkers = (int) $this->_match('/IdleWorkers: (.*)/');


        $this->scoreboard = $this->_match('/Scoreboard: (.*)/');

        $length = strlen($this->scoreboard);
        $used = 0;
        foreach (str_split($this->scoreboard) as $i) {
            if ($i != '.' AND $i != '_') {
                $used++;
            }
        }

        $this->utilization = $used / $length;

        return null;

    }

    /**
     * Returns first matched regex.
     * @param $pattern string regex pattern to match.
     * @return mixed
     */
    protected function _match($pattern)
    {
        $matches = [];

        if (preg_match($pattern, $this->data, $matches)) {
            $match = $matches[1];
        } else {
            $match = 0;
        }

        return $match;
    }

    /**
     * Curls a URL and returns contents.
     * @param $url
     * @return mixed
     */
    protected function _curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $content = curl_exec($ch);

        return $content;
    }

    /**
     * @return int
     */
    public function getTotalAccesses()
    {
        return $this->totalAccesses;
    }

    /**
     * @return int
     */
    public function getTotalKBytes()
    {
        return $this->totalKBytes;
    }

    /**
     * @return float
     */
    public function getCpuLoad()
    {
        return $this->cpuLoad;
    }

    /**
     * @return int
     */
    public function getUptime()
    {
        return $this->uptime;
    }

    /**
     * @return float
     */
    public function getReqPerSec()
    {
        return $this->reqPerSec;
    }

    /**
     * @return float
     */
    public function getBytesPerSec()
    {
        return $this->bytesPerSec;
    }

    /**
     * @return float
     */
    public function getBytesPerReq()
    {
        return $this->bytesPerReq;
    }

    /**
     * @return int
     */
    public function getBusyWorkers()
    {
        return $this->busyWorkers;
    }

    /**
     * @return int
     */
    public function getIdleWorkers()
    {
        return $this->idleWorkers;
    }

    /**
     * @return string
     */
    public function getScoreboard()
    {
        return $this->scoreboard;
    }

    /**
     * @return float
     */
    public function getUtilization()
    {
        return $this->utilization;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }



}