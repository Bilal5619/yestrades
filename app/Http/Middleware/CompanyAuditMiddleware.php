<?php

namespace App\Http\Middleware;

use App\Models\Company\CompanyAuditLog;
use Closure;
use Illuminate\Http\Request;

class CompanyAuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /*
         * Capture the user BEFORE the request is processed.
         * This is important for actions such as logout.
         */
        $companyUser = auth('company')->user();

        $response = $next($request);

        /*
         * Only log actions that can change data.
         *
         * We deliberately do not record form payloads because
         * Company HR forms may contain passwords, bank details,
         * National Insurance numbers and other sensitive data.
         */
        if (
            $companyUser &&
            in_array(
                strtoupper($request->method()),
                ['POST', 'PUT', 'PATCH', 'DELETE'],
                true
            )
        ) {
            try {
                $route = $request->route();

                $routeName = $route
                    ? $route->getName()
                    : null;

                CompanyAuditLog::create([
                    'company_user_id' => $companyUser->id,

                    'action' => $this->actionName(
                        $routeName,
                        $request->method()
                    ),

                    'method' => strtoupper(
                        $request->method()
                    ),

                    'route_name' => $routeName,

                    'request_path' => $request->path(),

                    'ip_address' => $request->ip(),

                    'user_agent' => substr(
                        (string) $request->userAgent(),
                        0,
                        2000
                    ),

                    'response_status' =>
                        method_exists($response, 'getStatusCode')
                            ? $response->getStatusCode()
                            : null,
                ]);
            } catch (\Throwable $exception) {
                /*
                 * Audit logging must never break the real HR action.
                 *
                 * If audit logging itself fails, Laravel can still
                 * process the employee/job/holiday/etc action.
                 */
                report($exception);
            }
        }

        return $response;
    }

    private function actionName(
        ?string $routeName,
        string $method
    ): string {
        if ($routeName) {
            return $routeName;
        }

        return strtoupper($method) . ' request';
    }
}