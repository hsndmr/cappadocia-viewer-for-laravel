<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\ExtractProperties;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

class JobWatcher extends Watcher
{
    public function register(): void
    {
        Event::listen(JobProcessing::class, [$this, 'handleJobProcessing']);
        Event::listen(JobProcessed::class, [$this, 'handleJobProcessed']);
        Event::listen(JobFailed::class, [$this, 'handleJobFailed']);
    }

    public function handleJobProcessing(JobProcessing $event): void
    {
        if (!$this->shouldHandleJob($event->job)) {
            return;
        }

        CappadociaViewer::setMessage($event->job->resolveName())
            ->setType(ViewerType::JOB)
            ->setBadge('processing')
            ->send([
                'data' => $this->getJobData($event->job),
            ]);

    }

    public function handleJobProcessed(JobProcessed $event): void
    {
        if (!$this->shouldHandleJob($event->job)) {
            return;
        }

        CappadociaViewer::setMessage($event->job->resolveName())
            ->setType(ViewerType::JOB)
            ->setBadgeType(BadgeType::SUCCESS)
            ->setBadge('processed')
            ->send([
                'data' => $this->getJobData($event->job),
            ]);
    }

    public function handleJobFailed(JobFailed $event): void
    {
        if (!$this->shouldHandleJob($event->job)) {
            return;
        }

        CappadociaViewer::setMessage($event->job->resolveName())
            ->setType(ViewerType::JOB)
            ->setBadgeType(BadgeType::ERROR)
            ->setBadge('failed')
            ->send([
                'data'              => $this->getJobData($event->job),
                'exception_message' => $event->exception->getMessage(),
                'exception_file'    => $event->exception->getFile().':'.$event->exception->getLine(),
                'exception_trace'   => $event->exception->getTraceAsString(),
            ]);
    }

    protected function isTelescopeJob($jobName): bool
    {
        return Str::startsWith($jobName, 'Laravel\Telescope');
    }

    protected function getJobData(Job $job): array
    {
        return ExtractProperties::from(unserialize($job->payload()['data']['command']));
    }

    protected function shouldHandleJob(Job $job): bool
    {
        if (!$this->isWatching()) {
            return false;
        }

        if ($this->isTelescopeJob($this->getJobName($job))) {
            return false;
        }

        return true;
    }

    protected function getJobName(Job $job): string
    {
        return $job->resolveName();
    }

    protected function getConfigName(): string
    {
        return 'jobs';
    }
}
