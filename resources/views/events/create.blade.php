@extends('layouts.admin')

@section('title')
    Admin | Feature Event Add
@endsection

@section('content')

<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <h4 class="py-3 mb-4"><span class="text-muted fw-light">Create/</span>Event</h4>
  <div class="row">
    <div class="col-xl">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"> Event Create</h5>
        </div>
        @php $e = $event ?? null; @endphp
        <div class="card-body">
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
          <form  action="{{route('events.store')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
            <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="title">Title<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="title"
                  id="slug-source"
                  value="{{ old('title', $e->title ?? '') }}"
                  placeholder="Event Title"/>
              </div>
              @if ($errors->has('title'))
                <span class="text-danger text-left">{{ $errors->first('title') }}</span>
              @endif
            </div>
          </div>
          <div class="col-6">
            <div class="mb-3">
                  <label class="form-label" for="title">slug<span class="text-danger">*</span></label>
                  <div class="input-group input-group-merge">
                    <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                    <input
                      type="text"
                      class="form-control"
                      name="slug"
                      id="slug-target"
                      value="{{ old('slug', $e->slug ?? '') }}"
                      placeholder="Slug"
                      />
                  </div>
                  @if ($errors->has('slug'))
                    <span class="text-danger text-left">{{ $errors->first('slug') }}</span>
                  @endif
            </div>
           </div>
           </div>

           <div class="row">
           <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="start_date">Start Date<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="date"
                  class="form-control"
                  name="start_date"
                  id="slug-source"
                  value="{{ old('start_date', isset($e) ? $e->start_date->format('Y-m-d') : '') }}"
                  placeholder="Start date"/>
              </div>
              @if ($errors->has('start_date'))
                <span class="text-danger text-left">{{ $errors->first('start_date') }}</span>
              @endif
            </div>
          </div>

         <div class="col-6">
             <div class="mb-3">
              <label class="form-label" for="end_date">End Date<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="date"
                  class="form-control"
                  name="end_date"
                  id="slug-source"
                  value="{{ old('end_date', isset($e) ? $e->end_date->format('Y-m-d') : '') }}"
                  placeholder="End date"/>
              </div>
              @if ($errors->has('end_date'))
                <span class="text-danger text-left">{{ $errors->first('end_date') }}</span>
              @endif
            </div>
          </div>
          </div>
             
           <div class="row">
  <div class="col-6">
    <div class="mb-3">
      <label class="form-label" for="location">Location<span class="text-danger">*</span></label>
      <div class="input-group input-group-merge">
        <span id="title-icon" class="input-group-text"><i class="bx bx-map"></i></span>
        <input
          type="text"
          class="form-control"
          name="location"
          id="location"
          value="{{ old('location', $e->location ?? '') }}"
          placeholder="Event Location"/>
      </div>
      @if ($errors->has('location'))
        <span class="text-danger text-left">{{ $errors->first('location') }}</span>
      @endif
    </div>
  </div>

  <div class="col-6">
    <div class="mb-3">
      <label class="form-label" for="youtube_link">YouTube Link</label>
      <div class="input-group input-group-merge">
        <span id="title-icon" class="input-group-text"><i class="bx bxl-youtube"></i></span>
        <input
          type="url"
          class="form-control"
          name="youtube_link"
          id="youtube_link"
          value="{{ old('youtube_link', $e->youtube_link ?? '') }}"
          placeholder="https://www.youtube.com/watch?v=xxxx"/>
      </div>
      @if ($errors->has('youtube_link'))
        <span class="text-danger text-left">{{ $errors->first('youtube_link') }}</span>
      @endif
    </div>
  </div>
</div>


            <div class="mb-3">
              <label class="form-label" for="description">Description<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge" id="quill-editor" style="height: 300px;">
                <textarea
                  type="text"
                  name="description"
                  id="description1"
                  class="form-control"
                  placeholder="Event Description"
                  rows="8" cols="50"
                >{{ old('description', $e->description ?? '') }}</textarea>
              </div>
              @if ($errors->has('description'))
                <span class="text-danger text-left">{{ $errors->first('description') }}</span>
              @endif
            </div>
            
            <div class="row">
                <div class="col-6">
                        <div class="mb-3">
                          <label class="form-label" for="title">Status<span class="text-danger">*</span></label>
                          <div class="input-group input-group-merge">
                            <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                            <select class="form-control" name="status">
                                @foreach(['draft', 'published', 'cancelled'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $e->status ?? '') === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                          </div>
                          @if ($errors->has('status'))
                            <span class="text-danger text-left">{{ $errors->first('status') }}</span>
                          @endif
                        </div>
                </div>
                <div class="col-6">
                    <div class="mb-3">
                      <label class="form-label" for="title">Visibility<span class="text-danger">*</span></label>
                      <div class="input-group input-group-merge">
                        <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                        <select class="form-control" name="visibility">
                            @foreach(['public', 'private', 'unlisted'] as $visibility)
                                <option value="{{ $visibility }}" @selected(old('visibility', $e->visibility ?? '') === $visibility)>
                                    {{ ucfirst($visibility) }}
                                </option>
                            @endforeach
                        </select>
                      </div>
                      @if ($errors->has('visibility'))
                        <span class="text-danger text-left">{{ $errors->first('visibility') }}</span>
                      @endif
                    </div>
                </div>
            </div>
            <div class="mb-3">
              <label class="form-label" for="title">Image<span class="text-danger">*</span><span class="text-danger">(Allowed file size : {{config('app.blog_image_size')." KB and allowed file type ".config('app.image_mime_types') }}) </span> </label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="file"
                  class="form-control"
                  name="image"
                  id="image"/>
              </div>
              @if ($errors->has('image'))
                <span class="text-danger text-left">{{ $errors->first('image') }}</span>
              @endif
            </div>
             

             <div class="mb-3">
              <label class="form-label" for="title">Tags<span class="text-danger">*</span></label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="tags"
                  id="tags"
                  value="{{ old('tags') }}"
                  placeholder="Event tags"/>
              </div>
              @if ($errors->has('tags'))
                <span class="text-danger text-left">{{ $errors->first('tags') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="about">About</label>
              <input type="hidden" name="meta_description" id="about" value="">
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea class="form-control description-cls" id="about" name="about" rows="12" placeholder="Paste or write about us here...">{{ old('about') }}</textarea>

              </div>
              @if ($errors->has('about'))
                <span class="text-danger text-left">{{ $errors->first('about') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="privacy_policy">Privacy Policy</label>
              <input type="hidden" name="meta_description" id="privacy_policy" value="">
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea class="form-control description-cls" id="privacy_policy" name="privacy_policy" rows="12" placeholder="Paste or write about us here...">{{ old('privacy_policy') }}</textarea>

              </div>
              @if ($errors->has('privacy_policy'))
                <span class="text-danger text-left">{{ $errors->first('privacy_policy') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="terms_condition">Terms & Condition</label>
              <input type="hidden" name="meta_description" id="terms_condition" value="">
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea class="form-control description-cls" id="terms_condition" name="terms_condition" rows="12" placeholder="Paste or write about us here...">{{ old('terms_condition') }}</textarea>

              </div>
              @if ($errors->has('terms_condition'))
                <span class="text-danger text-left">{{ $errors->first('terms_condition') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="help_support">Help & Support</label>
              <input type="hidden" name="meta_description" id="help_support" value="">
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea class="form-control description-cls" id="help_support" name="help_support" rows="12" placeholder="Paste or write about us here...">{{ old('help_support') }}</textarea>

              </div>
              @if ($errors->has('help_support'))
                <span class="text-danger text-left">{{ $errors->first('help_support') }}</span>
              @endif
            </div>

             <div class="mb-3">
              <label class="form-label" for="meta_title">Meta title</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="meta_title"
                  id="meta_title"
                  value="{{ old('meta_title', $e->meta_title ?? '') }}"
                  placeholder="Event meta title"/>
              </div>
              @if ($errors->has('meta_title'))
                <span class="text-danger text-left">{{ $errors->first('meta_title') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="meta_description">Meta description</label>
              <input type="hidden" name="meta_description" id="meta_description" value="{{ old('meta_description', $e->meta_description ?? '') }}">
              <div class="input-group input-group-merge" id="quill-editor1" style="height: 300px;">
                <textarea
                  type="text"
                  name="meta_description"
                  id="meta_description"
                  class="form-control"
                  placeholder="Meta description"
                  rows="8" cols="50"
                >{{ old('meta_description', $e->meta_description ?? '') }}</textarea>

              </div>
              @if ($errors->has('meta_description'))
                <span class="text-danger text-left">{{ $errors->first('meta_description') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <label class="form-label" for="meta_description">Meta keywords</label>
              <div class="input-group input-group-merge">
                <span id="title-icon" class="input-group-text"><i class="bx bx-book"></i></span>
                <input
                  type="text"
                  class="form-control"
                  name="meta_keywords"
                  id="meta_keywords"
                  value="{{ old('meta_keywords') }}"
                  placeholder="Event meta description"/>
              </div>
              @if ($errors->has('meta_keywords'))
                <span class="text-danger text-left">{{ old('meta_keywords', $e->meta_keywords ?? '') }}</span>
              @endif
            </div>



          <div class="d-flex pt-3 justify-content-end">
             <a href="{{route('events.index')}}" class="btn btn-outline-primary btn-pill btn-streach font-book ml-3 mt-6 fs-14 me-2">Cancel</a>
            <button type="submit" class="btn btn-primary btn-streach font-book mt-6 fs-14 add_user">Save</button>
          </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
  $("#slug-source").keyup(function() {
      var Text = $(this).val();
      Text = slugify(Text);
      $("#slug-target").val(Text);        
  });

  $("#slug-source").blur(function() {
      var Text = $(this).val();
      Text = slugify(Text);
      $("#slug-target").val(Text);        
  });

function slugify(str) {
  str = str.replace(/^\s+|\s+$/g, ''); // trim leading/trailing white space
  str = str.toLowerCase(); // convert string to lowercase
  str = str.replace(/[^a-z0-9 -]/g, '') // remove any non-alphanumeric characters
           .replace(/\s+/g, '-') // replace spaces with hyphens
           .replace(/-+/g, '-'); // remove consecutive hyphens
  return str.replace(/^-+|-+$/g, '');
}
</script>
@endsection