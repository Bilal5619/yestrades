@extends('vendors.layout')

@section('content')
  <div class="page-header">
    <h4 class="page-title">{{ __('Bank Accounts') }}</h4>
    <ul class="breadcrumbs">
      <li class="nav-home">
        <a href="{{ route('vendor.dashboard') }}">
          <i class="flaticon-home"></i>
        </a>
      </li>
      <li class="separator">
        <i class="flaticon-right-arrow"></i>
      </li>
      <li class="nav-item">
        <a href="#">{{ __('Bank Accounts') }}</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-lg-8">
              <div class="card-title d-inline-block">{{ __('My Bank Accounts') }}</div>
            </div>
            <div class="col-lg-4 mt-2 mt-lg-0">
              <button class="btn btn-primary btn-sm float-lg-right" data-toggle="modal" data-target="#createBankAccountModal">
                <i class="fas fa-plus"></i> {{ __('Add Bank Account') }}
              </button>
            </div>
          </div>
        </div>

        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if (count($bankAccounts) == 0)
            <h3 class="text-center">{{ __('NO BANK ACCOUNT FOUND') }}!</h3>
          @else
            <div class="table-responsive">
              <table class="table table-striped mt-3">
                <thead>
                  <tr>
                    <th scope="col">{{ __('Account Holder') }}</th>
                    <th scope="col">{{ __('Bank') }}</th>
                    <th scope="col">{{ __('Account Number') }}</th>
                    <th scope="col">{{ __('IBAN') }}</th>
                    <th scope="col">{{ __('Status') }}</th>
                    <th scope="col">{{ __('Action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($bankAccounts as $account)
                    <tr>
                      <td>{{ $account->account_holder_name }}</td>
                      <td>{{ $account->bank_name }}</td>
                      <td>{{ $account->account_number }}</td>
                      <td>{{ $account->iban ?? '-' }}</td>
                      <td>
                        @if ($account->is_active)
                          <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                          <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                      </td>
                      <td>
                        @if (!$account->is_active)
                          <form class="d-inline-block" action="{{ route('vendor.bank_accounts.activate', $account->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm mb-1">
                              <i class="fas fa-check"></i>
                            </button>
                          </form>
                        @endif
                        <button class="btn btn-secondary btn-sm mb-1" data-toggle="modal" data-target="#editBankAccountModal{{ $account->id }}">
                          <i class="fas fa-edit"></i>
                        </button>
                        <form class="deleteForm d-inline-block" action="{{ route('vendor.bank_accounts.delete', $account->id) }}" method="post">
                          @csrf
                          <button type="submit" class="btn btn-danger btn-sm mb-1 deleteBtn">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        <div class="card-footer">
          <div class="row">
            <div class="d-inline-block mx-auto">
              {{ $bankAccounts->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('vendors.bank-accounts.partials.form-modal', [
    'modalId' => 'createBankAccountModal',
    'title' => __('Add Bank Account'),
    'action' => route('vendor.bank_accounts.store'),
    'account' => null,
    'showActive' => true,
  ])

  @foreach ($bankAccounts as $account)
    @include('vendors.bank-accounts.partials.form-modal', [
      'modalId' => 'editBankAccountModal' . $account->id,
      'title' => __('Edit Bank Account'),
      'action' => route('vendor.bank_accounts.update', $account->id),
      'account' => $account,
      'showActive' => false,
    ])
  @endforeach
@endsection
