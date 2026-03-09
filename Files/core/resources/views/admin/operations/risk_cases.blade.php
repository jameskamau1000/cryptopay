@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Case')</th>
                            <th>@lang('Entity')</th>
                            <th>@lang('Rule')</th>
                            <th>@lang('Severity')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cases as $case)
                            <tr>
                                <td>#{{ $case->id }}</td>
                                <td>{{ $case->entity_type }} / {{ $case->entity_id }}</td>
                                <td>{{ $case->rule_code }}</td>
                                <td>{{ $case->severity }}</td>
                                <td>{{ $case->status }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.operations.risk.cases.update', $case->id) }}" class="d-flex gap-2">
                                        @csrf
                                        <select name="severity" class="form-control form-control-sm">
                                            @foreach(['low','medium','high','critical'] as $severity)
                                                <option value="{{ $severity }}" @selected($case->severity === $severity)>{{ ucfirst($severity) }}</option>
                                            @endforeach
                                        </select>
                                        <select name="status" class="form-control form-control-sm">
                                            @foreach(['open','in_review','blocked','resolved','dismissed'] as $status)
                                                <option value="{{ $status }}" @selected($case->status === $status)>{{ ucfirst(str_replace('_',' ', $status)) }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn--primary btn--sm">@lang('Save')</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($cases->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($cases) }}</div>
        @endif
    </div>
@endsection
