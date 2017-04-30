
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Statistics</h1>  
          <div class="col-xl-4 col-lg-6" style="float: left;">
               <div class="card card-inverse card-success" style="height: 180px">
                   <div class="card-block bg-success">
                        <div class="rotate">
                            <i class="fa fa-list fa-4x"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Online Visitors</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::countOnlineVisitors();  ?></h8>
                     </div>
                 </div>
              </div>
            <div class="col-xl-4 col-lg-6" style="float: left;">
               <div class="card card-inverse card-warning" style="height: 180px">
                   <div class="card-block bg-warning">
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
                   <div class="card-block bg-danger">
                        <div class="rotate">
                            <i class="fa fa-list fa-4x"></i>
                         </div>
                         <h6 class="text-uppercase" style="font-size: 35px">Unique Visitors</h6>
                         <h8 class="display-1" style="font-size: 30px"><?php echo Statistics::countAllUniqueVisitors();  ?></h8>
                     </div>
                 </div>
              </div>
        </main>
      </div>
    </div>