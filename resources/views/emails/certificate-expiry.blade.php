{{-- Body of the certificate expiry reminder. Wrapped by emails.layout when sent. --}}
<p>Hello,</p>

@if ($upcoming->isNotEmpty())
  <p><strong>{{ $upcoming->count() }}</strong> certificate(s) expire within the next {{ $days }} days:</p>
  <table cellpadding="8" cellspacing="0" border="0" width="100%"
    style="border-collapse:collapse;font-size:14px;margin:0 0 24px;">
    <tr style="background:#f3f6f4;">
      <th align="left" style="border-bottom:1px solid #dfe7e1;">Type</th>
      <th align="left" style="border-bottom:1px solid #dfe7e1;">Certificate</th>
      <th align="left" style="border-bottom:1px solid #dfe7e1;">Customer / staff</th>
      <th align="left" style="border-bottom:1px solid #dfe7e1;">Expires</th>
    </tr>
    @foreach ($upcoming as $row)
      <tr>
        <td style="border-bottom:1px solid #eef2ef;">{{ $row['type'] }}</td>
        <td style="border-bottom:1px solid #eef2ef;">{{ $row['title'] }}</td>
        <td style="border-bottom:1px solid #eef2ef;">{{ $row['who'] }}</td>
        <td style="border-bottom:1px solid #eef2ef;">
          {{ $row['expiry_display'] }}
          <span style="color:#5e6861;">({{ $row['days_left'] }} days)</span>
        </td>
      </tr>
    @endforeach
  </table>
@endif

@if ($expired->isNotEmpty())
  <p><strong style="color:#c62828;">{{ $expired->count() }}</strong> certificate(s) have already expired:</p>
  <table cellpadding="8" cellspacing="0" border="0" width="100%"
    style="border-collapse:collapse;font-size:14px;margin:0 0 24px;">
    <tr style="background:#fdf3f2;">
      <th align="left" style="border-bottom:1px solid #f3d6d3;">Type</th>
      <th align="left" style="border-bottom:1px solid #f3d6d3;">Certificate</th>
      <th align="left" style="border-bottom:1px solid #f3d6d3;">Customer / staff</th>
      <th align="left" style="border-bottom:1px solid #f3d6d3;">Expired on</th>
    </tr>
    @foreach ($expired as $row)
      <tr>
        <td style="border-bottom:1px solid #f7e7e5;">{{ $row['type'] }}</td>
        <td style="border-bottom:1px solid #f7e7e5;">{{ $row['title'] }}</td>
        <td style="border-bottom:1px solid #f7e7e5;">{{ $row['who'] }}</td>
        <td style="border-bottom:1px solid #f7e7e5;">{{ $row['expiry_display'] }}</td>
      </tr>
    @endforeach
  </table>
@endif

<p>
  The full list is in the admin panel under <strong>Appointments &rsaquo; Compliance &amp; Tasks</strong>,
  where every certificate expiry is listed with the job it belongs to.
</p>

<p style="margin-top:24px;">
  <a href="{{ route('admin.availability_compliance', ['language' => optional(\App\Models\Language::where('is_default', 1)->first())->code]) }}"
    style="background:#2da63b;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:8px;font-weight:700;display:inline-block;">
    Open Compliance &amp; Tasks
  </a>
</p>
