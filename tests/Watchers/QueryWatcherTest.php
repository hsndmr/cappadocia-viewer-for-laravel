<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Event;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Illuminate\Database\Events\QueryExecuted;
use Hsndmr\CappadociaViewer\Watchers\QueryWatcher;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

it('handles query executed event', function (): void {
    // Arrange
    $queryWatcher = $this
        ->spy(QueryWatcher::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $queryWatcher
        ->shouldReceive('isWatching')
        ->andReturnTrue();

    $queryWatcher
        ->shouldReceive('getSqlAndBindings')
        ->andReturn(['sql', []]);

    $eventMock = $this->mock(QueryExecuted::class, function ($mock): void {
        $mock->time = 1;
    });

    // Act & Assert
    CappadociaViewer::partialMock()
        ->shouldReceive('setMessage')
        ->once()
        ->with('sql')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setType')
        ->once()
        ->with(ViewerType::QUERY)
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('setBadge')
        ->once()
        ->with('query')
        ->andReturnSelf();

    CappadociaViewer::partialMock()
        ->shouldReceive('send')
        ->once()
        ->with([
            'bindings' => [],
            'time'     => ['1.00 ms'],
        ]);

    /* @var QueryWatcher $queryWatcher */
    $queryWatcher->handleQueryExecuted($eventMock);
});

it('should not send a message if isWatching method returns false', function (): void {
    // Arrange
    $queryWatcher = $this->spy(QueryWatcher::class)->makePartial();

    $queryWatcher
        ->shouldReceive('isWatching')
        ->once()
        ->andReturnFalse();

    // Act & Assert
    CappadociaViewer::partialMock()->shouldNotReceive('setMessage');

    /* @var QueryWatcher $queryWatcher */
    $queryWatcher->handleQueryExecuted($this->mock(QueryExecuted::class));
});

it('registers to listen for QueryExecuted events', function (): void {
    // Arrange
    $queryWatcher = new QueryWatcher();

    // Act & Assert
    Event::partialMock()
        ->shouldReceive('listen')
        ->with(QueryExecuted::class, [$queryWatcher, 'handleQueryExecuted'])
        ->once();

    $queryWatcher->register();
});

it('returns raw sql and bindings', function (): void {
    // Arrange
    $queryWatcher = new QueryWatcher();

    // Act & Assert
    $connectionMock = $this->mock(Connection::class, function (MockInterface $mock): void {
        $mock
            ->shouldReceive('getQueryGrammar->substituteBindingsIntoRawSql')
            ->with('sql', [])
            ->andReturn('raw sql');

        $mock->shouldReceive('prepareBindings')
            ->with([])
            ->andReturn([]);
    });

    $eventMock = $this->mock(QueryExecuted::class,
        function (MockInterface $mock) use ($connectionMock): void {
            $mock->sql        = 'sql';
            $mock->bindings   = [];
            $mock->connection = $connectionMock;
        });

    $getSqlAndBindingsReflection = new ReflectionMethod($queryWatcher, 'getSqlAndBindings');

    expect($getSqlAndBindingsReflection->invoke($queryWatcher, $eventMock))->toBe(['raw sql', []]);
});
