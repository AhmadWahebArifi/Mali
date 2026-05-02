@extends('layouts.app')

@section('title', 'Audit Log Details - FinTrack Pro')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mb-4">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Audit Logs
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Audit Log Details</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Basic Information</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->id }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Timestamp:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->created_at->format('M d, Y H:i:s') }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Action:</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($log->action === 'create') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                        @elseif($log->action === 'update') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                        @elseif($log->action === 'delete') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                        @elseif($log->action === 'approve') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                        @elseif($log->action === 'reject') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                        @endif
                                    ">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject Type:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->subject_type ? class_basename($log->subject_type) : '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subject ID:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->subject_id ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- User Information -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">User Information</h3>
                        @if($log->user)
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->user->first_name }} {{ $log->user->last_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->user->email }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User ID:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->user->id }}</dd>
                            </div>
                        </dl>
                        @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">System-generated log</p>
                        @endif
                    </div>
                </div>

                <!-- Technical Information -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Technical Information</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->ip_address ?: '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User Agent:</dt>
                                <dd class="text-sm text-gray-900 dark:text-gray-100 break-all">{{ $log->user_agent ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h3>
                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $log->description ?: 'No description available' }}</p>
                    </div>
                </div>
            </div>

            <!-- Old Values -->
            @if($log->old_values)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Old Values</h3>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <pre class="text-sm text-gray-900 dark:text-gray-100 overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <!-- New Values -->
            @if($log->new_values)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">New Values</h3>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <pre class="text-sm text-gray-900 dark:text-gray-100 overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
            @endif

            <!-- Subject Details -->
            @if($log->subject)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Subject Details</h3>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ get_class($log->subject) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ID:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->subject->id }}</dd>
                        </div>
                        @if(isset($log->subject->name))
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->subject->name }}</dd>
                        </div>
                        @endif
                        @if(isset($log->subject->email))
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email:</dt>
                            <dd class="text-sm text-gray-900 dark:text-gray-100">{{ $log->subject->email }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
