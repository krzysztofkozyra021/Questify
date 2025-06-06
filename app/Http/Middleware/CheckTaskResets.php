<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TaskService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTaskResets
{
    public function __construct(
        protected TaskService $taskService,
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            // Check and reset any due tasks for the authenticated user
            $this->taskService->resetDueTasks();
        }

        return $next($request);
    }
}
