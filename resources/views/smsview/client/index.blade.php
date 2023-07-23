@extends('layouts.smsapp')

@section('appbody')
    <!-- content -->
  <div id="content" class="app-content" role="main">
    <div class="app-content-body ">
        <div class="hbox hbox-auto-xs hbox-auto-sm" ng-init="
        app.settings.asideFolded = false; 
        app.settings.asideDock = false;
        ">
        <!-- main -->
        <div class="col">
        <!-- main header -->
        <div class="bg-light lter b-b wrapper-md">
            <div class="row">
            @include('smsview.client.client-admin-subhead')
            <div class="col-sm-6 text-right hidden-xs">
                
            </div>
            </div>
        </div>
        <!-- / main header -->
        <div class="wrapper-md" ng-controller="FlotChartDemoCtrl">
            <!-- stats -->
            <div class="row row-sm text-center">
                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>TODAY'S SMS SENT</span></div>
                            <span class="font-thin h6 font-bold">{{$totalsentsms}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS WEEK SMS SENT</span></div>
                            <span class="font-thin h6 font-bold">{{$thisweeksentsmsbal}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4 col-xs-12 m-b-md">
                    <div class="r bg-success dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 15px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h6"><span>THIS MONTH SMS SENT</span></div>
                            <span class="font-thin h6 font-bold">{{$thismonthsentsmsbal}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-sm text-center">
              <div class="col-md-6">
                <!--
                <table class="table table-responsive">
                  <thead>
                    <tr>
                      <th>Particular</th>
                      <th>Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Total Mask Purchased</td>
                      <td><?php echo $totalData['maskPurchase'] ?></td>
                    </tr>
                    <tr>
                      <td>Total Non-mask Purchased</td>
                      <td><?php echo $totalData['nonmaskPurchase'] ?></td>
                    </tr>
                    <tr>
                      <td>Total Mask Sent</td>
                      <td><?php echo $totalData['maskSent'] ?></td>
                    </tr>
                    <tr>
                      <td>Total Non-mask Sent</td>
                      <td><?php echo $totalData['nonmaskSent'] ?></td>
                    </tr>
                  </tbody>
                  
                </table>
                -->

              </div>
              
            </div>

            


            <!-- <div class="row">
           
            <div class="col-md-12">

                <div class="tab-container">
                    <ul class="nav nav-tabs">
                      <li class="active font-bold"><a href data-toggle="tab" data-target="#tab_1">TODAY'S SALE GRAPH <span class="badge badge-sm m-l-xs">16</span></a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_2">THIS MONTH SALE GRAPH <span class="badge bg-danger badge-sm m-l-xs">6</span></a></li>
                      <li class="font-bold"><a href data-toggle="tab" data-target="#tab_3">THIS YEAR SALE GRAPH <span class="badge bg-primary badge-sm m-l-xs">9</span></a></li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab_1">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold">TODAY'S SALE GRAPH</div>
                            <div class="panel-body">
                              <div ui-jq="plot" ui-options="
                                [
                                  { data: [ [1,6.5],[2,6.5],[3,7],[4,8],[5,7.5],[6,7],[7,6.8],[8,7],[9,7.2],[10,7],[11,6.8],[12,7] ], points: { show: true, radius: 6}, splines: { show: true, tension: 0.45, lineWidth: 5, fill: 0 } }
                                ], 
                                {
                                  colors: ['#23b7e5'],
                                  series: { shadowSize: 3 },
                                  xaxis:{ 
                                    font: { color: '#ccc' },
                                    position: 'bottom',
                                    ticks: [
                                      [ 1, 'Jan' ], [ 2, 'Feb' ], [ 3, 'Mar' ], [ 4, 'Apr' ], [ 5, 'May' ], [ 6, 'Jun' ], [ 7, 'Jul' ], [ 8, 'Aug' ], [ 9, 'Sep' ], [ 10, 'Oct' ], [ 11, 'Nov' ], [ 12, 'Dec' ]
                                    ]
                                  },
                                  yaxis:{ font: { color: '#ccc' } },
                                  grid: { hoverable: true, clickable: true, borderWidth: 0, color: '#ccc' },
                                  tooltip: true,
                                  tooltipOpts: { content: '%x.1 is %y.4',  defaultTheme: false, shifts: { x: 0, y: 20 } }
                                }
                              " style="height:240px" >
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class="tab-pane" id="tab_2">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold">THIS MONTH SALE GRAPH</div>
                            <div class="panel-body">
                              <div ui-jq="plot" ui-options="
                                [
                                  { data: [ [1,6.5],[2,6.5],[3,7],[4,8],[5,7.5],[6,7],[7,6.8],[8,7],[9,7.2],[10,7],[11,6.8],[12,7] ], points: { show: true, radius: 6}, splines: { show: true, tension: 0.45, lineWidth: 5, fill: 0 } }
                                ], 
                                {
                                  colors: ['#23b7e5'],
                                  series: { shadowSize: 3 },
                                  xaxis:{ 
                                    font: { color: '#ccc' },
                                    position: 'bottom',
                                    ticks: [
                                      [ 1, 'Jan' ], [ 2, 'Feb' ], [ 3, 'Mar' ], [ 4, 'Apr' ], [ 5, 'May' ], [ 6, 'Jun' ], [ 7, 'Jul' ], [ 8, 'Aug' ], [ 9, 'Sep' ], [ 10, 'Oct' ], [ 11, 'Nov' ], [ 12, 'Dec' ]
                                    ]
                                  },
                                  yaxis:{ font: { color: '#ccc' } },
                                  grid: { hoverable: true, clickable: true, borderWidth: 0, color: '#ccc' },
                                  tooltip: true,
                                  tooltipOpts: { content: '%x.1 is %y.4',  defaultTheme: false, shifts: { x: 0, y: 20 } }
                                }
                              " style="height:240px" >
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class="tab-pane" id="tab_3">
                        <div class="panel panel-default">
                            <div class="panel-heading font-bold">THIS YEAR SALES GRAPH</div>
                            <div class="panel-body">
                              <div ui-jq="plot" ui-options="
                                [
                                  { data: [ [1,6.5],[2,6.5],[3,7],[4,8],[5,7.5],[6,7],[7,6.8],[8,7],[9,7.2],[10,7],[11,6.8],[12,7] ], points: { show: true, radius: 6}, splines: { show: true, tension: 0.45, lineWidth: 5, fill: 0 } }
                                ], 
                                {
                                  colors: ['#23b7e5'],
                                  series: { shadowSize: 3 },
                                  xaxis:{ 
                                    font: { color: '#ccc' },
                                    position: 'bottom',
                                    ticks: [
                                      [ 1, 'Jan' ], [ 2, 'Feb' ], [ 3, 'Mar' ], [ 4, 'Apr' ], [ 5, 'May' ], [ 6, 'Jun' ], [ 7, 'Jul' ], [ 8, 'Aug' ], [ 9, 'Sep' ], [ 10, 'Oct' ], [ 11, 'Nov' ], [ 12, 'Dec' ]
                                    ]
                                  },
                                  yaxis:{ font: { color: '#ccc' } },
                                  grid: { hoverable: true, clickable: true, borderWidth: 0, color: '#ccc' },
                                  tooltip: true,
                                  tooltipOpts: { content: '%x.1 is %y.4',  defaultTheme: false, shifts: { x: 0, y: 20 } }
                                }
                              " style="height:240px" >
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </tabset>
                </div>
                
            </div>
            </div> -->
            <!-- / stats -->

            <!-- service -->
            
            <!-- / service -->

            <!-- tasks -->
            
            <!-- / tasks -->

        </div>
        </div>
        <!-- / main -->
        <!-- right col -->
        <!-- <div class="col w-md bg-white-only b-l bg-auto no-border-xs">
            <div class="row row-sm text-center">
                <div class="col-xs-12 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 25px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h4"><span>MASK SMS BALANCE</span></div>
                            <span class="text-white-dk font-thin font-bold h1">$totalmaskbal</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-sm text-center">
                <div class="col-xs-12 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 25px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h4"><span>NON MASK SMS BALANCE</span></div>
                            <span class="text-white-dk font-thin font-bold h1">$totalnonmaskbal</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-sm text-center">
                <div class="col-xs-12 m-b-md mt-3">
                    <div class="r bg-primary dker item hbox no-border">
                        <div class="col w-xs v-middle hidden-md">
                            <div class="sparkline inline"><i class="icon-envelope text-white-dker" style="font-size: 25px;"></i></div>
                        </div>
                        <div class="col dk padder-v r-r">
                            <div class="text-white-dk font-thin font-bold h4"><span>VOICE SMS BALANCE</span></div>
                            <span class="text-white-dk font-thin font-bold h1">$totalvoicebal</span>
                        </div>
                    </div>
                </div>
            </div>
        
        </div> -->
        <!-- / right col -->
        </div>



        </div>
        </div>
        <!-- /content -->
@endsection