@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row gy-3">
        <div class="col-12">
            <h3 class="mb-1">{{ __($pageTitle) }}</h3>
            <p class="mb-0">@lang('Upload a CSV file to create batch payouts. Required columns: destination, amount.')</p>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Upload CSV')</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.merchant.mass.payouts.upload') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <div class="col-md-5">
                            <label class="form-label">@lang('CSV File')</label>
                            <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">@lang('Default Chain')</label>
                            <select name="default_chain" class="form-control">
                                <option value="tron">TRON</option>
                                <option value="eth">ETH</option>
                                <option value="bsc">BSC</option>
                                <option value="ton">TON</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">@lang('Default Asset')</label>
                            <input type="text" name="default_asset" class="form-control" value="USDT">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn--base w-100" type="submit">@lang('Upload')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-header">
                    <h6 class="mb-0">@lang('Batches')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table--responsive--md">
                            <thead>
                                <tr>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Reference')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Items')</th>
                                    <th>@lang('Total Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($batches as $batch)
                                    <tr>
                                        <td>{{ showDateTime($batch->created_at) }}</td>
                                        <td>{{ $batch->reference }}</td>
                                        <td><span class="badge badge--warning">{{ $batch->status }}</span></td>
                                        <td>{{ $batch->items_count }}</td>
                                        <td>{{ showAmount($batch->total_amount, 8, true, false, false) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($batches->hasPages())
                        <div class="p-3">{{ paginateLinks($batches) }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

