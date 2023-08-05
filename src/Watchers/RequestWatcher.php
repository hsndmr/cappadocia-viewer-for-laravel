<?php

declare(strict_types=1);

namespace Hsndmr\CappadociaViewer\Watchers;

use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Hsndmr\CappadociaViewer\FormatModel;
use Hsndmr\CappadociaViewer\Enums\ViewerType;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Response as IlluminateResponse;
use Hsndmr\CappadociaViewer\Facades\CappadociaViewer;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RequestWatcher extends Watcher
{
    public function register(): void
    {
        Event::listen(RequestHandled::class, [$this, 'handleRequest']);
    }

    public function handleRequest(RequestHandled $event): void
    {
        if (!$this->isWatching()) {
            return;
        }

        $data = $this->getData($event);

        CappadociaViewer::setMessage($data['uri'])
            ->setBadge($data['method'])
            ->setType(ViewerType::REQUEST)
            ->send($data);
    }

    protected function getData(RequestHandled $event): array
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');

        return [
            'status' => [
                (string) $event->response->getStatusCode(),
            ],
            'response'   => $this->formatResponse($event->response),
            'headers'    => $event->request->headers->all(),
            'payload'    => $this->extractInput($event->request),
            'duration'   => $startTime ? floor((microtime(true) - $startTime) * 1000).' ms' : null,
            'ip'         => $event->request->ip(),
            'session'    => $this->getSessions($event->request),
            'controller' => optional($event->request->route())->getActionName(),
            'middleware' => array_values(optional($event->request->route())->gatherMiddleware() ?? []),
            'memory'     => round(memory_get_peak_usage(true) / 1024 / 1024, 1),
            'uri'        => str_replace($event->request->root(), '', $event->request->fullUrl()) ?: '/',
            'method'     => $event->request->method(),
        ];
    }

    protected function extractInput(Request $request): array
    {
        $files = $request->files->all();

        array_walk_recursive($files, function (&$file): void {
            $file = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->isFile() ? ($file->getSize() / 1000).'KB' : '0',
            ];
        });

        return array_replace_recursive($request->input(), $files);
    }

    protected function formatResponse(Response $response): array|string
    {
        $content = $response->getContent();

        if (is_string($content)) {
            $contentJson = json_decode($content, true);

            if (is_array($contentJson) && json_last_error() === JSON_ERROR_NONE) {
                return $contentJson;
            }

            if (Str::startsWith(strtolower($response->headers->get('Content-Type') ?? ''), 'text/plain')) {
                return $content;
            }
        }

        if ($response instanceof RedirectResponse) {
            return 'Redirected to '.$response->getTargetUrl();
        }

        if ($response instanceof IlluminateResponse && $response->getOriginalContent() instanceof View) {
            return [
                'view' => $response->getOriginalContent()->getPath(),
                'data' => $this->extractDataFromView($response->getOriginalContent()),
            ];
        }

        if (is_string($content) && empty($content)) {
            return 'Empty Response';
        }

        return 'HTML Response';
    }

    protected function extractDataFromView($view): array
    {
        return collect($view->getData())->map(function ($value) {
            if ($value instanceof Model) {
                return FormatModel::given($value);
            } elseif (is_object($value)) {
                return [
                    'class'      => get_class($value),
                    'properties' => json_decode(json_encode($value), true),
                ];
            } else {
                return json_decode(json_encode($value), true);
            }
        })->toArray();
    }

    protected function getSessions(Request $request): array
    {
        return $request->hasSession() ? $request->session()->all() : [];
    }

    protected function getConfigName(): string
    {
        return 'requests';
    }
}
