
<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
      {!! Form::model($data, ['route' => ['users.update', $id], 'method' => 'patch' , 'enctype' => 'multipart/form-data']) !!}
      @else
      {!! Form::open(['route' => ['users.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
      @endif
      <div class="row">
         <div class="col-xl-3 col-lg-4">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Update' : 'Add New' }} Employee</h4>
                  </div>
               </div>
               <div class="card-body">
                     <div class="form-group">
                        <div class="profile-img-edit position-relative">
                        <img src="{{ $profileImage ?? asset('images/avatars/01.png')}}" alt="User-Profile" class="profile-pic rounded avatar-100">
                           <div class="upload-icone bg-primary">
                              <svg class="upload-button" width="14" height="14" viewBox="0 0 24 24">
                                 <path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                              </svg>
                              <input class="file-upload" type="file" accept="image/*" name="profile_image">
                           </div>
                        </div>
                        <div class="img-extension mt-3">
                           <div class="d-inline-block align-items-center">
                              <span>Only</span>
                              <a href="javascript:void();">.jpg</a>
                              <a href="javascript:void();">.png</a>
                              <a href="javascript:void();">.jpeg</a>
                              <span>allowed</span>
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="form-label">Status:</label>
                        <div class="grid" style="--bs-gap: 1rem">
                            <div class="form-check g-col-6">
                                {{ Form::radio('status', 'active', old('status') || true, ['class' => 'form-check-input', 'id' => 'status-active']); }}
                                <label class="form-check-label" for="status-active">
                                    Active
                                </label>
                            </div>
                            <div class="form-check g-col-6">
                                {{ Form::radio('status', 'inactive', old('status'), ['class' => 'form-check-input', 'id' => 'status-inactive']); }}
                                <label class="form-check-label" for="status-inactive">
                                    Inactive
                                </label>
                            </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="form-label">Employee Role: <span class="text-danger">*</span></label>
                        {{ Form::select('role', $roles, old('role'), ['class' => 'form-control', 'placeholder' => 'Select Employee Role', 'required']) }}
                     </div>
               </div>
            </div>
         </div>
         <div class="col-xl-9 col-lg-8">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Update' : 'New' }} Employee Information</h4>
                  </div>
                  <div class="card-action">
                        <a href="{{route('users.index')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-user-info">
                        <div class="row">
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="fname">First Name: <span class="text-danger">*</span></label>
                              {{ Form::text('first_name', old('first_name'), ['class' => 'form-control', 'placeholder' => 'First Name', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="lname">Last Name: <span class="text-danger">*</span></label>
                              {{ Form::text('last_name', old('last_name'), ['class' => 'form-control', 'placeholder' => 'Last Name', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="email">Email: <span class="text-danger">*</span></label>
                              {{ Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => 'Enter e-mail', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="phone_number">Phone Number:</label>
                              {{ Form::text('phone_number', old('phone_number'), ['class' => 'form-control', 'placeholder' => 'Phone Number']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="hire_date">Hire Date:</label>
                              {{ Form::date('hire_date', old('hire_date'), ['class' => 'form-control', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="supervisor_id">Supervisor:</label>
                              {{ Form::select('supervisor_id', $supervisors, old('supervisor_id'), ['class' => 'form-control', 'placeholder' => 'Select Supervisor']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="password">Password: <span class="text-danger">*</span></label>
                              {{ Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold" for="password_confirmation">Confirm Password: <span class="text-danger">*</span></label>
                              {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm Password', 'required']) }}
                           </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{$id !== null ? 'Update' : 'Add Employee' }}</button>
                  </div>
               </div>
            </div>
         </div>
        </div>
        {!! Form::close() !!}
   </div>
</x-app-layout>