<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyAuditLog;
use App\Models\Company\CompanyUser;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = CompanyAuditLog::query()
            ->with('companyUser');

        if ($request->filled('user_id')) {
            $query->where(
                'company_user_id',
                $request->user_id
            );
        }

        if ($request->filled('method')) {
            $query->where(
                'method',
                strtoupper($request->method)
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($query) use ($search) {
                $query
                    ->where(
                        'action',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'route_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'request_path',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'ip_address',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $logs = $query
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $users = CompanyUser::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view(
            'company.audit-logs.index',
            compact(
                'logs',
                'users'
            )
        );
    }
}