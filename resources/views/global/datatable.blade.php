@push('scripts')
    {{ $dataTable->scripts() }}
    
@endpush


<x-app-layout :assets="$assets ?? []">
<div>
   
@if(request()->is('products*'))
   <div class="col-md-12">
       <div class="card" data-aos="fade-up" data-aos-delay="1000">
           <div class="flex-wrap card-header d-flex justify-content-between">
               <div class="header-title">
                   <h4 class="card-title">Warehouse Capacity Utilization Per Zone</h4>
               </div>
           </div>
           <div class="card-body">
               <div id="capacity" class="capacity" data-zones='@json($zoneCapacity)'></div>
           </div>
       </div>
   </div>
@endif
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
            <div class="card-header d-flex justify-content-between">
               <div class="header-title">
                  <h4 class="card-title">{{ $pageTitle ?? 'List'}}</h4>
               </div>
               <div class="card-action">
                   {!! $headerAction ?? '' !!}
               </div>
            </div>
            <div class="card-body px-0">
               <div class="table-responsive">
                    {{ $dataTable->table(['class' => 'table text-center table-striped w-100'],true) }}
               </div>
            </div>
         </div>
      </div>
   </div>


</div>



<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let chartElement = document.querySelector("#capacity");
    if (!chartElement) {
        console.error("Chart container not found!");
        return;
    }
    let zoneData = JSON.parse(chartElement.getAttribute("data-zones"));
    console.log(zoneData);

    if (!zoneData || zoneData.length === 0) {
        console.error("No zone data available!");
        return;
    }

    let categories = zoneData.map(zone => zone.zone_name);
    let usedCapacities = zoneData.map(zone => zone.used_capacity);
    let freeCapacities = zoneData.map(zone => zone.free_capacity);

    const options = {
        series: [
            { name: 'Used Capacity', data: usedCapacities },
            { name: 'Free Capacity', data: freeCapacities }
        ],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
        },
        xaxis: {
            categories: categories
        },
        colors: ["#3a57e8", "#00E396"],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + " slots";
                }
            }
        }
    };

    const chart = new ApexCharts(chartElement, options);
    chart.render();
});
</script>

</x-app-layout>