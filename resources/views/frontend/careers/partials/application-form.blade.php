<section id="apply-now" class="card border-0 shadow-sm mt-5">
  <div class="card-body">
    <h2 class="h4">Application form</h2>
    <form method="POST" action="{{ route('careers.store', $job) }}" enctype="multipart/form-data" novalidate>
      @csrf
      <input type="text" name="website" value="" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
      @include('frontend.careers.partials.fields', ['general' => false, 'job' => $job])
    </form>
  </div>
</section>
