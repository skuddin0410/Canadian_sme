@extends('layouts.admin')

@section('title', 'Audit Log Details')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Audit Log Details') }}
    </h2>
@endsection

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="mb-6">
            <a href="{{ route('audit.index') }}" class="text-indigo-600 hover:text-indigo-900">&larr; Back to Audit Logs</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Event Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Event Type:</span>
                        <span class="text-sm text-gray-900">
                            @if($log->event == 'created')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Created
                                </span>
                            @elseif($log->event == 'updated')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Updated
                                </span>
                            @elseif($log->event == 'deleted')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Deleted
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst($log->event) }}
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Entity Type:</span>
                        <span class="text-sm text-gray-900">{{ class_basename($log->auditable_type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Entity ID:</span>
                        <span class="text-sm text-gray-900">{{ $log->auditable_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Date/Time:</span>
                        <span class="text-sm text-gray-900">{{ $log->created_at->format('M d, Y h:i:s A') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">User Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">User:</span>
                        <span class="text-sm text-gray-900">{{ $log->user ? $log->user->name : 'System' }}</span>
                    </div>
                    @if($log->user)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <span class="text-sm text-gray-900">{{ $log->user->email }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">IP Address:</span>
                        <span class="text-sm text-gray-900">{{ $log->ip_address ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">User Agent:</span>
                        <span class="text-sm text-gray-900 truncate">{{ $log->user_agent ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($log->event == 'updated' && $log->old_values)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Changed Values</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($log->old_values as $key => $oldValue)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $key }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(is_array($oldValue))
                                            <pre class="text-xs">{{ json_encode($oldValue, JSON_PRETTY_PRINT) }}</pre>
                                        @elseif(is_bool($oldValue))
                                            {{ $oldValue ? 'true' : 'false' }}
                                        @else
                                            {!! $oldValue !!}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(isset($log->new_values[$key]))
                                            @if(is_array($log->new_values[$key]))
                                                <pre class="text-xs">{{ json_encode($log->new_values[$key], JSON_PRETTY_PRINT) }}</pre>
                                            @elseif(is_bool($log->new_values[$key]))
                                                {{ $log->new_values[$key] ? 'true' : 'false' }}
                                            @else
                                                {!! $log->new_values[$key] !!}
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($log->event == 'created' && $log->new_values)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Created Values</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($log->new_values as $key => $value)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $key }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(is_array($value))
                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @elseif(is_bool($value))
                                            {{ $value ? 'true' : 'false' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($log->event == 'deleted' && $log->old_values)
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Deleted Values</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($log->old_values as $key => $value)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $key }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(is_array($value))
                                            <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                        @elseif(is_bool($value))
                                            {{ $value ? 'true' : 'false' }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        @if($log->url)
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Additional Information</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">URL:</span>
                        <span class="text-sm text-gray-900">{{ $log->url }}</span>
                    </div>
                    @if($log->tags)
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Tags:</span>
                            <span class="text-sm text-gray-900">{{ $log->tags }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection