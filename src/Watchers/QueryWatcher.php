<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

use Illuminate\Support\Facades\Event;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Illuminate\Database\Events\QueryExecuted;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;

class QueryWatcher extends Watcher
{
    public function register(): void
    {
        Event::listen(QueryExecuted::class, [$this, 'handleQueryExecuted']);
    }

    public function handleQueryExecuted(QueryExecuted $event): void
    {
        if (!$this->isWatching()) {
            return;
        }

        [$sql, $bindings] = $this->getSqlAndBindings($event);

        CappadociaViewer::setMessage($sql)
            ->setType(ViewerType::QUERY)
            ->setBadge('query')
            ->send([
                'bindings' => $bindings,
                'time'     => [
                    number_format($event->time, 2, '.', '').' ms',
                ],
            ]);
    }

    protected function getSqlAndBindings(QueryExecuted $event): array
    {
        $bindings = $event->bindings;

        $sql = $event->connection->getQueryGrammar()->substituteBindingsIntoRawSql(
            $event->sql,
            $event->connection->prepareBindings($bindings)
        );

        return [$sql, $bindings];
    }

    protected function getConfigName(): string
    {
        return 'queries';
    }
}
