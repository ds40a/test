<?php

/**  */
namespace Test\lib;

/**
 * Class Logger
 */
class Logger extends ContainerAware
{
    const FORMAT_STR = "[%s] %s %s: %s\n";

    private $filename = 'app.log';

    private $loggerName = 'app';

    private $destination;

    /**
     * @param string $loggerName
     * @param string $fileName
     */
    public function configure($loggerName = 'app', $fileName = 'app.log')
    {
        $this->filename = $fileName;
        $this->loggerName = $loggerName;
    }

    /**
     * @param $messageType
     * @param $message
     */
    public function log($messageType, $message)
    {
        if (!$this->destination) {
            $logDir = $this->get('app')->getLogDir();
            if (!\is_dir($logDir)) {
                shell_exec("mkdir -p $logDir");
            }

            $this->filename = $this->filename ?: 'app.log';
            $this->destination = sprintf('%s/%s', $logDir, $this->filename);
        }

        \error_log(
            sprintf(
                self::FORMAT_STR,
                date('Y-m-d H:i:s'),
                $this->loggerName,
                $messageType,
                $message
            ),
            3,
            $this->destination
        );
    }

    /**
     * @param $message
     */
    public function info($message)
    {
        $this->log('Info', $message);
    }

    /**
     * @param $message
     */
    public function debug($message)
    {
        $this->log('Debug', $message);
    }

    /**
     * @param $message
     */
    public function error($message)
    {
        $this->log('Error', $message);
    }

    /**
     * @param $message
     */
    public function exception($message)
    {
        $this->log('Exception', $message);
    }
}
