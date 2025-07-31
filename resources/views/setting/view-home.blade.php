@extends('layouts.admin')

@section('title')
    Admin | Home Settings
@endsection

@section('content')
<div class="container flex-grow-1 container-p-y pt-0">
    <h4 class="py-3 mb-4"><span class="text-muted fw-light">Home Settings</span></h4>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0">Home Setting</h5>
		    </div> 	
            <div class="card-body pt-0">	
	            <div class="row"> 
		             <form  action="{{route('settings.store')}}" method="POST" enctype="multipart/form-data">	
		            {{ csrf_field() }}
		            
                    <input type="hidden" name="setting" value="home" />
                    <div class="row">
                    	<div class="col-6">
							<div class="custom-control custom-radio custom-control-inline mb-3">
							    <input type="radio" class="custom-control-input float-right" id="Giveaway-on-top" name="giveaway_quiz_on" value="{{getKeyValue('giveaway_on_top')->key}}" {{getKeyValue('giveaway_on_top')->value==1 ? 'checked' : ''}} >
							    <label class="custom-control-label" for="Giveaway-on-top">Giveaway on top</label>
							</div>	
                    	</div>

                    	<div class="col-6">
						  <div class="custom-control custom-radio custom-control-inline mb-3">
						    <input type="radio" class="custom-control-input float-right" id="Quiz-on-top" name="giveaway_quiz_on" value="{{getKeyValue('quiz_on_top')->key}}"  {{getKeyValue('quiz_on_top')->value==1 ? 'checked' : ''}}>
						    <label class="custom-control-label" for="Quiz-on-top">Quiz on top</label>
						  </div>
                    	</div>
                    </div>   
                    
                    <div class="row">
	                    <div class="col-2">  	
				            <div class="mb-3">
				              <div class="input-group input-group-merge">
				     
				                <input
				                  type="text"
				                  class="form-control"
				                  name=""
				                  id=""
				                  value="{{getKeyValue('home_page_link_1')->key}}"
				                  readonly
				                  />
				              </div>
				            </div>
			            </div> 
			            <div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                  type="text"
				                  class="form-control"
				                  name="{{getKeyValue('home_page_link_1')->key}}"
				                  id="{{getKeyValue('home_page_link_1')->key}}"
				                  value="{{getKeyValue('home_page_link_1')->value}}"
				                  placeholder="Value"/>
				              </div>
				            </div>
			            </div>
			            <div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_image_1"
				                    id="home_page_image_1"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_link_1')->photo !=null && getKeyValue('home_page_link_1')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_link_1')->photo && getKeyValue('home_page_link_1')->photo->file_path ? getKeyValue('home_page_link_1')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				            </div>
				        </div>    
		            </div>

		            <div class="row">
                    <div class="col-2">  	
			            <div class="mb-3">
			              <div class="input-group input-group-merge">
			     
			                <input
			                  type="text"
			                  class="form-control"
			                  name=""
			                  id=""
			                  value="{{getKeyValue('home_page_link_2')->key}}"
			                  readonly
			                  />
			              </div>
			            </div>
		            </div> 
		            <div class="col-4">  	
			            <div class="mb-3">
			              <div class="input-group input-group-merge">
			                <input
			                  type="text"
			                  class="form-control"
			                  name="{{getKeyValue('home_page_link_2')->key}}"
			                  id="{{getKeyValue('home_page_link_2')->key}}"
			                  value="{{getKeyValue('home_page_link_2')->value}}"
			                  placeholder="Value"/>
			              </div>
			            </div>
		            </div> 
			            <div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_image_2"
				                    id="home_page_image_2"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_link_2')->photo !=null && getKeyValue('home_page_link_2')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_link_2')->photo && getKeyValue('home_page_link_2')->photo->file_path ? getKeyValue('home_page_link_2')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				            </div>
				        </div>    
		            </div>

		            <div class="row">
                    <div class="col-2">  	
			            <div class="mb-3">
			              <div class="input-group input-group-merge">
			     
			                <input
			                  type="text"
			                  class="form-control"
			                  name=""
			                  id=""
			                  value="{{getKeyValue('home_page_link_3')->key}}"
			                  readonly
			                  />
			              </div>
			            </div>
		            </div> 
		            <div class="col-4">  	
			            <div class="mb-3">
			              
			              <div class="input-group input-group-merge">
			     
			                <input
			                  type="text"
			                  class="form-control"
			                  name="{{getKeyValue('home_page_link_3')->key}}"
			                  id="{{getKeyValue('home_page_link_3')->key}}"
			                  value="{{getKeyValue('home_page_link_3')->value}}"
			                  placeholder="Value"/>
			              </div>
			            </div>
		            </div> 
			            <div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_image_3"
				                    id="home_page_image_3"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_link_3')->photo !=null && getKeyValue('home_page_link_3')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_link_3')->photo && getKeyValue('home_page_link_3')->photo->file_path ? getKeyValue('home_page_link_3')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				             </div>
				        </div>
		            </div>

		            <div class="row">
                    <div class="col-2">  	
			            <div class="mb-3">
			              <div class="input-group input-group-merge">
			     
			                <input
			                  type="text"
			                  class="form-control"
			                  name=""
			                  id=""
			                  value="{{getKeyValue('home_page_link_4')->key}}"
			                  readonly
			                  />
			              </div>
			            </div>
		            </div> 
		            <div class="col-4">  	
			            <div class="mb-3">
			              
			              <div class="input-group input-group-merge">
			     
			                <input
			                  type="text"
			                  class="form-control"
			                  name="{{getKeyValue('home_page_link_4')->key}}"
			                  id="{{getKeyValue('home_page_link_4')->key}}"
			                  value="{{getKeyValue('home_page_link_4')->value}}"
			                  placeholder="Value"/>
			              </div>
			            </div>
		            </div>
		      
			            <div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_image_4"
				                    id="home_page_image_4"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_link_4')->photo !=null && getKeyValue('home_page_link_4')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_link_4')->photo && getKeyValue('home_page_link_4')->photo->file_path ? getKeyValue('home_page_link_4')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				             </div>
				        </div>
		            </div>
					<div class="row">
						<div class="col-2">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
					 
								<input
								  type="text"
								  class="form-control"
								  name=""
								  id=""
								  value="{{getKeyValue('home_page_giveaways')->key}}"
								  readonly
								  />
							  </div>
							</div>
						</div> 

						<div class="col-4">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
								<input
								  type="text"
								  class="form-control"
								  name="{{getKeyValue('home_page_giveaways')->key}}"
								  id="{{getKeyValue('home_page_giveaways')->key}}"
								  value="{{getKeyValue('home_page_giveaways')->value}}"
								  placeholder="Value"/>
							  </div>
							</div>
						</div>

						<div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_giveaways_image"
				                    id="home_page_giveaways_image"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_giveaways')->photo !=null && getKeyValue('home_page_giveaways')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_giveaways')->photo && getKeyValue('home_page_giveaways')->photo->file_path ? getKeyValue('home_page_giveaways')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				             </div>
				        </div>
					</div>

					<div class="row">
						<div class="col-2">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
					 
								<input
								  type="text"
								  class="form-control"
								  name=""
								  id=""
								  value="{{getKeyValue('home_page_quizzes')->key}}"
								  readonly
								  />
							  </div>
							</div>
						</div> 

						<div class="col-4">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
								<input
								  type="text"
								  class="form-control"
								  name="{{getKeyValue('home_page_quizzes')->key}}"
								  id="{{getKeyValue('home_page_quizzes')->key}}"
								  value="{{getKeyValue('home_page_quizzes')->value}}"
								  placeholder="Value"/>
							  </div>
							</div>
						</div>

						<div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_quizzes_image"
				                    id="home_page_quizzes_image"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_quizzes')->photo !=null && getKeyValue('home_page_quizzes')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_quizzes')->photo && getKeyValue('home_page_quizzes')->photo->file_path ? getKeyValue('home_page_quizzes')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				             </div>
				        </div>
					</div>

					<div class="row">
						<div class="col-2">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
					 
								<input
								  type="text"
								  class="form-control"
								  name=""
								  id=""
								  value="{{getKeyValue('home_page_spinners')->key}}"
								  readonly
								  />
							  </div>
							</div>
						</div> 

						<div class="col-4">  	
							<div class="mb-3">
							  <div class="input-group input-group-merge">
								<input
								  type="text"
								  class="form-control"
								  name="{{getKeyValue('home_page_spinners')->key}}"
								  id="{{getKeyValue('home_page_spinners')->key}}"
								  value="{{getKeyValue('home_page_spinners')->value}}"
								  placeholder="Value"/>
							  </div>
							</div>
						</div>

						<div class="col-4">  	
				            <div class="mb-3">
				              
				              <div class="input-group input-group-merge">
				     
				                <input
				                    type="file"
				                    class="form-control"
				                    name="home_page_spinners_image"
				                    id="home_page_spinners_image"
				                    value=""/>
				              </div>
				            </div>
			            </div>
			            <div class="col-2">  	
				            <div class="mb-3">
				            	@if(getKeyValue('home_page_spinners')->photo !=null && getKeyValue('home_page_spinners')->photo->file_path)
				            	<img src="{{getKeyValue('home_page_spinners')->photo && getKeyValue('home_page_spinners')->photo->file_path ? getKeyValue('home_page_spinners')->photo->file_path : ''}}" alt="User Image" width="50%">
				            	@endif
				             </div>
				        </div>
					</div>

		              <div class="d-flex pt-3 justify-content-end">
                        <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
                       </div>
		          </form>
	            </div>
            </div>
				
				<div class="card-body pt-0">
					@if(Session::has('success'))
		              <div class="alert alert-success">
		              {{ Session::get('success') }}
		              </div>
		          @endif
		          @if(Session::has('error'))
		              <div class="alert alert-danger">
		              {{ Session::get('error') }}
		              </div>
		          @endif

		        </div>
			</div>
		</div>
    </div>
</div>
@endsection
@section('scripts')
 <script>

</script
@endsection