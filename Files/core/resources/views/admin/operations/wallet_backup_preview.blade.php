@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">@lang('Unencrypted Wallet Backup Preview')</h5>
            <a href="{{ route('admin.operations.wallets') }}" class="btn btn--sm btn--primary">@lang('Back to Wallets')</a>
        </div>
        <div class="card-body">
            <div class="alert alert-warning mb-3">
                @lang('Sensitive data below includes unencrypted private keys. Do not share or screenshot this page.')
            </div>
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Chain')</th>
                            <th>@lang('Asset')</th>
                            <th>@lang('Label')</th>
                            <th>@lang('Address')</th>
                            <th>@lang('Private Key')</th>
                            <th>@lang('Treasury')</th>
                            <th>@lang('Active')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wallets as $wallet)
                            <tr>
                                <td>{{ strtoupper($wallet['chain'] ?? '-') }}</td>
                                <td>{{ strtoupper($wallet['asset'] ?? '-') }}</td>
                                <td>{{ $wallet['label'] ?? '-' }}</td>
                                <td class="text-break">{{ $wallet['address'] ?? '-' }}</td>
                                <td class="text-break">{{ $wallet['private_key'] ?? '-' }}</td>
                                <td>{{ !empty($wallet['is_treasury']) ? __('Yes') : __('No') }}</td>
                                <td>{{ !empty($wallet['is_active']) ? __('Yes') : __('No') }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

