<div class="col-xl-12 col-sm-12 col-12">
    <div class="col-12"></div>
    <div class="card">     
    <table id="post-manager" class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
    <thead>
    <tr>
    <th>Name</th>
    <th>Amount</th>
    <th>Created At</th>
    <th>Status</th>
    </tr>
    </thead>
    <tbody> 
    @foreach($latestEarnings as $latestEarning)
    <tr>
    <th>{{$latestEarning->user->name ?? ''}} {{$latestEarning->user->lastname ?? ''}}</th>
    <th>{{$latestEarning->amount ?? '' }}</th>
    <th>{{dateFormat($latestEarning->amount) ?? '' }}</th>
    <th>{!! $latestEarning->status=='success' ? '<button type="button" class="btn btn-outline-success btn-xs">'.$latestEarning->status.'</button>' : '<button type="button" class="btn btn-outline-info btn-xs">'.$latestEarning->status.'</button>' !!}</th>
    </tr>
    @endforeach
    @if(count($latestEarnings) <=0)
      <tr>
          <td colspan="14">No data available</td>
        </tr>
    @endif
    </tbody>
    </table>
    </div>
</div>