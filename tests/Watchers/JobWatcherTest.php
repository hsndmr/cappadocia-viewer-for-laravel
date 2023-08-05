<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\Facades\Event;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Hsndmr\CappadociaViewer\Enums\BadgeType;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Hsndmr\CappadociaViewer\Watchers\JobWatcher;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

it('registers to listen for Job events', function (): void {
    // Arrange
    $jobWatcher = new JobWatcher();

    // Act & Assert
    Event::partialMock()
        ->shouldReceive('listen')
        ->with(JobProcessing::class, [$jobWatcher, 'handleJobProcessing'])
        ->once();

    Event::partialMock()
        ->shouldReceive('listen')
        ->with(JobProcessed::class, [$jobWatcher, 'handleJobProcessed'])
        ->once();

    Event::partialMock()
        ->shouldReceive('listen')
        ->with(JobFailed::class, [$jobWatcher, 'handleJobFailed'])
        ->once();

    $jobWatcher->register();
});

it('handles handleJobProcessing event correctly', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock   = $this->mock(Job::class);
    $eventMock = $this->mock(JobProcessing::class,
        function (MockInterface $mock) use ($jobMock): void {
            $mock->job = $jobMock;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnTrue();

    $jobWatcher
        ->shouldReceive('getJobData')
        ->with($jobMock)
        ->once()
        ->andReturn([]);

    $jobMock
        ->shouldReceive('resolveName')
        ->andReturn('jobName')
        ->once();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->once()
        ->with('jobName')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setType')
        ->once()
        ->with(ViewerType::JOB)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadge')
        ->once()
        ->with('processing')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('send')
        ->with([
            'data' => [],
        ])
        ->once();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobProcessing($eventMock);
});

it('it should not handleJobProcessing if shouldHandleJob returns false', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock   = $this->mock(Job::class);
    $eventMock = $this->mock(JobProcessing::class,
        function (MockInterface $mock) use ($jobMock): void {
            $mock->job = $jobMock;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnFalse();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->never();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobProcessing($eventMock);
});

it('handles handleJobProcessed event correctly', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock   = $this->mock(Job::class);
    $eventMock = $this->mock(JobProcessed::class,
        function (MockInterface $mock) use ($jobMock): void {
            $mock->job = $jobMock;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnTrue();

    $jobWatcher
        ->shouldReceive('getJobData')
        ->with($jobMock)
        ->once()
        ->andReturn([]);

    $jobMock
        ->shouldReceive('resolveName')
        ->andReturn('jobName')
        ->once();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->once()
        ->with('jobName')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setType')
        ->once()
        ->with(ViewerType::JOB)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadgeType')
        ->once()
        ->with(BadgeType::SUCCESS)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadge')
        ->once()
        ->with('processed')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('send')
        ->with([
            'data' => [],
        ])
        ->once();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobProcessed($eventMock);
});

it('it should not handleJobProcessed if shouldHandleJob returns false', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock   = $this->mock(Job::class);
    $eventMock = $this->mock(JobProcessed::class,
        function (MockInterface $mock) use ($jobMock): void {
            $mock->job = $jobMock;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnFalse();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->never();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobProcessed($eventMock);
});

it('handles handleJobFailed event correctly', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $exception = new Exception('test');

    $jobMock = $this->mock(Job::class);

    $eventMock = $this->mock(JobFailed::class,
        function (MockInterface $mock) use ($exception, $jobMock): void {
            $mock->job       = $jobMock;
            $mock->exception = $exception;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnTrue();

    $jobWatcher
        ->shouldReceive('getJobData')
        ->with($jobMock)
        ->once()
        ->andReturn([]);

    $jobMock
        ->shouldReceive('resolveName')
        ->andReturn('jobName')
        ->once();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->once()
        ->with('jobName')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setType')
        ->once()
        ->with(ViewerType::JOB)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadgeType')
        ->once()
        ->with(BadgeType::ERROR)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadge')
        ->once()
        ->with('failed')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('send')
        ->with([
            'data'              => [],
            'exception_message' => $exception->getMessage(),
            'exception_file'    => $exception->getFile().':'.$exception->getLine(),
            'exception_trace'   => $exception->getTraceAsString(),
        ])
        ->once();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobFailed($eventMock);
});

it('it should not handleJobFailed if shouldHandleJob returns false', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock   = $this->mock(Job::class);
    $eventMock = $this->mock(JobFailed::class,
        function (MockInterface $mock) use ($jobMock): void {
            $mock->job = $jobMock;
        }
    );

    $jobWatcher
        ->shouldReceive('shouldHandleJob')
        ->once()
        ->with($jobMock)
        ->andReturnFalse();

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->never();

    /* @var JobWatcher $jobWatcher */
    $jobWatcher->handleJobFailed($eventMock);
});

it('returns config name correctly', function (): void {
    // Arrange
    $jobWatcher = new JobWatcher();

    $getConfigNameReflection = new ReflectionMethod($jobWatcher, 'getConfigName');

    // Act & Assert
    expect($getConfigNameReflection->invoke($jobWatcher))->toBe('jobs');
});

it('returns job data correctly', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock = $this->mock(Job::class);

    $jobMock
        ->shouldReceive('payload')
        ->andReturn([
            'data' => [
                'command' => serialize(['command']),
            ],
        ]);

    $getJobDataReflection = new ReflectionMethod($jobWatcher, 'getJobData');

    // Act
    $jobData = $getJobDataReflection->invoke($jobWatcher, $jobMock);

    // Assert
    expect($jobData)->toBe(['command']);
});

it('identifies Laravel Telescope job correctly', function (): void {
    // Arrange
    $jobWatcher = new JobWatcher();

    // Act
    $isTelescopeJobReflection = new ReflectionMethod($jobWatcher, 'isTelescopeJob');
    $isTelescopeJob           = $isTelescopeJobReflection->invoke($jobWatcher, 'Laravel\Telescope\Watchers\JobWatcher');

    // Assert
    expect($isTelescopeJob)->toBeTrue();
});

it('does not handle job when not watching', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock = $this->mock(Job::class);

    $jobWatcher
        ->shouldReceive('isWatching')
        ->andReturnFalse();

    // Act
    $shouldHandleJobReflection = new ReflectionMethod($jobWatcher, 'shouldHandleJob');
    $resultOfShouldHandleJob   = $shouldHandleJobReflection->invoke($jobWatcher, $jobMock);

    // Assert
    /* @var JobWatcher $jobWatcher */
    expect($resultOfShouldHandleJob)->toBeFalse();
});

it('does not handle job when job is a Telescope job', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock = $this->mock(Job::class);

    $jobWatcher
        ->shouldReceive('isWatching')
        ->andReturnTrue();

    $jobWatcher
        ->shouldReceive('getJobName')
        ->andReturn('jobName');

    $jobWatcher
        ->shouldReceive('isTelescopeJob')
        ->andReturnTrue();

    // Act
    $shouldHandleJobReflection = new ReflectionMethod($jobWatcher, 'shouldHandleJob');
    $resultOfShouldHandleJob   = $shouldHandleJobReflection->invoke($jobWatcher, $jobMock);

    // Assert
    /* @var JobWatcher $jobWatcher */
    expect($resultOfShouldHandleJob)->toBeFalse();
});

it('handles job when job is not a Telescope job and watcher is active', function (): void {
    // Arrange
    $jobWatcher = $this
        ->spy(JobWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $jobMock = $this->mock(Job::class);

    $jobWatcher
        ->shouldReceive('isWatching')
        ->andReturnTrue();

    $jobWatcher
        ->shouldReceive('getJobName')
        ->andReturn('jobName');

    $jobWatcher
        ->shouldReceive('isTelescopeJob')
        ->andReturnFalse();

    // Act
    $shouldHandleJobReflection = new ReflectionMethod($jobWatcher, 'shouldHandleJob');
    $resultOfShouldHandleJob   = $shouldHandleJobReflection->invoke($jobWatcher, $jobMock);

    // Assert
    /* @var JobWatcher $jobWatcher */
    expect($resultOfShouldHandleJob)->toBeTrue();
});
