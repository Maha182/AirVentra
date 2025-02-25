<x-app-layout :assets="$assets ?? []">
   <div>
      <?php
         $id = $id ?? null;
      ?>
      @if(isset($id))
         {!! Form::model($product, ['route' => ['products.update', $id], 'method' => 'patch', 'enctype' => 'multipart/form-data']) !!}
      @else
         {!! Form::open(['route' => ['products.store'], 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
      @endif

      <div class="row">
         <div class="col-xl-3 col-lg-4">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Update' : 'Add New' }} Product</h4>
                  </div>
               </div>
               <div class="card-body">
                     <div class="form-group">
                        <label class="form-label">Barcode Image:</label>
                        <div class="profile-img-edit position-relative">
                           <img src="{{ $barcodePath ?? asset('images/default-barcode.png')}}" alt="Barcode" class="profile-pic rounded avatar-100">
                           <div class="upload-icone bg-primary">
                              <svg class="upload-button" width="14" height="14" viewBox="0 0 24 24">
                                 <path fill="#ffffff" d="M14.06,9L15,9.94L5.92,19H5V18.08L14.06,9M17.66,3C17.41,3 17.15,3.1 16.96,3.29L15.13,5.12L18.88,8.87L20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18.17,3.09 17.92,3 17.66,3M14.06,6.19L3,17.25V21H6.75L17.81,9.94L14.06,6.19Z" />
                              </svg>
                              <input class="file-upload" type="file" accept="image/*" name="barcode_path">
                           </div>
                        </div>
                     </div>
               </div>
            </div>
         </div>
         <div class="col-xl-9 col-lg-8">
            <div class="card">
               <div class="card-header d-flex justify-content-between">
                  <div class="header-title">
                     <h4 class="card-title">{{$id !== null ? 'Update' : 'New' }} Product Information</h4>
                  </div>
                  <div class="card-action">
                        <a href="{{route('products.index')}}" class="btn btn-sm btn-primary" role="button">Back</a>
                  </div>
               </div>
               <div class="card-body">
                  <div class="new-user-info">
                        <div class="row">
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold">Title: <span class="text-danger">*</span></label>
                              {{ Form::text('title', old('title'), ['class' => 'form-control', 'placeholder' => 'Product Title', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold">Main Category: <span class="text-danger">*</span></label>
                              {{ Form::text('main_category', old('main_category'), ['class' => 'form-control', 'placeholder' => 'Category', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold">Quantity: <span class="text-danger">*</span></label>
                              {{ Form::number('quantity', old('quantity'), ['class' => 'form-control', 'placeholder' => 'Quantity', 'required']) }}
                           </div>
                           <div class="form-group col-md-6">
                              <label class="form-label fw-bold">Location ID: <span class="text-danger">*</span></label>
                              {{ Form::text('location_id', old('location_id'), ['class' => 'form-control', 'placeholder' => 'Location ID', 'required']) }}
                           </div>
                           <div class="form-group col-md-12">
                              <label class="form-label fw-bold">Description:</label>
                              {{ Form::textarea('description', old('description'), ['class' => 'form-control', 'placeholder' => 'Product Description', 'rows' => 3]) }}
                           </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">{{$id !== null ? 'Update' : 'Add Product' }}</button>
                  </div>
               </div>
            </div>
         </div>
        </div>
        {!! Form::close() !!}
   </div>
</x-app-layout>
