<x-guest-layout>
   <section class="login-content">
      <div class="row m-0 align-items-center bg-white vh-100">
         <div class="col-md-6">
            <div class="row justify-content-center">
               <div class="">
                  <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                     <div class="card-body">
                     
                        <h2 class="mb-2 text-center">Sign In</h2>
                        <p class="text-center">Login to stay connected.</p>
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <!-- Validation Errors -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />
                        <form method="POST" action="{{ route('login') }}" data-toggle="validator">
                            {{csrf_field()}}
                           <div class="row">
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" type="email" name="email"  value="{{env('IS_DEMO') ? '' : old('email')}}"   class="form-control"  placeholder=" " required autofocus>
                                 </div>
                              </div>
                              <div class="col-lg-12">
                                 <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input class="form-control" type="password" placeholder="" name="password" value="">
                                 </div>
                              </div>
                              <div class="col-lg-12 d-flex justify-content-between">
                                 <div class="form-check mb-3">
                                    
                                 </div>
                                 
                              </div>
                              {{-- <div class="col-lg-6">
                                 
                              </div> --}}
                           </div>
                           <div class="d-flex justify-content-center">
                              <button type="submit" class="btn btn-primary">{{ __('Sign In') }}</button>
                           </div>
                           
                        </form>
                     </div>
                  </div>
               </div>
            </div>
           
         </div>
         <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
            <a  href="{{ route('home') }}"> <img src="img\01.png" class="img-fluid gradient-main animated-scaleX" alt="images"></a>
         </div>
      </div>
   </section>
</x-guest-layout>
