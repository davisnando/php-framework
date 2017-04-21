
  <link rel="stylesheet" href="<?php LoadStatic(); echo GetStaticFile('board','board.css')?>">

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
          <h1>Table system</h1>          
          <h2><?php echo $_SESSION['table']; $table = $_SESSION['table'];?></h2>
          <div class="table-responsive">
           <?php
                  $db = new model();
                  $db->prepare("SELECT * FROM $table");
                  $result = $db->GetAll();
            if(count($result) != 0):
                  $i = 1;
                  $keynames = array_keys($result[0]);

            ?>
            <table class="table table-striped">
              <thead>
                <tr>
                  <?php foreach($keynames as $key):?>
                    <th><?php echo $key;?></th>
                  <?php endforeach;?>
                </tr>
              </thead>
              <tbody>
                <?php foreach($result as $items):?>
                  <tr onclick="window.location = '/admin/changeTable/<?php echo $items[$keynames[0]] ?>'">
                    <?php foreach($items as $item):?>
                      <td><?php echo $item; ?></td>
                    <?php endforeach;?>
                  </tr> 
                <?php endforeach;?>
              </tbody>
            </table>
            <?php else:?>
            <hr>
            <h1>Table Empty</h1>
            <?php endif; ?>
            </div>
        </main>
      </div>
    </div>
    <script>
    $("#Changes").submit(function(){
        var GetData = $('#Changes').serialize();
        $.ajax({
          method: "POST",
          url: "/admin/change",
          data: GetData
        })
          .done(function( msg ) {
              if(msg == "Done"){
                alert("Saved");
              }else{
                alert("something goes wrong!");
              }
          });
        return false;
      });
    </script>