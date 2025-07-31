<h5 class="pb-2 border-bottom mb-4">Ticket Details</h5>
<div class="row pt-2 pb-2">
    <div class="col-6">
    </div>
    <div class="col-6 d-flex justify-content-end">
        
        <span class="btn btn-primary me-2 p-4">Total Ticket: {{$totalCount ?? 0}} </span>
        <span class="btn btn-warning me-2 p-4">Quizzes: {{$quizCount ?? 0}}</span>
        <span class="btn btn-info me-2 p-4">Giveaways: {{$giveawayCount ?? 0}}</span>
        <span class="btn btn-dark p-4">Spinners: {{$spinnerCount ?? 0}}</span>
    </div>
</div>
<table  class="stripe row-border order-column dataTable no-footer table table-striped table-bordered dt-responsive display nowrap">
<thead>
<tr>
<th>Ticket Type</th>
<th>Photo</th>
<th>Name</th>
<th>Amount</th>
<th>winning type</th>
<th>Winning</th>
</tr>
</thead>
<tbody>
    @foreach ($orders as $item)	
    @if($item->table_type=='giveaways')
    <tr>
       <th>{{$item->table_type}} </th>
       @if(!empty($item->giveaway->photo) && $item->giveaway->photo->file_path)
       <th><img src="{{asset($item->giveaway->photo->file_path)  ?? ''}}" alt="{{$item->table_type}}" height="30px;"></th>
       @else
        <th></th>
       @endif
       <th> {{$item->giveaway->name}} </th>
       <th> {{$item->amount}} </th>
       <th> {{$item->winning_type ?? ''}} </th>
       <th> {{$item->winning ?? ''}} </th>
    </tr>
    @endif

    @if($item->table_type=='quizzes')
    <tr>
       <th>{{$item->table_type}} </th>
       @if(!empty($item->quiz->photo) && $item->quiz->photo->file_path)
       <th><img src="{{asset($item->quiz->photo->file_path)  ?? ''}}" alt="{{$item->table_type}}" height="30px;"></th>
       @else
        <th></th>
       @endif
       <th> {{$item->quiz->name}} </th>
       <th> {{$item->amount}} </th>
       <th> {{$item->winning_type ?? ''}} </th>
       <th> {{$item->winning ?? ''}} </th>
    </tr>
    @endif
    @if($item->table_type=='spinners')
    <tr>
       <th>{{$item->table_type}} </th>
       <th></th>
       <th> {{$item->spinner->name}} </th>
       <th> {{$item->amount}} </th>
       <th> {{$item->winning_type ?? ''}} </th>
       <th> {{$item->winning ?? ''}} </th>
    </tr>
    @endif
    @endforeach
    @if(count($orders)<=0)
    <tr>
        <th colspan="14">No data available</th>
    </tr>
    @endif
</tbody>
</table>