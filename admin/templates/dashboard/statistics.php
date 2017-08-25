
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Statistics</h1>  
          <div class="col-xl-4 col-lg-6" style="float: left;">
               <div class="card card-inverse card-success" style="height: 180px">
                   <div class="card-block bg-success" style=" height: 100%">
                        <div class="rotate" style='display: inline-block;'>
                            <i class="fa fa-list fa-4x" style="display: inline-block"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Online Visitors</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::countOnlineVisitors();  ?></h8>
                     </div>
                 </div>
              </div>
            <div class="col-xl-4 col-lg-6" style="float: left;">
               <div class="card card-inverse card-warning" style="height: 180px">
                   <div class="card-block bg-warning" style=" height: 100%">
                        <div class="rotate">
                            <i class="fa fa-list fa-4x"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Today Visitors</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::countAllVisitorsTodayOnly();  ?></h8>
                     </div>
                 </div>
              </div>
            <div class="col-xl-4 col-lg-6" style="float: left;">
               <div class="card card-inverse card-danger" style="height: 180px">
                   <div class="card-block bg-danger" style=" height: 100%">
                        <div class="rotate">
                            <i class="fa fa-list fa-4x"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Unique Visitors</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::countAllUniqueVisitors();  ?></h8>
                     </div>
                 </div>
              </div>
              <div class="col-xl-4 col-lg-6" style="float: left; height: auto !important; min-height: 180px;">
               <div class="card card-inverse card-danger" style="height: auto; min-height: 180px; margin-top: 15px; border-color: purple !IMPORTANT;">
                   <div class="card-block bg-danger" style="background-color: purple !IMPORTANT; height: 100%" >
                        <div class="rotate">
                            <i class="fa fa-list fa-4x"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Best day</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::BestDay();  ?></h8>
                     </div>
                 </div>
              </div>
              <table class="table table-striped" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Page</th>
                        <th>IP</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $items = Statistics::GetAll();
                        $items = json_decode($items, True);
                        $i = 0;
                        foreach($items as $item):
                    ?>
                        <tr>
                            <td><?php echo $i; $i++; ?></td>
                            <td><?php echo $item['Page']; ?></td>
                            <td><?php echo $item['IP']; ?></td>
                            <td><?php echo $item['VisitDate']; ?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
              </table>
        </main>
      </div>
    </div>
    