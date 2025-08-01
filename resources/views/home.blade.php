@extends('layouts.admin')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
@section('content')

<div class="container align-items-center grey-bg">
  <section id="minimal-statistics">
    <div class="row">
      <div class="col-12 mt-3 mb-1">
        
      </div>
    </div>
    <div class="row">
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card text-bg-primary">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                <div class="emdia-body text-left">
                 <!--  <h6 class="success text-white">Total Users: {{$usersCount ?? 0}}</h6><br>
                  <h6 class="success text-white">Total Orders: {{$order ?? 0}}</h6><br>
                  <h6 class="success text-white">Earnings: {{config('app.currency_sign')}}{{$summary[0]->balance ?? 0}}</h6> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card text-bg-info">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                {{-- <div class="align-self-center">
                  <i class="icon-pointer danger font-large-2 float-left"></i>
                </div> --}}
                <div class="media-body text-left">
                <!--   <h6 class="success text-white">Total Giveaway : {{$giveawayCount ?? 0}}</h6><br>
                  <h6 class="success text-white">Giveaway purchased: {{$giveawayOrderCount ?? 0}}</h6><br>
                  <h6 class="success text-white">Earnings: {{$orderAmountGiveaway ?? 0}}</h6> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12"> 
        <div class="card text-bg-secondary">
          <div class="card-content">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
                {{-- <div class="align-self-center">
                  <i class="icon-pencil primary font-large-2 float-left"></i>
                </div> --}}
                <div class="media-body text-left">
                  <!-- <h6 class="success text-white">Total Quiz : {{$quizCount ?? 0}}</h6><br>
                  <h6 class="success text-white">Quiz Attempts: {{$quizOrderCount ?? 0}}</h6><br>
                  <h6 class="success text-white">Earnings: {{$orderAmountQuiz ?? 0}}</h6> -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6 col-12">
        <div class="card text-bg-warning">
          <div class="card-content" style="height:190px">
            <div class="card-body">
              <div class="media d-flex justify-content-center">
               {{--  <div class="align-self-center">
                  <i class="icon-graph success font-large-2 float-left"></i>
                </div> --}}
                <div class="media-body text-left">
                
                  <!-- <h6 class="success text-white">Spinner played : {{$spinnerOrderCount ?? 0}}</h6><br> -->
                 
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<!-- 
    <div class="row pt-2">
      <div class="col-6">
        <div class="card">
          <div class="card-content">
            <div class="card-body">
          <div id="myChart" style="width:100%; height:500px;"></div>
            </div>
           </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card">
          <div class="card-content">
            <div class="card-body">
          <div id="myChartline" style="width:100%; height:500px;"></div>
            </div>
           </div>
        </div>
      </div>
      
    </div> -->

<!--     <div class="row pt-2">
      <div class="col-12">
        <div class="card">
          <div class="card-content">
            <div class="card-body">
            <div id="statewiselinechart" style="width:100%; height:500px;"></div>
            </div>
           </div>
        </div>
      </div>
    </div> -->
  </section>
</div>


<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartLine);

function drawChartLine() {
const data = google.visualization.arrayToDataTable([
  ['Contry', ''],
  ['User',{{$usersCount ?? 0}}],
  ['Winner',{{$summary[0]->total_winnings ?? 0}}],
  ['Earning',{{$summary[0]->balance ?? 0}}],
  ['Deposit',{{$summary[0]->total_deposits ?? 0}}],
  ['Withdrawal',{{$summary[0]->total_withdrawals ?? 0}}]
]);


const options = {
  title:'User, Winner, Earning, Deposit, Withdrawal '
};

const chart = new google.visualization.BarChart(document.getElementById('myChartline'));
  chart.draw(data, options);
}
</script>
<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
const data = google.visualization.arrayToDataTable([
  ['Contry', 'Mhl'],
  ['Quiz order',{{$quizOrderCount ?? 0}}],
  ['Giveaway order',{{$giveawayOrderCount ?? 0}}],
  ['spinner order',{{$spinnerOrderCount ?? 0}}],
]);

const options = {
  title:'Total order: <?php echo $order ?? 0 ?>, Quiz order, Giveaway order, spinner order',
  is3D:true
};

const chart = new google.visualization.PieChart(document.getElementById('myChart'));
  chart.draw(data, options);
}
</script>

<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChartLine);

function drawChartLine() {
const data = google.visualization.arrayToDataTable([
  ['Contry', ''],

  @if(!empty($usersStateWiseCounts))
      @foreach($usersStateWiseCounts as $val)
        [ '{{ $val->user_state }}' , {{ $val->state_count ?? 0 }} ],
      @endforeach
  @endif

]);


const options = {
  title:'State Vs User'
};

const chart = new google.visualization.BarChart(document.getElementById('statewiselinechart'));
  chart.draw(data, options);
}
</script>
</div>
@endsection
