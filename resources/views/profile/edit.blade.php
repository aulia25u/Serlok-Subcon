@extends('layouts.app')

@section('title', 'Profile')

@section('css')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.7/css/dataTables.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@stop

@inject('google2fa', \App\Services\Google2FAService::class)

@section('content')
<div class="container-fluid">
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            Profile has been updated successfully.
        </div>
    @elseif (session('status') === 'photo-updated')
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            Profile photo has been updated successfully.
        </div>
    @elseif (session('status') === 'two-factor-prepared')
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Scan the QR code with Google Authenticator and confirm the code below to enable 2FA.
        </div>
    @elseif (session('status') === 'two-factor-enabled')
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Two-factor authentication is now enabled for your account.
        </div>
    @elseif (session('status') === 'two-factor-disabled')
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Two-factor authentication has been disabled.
        </div>
    @elseif (session('status') === 'two-factor-not-enabled')
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Two-factor authentication is not currently active.
        </div>
    @elseif (session('status') === 'two-factor-already-enabled')
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            Two-factor authentication is already turned on for your account.
        </div>
    @endif

    <div class="row">
        <div class="col-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Settings</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#general" data-toggle="tab">
                                <i class="fas fa-user mr-2"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#security" data-toggle="tab">
                                <i class="fas fa-lock mr-2"></i> Security
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#history" data-toggle="tab">
                                <i class="fas fa-history mr-2"></i> History
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-10">
            <div class="tab-content">
                <!-- General Tab -->
                <div class="tab-pane active" id="general">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Profile Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 d-flex flex-column align-items-center">
                                    <img src="{{ $photoUrl ?? asset('images/icon.png') }}" alt="Profile"
                                        class="rounded-circle border mb-3"
                                        style="width: 130px; height: 130px; object-fit: cover;">
                                    <form action="{{ route('profile.photo.update') }}" method="POST"
                                        enctype="multipart/form-data" class="w-100">
                                        @csrf
                                        <label class="btn btn-sm btn-primary btn-block mb-0" style="cursor: pointer;">
                                            <i class="fas fa-camera"></i> Change Photo
                                            <input type="file" name="employee_photo" accept="image/*" class="d-none"
                                                onchange="this.form.submit()">
                                        </label>
                                    </form>
                                    @error('employee_photo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Nama Lengkap</strong>
                                            <p class="mb-0">{{ $userDetail->employee_name ?? 'Not Set' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Email</strong>
                                            <p class="mb-0">{{ $user->email }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Phone</strong>
                                            <p class="mb-0">{{ $userDetail->phone ?? 'Not Set' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Address</strong>
                                            <p class="mb-0">{{ $userDetail->address ?? 'Not Set' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Role</strong>
                                            <p class="mb-0">{{ $userDetail->role->role_name ?? 'Not Assigned' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>NIP</strong>
                                            <p class="mb-0">{{ $userDetail->nip ?? 'Not Set' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Employee Status</strong>
                                            <p class="mb-0">{{ $userDetail->employee_status ?? 'Not Set' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Plant</strong>
                                            <p class="mb-0">{{ $userDetail->plant->plant_name ?? 'Not Assigned' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Update Profile</h3>
                        </div>
                        <div class="card-body">
                            <form id="profileForm" method="POST" action="{{ route('profile.update') }}">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="{{ old('username', $user->username) }}" required>
                                            @error('username')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="employee_name">Full Name</label>
                                            <input type="text" class="form-control" id="employee_name"
                                                name="employee_name"
                                                value="{{ old('employee_name', $userDetail->employee_name ?? '') }}"
                                                required>
                                            @error('employee_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" id="gender" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="Male" {{ old('gender', $userDetail->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender', $userDetail->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone', $userDetail->phone ?? '') }}">
                                            @error('phone')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control" id="address" name="address"
                                                rows="1">{{ old('address', $userDetail->address ?? '') }}</textarea>
                                            @error('address')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_name">Bank Name</label>
                                            <input type="text" class="form-control" id="bank_name" name="bank_name"
                                                value="{{ old('bank_name', $userDetail->bank_name ?? '') }}">
                                            @error('bank_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_account_name">Bank Account Name</label>
                                            <input type="text" class="form-control" id="bank_account_name"
                                                name="bank_account_name"
                                                value="{{ old('bank_account_name', $userDetail->bank_account_name ?? '') }}">
                                            @error('bank_account_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="bank_account_number">Bank Account Number</label>
                                            <input type="text" class="form-control" id="bank_account_number"
                                                name="bank_account_number"
                                                value="{{ old('bank_account_number', $userDetail->bank_account_number ?? '') }}">
                                            @error('bank_account_number')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Role</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->role->role_name ?? 'Not Assigned' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Current Position</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->position->position_name ?? 'Not Assigned' }} ({{ $userDetail->position->section->section_name ?? '' }} - {{ $userDetail->position->section->dept->dept_name ?? '' }})">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Employee ID</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->employee_id ?? 'Not Assigned' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Join Date</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->join_date ? $userDetail->join_date->format('d M Y') : 'Not Set' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->nip ?? 'Not Set' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Employee Status</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->employee_status ?? 'Not Set' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Plant</label>
                                            <input type="text" class="form-control" readonly
                                                value="{{ $userDetail->plant->plant_name ?? 'Not Assigned' }}">
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tab -->
                <div class="tab-pane" id="security">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Two-factor Authentication</h3>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                Status:
                                @if ($user->two_factor_enabled)
                                    <span class="text-success">Enabled</span>
                                @else
                                    <span class="text-muted">Disabled</span>
                                @endif
                            </p>

                            @if (!$user->two_factor_enabled)
                                <p class="text-muted">
                                    Protect your account with Google Authenticator. Start the setup to generate a secret key
                                    and QR code.
                                </p>
                                <form method="POST" action="{{ route('profile.two-factor.prepare') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fas fa-shield-alt mr-2"></i>Configure 2FA
                                    </button>
                                </form>
                            @endif

                            @if (session('two_factor_secret_setup'))
                                <div class="border rounded p-3 mt-4">
                                    <p class="mb-2 font-weight-bold">Step 2: Scan QR Code</p>
                                    <img src="{{ $google2fa->getQrCodeUrl(config('app.name'), $user->email, session('two_factor_secret_setup')) }}"
                                        alt="QR Code" class="mb-3" style="width: 200px;">
                                    <p class="small text-muted mb-1">Secret Key</p>
                                    <p class="mb-3"><code>{{ session('two_factor_secret_setup') }}</code></p>
                                    <form method="POST" action="{{ route('profile.two-factor.enable') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="two_factor_code">Enter the 6-digit code</label>
                                            <input type="text" id="two_factor_code" name="two_factor_code"
                                                class="form-control @error('two_factor_code') is-invalid @enderror"
                                                value="{{ old('two_factor_code') }}" maxlength="6" autofocus>
                                            @error('two_factor_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check-circle mr-2"></i>Enable 2FA
                                        </button>
                                    </form>
                                </div>
                            @endif

                            @if ($user->two_factor_enabled)
                                <div class="mt-4 border rounded p-3">
                                    <p class="text-muted mb-3">Enter the current code to turn off two-factor authentication.
                                    </p>
                                    <form method="POST" action="{{ route('profile.two-factor.disable') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="two_factor_disable_code">Current 6-digit code</label>
                                            <input type="text" id="two_factor_disable_code" name="two_factor_code"
                                                class="form-control @error('two_factor_code') is-invalid @enderror"
                                                maxlength="6">
                                            @error('two_factor_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-power-off mr-2"></i>Disable 2FA
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane" id="history">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title mb-0">Activity History</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Action</th>
                                        <th>Module</th>
                                        <th>Description</th>
                                        <th>Time</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activities as $activity)
                                        <tr>
                                            <td>{!! $activity->action_badge !!}</td>
                                            <td>{{ $activity->table_name_formatted }}</td>
                                            <td>
                                                @if($activity->action == 'login' || $activity->action == 'logout')
                                                    IP: {{ $activity->new_values['ip'] ?? '-' }}
                                                @else
                                                    Record ID: {{ $activity->record_id }}
                                                @endif
                                            </td>
                                            <td>{{ $activity->created_at->diffForHumans() }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                    data-target="#activityModal{{ $activity->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1"
                                                    role="dialog" aria-labelledby="activityModalLabel{{ $activity->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="activityModalLabel{{ $activity->id }}">Activity
                                                                    Details</h5>
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <strong>Action:</strong>
                                                                        {!! $activity->action_badge !!}<br>
                                                                        <strong>Module:</strong>
                                                                        {{ $activity->table_name_formatted }}<br>
                                                                        <strong>Time:</strong>
                                                                        {{ $activity->created_at->format('d M Y H:i:s') }}
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <strong>IP Address:</strong>
                                                                        {{ $activity->ip_address }}<br>
                                                                        <strong>Browser:</strong>
                                                                        {{ $activity->browser }}<br>
                                                                        <strong>Operating System:</strong>
                                                                        {{ $activity->platform }}
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                @if($activity->old_values)
                                                                    <h5>Old Values</h5>
                                                                    <pre>{{ json_encode($activity->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                                @endif
                                                                @if($activity->new_values)
                                                                    <h5>New Values</h5>
                                                                    <pre>{{ json_encode($activity->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No activity found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@stop