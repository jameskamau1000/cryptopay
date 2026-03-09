@extends('admin.layouts.app')

@section('panel')
    <div class="card b-radius--10 mb-3">
        <div class="card-header">
            <h5 class="mb-0">@lang('Add Treasury Wallet')</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.operations.wallets.store') }}" class="row g-2">
                @csrf
                <div class="col-md-2">
                    <select name="chain" class="form-control" required>
                        <option value="tron">TRON</option>
                        <option value="eth">ETH</option>
                        <option value="bsc">BEP20 (BSC)</option>
                        <option value="ton">TON</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="asset" class="form-control" value="USDT" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="label" class="form-control" placeholder="@lang('Label')">
                </div>
                <div class="col-md-3">
                    <input type="text" name="address" class="form-control" placeholder="@lang('Wallet address')" required>
                </div>
                <div class="col-md-2">
                    <input type="password" name="private_key" class="form-control" placeholder="@lang('Private key (optional)')">
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_treasury" value="1" checked id="is_treasury">
                        <label class="form-check-label" for="is_treasury">@lang('Treasury')</label>
                    </div>
                    <button type="submit" class="btn btn--primary btn--sm mt-1 w-100">@lang('Add')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive--md table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Chain')</th>
                            <th>@lang('Asset')</th>
                            <th>@lang('Label')</th>
                            <th>@lang('Address')</th>
                            <th>@lang('Vault')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wallets as $wallet)
                            <tr>
                                <td>{{ strtoupper($wallet->chain) }}</td>
                                <td>{{ strtoupper($wallet->asset) }}</td>
                                <td>{{ $wallet->label ?: '-' }}</td>
                                <td class="text-break">{{ $wallet->address }}</td>
                                <td>{{ $wallet->encrypted_private_key ? __('Stored') : __('Empty') }}</td>
                                <td>{{ $wallet->is_active ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.operations.wallets.status', $wallet->id) }}" class="mb-1">
                                        @csrf
                                        <button type="submit" class="btn btn--sm {{ $wallet->is_active ? 'btn--danger' : 'btn--success' }}">
                                            {{ $wallet->is_active ? __('Disable') : __('Enable') }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.operations.wallets.vault.update', $wallet->id) }}" class="d-flex gap-1 mb-1">
                                        @csrf
                                        <input type="password" name="private_key" class="form-control form-control-sm" placeholder="@lang('Replace key')" required>
                                        <button type="submit" class="btn btn--primary btn--sm">@lang('Save')</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.operations.wallets.vault.clear', $wallet->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn--warning btn--sm">@lang('Clear Key')</button>
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
        @if($wallets->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($wallets) }}</div>
        @endif
    </div>
@endsection
