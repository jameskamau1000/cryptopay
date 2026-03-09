@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h3 class="mb-1">{{ __($pageTitle) }}</h3>
            <p class="mb-0">@lang('Manage reusable deposit channels for your payers.')</p>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Create channel')</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.merchant.channels.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label">@lang('Chain')</label>
                            <select name="chain" class="form-control" required>
                                <option value="tron">TRON</option>
                                <option value="eth">ETH</option>
                                <option value="bsc">BSC</option>
                                <option value="ton">TON</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Asset')</label>
                            <input type="text" name="asset" class="form-control" value="USDT">
                        </div>
                        <div class="col-12">
                            <button class="btn btn--base" type="submit">@lang('Create channel')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Your channels')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Address')</th>
                                    <th>@lang('Chain')</th>
                                    <th>@lang('Asset')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Memo')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($channels as $channel)
                                    <tr>
                                        <td>{{ showDateTime($channel->created_at) }}</td>
                                        <td class="text-break">{{ $channel->address }}</td>
                                        <td>{{ strtoupper($channel->chain) }}</td>
                                        <td>{{ strtoupper($channel->asset) }}</td>
                                        <td><span class="badge badge--success">{{ $channel->status }}</span></td>
                                        <td>{{ $channel->memo ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($channels->hasPages())
                        <div class="p-3">{{ paginateLinks($channels) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

