<table class="table table-bordered">
    <tr>
        <th width="180">Invoice #</th>
        <td>{{ $invoice->id }}</td>
    </tr>

    <tr>
        <th>User</th>
        <td>
            {{ $invoice->user->first_name }}
            {{ $invoice->user->last_name }}
        </td>
    </tr>

    <tr>
        <th>Package</th>
        <td>{{ $invoice->userPackage->getPackage->name ?? '-' }}</td>
    </tr>

    <tr>
        <th>Amount</th>
        <td>{{ number_format($invoice->total_amount,2) }}</td>
    </tr>

    <tr>
        <th>Status</th>
        <td>
            <span class="badge bg-info">
                {{ ucfirst($invoice->status) }}
            </span>
        </td>
    </tr>

    <tr>
        <th>Created At</th>
        <td>{{ $invoice->created_at->format('Y-m-d H:i') }}</td>
    </tr>
</table>
