<?php

namespace Charcoal\App\Email\Script;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Intra-module (`charcoal-app`) dependencies
use \Charcoal\App\Script\AbstractScript;
use \Charcoal\App\Script\CronScriptInterface;
use \Charcoal\App\Script\CronScriptTrait;
use \Charcoal\App\Email\EmailQueueManager;

/**
 *
 */
class ProcessQueueScript extends AbstractScript implements CronScriptInterface
{
    use CronScriptTrait;

    /**
     * A copy of all sent message.
     * @var array $sent
     */
    private $sent;

    /**
     * Process all messages currently in queue.
     *
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        // Unused parameter
        unset($request);

        $this->start_lock();

        $climate = $this->climate();

        $processed_callback = function($success, $failures, $skipped) use ($climate) {
            if (!empty($success)) {
                $climate->green()->out(sprintf('%s emails were successfully sent.', count($success)));
            }
            if (!empty($failures)) {
                $climate->red()->out(sprintf('%s emails were not successfully sent', count($failures)));
            }
            if (!empty($skipped)) {
                $climate->orange()->out(sprintf('%s emails were skipped.', count($skipped)));
            }
        };

        $queue_manager = new EmailQueueManager();
        $queue_manager->set_processed_callback($processed_callback);
        $queue_manager->process_queue();

        $this->stop_lock();
        return $response;
    }
}